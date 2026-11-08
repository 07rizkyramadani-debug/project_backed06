<?php

use Illuminate\Support\Facades\DB;
use Modules\Catalog\Models\Author;
use Modules\Catalog\Models\Book;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Publisher;

function seedBooks(int $n = 10): void
{
    $authors = Author::factory()->count(5)->create();
    $publisher = Publisher::factory()->create();
    $cats = Category::factory()->count(3)->create();

    Book::factory()->count($n)->create(['publisher_id' => $publisher->id])->each(function ($b) use ($authors, $cats) {
        $b->authors()->attach($authors->random(2)->pluck('id')->all());
        $b->categories()->attach($cats->random(2)->mapWithKeys(fn ($c) => [$c->id => ['added_at' => now()]])->all());
    });
}

function countQueries(Closure $fn): int
{
    DB::flushQueryLog();
    DB::enableQueryLog();
    $fn();
    $n = count(DB::getQueryLog());
    DB::disableQueryLog();

    return $n;
}

it('eager loading menghindari N+1 pada daftar buku', function () {
    seedBooks(10);

    $before = countQueries(function () {
        foreach (Book::all() as $b) {
            $b->authors->count();
            $b->publisher?->name;
            $b->categories->count();
        }
    });

    $after = countQueries(function () {
        foreach (Book::with(['authors', 'publisher', 'categories'])->get() as $b) {
            $b->authors->count();
            $b->publisher?->name;
            $b->categories->count();
        }
    });

    expect($before)->toBe(1 + 10 * 3)   // 31 query (N+1)
        ->and($after)->toBe(4)          // 1 + 3 relasi
        ->and($after)->toBeLessThan($before);
});

it('query scopes available, search, dan inCategory bekerja', function () {
    seedBooks(6);
    $first = Book::first();
    $first->update(['stock' => 0]);

    expect(Book::available()->count())->toBe(5)
        ->and(Book::search($first->title)->count())->toBeGreaterThanOrEqual(1)
        ->and(Book::inCategory($first->categories()->first()->id)->count())->toBeGreaterThanOrEqual(1);
});
