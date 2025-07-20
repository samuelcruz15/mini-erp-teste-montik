<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Collection;

class ProductService
{
    public function getAllActiveProducts(): Collection
    {
        return Product::where('active', true)
            ->with('stocks')
            ->orderBy('name')
            ->get();
    }

    public function findById($id): Product
    {
        return Product::with('stocks')->findOrFail($id);
    }

    public function createProduct(array $data): Product
    {
        $product = Product::create($data);

        if (!isset($data['has_variations']) || !$data['has_variations']) {
            Stock::create([
                'product_id' => $product->id,
                'quantity' => $data['default_quantity'] ?? 0,
                'price_adjustment' => 0
            ]);
        } else {
            if (isset($data['variations']) && is_array($data['variations'])) {
                foreach ($data['variations'] as $variation) {
                    Stock::create([
                        'product_id' => $product->id,
                        'variation_name' => $variation['name'],
                        'variation_value' => $variation['value'],
                        'quantity' => $variation['quantity'],
                        'price_adjustment' => $variation['price_adjustment'] ?? 0
                    ]);
                }
            }
        }

        return $product;
    }

    public function updateProduct($id, array $data): Product
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        
        // Update default stock if product doesn't have variations
        if (!isset($data['has_variations']) || !$data['has_variations']) {
            $defaultStock = $product->stocks()->whereNull('variation_name')->first();
            if ($defaultStock && isset($data['default_quantity'])) {
                $defaultStock->update(['quantity' => $data['default_quantity']]);
            }
        }
        
        return $product;
    }

    public function deleteProduct($id): bool
    {
        $product = Product::findOrFail($id);
        $product->stocks()->delete();
        return $product->delete();
    }

    public function toggleProductStatus($id): Product
    {
        $product = Product::findOrFail($id);
        $product->update(['active' => !$product->active]);
        return $product;
    }

    public function checkStockAvailability($productId, $stockId = null, $quantity = 1): bool
    {
        $query = Stock::where('product_id', $productId);

        if ($stockId) {
            $query->where('id', $stockId);
        }

        $stock = $query->first();

        return $stock && $stock->quantity >= $quantity;
    }

    public function reduceStock($productId, $stockId = null, $quantity = 1): bool
    {
        $query = Stock::where('product_id', $productId);

        if ($stockId) {
            $query->where('id', $stockId);
        }

        $stock = $query->first();

        if ($stock && $stock->quantity >= $quantity) {
            $stock->decrement('quantity', $quantity);
            return true;
        }

        return false;
    }

    public function restoreStock($productId, $stockId = null, $quantity = 1): bool
    {
        $query = Stock::where('product_id', $productId);

        if ($stockId) {
            $query->where('id', $stockId);
        }

        $stock = $query->first();

        if ($stock) {
            $stock->increment('quantity', $quantity);
            return true;
        }

        return false;
    }



    public function getProductsForCart(): Collection
    {
        return Product::where('active', true)
            ->with(['stocks' => function ($query) {
                $query->where('quantity', '>', 0);
            }])
            ->get();
    }

    public function calculatePriceWithVariation($productId, $stockId = null): float
    {
        $product = Product::findOrFail($productId);
        $basePrice = $product->price;

        if ($stockId) {
            $stock = Stock::find($stockId);
            if ($stock && $stock->product_id == $productId) {
                return $basePrice + $stock->price_adjustment;
            }
        }

        return $basePrice;
    }

    public function saveVariation($productId, array $variationData, $stockId = null): Stock
    {
        $product = Product::findOrFail($productId);

        $existingVariation = $product->stocks()
            ->where('variation_value', $variationData['variation_value']);

        if ($stockId) {
            $existingVariation->where('id', '!=', $stockId);
        }

        $existingVariation = $existingVariation->first();

        if ($existingVariation) {
            throw new \Exception('Já existe uma variação com este valor.');
        }

        if ($stockId) {
            $stock = $product->stocks()->findOrFail($stockId);
            $stock->update([
                'variation_name' => $variationData['variation_name'],
                'variation_value' => $variationData['variation_value'],
                'quantity' => $variationData['quantity'],
                'price_adjustment' => $variationData['price_adjustment'] ?? 0
            ]);
        } else {
            $stock = $product->stocks()->create([
                'variation_name' => $variationData['variation_name'],
                'variation_value' => $variationData['variation_value'],
                'quantity' => $variationData['quantity'],
                'price_adjustment' => $variationData['price_adjustment'] ?? 0
            ]);
        }

        return $stock;
    }

    public function removeVariation($productId, $stockId): bool
    {
        $product = Product::findOrFail($productId);
        $stock = $product->stocks()->findOrFail($stockId);

        if ($stock->orderItems()->exists()) {
            throw new \Exception('Não é possível remover uma variação que já foi utilizada em pedidos.');
        }

        return $stock->delete();
    }

    public function getProductVariations($productId): Collection
    {
        $product = Product::findOrFail($productId);
        return $product->stocks()->orderBy('variation_name')->get();
    }
} 