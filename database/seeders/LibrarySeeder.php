<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Catalog\Models\Author;
use Modules\Catalog\Models\Book;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Publisher;
use Modules\Circulation\Models\Loan;
use Modules\Circulation\Services\LoanService;
use Modules\Membership\Models\Member;
use Modules\Membership\Models\MemberRole;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        // ---- Membership Context ----
        $mahasiswa = MemberRole::create(['name' => 'Mahasiswa', 'slug' => 'mahasiswa', 'max_loans' => 3, 'loan_days' => 7]);
        $dosen = MemberRole::create(['name' => 'Dosen', 'slug' => 'dosen', 'max_loans' => 5, 'loan_days' => 14]);
        $umum = MemberRole::create(['name' => 'Umum', 'slug' => 'umum', 'max_loans' => 2, 'loan_days' => 7]);

        $members = collect();
        foreach ([[$mahasiswa, 8], [$dosen, 3], [$umum, 2]] as [$role, $count]) {
            $members = $members->merge(
                Member::factory()->count($count)->withMembership()->create(['member_role_id' => $role->id])
            );
        }
        Member::factory()->suspended()->withMembership()->create(['member_role_id' => $umum->id, 'name' => 'Anggota Ditangguhkan']);

        // ---- Catalog Context ----
        $authors = Author::factory()->count(15)->create();
        $publishers = Publisher::factory()->count(6)->create();

        $roots = collect(['Teknologi', 'Sains', 'Sastra', 'Bisnis', 'Pendidikan'])->map(
            fn ($name) => Category::create(['name' => $name, 'slug' => strtolower($name)])
        );
        $subs = collect([
            ['Pemrograman', 'Teknologi'], ['Jaringan', 'Teknologi'], ['Fisika', 'Sains'],
            ['Novel', 'Sastra'], ['Manajemen', 'Bisnis'], ['Pedagogi', 'Pendidikan'],
        ])->map(fn ($s) => Category::create([
            'name' => $s[0],
            'slug' => strtolower($s[0]),
            'parent_id' => $roots->firstWhere('name', $s[1])->id,
        ]));
        $categories = $roots->merge($subs);

        $books = Book::factory()->count(40)->create()->each(function (Book $book) use ($authors, $publishers, $categories) {
            $book->update(['publisher_id' => $publishers->random()->id]);
            $book->authors()->attach($authors->random(rand(1, 2))->pluck('id')->all());
            $book->categories()->attach(
                $categories->random(rand(1, 3))->mapWithKeys(fn ($c) => [$c->id => ['added_at' => now()]])->all()
            );
        });

        // ---- Circulation Context: contoh peminjaman melalui LoanService (transaction + event) ----
        $loanService = app(LoanService::class);

        foreach ($members->take(4) as $i => $member) {
            $loanService->borrow($member->id, [
                ['book_id' => $books[$i * 2]->id, 'quantity' => 1],
                ['book_id' => $books[$i * 2 + 1]->id, 'quantity' => 1],
            ]);
        }

        // Satu peminjaman dibuat terlambat agar denda terlihat saat pengembalian
        $late = $loanService->borrow($members[5]->id, [['book_id' => $books[10]->id, 'quantity' => 1]]);
        Loan::whereKey($late->id)->update([
            'borrowed_at' => now()->subDays(12),
            'due_at' => now()->subDays(5),
        ]);
    }
}
