<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Catalog\Models\Book;
use Modules\Circulation\Events\BookBorrowed;
use Modules\Circulation\Listeners\LogBookBorrowed;
use Modules\Circulation\Services\LoanService;
use Modules\Membership\Models\Member;
use Modules\Membership\Models\MemberRole;
use Modules\Shared\Exceptions\BookNotAvailableException;

beforeEach(function () {
    $role = MemberRole::create(['name' => 'Dosen', 'slug' => 'dosen', 'max_loans' => 5, 'loan_days' => 14]);
    $this->member = Member::factory()->withMembership()->create(['member_role_id' => $role->id]);
});

it('BookBorrowed ditembakkan setelah peminjaman berhasil', function () {
    Event::fake([BookBorrowed::class]);
    $book = Book::factory()->create(['stock' => 3, 'total_stock' => 3]);

    $loan = app(LoanService::class)->borrow($this->member->id, [['book_id' => $book->id]]);

    Event::assertDispatched(BookBorrowed::class, fn ($e) => $e->loan->is($loan));
});

it('BookBorrowed TIDAK ditembakkan bila transaction di-rollback', function () {
    Event::fake([BookBorrowed::class]);
    $book = Book::factory()->create(['stock' => 1, 'total_stock' => 1]);

    try {
        app(LoanService::class)->borrow($this->member->id, [['book_id' => $book->id, 'quantity' => 3]]);
    } catch (BookNotAvailableException) {
    }

    Event::assertNotDispatched(BookBorrowed::class);
});

it('listener terdaftar dan mencatat log peminjaman', function () {
    Event::fake();
    Event::assertListening(BookBorrowed::class, LogBookBorrowed::class);
});

it('listener menulis log berisi nomor peminjaman dan judul buku', function () {
    Log::spy();
    $book = Book::factory()->create(['stock' => 3, 'total_stock' => 3]);

    $loan = app(LoanService::class)->borrow($this->member->id, [['book_id' => $book->id]]);

    Log::shouldHaveReceived('info')->withArgs(
        fn ($message, $context) => str_contains($message, 'BookBorrowed')
            && $context['loan_number'] === $loan->loan_number
            && $context['books'] === [$book->title]
    )->once();
});
