<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Pivot\DocumentProduct;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Product::factory(10)->create();
        Document::factory(100)->create();
        DocumentProduct::factory(1000)->create();
    }
}
