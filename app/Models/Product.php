<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'has_variations',
        'image',
        'active'
    ];

    protected $casts = [
        'has_variations' => 'boolean',
        'active' => 'boolean',
        'price' => 'decimal:2'
    ];

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getTotalStockAttribute()
    {
        return $this->stocks()->sum('quantity');
    }

    public function hasStock($quantity = 1): bool
    {
        return $this->getTotalStockAttribute() >= $quantity;
    }

    public function getPriceWithVariation($stockId = null): float
    {
        if ($stockId && $this->has_variations) {
            $stock = $this->stocks()->find($stockId);
            if ($stock) {
                return (float) ($this->price + $stock->price_adjustment);
            }
        }
        return (float) $this->price;
    }

    public static function getActiveProducts(): Builder
    {
        return self::where('active', true)->with('stocks');
    }

    public static function searchProducts($searchTerm): Builder
    {
        return self::where('active', true)
            ->where(function($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            })
            ->with('stocks');
    }

    public static function findWithStockByVariation($productId, $variationValue = null)
    {
        $query = self::with('stocks')->where('id', $productId);
        
        if ($variationValue) {
            $query->whereHas('stocks', function($q) use ($variationValue) {
                $q->where('variation_value', $variationValue);
            });
        }
        
        return $query->first();
    }

    public function hasVariation($variationValue): bool
    {
        return $this->stocks()->where('variation_value', $variationValue)->exists();
    }

    public function getStockByVariation($variationValue): ?Stock
    {
        return $this->stocks()->where('variation_value', $variationValue)->first();
    }
}
