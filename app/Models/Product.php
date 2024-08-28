<?php

namespace App\Models;

use App\Models\Pivot\DocumentProduct;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $name
 * @property Collection $documents
 * @property DocumentProduct $pivot
 */
class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
    ];

    /**
     * @return BelongsToMany
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class)
            ->withPivot('value', 'inv_error', 'inv_error_cash', 'remains', 'remains_cash', 'cost');
    }

    /**
     * @return HasOne
     */
    public function product_remain(): HasOne
    {
        return $this->hasOne(ProductRemain::class);
    }
}
