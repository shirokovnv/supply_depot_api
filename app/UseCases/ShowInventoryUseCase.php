<?php

declare(strict_types=1);

namespace App\UseCases;

use App\Enums\DocumentType;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShowInventoryUseCase
{
    public function __invoke(Carbon $performedAt): Collection
    {
        DB::connection()->enableQueryLog();
        $result = Product::query()
            ->with(['documents' => function (BelongsToMany $query) use ($performedAt) {
                $query
                    ->where('type', DocumentType::Inventory->value)
                    ->whereDate('performed_at', $performedAt)
                    ->orderBy('performed_at', 'desc')
                    ->limit(1);
            }])->get();

        $queries = DB::getQueryLog();
        $last_query = end($queries);

        Log::info($queries);

        DB::disableQueryLog();

        return $result;
    }
}
