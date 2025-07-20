<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação do Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .order-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th,
        .items-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .items-table th {
            background-color: #f8f9fa;
        }
        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Confirmação do Pedido</h1>
        <p>Obrigado por comprar conosco!</p>
    </div>

    <div class="content">
        <h2>Olá, {{ $order->userProfile->full_name ?? 'Cliente' }}!</h2>
        
        <p>Seu pedido foi recebido com sucesso e está sendo processado. Abaixo estão os detalhes do seu pedido:</p>

        <div class="order-info">
            <h3>Informações do Pedido</h3>
            <p><strong>Número do Pedido:</strong> {{ $order->order_number }}</p>
            <p><strong>Data:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
        </div>

        <h3>Itens do Pedido</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}
                        @if($item->variation_info && $item->variation_info != 'Produto padrão')
                            <br><small>{{ $item->variation_info }}</small>
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($item->total_price, 2, ',', '.') }}</td>
                </tr>
                @endforeach
                
                <tr>
                    <td colspan="3"><strong>Subtotal:</strong></td>
                    <td><strong>R$ {{ number_format($order->subtotal, 2, ',', '.') }}</strong></td>
                </tr>
                
                <tr>
                    <td colspan="3"><strong>Frete:</strong></td>
                    <td><strong>
                        @if($order->shipping_cost == 0)
                            Grátis
                        @else
                            R$ {{ number_format($order->shipping_cost, 2, ',', '.') }}
                        @endif
                    </strong></td>
                </tr>
                
                @if($order->discount > 0)
                <tr>
                    <td colspan="3"><strong>Desconto:</strong></td>
                    <td><strong>- R$ {{ number_format($order->discount, 2, ',', '.') }}</strong></td>
                </tr>
                @endif
                
                <tr class="total-row">
                    <td colspan="3"><strong>TOTAL:</strong></td>
                    <td><strong>R$ {{ number_format($order->total, 2, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="order-info">
            <h3>Endereço de Entrega</h3>
            <p>
                {{ $order->address }}, {{ $order->number }}
                @if($order->complement)
                    - {{ $order->complement }}
                @endif
                <br>
                {{ $order->neighborhood }} - {{ $order->city }}/{{ $order->state }}
                <br>
                CEP: {{ substr($order->cep, 0, 5) }}-{{ substr($order->cep, 5) }}
            </p>
        </div>

        <h3>Próximos Passos</h3>
        <p>
            Seu pedido está sendo processado e você receberá atualizações sobre o status do seu pedido por email.
            O prazo de entrega será informado assim que o pedido for enviado.
        </p>

        <p>
            Se você tiver alguma dúvida sobre seu pedido, entre em contato conosco através do email 
            de resposta deste email.
        </p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Mini ERP. Todos os direitos reservados.</p>
        <p>Este é um email automático, não responda a esta mensagem.</p>
    </div>
</body>
</html> 