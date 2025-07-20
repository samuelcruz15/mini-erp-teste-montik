@extends('layout.app')

@section('title', 'Pedido #' . $order->order_number . ' - Mini ERP')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>
                <i class="bi bi-receipt text-primary"></i> 
                Pedido #{{ $order->order_number }}
            </h2>
            <p class="text-muted">Detalhes completos do pedido</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar à Lista
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Order Information -->
        <div class="col-lg-8">
            <!-- Order Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Status do Pedido
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4>
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
                            </h4>
                            <p class="text-muted mb-0">
                                Pedido realizado em {{ $order->created_at->format('d/m/Y \à\s H:i') }}
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            @if($order->status !== 'cancelled' && $order->status !== 'delivered' && auth()->user()->is_admin)
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary dropdown-toggle" 
                                            type="button" 
                                            data-bs-toggle="dropdown">
                                        <i class="bi bi-gear"></i> Alterar Status
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if($order->status === 'pending')
                                            <li>
                                                <form action="{{ route('orders.update-status', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="confirmed">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bi bi-check text-info"></i> Confirmar Pedido
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
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person"></i> Dados do Cliente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Nome:</label>
                                <div class="fw-bold">{{ $order->userProfile->full_name ?? 'N/A' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Email:</label>
                                <div>{{ $order->user->email ?? 'N/A' }}</div>
                            </div>
                            @if($order->userProfile && $order->userProfile->phone)
                                <div class="mb-3">
                                    <label class="form-label text-muted">Telefone:</label>
                                    <div>{{ $order->userProfile->phone }}</div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Endereço de Entrega:</label>
                                <div>
                                    @if($order->address)
                                        {{ $order->address->street }}, {{ $order->address->number }}
                                        @if($order->address->complement)
                                            <br>{{ $order->address->complement }}
                                        @endif
                                        <br>{{ $order->address->neighborhood }}
                                        <br>{{ $order->address->city }} - {{ $order->address->state }}
                                        <br>CEP: {{ substr($order->address->cep, 0, 5) . '-' . substr($order->address->cep, 5) }}
                                    @else
                                        <span class="text-muted">Endereço não disponível</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-box"></i> Itens do Pedido ({{ $order->items->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Produto</th>
                                    <th>Variação</th>
                                    <th>Preço Unit.</th>
                                    <th>Qtd.</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->image)
                                                    <img src="{{ $item->product->image }}" 
                                                         alt="{{ $item->product_name }}" 
                                                         class="me-3 rounded" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $item->product_name }}</div>
                                                    @if(!$item->product)
                                                        <small class="text-danger">Produto removido</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($item->stock && $item->stock->variation_name)
                                                <span class="badge bg-light text-dark border">
                                                    {{ $item->stock->variation_name }}: {{ $item->stock->variation_value }}
                                                </span>
                                            @else
                                                <span class="text-muted">Padrão</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="price">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</span>
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>
                                            <span class="price fw-bold">
                                                R$ {{ number_format($item->total_price, 2, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-calculator"></i> Resumo Financeiro
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Subtotal -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span class="fw-bold">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                    </div>

                    <!-- Shipping -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Frete:</span>
                        <span class="fw-bold">
                            @if($order->shipping_cost > 0)
                                R$ {{ number_format($order->shipping_cost, 2, ',', '.') }}
                            @else
                                <span class="text-success">Grátis</span>
                            @endif
                        </span>
                    </div>

                    <!-- Coupon Discount -->
                    @if($order->cupon)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>
                                <i class="bi bi-tag-fill"></i> Desconto ({{ $order->cupon->code }}):
                            </span>
                            <span class="fw-bold">- R$ {{ number_format($order->discount, 2, ',', '.') }}</span>
                        </div>
                    @endif

                    <hr>

                    <!-- Total -->
                    <div class="d-flex justify-content-between mb-3">
                        <span class="h5">Total:</span>
                        <span class="h5 price fw-bold">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                    </div>

                    @if($order->cupon)
                        <div class="alert alert-success border-0">
                            <h6><i class="bi bi-tag-fill"></i> Cupom Aplicado</h6>
                            <strong>{{ $order->cupon->code }}</strong><br>
                            <small>{{ $order->cupon->name }}</small>
                        </div>
                    @endif

                    <!-- Payment Method -->
                    <div class="bg-light p-3 rounded">
                        <h6><i class="bi bi-credit-card"></i> Forma de Pagamento</h6>
                        <p class="mb-0 text-muted">Pagamento na entrega</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
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