@extends('layout.app')

@section('title', 'Carrinho - Mini ERP')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="bi bi-cart3 text-primary"></i> Meu Carrinho</h2>
            <p class="text-muted">Revise seus itens antes de finalizar a compra</p>
        </div>
    </div>

    @if(!empty($cart))
        <div class="row">
            <div class="col-lg-8">
                <!-- Cart Items -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-0">
                        @foreach($cart as $cartKey => $item)
                            <div class="d-flex align-items-center p-4 border-bottom cart-item" data-cart-key="{{ $cartKey }}">
                                <!-- Product Image -->
                                <div class="flex-shrink-0 me-4">
                                    <div class="position-relative">
                                        <img src="{{ $item['image'] }}" 
                                             class="rounded border cart-item-image" 
                                             alt="{{ $item['name'] }}"
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                </div>
                                
                                <!-- Product Info -->
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $item['name'] }}</h6>
                                    @if($item['variation_info'] != 'Produto padrão')
                                        <div class="mb-2">
                                            <span class="badge bg-light text-dark border">
                                                {{ $item['variation_info'] }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="text-muted small mb-2">
                                        Preço unitário: <span class="price fw-bold">R$ {{ number_format($item['price'], 2, ',', '.') }}</span>
                                    </div>
                                    
                                    <!-- Quantity Controls -->
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex align-items-center">
                                            <span class="small text-muted me-2">Quantidade:</span>
                                            @auth
                                                <div class="input-group input-group-sm" style="width: 120px;">
                                                    <button type="button" class="btn btn-outline-secondary decrease-qty">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                    <input type="number" class="form-control text-center quantity-input" 
                                                           value="{{ $item['quantity'] }}" min="1" 
                                                           data-cart-key="{{ $cartKey }}">
                                                    <button type="button" class="btn btn-outline-secondary increase-qty">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="badge bg-light text-dark">{{ $item['quantity'] }}</span>
                                                <small class="text-muted ms-2">(Faça login para alterar)</small>
                                            @endauth
                                        </div>
                                        
                                        @auth
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-item" 
                                                    data-cart-key="{{ $cartKey }}"
                                                    title="Remover item">
                                                <i class="bi bi-trash"></i> Remover
                                            </button>
                                        @else
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle"></i> Faça login para remover
                                            </small>
                                        @endauth
                                    </div>
                                </div>
                                
                                <!-- Item Total -->
                                <div class="text-end">
                                    <div class="h5 price mb-0 item-total">
                                        R$ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}
                                    </div>
                                    <small class="text-muted">{{ $item['quantity'] }} {{ $item['quantity'] > 1 ? 'itens' : 'item' }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left"></i> Continuar Comprando
                            </a>
                            @auth
                                <form action="{{ route('cart.clear') }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Tem certeza que deseja limpar o carrinho?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i> Limpar Carrinho
                                    </button>
                                </form>
                            @else
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i> Faça login para limpar o carrinho
                                </small>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-calculator"></i> Resumo do Pedido
                        </h5>
                    </div>
                    
                    <div class="card-body">
                        <!-- Coupon Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-tag"></i> Cupom de Desconto
                            </h6>
                            
                            @auth
                                @if(!session('applied_cupon'))
                                    <form action="{{ route('cart.apply-coupon') }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="cupon_code" 
                                                   placeholder="Digite o código do cupom"
                                                   style="font-family: monospace;">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check"></i> Aplicar
                                            </button>
                                        </div>
                                        <small class="text-muted">Ex: DESCONTO10, FRETE15</small>
                                    </form>
                                @else
                                    <div class="alert alert-success border-0 d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-tag-fill text-success"></i> 
                                            <strong>{{ session('applied_cupon.code') }}</strong>
                                            <br><small>{{ session('applied_cupon.name') }}</small>
                                        </div>
                                        <form action="{{ route('cart.remove-coupon') }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Remover cupom">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-info border-0">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Faça login</strong> para aplicar cupons de desconto
                                </div>
                            @endauth
                        </div>

                        <!-- Totals -->
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal ({{ count($cart) }} {{ count($cart) > 1 ? 'itens' : 'item' }}):</span>
                                <span class="fw-bold">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="d-flex align-items-center">
                                    <i class="bi bi-truck me-1"></i> Frete:
                                </span>
                                <span class="fw-bold">
                                    @if($shipping == 0)
                                        <span class="text-success">Grátis!</span>
                                    @else
                                        R$ {{ number_format($shipping, 2, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                            @if($shipping == 0)
                                <small class="text-success">
                                    <i class="bi bi-check-circle"></i> Parabéns! Você ganhou frete grátis
                                </small>
                            @elseif($subtotal >= 52 && $subtotal <= 166.59)
                                <small class="text-info">
                                    <i class="bi bi-info-circle"></i> Frete promocional aplicado
                                </small>
                            @else
                                <small class="text-muted">
                                    @php $faltaFrete = 200 - $subtotal; @endphp
                                    @if($faltaFrete > 0)
                                        Faltam R$ {{ number_format($faltaFrete, 2, ',', '.') }} para frete grátis
                                    @endif
                                </small>
                            @endif
                        </div>
                        
                        @if($cuponDiscount > 0 && session('applied_cupon'))
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between text-success">
                                    <span>
                                        <i class="bi bi-tag-fill"></i> Desconto ({{ session('applied_cupon.code') }}):
                                    </span>
                                    <span class="fw-bold">- R$ {{ number_format($cuponDiscount, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        @endif
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0">Total:</span>
                                <span class="h4 price mb-0 fw-bold">R$ {{ number_format($total, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            @auth
                                <a href="{{ route('cart.checkout') }}" class="btn btn-success btn-lg">
                                    <i class="bi bi-credit-card"></i> Finalizar Compra
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                    <i class="bi bi-person-circle"></i> Fazer Login para Finalizar
                                </a>
                                <small class="text-muted text-center mt-2">
                                    <i class="bi bi-info-circle"></i> 
                                    Faça login para continuar com a compra
                                </small>
                            @endauth
                        </div>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-shield-check"></i> Compra 100% segura
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-cart-x text-muted" style="font-size: 5rem;"></i>
            </div>
            <h3 class="text-muted mb-3">Seu carrinho está vazio</h3>
            <p class="text-muted mb-4">Adicione produtos ao seu carrinho para continuar</p>
            <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-shop"></i> Começar a Comprar
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<style>
    .cart-item {
        transition: background-color 0.2s ease;
    }
    .cart-item:hover {
        background-color: #f8f9fa;
    }
    .cart-item-image {
        transition: transform 0.2s ease;
    }
    .cart-item:hover .cart-item-image {
        transform: scale(1.05);
    }
    .sticky-top {
        z-index: 1020;
    }
</style>

<script>
    @auth
    // Handle quantity changes
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            updateQuantity(this.dataset.cartKey, this.value);
        });
    });

    document.querySelectorAll('.increase-qty').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            const newValue = parseInt(input.value) + 1;
            input.value = newValue;
            updateQuantity(input.dataset.cartKey, newValue);
        });
    });

    document.querySelectorAll('.decrease-qty').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            const newValue = Math.max(1, parseInt(input.value) - 1);
            input.value = newValue;
            updateQuantity(input.dataset.cartKey, newValue);
        });
    });

    // Handle item removal
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Tem certeza que deseja remover este item?')) {
                removeItem(this.dataset.cartKey);
            }
        });
    });
    @endauth

    function updateQuantity(cartKey, quantity) {
        fetch('{{ route("cart.update") }}', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                cart_key: cartKey,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: data.message || 'Erro ao atualizar quantidade',
                    confirmButtonText: 'OK'
                });
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Erro ao atualizar quantidade',
                confirmButtonText: 'OK'
            });
            location.reload();
        });
    }

    function removeItem(cartKey) {
        fetch('{{ route("cart.remove") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                cart_key: cartKey
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: data.message || 'Erro ao remover item',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Erro ao remover item',
                confirmButtonText: 'OK'
            });
        });
    }

    // Auto-update cart count
    updateCartCount();
</script>
@endpush 