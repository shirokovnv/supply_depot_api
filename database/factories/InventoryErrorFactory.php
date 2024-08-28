<?php

namespace Database\Factories;

use App\Enums\DocumentType;
use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryError>
 */
class InventoryErrorFactory extends Factory
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
            'document_id' => Document::factory()->create(['type' => DocumentType::Inventory->value]),
            'value' => $this->faker->numberBetween(-10, 10),
        ];
    }
}
