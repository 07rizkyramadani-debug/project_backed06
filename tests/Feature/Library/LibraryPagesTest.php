<?php

use App\Models\User;
use Modules\Catalog\Models\Author;
use Modules\Catalog\Models\Book;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Publisher;
use Modules\Circulation\Models\Loan;
use Modules\Membership\Models\Member;
use Modules\Membership\Models\MemberRole;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->role = MemberRole::create(['name' => 'Mahasiswa', 'slug' => 'mahasiswa', 'max_loans' => 3, 'loan_days' => 7]);
});

it('semua halaman perpustakaan dapat dibuka admin', function () {
    $book = Book::factory()->create();
    $book->authors()->attach(Author::factory()->create());
    $book->categories()->attach(Category::factory()->create(), ['added_at' => now()]);
    Member::factory()->withMembership()->create(['member_role_id' => $this->role->id]);

    foreach (['books.index', 'books.create', 'books.trashed', 'categories.index', 'authors.index',
              'members.index', 'members.create', 'members.trashed', 'loans.index', 'loans.create', 'loans.trashed'] as $route) {
        $this->actingAs($this->admin)->get(route($route))->assertOk();
    }

    $this->actingAs($this->admin)->get(route('books.show', $book))->assertOk()->assertSee($book->title);
    $this->actingAs($this->admin)->get(route('books.edit', $book))->assertOk();
});

it('tamu diarahkan ke login dan user biasa tidak bisa membuka form tambah buku', function () {
    $this->get(route('books.index'))->assertRedirect(route('login'));

    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user)->get(route('books.index'))->assertOk();
    $this->actingAs($user)->get(route('books.create'))->assertForbidden();
});

it('admin menambah buku baru dengan ISBN valid, relasi penulis dan kategori tersimpan', function () {
    $author = Author::factory()->create();
    $category = Category::factory()->create();
    $publisher = Publisher::factory()->create();

    $this->actingAs($this->admin)->post(route('books.store'), [
        'title' => 'Domain-Driven Design Praktis',
        'isbn' => '978-0-306-40615-7',
        'publisher_id' => $publisher->id,
        'published_year' => 2024,
        'total_stock' => 4,
        'author_ids' => [$author->id],
        'category_ids' => [$category->id],
    ])->assertRedirect(route('books.index'));

    $book = Book::where('title', 'Domain-Driven Design Praktis')->first();

    expect((string) $book->isbn)->toBe('9780306406157')
        ->and($book->stock)->toBe(4)
        ->and($book->authors)->toHaveCount(1)
        ->and($book->categories->first()->pivot->added_at)->not->toBeNull();
});

it('ISBN tidak valid ditolak oleh validasi', function () {
    $author = Author::factory()->create();

    $this->actingAs($this->admin)->post(route('books.store'), [
        'title' => 'Buku Salah ISBN', 'isbn' => '9780306406158',
        'total_stock' => 1, 'author_ids' => [$author->id],
    ])->assertSessionHasErrors('isbn');
});

it('peminjaman lewat HTTP berhasil dan stok tidak cukup ditolak tanpa mengubah data', function () {
    $member = Member::factory()->withMembership()->create(['member_role_id' => $this->role->id]);
    $ok = Book::factory()->create(['stock' => 3, 'total_stock' => 3]);
    $low = Book::factory()->create(['stock' => 0, 'total_stock' => 1]);

    $this->actingAs($this->admin)->post(route('loans.store'), [
        'member_id' => $member->id,
        'items' => [['book_id' => $ok->id, 'quantity' => 1]],
    ])->assertRedirect();
    expect(Loan::count())->toBe(1)->and($ok->fresh()->stock)->toBe(2);

    $this->actingAs($this->admin)->post(route('loans.store'), [
        'member_id' => $member->id,
        'items' => [['book_id' => $ok->id, 'quantity' => 1], ['book_id' => $low->id, 'quantity' => 1]],
    ])->assertSessionHasErrors('stock');
    expect(Loan::count())->toBe(1)->and($ok->fresh()->stock)->toBe(2)->and($low->fresh()->stock)->toBe(0);
});
