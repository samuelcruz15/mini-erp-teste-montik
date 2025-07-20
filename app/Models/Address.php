<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'cep',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getFullAddressAttribute(): string
    {
        $address = "{$this->street}, {$this->number}";
        
        if ($this->complement) {
            $address .= " - {$this->complement}";
        }
        
        $address .= " - {$this->neighborhood}, {$this->city}/{$this->state} - CEP: {$this->cep}";
        
        return $address;
    }

    public function setAsDefault(): void
    {
        $this->user->addresses()->where('id', '!=', $this->id)->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }
}
