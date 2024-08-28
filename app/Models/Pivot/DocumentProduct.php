<?php

declare(strict_types=1);

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DocumentProduct extends Pivot
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'document_product';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'document_id',
        'product_id',
        'value'
    ];
}
