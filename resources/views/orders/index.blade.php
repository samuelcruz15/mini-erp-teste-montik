@extends('layout.app')

@section('title', 'Todos os Pedidos - Mini ERP')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-list-ul text-primary"></i> Todos os Pedidos</h2>
            <p class="text-muted">Gerencie todos os pedidos do sistema</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-funnel"></i> Filtrar por Status
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('orders.index') }}">Todos</a></li>
                    <li><a class="dropdown-item" href="{{ route('orders.index') }}?status=pending">Pendentes</a></li>
                    <li><a class="dropdown-item" href="{{ route('orders.index') }}?status=confirmed">Confirmados</a></li>
                    <li><a class="dropdown-item" href="{{ route('orders.index') }}?status=processing">Processando</a></li>
                    <li><a class="dropdown-item" href="{{ route('orders.index') }}?status=shipped">Enviados</a></li>
                    <li><a class="dropdown-item" href="{{ route('orders.index') }}?status=delivered">Entregues</a></li>
                    <li><a class="dropdown-item" href="{{ route('orders.index') }}?status=cancelled">Cancelados</a></li>
                </ul>
            </div>
        </div>
    </div>

    @if($orders->count() > 0)
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
                        <h5 class="mt-2">{{ $stats['pending'] }}</h5>
                        <small class="text-muted">Pendentes</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle text-info" style="font-size: 2rem;"></i>
                        <h5 class="mt-2">{{ $stats['in_progress'] }}</h5>
                        <small class="text-muted">Em Andamento</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-truck text-success" style="font-size: 2rem;"></i>
                        <h5 class="mt-2">{{ $stats['shipped_delivered'] }}</h5>
                        <small class="text-muted">Enviados/Entregues</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-currency-dollar text-primary" style="font-size: 2rem;"></i>
                        <h5 class="mt-2">R$ {{ number_format($stats['total_sales'], 2, ',', '.') }}</h5>
                        <small class="text-muted">Total em Vendas</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th>Data</th>
                                <th>Itens</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th width="150">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $order->order_number }}</div>
                                        @if($order->cupon)
                                            <small class="text-success">
                                                <i class="bi bi-tag"></i> {{ $order->cupon->code }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $order->userProfile->full_name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $order->user->email ?? 'N/A' }}</small>
                                        @if($order->userProfile && $order->userProfile->phone)
                                            <br><small class="text-muted">{{ $order->userProfile->phone }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $order->items->count() }} {{ $order->items->count() > 1 ? 'itens' : 'item' }}</div>
                                        <small class="text-muted">
                                            @foreach($order->items->take(2) as $item)
                                                {{ $item->product_name }}@if(!$loop->last), @endif
                                            @endforeach
                                            @if($order->items->count() > 2)
                                                ...
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <div class="price fw-bold">R$ {{ number_format($order->total, 2, ',', '.') }}</div>
                                        @if($order->shipping_cost > 0)
                                            <small class="text-muted">+ R$ {{ number_format($order->shipping_cost, 2, ',', '.') }} frete</small>
                                        @else
                                            <small class="text-success">Frete grátis</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @switch($order->status)
                                                @case('pending') bg-warning text-dark @break
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
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('orders.show', $order) }}" 
                                               class="btn btn-outline-primary" 
                                               title="Ver detalhes">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            @if($order->status !== 'cancelled' && $order->status !== 'delivered')
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-secondary dropdown-toggle" 
                                                            type="button" 
                                                            data-bs-toggle="dropdown"
                                                            title="Alterar status">
                                                        <i class="bi bi-gear"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @if($order->status === 'pending')
                                                            <li>
                                                                <form action="{{ route('orders.update-status', $order) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="status" value="confirmed">
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="bi bi-check text-info"></i> Confirmar
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        
                                                        @if(in_array($order->status, ['confirmed', 'processing']))
                                                            <li>
                                                                <form action="{{ route('orders.update-status', $order) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="status" value="shipped">
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="bi bi-truck text-success"></i> Marcar como Enviado
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        
                                                        @if($order->status === 'shipped')
                                                            <li>
                                                                <form action="{{ route('orders.update-status', $order) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="status" value="delivered">
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="bi bi-check-circle text-success"></i> Marcar como Entregue
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('orders.cancel', $order) }}" 
                                                                  method="POST" 
                                                                  class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="bi bi-x-circle"></i> Cancelar Pedido
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
            <h3 class="mt-3 text-muted">Nenhum pedido encontrado</h3>
            <p class="text-muted">Ainda não há pedidos no sistema</p>
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="bi bi-shop"></i> Ver Produtos
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
            // SweetAlert2 status changes
    document.querySelectorAll('form').forEach(form => {
        if (form.querySelector('input[name="status"]')) {
            form.addEventListener('submit', function(e) {
                const status = this.querySelector('input[name="status"]').value;
                let message = '';
                let icon = 'question';
                let confirmButtonText = 'Sim';
                switch(status) {
                    case 'confirmed':
                        message = 'Confirmar este pedido?';
                        confirmButtonText = 'Confirmar';
                        break;
                    case 'shipped':
                        message = 'Marcar pedido como enviado?';
                        confirmButtonText = 'Marcar como Enviado';
                        break;
                    case 'delivered':
                        message = 'Marcar pedido como entregue?';
                        confirmButtonText = 'Marcar como Entregue';
                        break;
                }
                if (message) {
                    e.preventDefault();
                    Swal.fire({
                        title: message,
                        icon: icon,
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#aaa',
                        confirmButtonText: confirmButtonText,
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        }
    });

    // SweetAlert2 para cancelar pedido 
    document.addEventListener('DOMContentLoaded', function() {
        // Capture all forms with DELETE method
        document.querySelectorAll('form').forEach(form => {
            const methodInput = form.querySelector('input[name="_method"][value="DELETE"]');
            if (methodInput) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Cancelar Pedido?',
                        text: 'Tem certeza que deseja cancelar este pedido? Esta ação irá restaurar o estoque.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sim, cancelar',
                        cancelButtonText: 'Não'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }
        });
    });
</script>
@endpush 