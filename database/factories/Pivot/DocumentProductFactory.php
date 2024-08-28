<?php

declare(strict_types=1);

namespace Database\Factories\Pivot;

use App\Models\Document;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;

class DocumentProductFactory extends Factory
{
    use WithFaker;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_id' => Document::query()->inRandomOrder()->first(),
            'product_id' => Product::query()->inRandomOrder()->first(),
            'value' => $this->faker->numberBetween(1, 10),
        ];
    }
}
