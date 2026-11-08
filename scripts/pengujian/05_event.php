<?php
// Pengujian domain event BookBorrowed dan listener LogBookBorrowed.
// Jalankan dari root project SETELAH `php artisan migrate:fresh --seed`:
//   php scripts/pengujian/05_event.php
require __DIR__ . '/bootstrap.php';
use Modules\Catalog\Models\Book;
use Modules\Circulation\Services\LoanService;
use Modules\Membership\Models\Member;

$logFile = storage_path('logs/laravel.log');
file_put_contents($logFile, '');
$member = Member::whereHas('role', fn ($q) => $q->where('slug', 'dosen'))->where('active_loans_count', 0)->orderBy('id', 'desc')->first();
$books = Book::available()->orderBy('id', 'desc')->take(2)->get();
echo "Anggota : {$member->member_number} - {$member->name}\n";
echo "Buku    : " . $books->pluck('title')->join(', ') . "\n";
echo "Menjalankan LoanService::borrow() ...\n";
$loan = app(LoanService::class)->borrow($member->id, $books->map(fn ($b) => ['book_id' => $b->id])->all());
echo "Peminjaman {$loan->loan_number} berhasil -> event BookBorrowed ditembakkan\n\n";
echo "Isi storage/logs/laravel.log (hasil Listener LogBookBorrowed):\n";
echo trim(file_get_contents($logFile)) . "\n";
