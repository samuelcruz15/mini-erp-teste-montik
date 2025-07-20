<?php

namespace App\Services;

use App\Models\Cupon;
use App\Models\Order;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CuponService
{
    public function applyCupon($code, $subtotal): array
    {
        $cupon = Cupon::where('code', strtoupper($code))
            ->where('active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->first();

        if (!$cupon) {
            return ['success' => false, 'message' => 'Cupom inválido ou expirado'];
        }

        if ($subtotal < $cupon->minimum_amount) {
            return [
                'success' => false, 
                'message' => "Valor mínimo para este cupom é R$ " . number_format($cupon->minimum_amount, 2, ',', '.')
            ];
        }

        if ($cupon->usage_limit > 0) {
            $usageCount = Order::where('cupon_id', $cupon->id)->count();
            if ($usageCount >= $cupon->usage_limit) {
                return ['success' => false, 'message' => 'Cupom atingiu o limite de uso'];
            }
        }

        $discount = $this->calculateDiscount($cupon, $subtotal);

        Session::put('applied_cupon', [
            'id' => $cupon->id,
            'code' => $cupon->code,
            'name' => $cupon->name,
            'discount' => $discount,
            'type' => $cupon->type
        ]);

        return [
            'success' => true,
            'message' => "Cupom aplicado! Desconto: R$ " . number_format($discount, 2, ',', '.'),
            'discount' => $discount,
            'cupon' => $cupon
        ];
    }

    public function removeCupon(): array
    {
        Session::forget('applied_cupon');
        return ['success' => true, 'message' => 'Cupom removido'];
    }

    public function getAppliedCupon(): ?array
    {
        return Session::get('applied_cupon');
    }

    public function calculateDiscount($cupon, $subtotal): float
    {
        if ($cupon->type === 'percentage') {
            return ($subtotal * $cupon->discount) / 100;
        } else {
            return $cupon->discount;
        }
    }

    public function validateCupon($code): array
    {
        $cupon = Cupon::where('code', strtoupper($code))
            ->where('active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->first();

        if (!$cupon) {
            return ['valid' => false, 'message' => 'Cupom inválido ou expirado'];
        }

        if ($cupon->usage_limit > 0) {
            $usageCount = Order::where('cupon_id', $cupon->id)->count();
            if ($usageCount >= $cupon->usage_limit) {
                return ['valid' => false, 'message' => 'Cupom atingiu o limite de uso'];
            }
        }

        return ['valid' => true, 'cupon' => $cupon];
    }

    public function getAllCupons(): LengthAwarePaginator
    {
        return Cupon::orderBy('created_at', 'desc')->paginate(15);
    }

    public function findById($id): Cupon
    {
        return Cupon::findOrFail($id);
    }

    public function createCupon(array $data): Cupon
    {
        if (Cupon::codeExists($data['code'])) {
            throw new \Exception('Este código de cupom já existe.');
        }

        return Cupon::create($data);
    }

    public function updateCupon($id, array $data): Cupon
    {
        $cupon = Cupon::findOrFail($id);
        
        if (Cupon::codeExists($data['code'], $id)) {
            throw new \Exception('Este código de cupom já existe.');
        }
        
        $cupon->update($data);
        return $cupon;
    }

    public function deleteCupon($id): bool
    {
        $cupon = Cupon::findOrFail($id);
        return $cupon->delete();
    }

    public function toggleCuponStatus($id): Cupon
    {
        $cupon = Cupon::findOrFail($id);
        $cupon->update(['active' => !$cupon->active]);
        return $cupon;
    }

    public function getActiveCupons(): Collection
    {
        return Cupon::where('active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->get();
    }

    public function getCuponStats(): array
    {
        return [
            'total_cupons' => Cupon::count(),
            'active_cupons' => Cupon::where('active', true)->count(),
            'expired_cupons' => Cupon::where('valid_until', '<', now())->count(),
            'percentage_cupons' => Cupon::where('type', 'percentage')->count(),
            'fixed_cupons' => Cupon::where('type', 'fixed')->count()
        ];
    }

    public function getCuponsByType($type): LengthAwarePaginator
    {
        return Cupon::where('type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getCuponsByValidity($valid = true): LengthAwarePaginator    
    {
        $query = Cupon::query();
        
        if ($valid) {
            $query->where('valid_from', '<=', now())
                  ->where('valid_until', '>=', now());
        } else {
            $query->where(function($q) {
                $q->where('valid_from', '>', now())
                  ->orWhere('valid_until', '<', now());
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }
} 