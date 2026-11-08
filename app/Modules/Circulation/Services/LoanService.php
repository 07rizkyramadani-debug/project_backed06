<?php

namespace Modules\Circulation\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Modules\Circulation\Events\BookBorrowed;
use Modules\Circulation\Models\Loan;
use Modules\Circulation\Models\LoanDetail;
use Modules\Circulation\ValueObjects\Denda;
use Modules\Shared\Contracts\BookServiceInterface;
use Modules\Shared\Contracts\MemberServiceInterface;
use Modules\Shared\Exceptions\BookNotAvailableException;
use Modules\Shared\Exceptions\MemberCannotBorrowException;

class LoanService
{
    public function __construct(
        private BookServiceInterface $bookService,
        private MemberServiceInterface $memberService
    ) {}

    /**
     * Proses peminjaman dalam satu transaction:
     *   1. validasi anggota (via contract Membership)
     *   2. buat record peminjaman + detail (snapshot buku)
     *   3. kurangi stok buku (via contract Catalog)
     *   4. perbarui status anggota
     * Jika ada langkah yang gagal (mis. stok tidak mencukupi) semua perubahan di-rollback.
     *
     * @param  array<int, array{book_id:int, quantity?:int}>  $items
     */
    public function borrow(int $memberId, array $items, ?int $loanDays = null, ?string $notes = null): Loan
    {
        $loan = DB::transaction(function () use ($memberId, $items, $loanDays, $notes) {
            $member = $this->memberService->getMemberForLoan($memberId);

            if (!$member) {
                throw new MemberCannotBorrowException('Anggota tidak ditemukan.');
            }

            $totalQuantity = (int) collect($items)->sum(fn ($i) => $i['quantity'] ?? 1);

            if ($items === [] || $totalQuantity < 1) {
                throw new MemberCannotBorrowException('Pilih minimal satu buku untuk dipinjam.');
            }

            if ($reason = $this->memberService->getBorrowBlockReason($memberId, $totalQuantity)) {
                throw new MemberCannotBorrowException($reason);
            }

            $loan = Loan::create([
                'member_id' => $member['id'],
                'member_number' => $member['member_number'],
                'member_name' => $member['name'],
                'loan_number' => 'LN-' . strtoupper(uniqid()),
                'borrowed_at' => now(),
                'due_at' => now()->addDays($loanDays ?? $member['loan_days']),
                'status' => Loan::STATUS_BORROWED,
                'notes' => $notes,
            ]);

            foreach ($items as $item) {
                $quantity = (int) ($item['quantity'] ?? 1);
                $book = $this->bookService->getBookForLoan((int) $item['book_id']);

                if (!$book) {
                    throw new BookNotAvailableException("(id {$item['book_id']})", 0, $quantity);
                }

                if (!$this->bookService->isAvailable($book['id'], $quantity)) {
                    throw new BookNotAvailableException($book['title'], $book['stock'], $quantity);
                }

                LoanDetail::create([
                    'loan_id' => $loan->id,
                    'book_id' => $book['id'],
                    'book_title' => $book['title'],
                    'book_isbn' => $book['isbn'],
                    'quantity' => $quantity,
                ]);

                if (!$this->bookService->reduceStock($book['id'], $quantity)) {
                    throw new BookNotAvailableException($book['title'], $book['stock'], $quantity);
                }
            }

            $this->memberService->markBorrowing($member['id'], $totalQuantity);

            return $loan->load('details');
        });

        // Domain event hanya ditembakkan setelah transaction berhasil di-commit
        event(new BookBorrowed($loan));

        return $loan;
    }

    /**
     * Proses pengembalian: hitung denda (Value Object Denda), tambah stok buku,
     * perbarui status anggota. Semua dalam satu transaction.
     */
    public function returnLoan(Loan $loan, ?CarbonInterface $returnedAt = null): Loan
    {
        return DB::transaction(function () use ($loan, $returnedAt) {
            $returnedAt = $returnedAt ?? now();
            $loan->loadMissing('details');

            if ($loan->isReturned()) {
                throw new MemberCannotBorrowException('Peminjaman ini sudah dikembalikan.');
            }

            $denda = Denda::hitung($loan->due_at, $returnedAt);
            $totalFine = 0.0;
            $totalQuantity = 0;

            foreach ($loan->details as $detail) {
                $fine = $denda->getAmount() * $detail->quantity;

                $detail->update([
                    'returned_at' => $returnedAt,
                    'fine_amount' => $fine,
                ]);

                $this->bookService->increaseStock($detail->book_id, $detail->quantity);

                $totalFine += $fine;
                $totalQuantity += $detail->quantity;
            }

            $this->memberService->markReturned($loan->member_id, $totalQuantity);

            $loan->update([
                'status' => Loan::STATUS_RETURNED,
                'returned_at' => $returnedAt,
                'total_fine' => $totalFine,
            ]);

            return $loan->fresh('details');
        });
    }
}

