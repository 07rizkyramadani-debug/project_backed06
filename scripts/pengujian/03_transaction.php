<?php
// Pengujian database transaction: berhasil, rollback, dan pengembalian terlambat (denda).
// Jalankan dari root project SETELAH `php artisan migrate:fresh --seed`:
//   php scripts/pengujian/03_transaction.php
require __DIR__ . '/bootstrap.php';
use Modules\Catalog\Models\Book;
use Modules\Circulation\Models\Loan;
use Modules\Circulation\Models\LoanDetail;
use Modules\Circulation\Services\LoanService;
use Modules\Membership\Models\Member;

$service = app(LoanService::class);
$member = Member::where('status', 'active')->where('active_loans_count', 0)->orderBy('id')->first();
$bookA = Book::where('stock', '>=', 3)->orderBy('id')->skip(20)->first();
$bookB = Book::where('stock', '>=', 3)->orderBy('id')->skip(21)->first();
$bookB->update(['stock' => 1]);          // sengaja dibuat hampir habis
$bookB->refresh();

function state($member, $a, $b) {
    $m = Member::find($member->id);
    echo "  Stok '{$a->title}'  : " . Book::find($a->id)->stock . "\n";
    echo "  Stok '{$b->title}'  : " . Book::find($b->id)->stock . "\n";
    echo "  Anggota {$m->name}  : status={$m->status}, pinjaman aktif={$m->active_loans_count}\n";
    echo "  Jumlah loans / loan_details : " . Loan::count() . " / " . LoanDetail::count() . "\n";
}

echo "=== 1. TRANSACTION BERHASIL ===\n";
echo "Sebelum:\n"; state($member, $bookA, $bookB);
$loan = $service->borrow($member->id, [['book_id' => $bookA->id, 'quantity' => 1]]);
echo "Peminjaman {$loan->loan_number} berhasil dibuat (jatuh tempo {$loan->due_at->toDateString()})\n";
echo "Sesudah:\n"; state($member, $bookA, $bookB);

echo "\n=== 2. TRANSACTION ROLLBACK (stok buku kedua tidak mencukupi) ===\n";
$member2 = Member::whereHas('role', fn ($q) => $q->where('slug', 'dosen'))->where('status', 'active')->where('active_loans_count', 0)->orderBy('id')->first();
echo "Sebelum:\n"; state($member2, $bookA, $bookB);
echo "Mencoba meminjam: 1x '{$bookA->title}' + 2x '{$bookB->title}' (stok hanya 1)\n";
try {
    $service->borrow($member2->id, [
        ['book_id' => $bookA->id, 'quantity' => 1],
        ['book_id' => $bookB->id, 'quantity' => 2],
    ]);
} catch (Throwable $e) {
    echo "EXCEPTION  : " . get_class($e) . "\n";
    echo "PESAN      : " . $e->getMessage() . "\n";
}
echo "Sesudah (harus sama persis dengan sebelum -> rollback berhasil):\n"; state($member2, $bookA, $bookB);

echo "\n=== 3. PENGEMBALIAN TERLAMBAT (Value Object Denda) ===\n";
$late = Loan::overdue()->first();
echo "Peminjaman terlambat: {$late->loan_number}, jatuh tempo {$late->due_at->toDateString()}\n";
$returned = $service->returnLoan($late);
echo "Status: {$returned->status}, total denda: Rp " . number_format($returned->total_fine, 0, ',', '.') . "\n";
