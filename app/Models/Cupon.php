<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Cupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'discount',
        'minimum_amount',
        'valid_from',
        'valid_until',
        'usage_limit',
        'used_count',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'discount' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date'
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isValid($subtotal = 0): bool
    {
        $now = Carbon::now()->format('Y-m-d');
        
        return $this->active 
            && $now >= $this->valid_from->format('Y-m-d')
            && $now <= $this->valid_until->format('Y-m-d')
            && $subtotal >= $this->minimum_amount
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    public function calculateDiscount($subtotal): float
    {
        if (!$this->isValid($subtotal)) {
            return 0;
        }

        if ($this->type === 'percentage') {
            return ($subtotal * $this->discount) / 100;
        }

        return $this->discount;
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    public static function findByCode($code): ?self
    {
        return self::where('code', strtoupper($code))->first();
    }

    public static function getActiveCupons(): Collection
    {
        return self::where('active', true)
            ->where('valid_until', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getActiveCuponsForCarousel($limit = 5): Collection
    {
        return self::where('active', true)
            ->where('valid_until', '>=', now())
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    public static function codeExists($code, $excludeId = null): bool
    {
        $query = self::where('code', strtoupper($code));
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
}
