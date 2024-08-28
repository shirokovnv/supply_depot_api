<?php

declare(strict_types=1);

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $product_id
 * @property int $document_id
 * @property int $value
 * @property int|null $inv_error
 * @property float|null $inv_error_cash
 * @property int $remains
 * @property float|null $remains_cash
 * @property int|null $cost
 */
class DocumentProduct extends Pivot
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'document_product';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'document_id',
        'product_id',
        'value',
        'inv_error',
        'inv_error_cash',
        'remains',
        'remains_cash',
        'cost',
    ];
}
