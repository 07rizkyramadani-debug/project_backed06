<?php

use App\Models\User;
use Modules\Catalog\Models\Book;
use Modules\Circulation\Models\Loan;
use Modules\Membership\Models\Member;
use Modules\Membership\Models\MemberRole;
use Modules\Circulation\Services\LoanService;

it('Buku: delete() menyembunyikan data, withTrashed() menampilkan, restore() memulihkan', function () {
    $books = Book::factory()->count(3)->create();
    $book = $books->first();

    $book->delete();

    expect(Book::count())->toBe(2)
        ->and(Book::withTrashed()->count())->toBe(3)
        ->and(Book::onlyTrashed()->count())->toBe(1);

    $book->restore();

    expect(Book::count())->toBe(3);
});

it('Anggota dan Peminjaman mendukung soft delete + restore', function () {
    $role = MemberRole::create(['name' => 'Umum', 'slug' => 'umum', 'max_loans' => 2, 'loan_days' => 7]);
    $member = Member::factory()->withMembership()->create(['member_role_id' => $role->id]);
    $book = Book::factory()->create(['stock' => 2, 'total_stock' => 2]);
    $loan = app(LoanService::class)->borrow($member->id, [['book_id' => $book->id]]);

    $loan->delete();
    $member->delete();

    expect(Loan::count())->toBe(0)->and(Loan::withTrashed()->count())->toBe(1)
        ->and(Member::count())->toBe(0)->and(Member::withTrashed()->count())->toBe(1);

    Loan::withTrashed()->first()->restore();
    Member::withTrashed()->first()->restore();

    expect(Loan::count())->toBe(1)->and(Member::count())->toBe(1);
});

it('admin dapat restore buku lewat HTTP, user biasa mendapat 403', function () {
    $book = Book::factory()->create();
    $book->delete();

    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user)->post(route('books.restore', $book->id))->assertForbidden();
    expect(Book::onlyTrashed()->count())->toBe(1);

    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)->post(route('books.restore', $book->id))->assertRedirect(route('books.trashed'));
    expect(Book::count())->toBe(1);
});
