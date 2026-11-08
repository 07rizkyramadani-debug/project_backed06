<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Catalog\Models\Book;
use Modules\Catalog\ValueObjects\Isbn;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        $stock = fake()->numberBetween(1, 10);

        return [
            'title' => ucfirst(fake()->unique()->words(fake()->numberBetween(2, 4), true)),
            'isbn' => self::generateIsbn13(),
            'published_year' => fake()->numberBetween(1995, 2025),
            'total_stock' => $stock,
            'stock' => $stock,
            'is_active' => true,
            'description' => fake()->paragraph(),
        ];
    }

    /** ISBN-13 acak yang check digit-nya valid. */
    public static function generateIsbn13(): string
    {
        $first12 = '978' . str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT);

        return $first12 . Isbn::checkDigit13($first12);
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock' => 0]);
    }
}
