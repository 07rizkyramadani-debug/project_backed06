<?php

namespace Modules\Circulation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Circulation\Models\Loan;
use Modules\Circulation\Requests\StoreLoanRequest;
use Modules\Circulation\Services\LoanService;
use Modules\Shared\Contracts\BookServiceInterface;
use Modules\Shared\Contracts\MemberServiceInterface;

class LoanController extends Controller
{
    public function __construct(
        private LoanService $loanService,
        private BookServiceInterface $bookService,
        private MemberServiceInterface $memberService
    ) {}

    public function index(Request $request)
    {
        // Eager loading: detail peminjaman dimuat sekaligus (menghindari N+1)
        $loans = Loan::with('details')
            ->when($request->query('status') === 'overdue', fn ($q) => $q->overdue())
            ->when(in_array($request->query('status'), ['borrowed', 'returned'], true),
                fn ($q) => $q->where('status', $request->query('status')))
            ->latest('borrowed_at')
            ->paginate(10)
            ->withQueryString();

        return view('library.loans.index', compact('loans'));
    }

    public function create()
    {
        // Data lintas context diambil lewat contract, bukan model context lain
        return view('library.loans.create', [
            'members' => $this->memberService->listActiveMembers(),
            'books' => $this->bookService->listAvailableBooks(),
        ]);
    }

    public function store(StoreLoanRequest $request)
    {
        $data = $request->validated();

        $loan = $this->loanService->borrow(
            (int) $data['member_id'],
            $data['items'],
            isset($data['loan_days']) ? (int) $data['loan_days'] : null,
            $data['notes'] ?? null
        );

        return redirect()->route('loans.show', $loan)->with('success', 
        'Peminjaman berhasil dicatat.');
    }

    public function show(Loan $loan)
    {
        $loan->load('details');

        return view('library.loans.show', compact('loan'));
    }

    public function returnLoan(Loan $loan)
    {
        $loan = $this->loanService->returnLoan($loan);

        return redirect()->route('loans.show', $loan)->with(
            'success',
            'Buku dikembalikan. Total denda: Rp ' . number_format($loan->total_fine, 0, ',', '.')
        );
    }

    /** Soft delete */
    public function destroy(Loan $loan)
    {
        $loan->delete();

        return redirect()->route('loans.index')->with('success', 
        'Peminjaman dipindahkan ke daftar terhapus (soft delete).');
    }

    public function trashed()
    {
        $loans = Loan::onlyTrashed()->with('details')->latest('deleted_at')->paginate(10);

        return view('library.loans.trashed', compact('loans'));
    }

    public function restore(int $id)
    {
        Loan::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('loans.trashed')->with('success',
         'Peminjaman berhasil dipulihkan.');
    }
}


