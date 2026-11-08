<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Catalog\Models\Publisher;

class PublisherFactory extends Factory
{
    protected $model = Publisher::class;

    public function definition(): array
    {
        return [
            'name' => 'Penerbit ' . fake()->unique()->lastName(),
            'city' => fake()->randomElement(['Jakarta', 'Yogyakarta', 'Bandung', 'Surabaya', 'Semarang']),
        ];
    }
}
