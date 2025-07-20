<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\WebhookOrderStatusRequest;

class WebhookController extends Controller
{
    public function updateOrderStatus(WebhookOrderStatusRequest $request): JsonResponse
    {
        try {
            $order = Order::findOrFail($request->validated('order_id'));
            
            Log::info('Webhook recebido', [
                'order_id' => $request->validated('order_id'),
                'status' => $request->validated('status'),
                'current_status' => $order->status
            ]);

            if ($request->validated('status') === 'cancelled' || $request->validated('status') === 'canceled') {
                if ($order->cancel()) {
                    Log::info('Pedido cancelado via webhook', ['order_id' => $order->id]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Pedido cancelado com sucesso',
                        'order_id' => $order->id,
                        'old_status' => $order->status,
                        'new_status' => 'cancelled'
                    ]);
                } else {
                    Log::warning('Não foi possível cancelar o pedido via webhook', ['order_id' => $order->id]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Não foi possível cancelar o pedido',
                        'order_id' => $order->id
                    ], 400);
                }
            }

            if (!Order::isValidStatus($request->validated('status'))) {
                Log::warning('Status inválido recebido via webhook', [
                    'order_id' => $order->id,
                    'status' => $request->validated('status')
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Status inválido',
                    'valid_statuses' => Order::getValidStatuses()
                ], 400);
            }

            $oldStatus = $order->status;
            $order->update(['status' => $request->validated('status')]);

            Log::info('Status do pedido atualizado via webhook', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $request->validated('status')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso',
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $request->validated('status')
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook', [
                'order_id' => $request->validated('order_id'),
                'status' => $request->validated('status'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function test(): JsonResponse
    {
        return response()->json([
            'message' => 'Webhook funcionando',
            'timestamp' => now(),
            'endpoints' => [
                'update_status' => url('/webhook/order-status'),
                'list_orders' => url('/webhook/orders')
            ]
        ]);
    }

    public function listOrders(): JsonResponse
    {
        try {
            $orders = Order::with(['items.product', 'items.stock', 'user', 'userProfile', 'address'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'total' => $order->total,
                        'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $order->updated_at->format('Y-m-d H:i:s'),
                        'customer' => [
                            'name' => $order->user->name,
                            'email' => $order->user->email,
                            'profile' => $order->userProfile ? [
                                'full_name' => $order->userProfile->full_name,
                                'phone' => $order->userProfile->phone,
                                'cpf' => $order->userProfile->cpf
                            ] : null
                        ],
                        'address' => $order->address ? [
                            'street' => $order->address->street,
                            'number' => $order->address->number,
                            'complement' => $order->address->complement,
                            'neighborhood' => $order->address->neighborhood,
                            'city' => $order->address->city,
                            'state' => $order->address->state,
                            'zipcode' => $order->address->zipcode
                        ] : null,
                        'items_count' => $order->items->count(),
                        'items' => $order->items->map(function ($item) {
                            return [
                                'product_name' => $item->product->name,
                                'variation' => $item->stock ? $item->stock->variation : null,
                                'quantity' => $item->quantity,
                                'price' => $item->price,
                                'subtotal' => $item->subtotal
                            ];
                        })
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Orders listadas com sucesso',
                'total_orders' => $orders->count(),
                'data' => $orders
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao listar orders via webhook: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
