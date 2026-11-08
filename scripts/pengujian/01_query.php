<?php
// Perbandingan jumlah query sebelum vs sesudah eager loading + query scopes.
// Jalankan dari root project SETELAH `php artisan migrate:fresh --seed`:
//   php scripts/pengujian/01_query.php
require __DIR__ . '/bootstrap.php';
use Modules\Catalog\Models\Book;
use Modules\Circulation\Models\Loan;
use Modules\Membership\Models\Member;

echo "=== A. Daftar Buku (10 buku per halaman: penulis, penerbit, kategori) ===\n";
$before = qcount(function () { foreach (Book::limit(10)->get() as $b) { $b->authors->count(); $b->publisher?->name; $b->categories->count(); } });
$after  = qcount(function () { foreach (Book::with(['authors','publisher','categories'])->limit(10)->get() as $b) { $b->authors->count(); $b->publisher?->name; $b->categories->count(); } });
echo "Sebelum eager loading (lazy loading)      : {$before} query\n";
echo "Sesudah  eager loading (with([...]))      : {$after} query\n\n";

echo "=== B. Daftar Peminjaman (semua peminjaman + detail) ===\n";
$loans = Loan::count();
$before = qcount(function () { foreach (Loan::all() as $l) { $l->details->count(); } });
$after  = qcount(function () { foreach (Loan::with('details')->get() as $l) { $l->details->count(); } });
echo "Jumlah peminjaman                         : {$loans}\n";
echo "Sebelum eager loading                     : {$before} query\n";
echo "Sesudah  eager loading (with('details'))  : {$after} query\n\n";

echo "=== C. Daftar Anggota (role + keanggotaan aktif) ===\n";
$members = Member::count();
$before = qcount(function () { foreach (Member::all() as $m) { $m->role->name; $m->currentMembership?->type; } });
$after  = qcount(function () { foreach (Member::with(['role','currentMembership'])->get() as $m) { $m->role->name; $m->currentMembership?->type; } });
echo "Jumlah anggota                            : {$members}\n";
echo "Sebelum eager loading                     : {$before} query\n";
echo "Sesudah  eager loading                    : {$after} query\n\n";

echo "=== D. Query scopes ===\n";
echo "Book::count()                             : " . Book::count() . "\n";
echo "Book::available()->count()                : " . Book::available()->count() . "\n";
$first = Book::first();
echo "Book::search('" . $first->title . "')->count()  : " . Book::search($first->title)->count() . "\n";
$cat = $first->categories()->first();
echo "Book::inCategory({$cat->id}) [{$cat->name}]->count() : " . Book::inCategory($cat->id)->count() . "\n";
echo "\nSQL (eager loading Book::with(['authors','publisher','categories'])):\n";
DB::flushQueryLog(); DB::enableQueryLog();
Book::with(['authors','publisher','categories'])->limit(10)->get();
foreach (DB::getQueryLog() as $i => $q) { echo ($i+1) . ". " . substr($q['query'], 0, 150) . "\n"; }
