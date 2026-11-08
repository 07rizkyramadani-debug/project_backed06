<?php
// Pengujian soft delete dan restore (Buku, Anggota, Peminjaman).
// Jalankan dari root project SETELAH `php artisan migrate:fresh --seed`:
//   php scripts/pengujian/04_softdelete.php
require __DIR__ . '/bootstrap.php';
use Modules\Catalog\Models\Book;
use Modules\Circulation\Models\Loan;
use Modules\Membership\Models\Member;

echo "=== SOFT DELETE & RESTORE: Buku ===\n";
$book = Book::first();
echo "Buku uji          : #{$book->id} {$book->title}\n";
echo "Book::count()                 sebelum delete : " . Book::count() . "\n";
echo "Book::withTrashed()->count()  sebelum delete : " . Book::withTrashed()->count() . "\n";
$book->delete();
echo "--- \$book->delete() ---\n";
echo "Book::count()                 sesudah delete : " . Book::count() . "   (berkurang)\n";
echo "Book::withTrashed()->count()  sesudah delete : " . Book::withTrashed()->count() . "   (tetap)\n";
echo "Book::onlyTrashed()->count()  sesudah delete : " . Book::onlyTrashed()->count() . "\n";
echo "deleted_at                                   : " . Book::onlyTrashed()->first()->deleted_at . "\n";
Book::onlyTrashed()->first()->restore();
echo "--- \$book->restore() ---\n";
echo "Book::count()                 sesudah restore: " . Book::count() . "   (kembali)\n";

echo "\n=== SOFT DELETE & RESTORE: Anggota ===\n";
$member = Member::orderBy('id', 'desc')->first();
echo "Anggota uji       : #{$member->id} {$member->name}\n";
echo "Member::count() sebelum: " . Member::count() . "\n";
$member->delete();
echo "Member::count() sesudah delete: " . Member::count() . " | withTrashed: " . Member::withTrashed()->count() . " | onlyTrashed: " . Member::onlyTrashed()->count() . "\n";
Member::onlyTrashed()->first()->restore();
echo "Member::count() sesudah restore: " . Member::count() . "\n";

echo "\n=== SOFT DELETE & RESTORE: Peminjaman ===\n";
$loan = Loan::first();
echo "Peminjaman uji    : #{$loan->id} {$loan->loan_number}\n";
echo "Loan::count() sebelum: " . Loan::count() . "\n";
$loan->delete();
echo "Loan::count() sesudah delete: " . Loan::count() . " | withTrashed: " . Loan::withTrashed()->count() . " | onlyTrashed: " . Loan::onlyTrashed()->count() . "\n";
Loan::onlyTrashed()->first()->restore();
echo "Loan::count() sesudah restore: " . Loan::count() . "\n";
