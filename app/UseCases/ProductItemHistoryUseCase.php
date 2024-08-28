<?php

declare(strict_types=1);

namespace App\UseCases;

use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductItemHistoryUseCase
{
    public function __invoke(int $id): Product
    {
        return Product::query()
            ->where('id', $id)
            ->with(['documents' => function (BelongsToMany $query) {
                $query->orderBy('performed_at');
            }])->first();
    }
}
