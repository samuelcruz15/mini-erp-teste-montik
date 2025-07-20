<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'user_profile_id',
        'address_id',
        'subtotal',
        'shipping_cost',
        'discount',
        'total',
        'cupon_id',
        'status'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userProfile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function cupon(): BelongsTo
    {
        return $this->belongsTo(Cupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'PED-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('order_number', $number)->exists());

        return $number;
    }

    public static function calculateShipping($subtotal): float
    {
        if ($subtotal >= 200) {
            return 0;
        } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
            return 15;
        } else {
            return 20;
        }
    }

    public function calculateTotal(): void
    {
        $subtotal = $this->subtotal ?? 0;
        $shipping = $this->shipping_cost ?? 0;
        $discount = $this->discount ?? 0;
        
        $this->total = $subtotal + $shipping - $discount;
        $this->save();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function cancel(): bool
    {
        if ($this->canBeCancelled()) {
            foreach ($this->items as $item) {
                if ($item->stock_id) {
                    $item->stock->increaseStock($item->quantity);
                }
            }
            $this->update(['status' => 'cancelled']);
            return true;
        }
        return false;
    }

    public function getCustomerNameAttribute(): string
    {
        return $this->userProfile?->full_name ?? $this->user?->name ?? '';
    }

    public function getCustomerEmailAttribute(): string
    {
        return $this->user?->email ?? '';
    }

    public function getCustomerPhoneAttribute(): string
    {
        return $this->userProfile?->phone ?? '';
    }

    public function getShippingCepAttribute(): string
    {
        return $this->address?->cep ?? '';
    }

    public function getShippingStreetAttribute(): string
    {
        return $this->address?->street ?? '';
    }

    public function getShippingNumberAttribute(): string
    {
        return $this->address?->number ?? '';
    }

    public function getShippingComplementAttribute(): string
    {
        return $this->address?->complement ?? '';
    }

    public function getShippingNeighborhoodAttribute(): string
    {
        return $this->address?->neighborhood ?? '';
    }

    public function getShippingCityAttribute(): string
    {
        return $this->address?->city ?? '';
    }

    public function getShippingStateAttribute(): string
    {
        return $this->address?->state ?? '';
    }

    public static function findWithRelations($orderId): ?self
    {
        return self::with(['items.product', 'items.stock', 'cupon'])
            ->find($orderId);
    }

    public static function findUserOrder($orderId, $userId): ?self
    {
        return self::with(['items.product', 'items.stock', 'cupon'])
            ->where('id', $orderId)
            ->where('user_id', $userId)
            ->first();
    }

    public static function getAllWithRelations(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return self::with(['items.product', 'items.stock', 'user', 'userProfile', 'address', 'cupon'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public static function getUserOrders($userId): \Illuminate\Pagination\LengthAwarePaginator
    {
        return self::with(['items.product', 'items.stock', 'cupon'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public static function getByStatus($status): Collection
    {
        return self::with(['items.product', 'items.stock', 'user', 'userProfile', 'address'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getValidStatuses(): array
    {
        return ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
    }

    public static function isValidStatus($status): bool
    {
        return in_array($status, self::getValidStatuses());
    }
}
