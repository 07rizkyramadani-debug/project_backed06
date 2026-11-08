# Skrip Pengujian (Tugas 5.1 - Sistem Perpustakaan)

Skrip ini dipakai untuk mengambil bukti pengujian pada laporan (BAB IV).
Jalankan dari **root project**, berurutan, setelah database di-reset:

```bash
php artisan migrate:fresh --seed
php scripts/pengujian/01_query.php        # jumlah query sebelum vs sesudah eager loading, query scopes
php scripts/pengujian/02_relasi.php       # pivot added_at, self-referencing, HasMany, Value Object
php scripts/pengujian/03_transaction.php  # transaction berhasil, ROLLBACK, pengembalian + denda
php scripts/pengujian/04_softdelete.php   # soft delete & restore (Buku, Anggota, Peminjaman)
php scripts/pengujian/05_event.php        # event BookBorrowed -> listener menulis log
```

Pengujian otomatis (Pest):

```bash
php artisan test tests/Feature/Library
```

> Catatan: skrip 03 dan 05 mengubah data (membuat peminjaman). Jalankan ulang
> `php artisan migrate:fresh --seed` bila ingin kembali ke data awal.
