<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Auth\Models\Role;
use Modules\Products\Models\Category;
use Modules\Products\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed roles
        $adminRole = Role::create(['name' => 'Administrator', 'slug' => 'admin']);
        $userRole = Role::create(['name' => 'User', 'slug' => 'user']);

        // Seed user bawaan (admin@example.com & user@example.com)
        $this->call([
            AdminUserSeeder::class,
        ]);

        User::where('email', 'admin@example.com')->first()?->roles()->attach($adminRole);
        User::where('email', 'user@example.com')->first()?->roles()->attach($userRole);

        // Seed categories
        $categories = Category::factory()->count(10)->create();

        // Seed products
        $products = Product::factory()->count(50)->create();

        // Attach categories to products
        $products->each(function ($product) use ($categories) {
            $product->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray(),
                ['added_at' => now()]
            );
        });

        // Sistem Perpustakaan (Catalog, Membership, Circulation)
        $this->call(LibrarySeeder::class);
    }
}
