<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getDefaultAddressAttribute()
    {
        return $this->addresses()->where('is_default', true)->first();
    }

    public function getFullNameAttribute(): string
    {
        return $this->profile?->full_name ?? $this->name;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->profile?->phone;
    }

    public static function findWithProfileAndAddresses($userId): ?self
    {
        return self::with(['profile', 'addresses'])->find($userId);
    }

    public static function findWithProfile($userId): ?self
    {
        return self::with('profile')->find($userId);
    }

    public function hasProfile(): bool
    {
        return $this->profile !== null;
    }

    public function hasAddresses(): bool
    {
        return $this->addresses()->exists();
    }

    public function getOrderedAddresses(): Collection
    {
        return $this->addresses()->orderBy('is_default', 'desc')->get();
    }
}
