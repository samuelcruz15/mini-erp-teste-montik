<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderService
{
    private $cartService;
    private $productService;

    public function __construct(CartService $cartService, ProductService $productService)
    {
        $this->cartService = $cartService;
        $this->productService = $productService;
    }

    public function createOrder(array $data, $userId): array|Order
    {
        $cartErrors = $this->cartService->validateCart();
        if (!empty($cartErrors)) {
            return ['success' => false, 'errors' => $cartErrors];
        }

        $cart = $this->cartService->getCartItems();
        if (empty($cart)) {
            return ['success' => false, 'errors' => ['Carrinho vazio']];
        }

        try {
            DB::beginTransaction();

            $user = User::findOrFail($userId);
            $userProfile = $this->getOrCreateUserProfile($user, $data);

            $address = $this->getOrCreateAddress($user, $data);
            
            if (!$address) {
                return ['success' => false, 'errors' => ['Endereço não encontrado ou inválido']];
            }

            $subtotal = $this->cartService->getSubtotal();
            $shipping = $this->calculateShipping($subtotal);
            $discount = 0;
            $cuponId = null;

            $appliedCupon = session('applied_cupon');
            if ($appliedCupon) {
                $cupon = \App\Models\Cupon::find($appliedCupon['id']);
                if ($cupon && $cupon->isValid($subtotal)) {
                    $discount = $cupon->calculateDiscount($subtotal);
                    $cuponId = $cupon->id;

                }
            }

            $total = $subtotal + $shipping - $discount;

            $order = Order::create([
                'user_id' => $userId,
                'user_profile_id' => $userProfile->id,
                'address_id' => $address->id,
                'order_number' => $this->generateOrderNumber(),
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'discount' => $discount,
                'total' => $total,
                'cupon_id' => $cuponId,
                'status' => 'pending'
            ]);

            if ($cuponId && $cupon) {
                $cupon->incrementUsage();
            }

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'stock_id' => $item['stock_id'] ?? null,
                    'product_name' => $item['name'],
                    'variation_info' => $item['variation_info'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity']
                ]);

                if (isset($item['stock_id'])) {
                    $this->productService->reduceStock(
                        $item['product_id'],
                        $item['stock_id'],
                        $item['quantity']
                    );
                }
            }

            $this->cartService->clearCart();
            session()->forget('applied_cupon');

            $this->sendOrderConfirmation($order);

            DB::commit();

            return [
                'success' => true,
                'order' => $order,
                'message' => 'Pedido criado com sucesso!'
            ];

        } catch (\Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                'errors' => ['Erro ao processar pedido: ' . $e->getMessage()]
            ];
        }
    }

    private function getOrCreateUserProfile(User $user, array $data)
    {
        $userProfile = $user->profile;
        
        if (!$userProfile) {
            $userProfile = UserProfile::create([
                'user_id' => $user->id,
                'full_name' => $data['customer_name'],
                'phone' => $data['customer_phone'],
                'cpf' => $data['cpf'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'gender' => $data['gender'] ?? null,
            ]);
        } else {
            $userProfile->update([
                'full_name' => $data['customer_name'],
                'phone' => $data['customer_phone'],
            ]);
        }
        
        return $userProfile;
    }

    private function getOrCreateAddress(User $user, array $data)
    {
        if (isset($data['selected_address']) && $data['selected_address']) {
            $address = $user->addresses()->find($data['selected_address']);
            if ($address) {
                return $address;
            }
        }

        if (!isset($data['shipping_zipcode']) || !isset($data['shipping_address'])) {
            throw new \Exception('Dados de endereço não fornecidos. Por favor, selecione um endereço salvo ou preencha um novo endereço.');
        }

        $existingAddress = $user->addresses()
            ->where('cep', str_replace('-', '', $data['shipping_zipcode']))
            ->where('street', $data['shipping_address'])
            ->where('number', $data['number'])
            ->first();
        
        if ($existingAddress) {
            return $existingAddress;
        }
        
        return Address::create([
            'user_id' => $user->id,
            'name' => 'Endereço de Entrega',
            'cep' => str_replace('-', '', $data['shipping_zipcode']),
            'street' => $data['shipping_address'],
            'number' => $data['number'],
            'complement' => $data['complement'] ?? null,
            'neighborhood' => $data['neighborhood'],
            'city' => $data['shipping_city'],
            'state' => $data['shipping_state'],
            'is_default' => false,
        ]);
    }

    public function findById($id): Order
    {
        return Order::with(['items.product', 'items.stock', 'user', 'userProfile', 'address'])->findOrFail($id);
    }

    public function getUserOrders($userId): LengthAwarePaginator
    {
        return Order::with(['items.product', 'items.stock', 'userProfile', 'address'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getAllOrders(): LengthAwarePaginator
    {
        return Order::with(['items.product', 'items.stock', 'user', 'userProfile', 'address'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function updateOrderStatus($orderId, $status): Order
    {
        $order = Order::findOrFail($orderId);
        $oldStatus = $order->status;
        
        $order->update(['status' => $status]);

        if ($status === 'cancelled' && $oldStatus !== 'cancelled') {
            $this->restoreOrderStock($order);
        }

        return $order;
    }

    public function cancelOrderViaWebhook($orderId): array
    {
        $order = Order::find($orderId);
        
        if (!$order) {
            return ['success' => false, 'message' => 'Pedido não encontrado'];
        }

        if ($order->status === 'cancelled') {
            return ['success' => false, 'message' => 'Pedido já está cancelado'];
        }

        try {
            DB::beginTransaction();

            $order->update(['status' => 'cancelled']);
            $this->restoreOrderStock($order);

            DB::commit();

            return ['success' => true, 'message' => 'Pedido cancelado com sucesso'];

        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => 'Erro ao cancelar pedido: ' . $e->getMessage()];
        }
    }

    private function restoreOrderStock($order): void
    {
        foreach ($order->items as $item) {
            if ($item->stock_id) {
                $this->productService->restoreStock(
                    $item->product_id,
                    $item->stock_id,
                    $item->quantity
                );
            }
        }
    }

    private function calculateShipping($subtotal): float
    {
        if ($subtotal >= 200) {
            return 0;
        } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
            return 15.00;
        } else {
            return 20.00;
        }
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'PED';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        
        return $prefix . $date . $random;
    }

    private function sendOrderConfirmation($order): void
    {
        try {
            Mail::to($order->user->email)->send(new OrderConfirmation($order));
        } catch (\Exception $e) {
            // Email error handled silently
        }
    }

    public function getOrderStats(): array
    {
        return [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total')
        ];
    }

    public function getOrdersByStatus($status): LengthAwarePaginator
    {
        return Order::with(['items', 'user', 'userProfile', 'address'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getOrdersByDateRange($startDate, $endDate): LengthAwarePaginator
    {
        return Order::with(['items', 'user', 'userProfile', 'address'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }
} 