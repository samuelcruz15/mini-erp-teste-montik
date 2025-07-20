<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\Session;

class CartService
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function addToCart($productId, $quantity = 1, $stockId = null): array
    {
        $product = Product::findOrFail($productId);
        
        if (!$product->active) {
            return ['success' => false, 'message' => 'Produto não está disponível'];
        }

        if (!$this->productService->checkStockAvailability($productId, $stockId, $quantity)) {
            return ['success' => false, 'message' => 'Quantidade não disponível em estoque'];
        }

        $cart = Session::get('cart', []);
        $itemKey = $this->generateItemKey($productId, $stockId);

        if (isset($cart[$itemKey])) {
            $newQuantity = $cart[$itemKey]['quantity'] + $quantity;
            
            if (!$this->productService->checkStockAvailability($productId, $stockId, $newQuantity)) {
                return ['success' => false, 'message' => 'Quantidade não disponível em estoque'];
            }
            
            $cart[$itemKey]['quantity'] = $newQuantity;
        } else {
            $price = $this->productService->calculatePriceWithVariation($productId, $stockId);
            
            $variationInfo = 'Produto padrão';
            $stock = null;
            if ($stockId) {
                $stock = \App\Models\Stock::find($stockId);
                if ($stock) {
                    $variationInfo = $stock->full_variation_name;
                }
            }
            
            $cart[$itemKey] = [
                'product_id' => $productId,
                'stock_id' => $stockId,
                'name' => $product->name,
                'price' => $price,
                'quantity' => $quantity,
                'variation_info' => $variationInfo,
                'image' => $product->image,
                'has_variations' => $product->has_variations
            ];
        }

        Session::put('cart', $cart);
        
        return ['success' => true, 'message' => 'Produto adicionado ao carrinho'];
    }

    public function removeFromCart($itemKey): array
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$itemKey])) {
            unset($cart[$itemKey]);
            Session::put('cart', $cart);
            return ['success' => true, 'message' => 'Item removido do carrinho'];
        }

        return ['success' => false, 'message' => 'Item não encontrado no carrinho'];
    }

    public function updateQuantity($itemKey, $quantity): array
    {
        $cart = Session::get('cart', []);
        
        if (!isset($cart[$itemKey])) {
            return ['success' => false, 'message' => 'Item não encontrado no carrinho'];
        }

        if ($quantity <= 0) {
            return $this->removeFromCart($itemKey);
        }

        $item = $cart[$itemKey];
        
        if (!$this->productService->checkStockAvailability($item['product_id'], $item['stock_id'], $quantity)) {
            return ['success' => false, 'message' => 'Quantidade não disponível em estoque'];
        }

        $cart[$itemKey]['quantity'] = $quantity;
        Session::put('cart', $cart);
        
        return ['success' => true, 'message' => 'Quantidade atualizada'];
    }

    public function getCartItems(): array
    {
        return Session::get('cart', []);
    }

    public function getSubtotal(): float
    {
        $cart = Session::get('cart', []);
        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return $subtotal;
    }
    public function getTotalItems(): int
    {
        $cart = Session::get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['quantity'];
        }

        return $total;
    }

    public function clearCart(): array
    {
        Session::forget('cart');
        return ['success' => true, 'message' => 'Carrinho limpo'];
    }

    public function isEmpty(): bool
    {
        return empty(Session::get('cart', []));
    }

    private function generateItemKey($productId, $stockId = null): string
    {
        return $productId . '_' . ($stockId ?? 'default');
    }

    public function validateCart(): array
    {
        $cart = Session::get('cart', []);
        $errors = [];

        foreach ($cart as $itemKey => $item) {
            $product = Product::find($item['product_id']);
            
            if (!$product) {
                $errors[] = "Produto '{$item['name']}' não encontrado";
                continue;
            }

            if (!$product->active) {
                $errors[] = "Produto '{$item['name']}' não está mais disponível";
                continue;
            }

            if (!$this->productService->checkStockAvailability($item['product_id'], $item['stock_id'], $item['quantity'])) {
                $errors[] = "Quantidade insuficiente em estoque para '{$item['name']}'";
            }
        }

        return $errors;
    }

    public function getCartWithProducts(): array        
    {
        $cart = Session::get('cart', []);
        $cartWithProducts = [];

        foreach ($cart as $itemKey => $item) {
            $product = Product::find($item['product_id']);
            
            if ($product) {
                $cartWithProducts[$itemKey] = array_merge($item, [
                    'product' => $product
                ]);
            }
        }

        return $cartWithProducts;
    }
} 