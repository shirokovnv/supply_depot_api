<?php

namespace App\Http\Resources\Api\v1;

use App\Models\Document;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property Collection $documents
 */
class ProductHistoryResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'documents' => $this->documents->map(function (Document $document) {
                return array_filter([
                    'type' => $document->type,
                    'value' => $document->pivot->value,
                    'remains' => $document->pivot->remains,
                    'remains_cash' => $document->pivot->remains_cash,
                    'inv_error' => $document->pivot->inv_error,
                    'inv_error_cash' => $document->pivot->inv_error_cash,
                    'cost' => $document->pivot->cost,
                ]);
            }),
        ];
    }
}
