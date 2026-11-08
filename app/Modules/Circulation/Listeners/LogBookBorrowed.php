<?php

namespace Modules\Circulation\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Circulation\Events\BookBorrowed;

/**
 * Listener: mencatat log setiap peminjaman buku yang berhasil.
 * (Tempat yang tepat untuk mengirim notifikasi ke anggota.)
 */
class LogBookBorrowed
{
    public function handle(BookBorrowed $event): void
    {
        $loan = $event->loan->loadMissing('details');

        Log::info('BookBorrowed: peminjaman buku berhasil', [
            'loan_number' => $loan->loan_number,
            'member' => "{$loan->member_number} - {$loan->member_name}",
            'books' => $loan->details->pluck('book_title')->all(),
            'due_at' => $loan->due_at->toDateTimeString(),
        ]);
    }
}

