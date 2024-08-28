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
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class StoreDocumentUseCase
{
    private const DEFAULT_PRODUCT_NAME = 'Unknown product';

    /**
     * @param DocumentType $type
     * @param Carbon $performedAt
     * @param array<string, mixed> $items
     */
    public function __construct(
        readonly private DocumentType $type,
        readonly private Carbon $performedAt,
        readonly private array $items
    )
    {
    }

    /**
     * @return Document
     * @throws InvalidRemainsException
     * @throws QueryException
     */
    public function __invoke(): Document
    {
        return DB::transaction(function(): Document {
            /** @var Document $document */
            $document = Document::query()->create([
                'type' => $this->type->value,
                'performed_at' => $this->performedAt
            ]);

            foreach ($this->items as $item) {
                /** @var Product $product */
                $product = Product::query()->firstOrCreate(
                    [
                        'id' => $item['product_id'],
                    ],
                    [
                        'id' => $item['product_id'],
                        'name' => $item['product_name'] ?? self::DEFAULT_PRODUCT_NAME
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

                /** @var  $query */
                $query = ProductRemain::query()->where('product_id', $product->id);

                DocumentProduct::query()->create([
                    'document_id' => $document->id,
                    'product_id' => $item['product_id'],
                    'value' => $item['value'],
                    'remains' => $this->calculateProductRemains($query, $this->type, $item['value']),
                    'inv_error' => $this->calculateInventoryError(
                        $this->type,
                        $productRemain->remains,
                        $item['value']
                    )
                ]);
            }

            return $document;
        });
    }

    /**
     * TODO: refactor it! Use specification. Add as external dependency.
     *
     * @param DocumentType $type
     * @param int $currentRemains
     * @param int $value
     * @return bool
     */
    private function isPositiveRemains(
        DocumentType $type,
        int          $currentRemains,
        int          $value
    ): bool
    {
        if ($type === DocumentType::Outcome && $currentRemains < $value) {
            return false;
        }

        return true;
    }

    /**
     * TODO: refactor it! Use factory.
     *
     * @param Builder $query
     * @param DocumentType $type
     * @param int $remains
     * @return int
     */
    private function calculateProductRemains(Builder $query, DocumentType $type, int $remains): int
    {
        $query->lockForUpdate();

        switch($type)
        {
            case DocumentType::Income: $query->increment('remains', $remains); break;
            case DocumentType::Outcome: $query->decrement('remains', $remains); break;
            case DocumentType::Inventory: $query->update(['remains' => $remains]); break;
        }

        return $query->first()->remains;
    }

    /**
     * @param DocumentType $type
     * @param int $currentRemains
     * @param int $value
     * @return int|null
     */
    private function calculateInventoryError(
        DocumentType $type,
        int $currentRemains,
        int $value
    ): ?int
    {
        if ($type !== DocumentType::Inventory) {
            return null;
        }

        return $value - $currentRemains;
    }
}
