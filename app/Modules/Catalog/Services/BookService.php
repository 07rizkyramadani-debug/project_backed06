<?php

namespace Modules\Catalog\Services;

use Modules\Catalog\Models\Book;
use Modules\Shared\Contracts\BookServiceInterface;

class BookService implements BookServiceInterface
{
    public function getBookForLoan(int $bookId): ?array
    {
        $book = Book::find($bookId);

        if (!$book) {
            return null;
        }

        return [
            'id' => $book->id,
            'title' => $book->title,
            'isbn' => (string) $book->isbn,
            'stock' => $book->stock,
        ];
    }

    public function isAvailable(int $bookId, int $quantity): bool
    {
        $book = Book::find($bookId);

        return $book && $book->is_active && $book->isInStock($quantity);
    }

    public function reduceStock(int $bookId, int $quantity): bool
    {
        $book = Book::lockForUpdate()->find($bookId);

        if (!$book || !$book->is_active || !$book->isInStock($quantity)) {
            return false;
        }

        $book->decrement('stock', $quantity);

        return true;
    }

    public function increaseStock(int $bookId, int $quantity): bool
    {
        // withTrashed: buku yang sudah di-soft delete tetap bisa dikembalikan stoknya
        $book = Book::withTrashed()->lockForUpdate()->find($bookId);

        if (!$book) {
            return false;
        }

        $book->increment('stock', $quantity);

        return true;
    }

    public function listAvailableBooks(): array
    {
        return Book::available()
            ->orderBy('title')
            ->get(['id', 'title', 'isbn', 'stock'])
            ->map(fn (Book $b) => [
                'id' => $b->id,
                'title' => $b->title,
                'isbn' => (string) $b->isbn,
                'stock' => $b->stock,
            ])
            ->all();
    }
}

