<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    protected $fillable = [
        'product_id',
        'variation_name',
        'variation_value',
        'quantity',
        'min_quantity',
        'price_adjustment'
    ];

    protected $casts = [
        'price_adjustment' => 'decimal:2'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function hasStock($quantity = 1)
    {
        return $this->quantity >= $quantity;
    }

    public function reduceStock($quantity)
    {
        if ($this->hasStock($quantity)) {
            $this->decrement('quantity', $quantity);
            return true;
        }
        return false;
    }

    public function increaseStock($quantity)
    {
        $this->increment('quantity', $quantity);
    }

    public function isBelowMinimum()
    {
        return $this->quantity <= $this->min_quantity;
    }

    public function getFullVariationNameAttribute()
    {
        if ($this->variation_name && $this->variation_value) {
            return $this->variation_name . ': ' . $this->variation_value;
        }
        return 'Produto padrão';
    }

    public function getFinalPriceAttribute()
    {
        return $this->product->price + $this->price_adjustment;
    }
}
