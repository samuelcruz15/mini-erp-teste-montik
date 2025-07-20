<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function processStatusUpdate($orderId, $status): array
    {
        try {
            Log::info("Webhook recebido: Pedido {$orderId}, Status: {$status}");

            $order = Order::find($orderId);
            
            if (!$order) {
                Log::error("Webhook: Pedido {$orderId} não encontrado");
                return [
                    'success' => false,
                    'message' => 'Pedido não encontrado'
                ];
            }

            $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            if (!in_array($status, $validStatuses)) {
                Log::error("Webhook: Status inválido '{$status}' para pedido {$orderId}");
                return [
                    'success' => false,
                    'message' => 'Status inválido'
                ];
            }

            $result = $this->orderService->updateOrderStatus($orderId, $status);

            Log::info("Webhook processado com sucesso: Pedido {$orderId} atualizado para {$status}");

            return [
                'success' => true,
                'message' => "Status do pedido atualizado para {$status}",
                'order' => $result
            ];

        } catch (\Exception $e) {
            Log::error("Erro no webhook: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno do servidor'
            ];
        }
    }

    public function processOrderCancellation($orderId): array
    {
        try {
            Log::info("Webhook de cancelamento recebido: Pedido {$orderId}");

            $result = $this->orderService->cancelOrderViaWebhook($orderId);

            if ($result['success']) {
                Log::info("Pedido {$orderId} cancelado com sucesso via webhook");
            } else {
                Log::error("Erro ao cancelar pedido {$orderId}: " . $result['message']);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error("Erro no webhook de cancelamento: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno do servidor'
            ];
        }
    }

    public function validateWebhookPayload($payload): array
    {
        $requiredFields = ['order_id', 'status'];
        
        foreach ($requiredFields as $field) {
            if (!isset($payload[$field])) {
                return [
                    'valid' => false,
                    'message' => "Campo obrigatório '{$field}' não encontrado"
                ];
            }
        }

        if (!is_numeric($payload['order_id'])) {
            return [
                'valid' => false,
                'message' => 'order_id deve ser um número'
            ];
        }

        if (!is_string($payload['status'])) {
            return [
                'valid' => false,
                'message' => 'status deve ser uma string'
            ];
        }

        return ['valid' => true];
    }

    public function logWebhook($method, $payload, $response): void
    {
        $logData = [
            'method' => $method,
            'payload' => $payload,
            'response' => $response,
            'timestamp' => now()->toISOString()
        ];

        Log::info('Webhook log', $logData);
    }

    public function getWebhookStats(): array
    {
        return [
            'total_webhooks_processed' => 0, 
            'successful_webhooks' => 0,
            'failed_webhooks' => 0,
            'last_webhook_time' => null
        ];
    }

    public function processWebhook($payload): array
    {
        $validation = $this->validateWebhookPayload($payload);
        if (!$validation['valid']) {
            return $validation;
        }

        $orderId = $payload['order_id'];
        $status = $payload['status'];

        if ($status === 'cancelled') {
            return $this->processOrderCancellation($orderId);
        } else {
            return $this->processStatusUpdate($orderId, $status);
        }
    }
} 