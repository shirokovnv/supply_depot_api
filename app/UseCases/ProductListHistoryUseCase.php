<?php

declare(strict_types=1);

namespace App\UseCases;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductListHistoryUseCase
{
    public function __invoke(): Collection
    {
        return Product::query()->with(['documents' => function (BelongsToMany $query) {
            $query->orderBy('performed_at');
        }])->get();
    }
}
