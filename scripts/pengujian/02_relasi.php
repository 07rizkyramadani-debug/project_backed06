<?php
// Pengujian relasi lanjutan (pivot added_at, self-referencing, HasMany) dan Value Object.
// Jalankan dari root project SETELAH `php artisan migrate:fresh --seed`:
//   php scripts/pengujian/02_relasi.php
require __DIR__ . '/bootstrap.php';
use Modules\Catalog\Models\Book;
use Modules\Catalog\Models\Category;
use Modules\Circulation\Models\Loan;

echo "=== 1. Many-to-Many dengan pivot (Buku <-> Kategori, kolom added_at) ===\n";
$book = Book::with('categories')->first();
echo "Buku: {$book->title}\n";
foreach ($book->categories as $c) { echo "  - {$c->name}  (added_at: {$c->pivot->added_at})\n"; }

echo "\n=== 2. Self-referencing (Kategori -> Sub-kategori) ===\n";
$root = Category::roots()->with('children')->get()->first(fn ($c) => $c->children->isNotEmpty());
echo "Induk : {$root->name}\n";
foreach ($root->children as $child) { echo "  Sub   : {$child->name} (parent: {$child->parent->name})\n"; }

echo "\n=== 3. HasMany (Peminjaman -> DetailPeminjaman) ===\n";
$loan = Loan::with('details')->has('details', '>=', 2)->first();
echo "Peminjaman {$loan->loan_number} oleh {$loan->member_name}\n";
foreach ($loan->details as $d) { echo "  - {$d->book_title} | ISBN {$d->book_isbn} | jumlah {$d->quantity}\n"; }

echo "\n=== 4. Value Object ISBN pada model Book ===\n";
echo "get_class(\$book->isbn)  : " . get_class($book->isbn) . "\n";
echo "(string) \$book->isbn    : " . $book->isbn . "\n";
echo "\$book->isbn->format()   : " . $book->isbn->format() . "\n";
try { new Modules\Catalog\ValueObjects\Isbn('978-0-306-40615-8'); } catch (InvalidArgumentException $e) { echo "ISBN salah             : " . $e->getMessage() . "\n"; }

echo "\n=== 5. Value Object Denda ===\n";
$d = Modules\Circulation\ValueObjects\Denda::hitung(now()->subDays(5), now());
echo "Terlambat 5 hari       : {$d}\n";
