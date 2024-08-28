<?php

declare(strict_types=1);

namespace App\UseCases;

use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\Pivot\DocumentProduct;
use App\Models\Product;
use App\Models\ProductRemain;
use App\UseCases\Exceptions\InvalidRemainsException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class StoreDocumentUseCase
{
    private const DEFAULT_PRODUCT_NAME = 'Unknown product';

    private const DAYS_INTERVAL = 20;

    /**
     * @param  array<string, mixed>  $items
     */
    public function __construct(
        readonly private DocumentType $type,
        readonly private Carbon $performedAt,
        readonly private array $items
    ) {}

    /**
     * @throws InvalidRemainsException
     * @throws QueryException
     */
    public function __invoke(): Document
    {
        return DB::transaction(function (): Document {
            /** @var Document $document */
            $document = Document::query()->create([
                'type' => $this->type->value,
                'performed_at' => $this->performedAt,
            ]);

            foreach ($this->items as $item) {
                /** @var Product $product */
                $product = Product::query()->firstOrCreate(
                    [
                        'id' => $item['product_id'],
                    ],
                    [
                        'id' => $item['product_id'],
                        'name' => $item['product_name'] ?? self::DEFAULT_PRODUCT_NAME,
                    ]
                );

                /** @var ProductRemain $productRemain */
                $productRemain = ProductRemain::query()->firstOrCreate(
                    [
                        'product_id' => $product->id,
                    ],
                    [
                        'product_id' => $product->id,
                        'remains' => 0,
                    ]
                );

                if (! $this->isPositiveRemains($this->type, $productRemain->remains, $item['value'])) {
                    throw new InvalidRemainsException(
                        sprintf(
                            'Cannot perform document (type: %s) due to invalid remains (product_id: %d)',
                            $this->type->value,
                            $product->id,
                        )
                    );
                }

                $query = ProductRemain::query()->where('product_id', $product->id);

                $inventoryError = $this->calculateInventoryError(
                    $this->type,
                    $productRemain->remains,
                    $item['value']
                );
                $remains = $this->calculateProductRemains($query, $this->type, $item['value']);
                $cost = $this->calculateCost($this->type, $item['cost'] ?? null);
                $productPrimeCost = $this->calculateAveragePrimeCost($product->id, $this->performedAt) ?? $cost;

                DocumentProduct::query()->create([
                    'document_id' => $document->id,
                    'product_id' => $item['product_id'],
                    'value' => $item['value'],
                    'cost' => $cost,
                    'remains' => $remains,
                    'remains_cash' => $remains * $productPrimeCost,
                    'inv_error' => $inventoryError,
                    'inv_error_cash' => $inventoryError !== null ? $inventoryError * $productPrimeCost : null,
                ]);
            }

            return $document;
        });
    }

    /**
     * TODO: refactor it! Use specification. Add as external dependency.
     */
    private function isPositiveRemains(
        DocumentType $type,
        int $currentRemains,
        int $value
    ): bool {
        if ($type === DocumentType::Outcome && $currentRemains < $value) {
            return false;
        }

        return true;
    }

    /**
     * TODO: refactor it! Use factory.
     */
    private function calculateProductRemains(Builder $query, DocumentType $type, int $remains): int
    {
        $query->lockForUpdate();

        switch ($type) {
            case DocumentType::Income: $query->increment('remains', $remains);
                break;
            case DocumentType::Outcome: $query->decrement('remains', $remains);
                break;
            case DocumentType::Inventory: $query->update(['remains' => $remains]);
                break;
        }

        return $query->first()->remains;
    }

    private function calculateInventoryError(
        DocumentType $type,
        int $currentRemains,
        int $value
    ): ?int {
        if ($type !== DocumentType::Inventory) {
            return null;
        }

        return $value - $currentRemains;
    }

    private function calculateCost(DocumentType $type, int $cost): ?int
    {
        return $type === DocumentType::Income ? $cost : null;
    }

    private function calculateAveragePrimeCost(
        int $productId,
        Carbon $performedAt
    ): ?float {
        /** @var int|float|null $avgCost */
        $avgCost = Product::query()
            ->where('id', $productId)
            ->with(['documents' => function (BelongsToMany $query) use ($performedAt) {
                $query
                    ->where('type', DocumentType::Income->value)
                    ->whereDate('performed_at', '<=', $performedAt)
                    ->whereDate('performed_at', '>=', $performedAt->clone()->subDays(self::DAYS_INTERVAL));
            }])
            ->get()
            ->map(function (Product $product) {
                $product['avg_cost'] = $product->documents->avg('pivot.cost');

                return $product;
            })
            ->avg('avg_cost');

        /** @var int|float|null $lastCost */
        $lastCost = Product::query()
            ->where('id', $productId)
            ->with(['documents' => function (BelongsToMany $query) {
                $query
                    ->where('type', DocumentType::Income->value)
                    ->orderBy('performed_at', 'desc')
                    ->limit(1);
            }])
            ->get()
            ->map(function (Product $product) {
                $product['last_cost'] = $product->documents->avg('pivot.cost');

                return $product;
            })
            ->avg('last_cost');

        return $avgCost ?? $lastCost;
    }
}
