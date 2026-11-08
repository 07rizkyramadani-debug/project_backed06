<?php

namespace Modules\Shared\Contracts;

/**
 * Contract Catalog Context -> dipakai oleh Circulation Context.
 */
interface BookServiceInterface
{
    /** Data buku untuk keperluan peminjaman (id, title, isbn, stock) atau null. */
    public function getBookForLoan(int $bookId): ?array;

    public function isAvailable(int $bookId, int $quantity): bool;

    /** Kurangi stok buku. Mengembalikan false bila stok tidak mencukupi. */
    public function reduceStock(int $bookId, int $quantity): bool;

    /** Tambah stok buku (saat buku dikembalikan). */
    public function increaseStock(int $bookId, int $quantity): bool;

    /** Daftar buku yang tersedia (untuk form peminjaman). */
    public function listAvailableBooks(): array;
}

