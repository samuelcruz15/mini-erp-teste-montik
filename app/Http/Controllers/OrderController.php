<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Order;
use App\Services\OrderService;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderStatusRequest;

class OrderController extends Controller
{
    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(): View
    {
        $orders = Order::getAllWithRelations();
        
        $stats = [
            'pending' => Order::where('status', 'pending')->count(),
            'in_progress' => Order::whereIn('status', ['confirmed', 'processing'])->count(),
            'shipped_delivered' => Order::whereIn('status', ['shipped', 'delivered'])->count(),
            'total_sales' => Order::sum('total')
        ];
        
        return view('orders.index', compact('orders', 'stats'));
    }

    public function show($orderId): View|RedirectResponse
    {
        $user = auth()->user();
        if ($user->is_admin) {
            $order = Order::findWithRelations($orderId);
        } else {
            $order = Order::findUserOrder($orderId, $user->id);
            if (!$order) {
             return redirect()->route('my-orders')->with('error', 'Acesso negado ao pedido.');
            }
        }
       return view('orders.show', compact('order'));
    }

    public function store(OrderStoreRequest $request): RedirectResponse
    {
        $result = $this->orderService->createOrder(
            $request->validated(),
            auth()->id()
        );

        if ($result['success']) {
            return redirect()->route('my-orders.show', $result['order'])
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->with('error', implode(', ', $result['errors']))
                ->withInput();
        }
    }

    public function updateStatus(OrderStatusRequest $request, Order $order): RedirectResponse
    {
        try {
            $this->orderService->updateOrderStatus($order->id, $request->validated('status'));
            return redirect()->back()->with('success', 'Status do pedido atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao atualizar status: ' . $e->getMessage());
        }
    }

    public function cancel(Order $order): RedirectResponse
    {
        try {
            $this->orderService->updateOrderStatus($order->id, 'cancelled');
            return redirect()->back()->with('success', 'Pedido cancelado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao cancelar pedido: ' . $e->getMessage());
        }
    }

    public function myOrders(): View
    {
        $orders = Order::getUserOrders(auth()->id());
        return view('orders.my-orders', compact('orders'));
    }
}
