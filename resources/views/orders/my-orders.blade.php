@extends('layout.app')

@section('title', 'Meus Pedidos - Mini ERP')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h2><i class="bi bi-bag-check"></i> Meus Pedidos</h2>
            <p class="text-muted">Acompanhe o status dos seus pedidos</p>
        </div>
    </div>

    @if($orders->count() > 0)
        <div class="row">
            @foreach($orders as $order)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $order->order_number }}</h6>
                            <span class="badge 
                                @switch($order->status)
                                    @case('pending') bg-warning @break
                                    @case('confirmed') bg-info @break
                                    @case('processing') bg-primary @break
                                    @case('shipped') bg-success @break
                                    @case('delivered') bg-success @break
                                    @case('cancelled') bg-danger @break
                                    @default bg-secondary
                                @endswitch">
                                @switch($order->status)
                                    @case('pending') Pendente @break
                                    @case('confirmed') Confirmado @break
                                    @case('processing') Processando @break
                                    @case('shipped') Enviado @break
                                    @case('delivered') Entregue @break
                                    @case('cancelled') Cancelado @break
                                    @default {{ ucfirst($order->status) }}
                                @endswitch
                            </span>
                        </div>
                        
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">Data do Pedido:</small><br>
                                <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">Total:</small><br>
                                <span class="h5 price mb-0">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted">Itens ({{ $order->items->count() }}):</small><br>
                                @foreach($order->items->take(2) as $item)
                                    <small>• {{ $item->product_name }} ({{ $item->quantity }}x)</small><br>
                                @endforeach
                                @if($order->items->count() > 2)
                                    <small class="text-muted">... e mais {{ $order->items->count() - 2 }} item(s)</small>
                                @endif
                            </div>
                            
                            @if($order->cupon)
                                <div class="mb-2">
                                    <span class="badge bg-success">
                                        <i class="bi bi-tag"></i> {{ $order->cupon->code }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    @if($order->status == 'shipped')
                                        <i class="bi bi-truck text-success"></i> Em transporte
                                    @elseif($order->status == 'delivered')
                                        <i class="bi bi-check-circle text-success"></i> Entregue
                                    @elseif($order->status == 'cancelled')
                                        <i class="bi bi-x-circle text-danger"></i> Cancelado
                                    @else
                                        <i class="bi bi-clock text-warning"></i> Em processamento
                                    @endif
                                </small>
                                
                                <a href="{{ route('my-orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center">
           {!! $orders->links('vendor.pagination.bootstrap-5') !!}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <i class="bi bi-bag-x text-muted" style="font-size: 4rem;"></i>
            <h3 class="mt-3 text-muted">Nenhum pedido encontrado</h3>
            <p class="text-muted">Você ainda não fez nenhum pedido em nossa loja</p>
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="bi bi-shop"></i> Começar a Comprar
            </a>
        </div>
    @endif
</div>
@endsection 