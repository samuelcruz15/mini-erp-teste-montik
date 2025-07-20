@extends('layout.app')

@section('title', 'Mini ERP - Sua loja online')

@section('content')
<div class="container py-4">
    
    <!-- Carrossel de Cupons -->
    @if($cupons->count() > 0)
        <div id="cuponsCarousel" class="carousel slide carousel-cupons mb-4" data-bs-ride="carousel">
            <div class="carousel-indicators">
                @foreach($cupons as $index => $cupon)
                    <button type="button" data-bs-target="#cuponsCarousel" data-bs-slide-to="{{ $index }}" 
                            @if($index === 0) class="active" @endif></button>
                @endforeach
            </div>
            
            <div class="carousel-inner">
                @foreach($cupons as $index => $cupon)
                    <div class="carousel-item @if($index === 0) active @endif">
                        <div class="d-flex justify-content-center align-items-center text-white p-4" style="height: 200px;">
                            <div class="text-center">
                                <h3 class="fw-bold mb-4">
                                    <i class="bi bi-tag-fill"></i> {{ $cupon->code }}
                                </h3>
                                <h5 class="mb-2">{{ $cupon->name }}</h5>
                                @if($cupon->minimum_amount > 0)
                                    <div class="mb-3">
                                        <span class="h6">em compras acima de R$ {{ number_format($cupon->minimum_amount, 2, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="mt-auto">
                                    <small class="d-block">
                                        <i class="bi bi-calendar-event"></i> 
                                        Válido até {{ $cupon->valid_until->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#cuponsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#cuponsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    @endif

    <!-- Header da seção de produtos -->
    <div class="row mb-4">
        <div class="col-12">
            @if(request('search'))
                <h3 class="mb-2">
                    <i class="bi bi-search text-primary"></i> 
                    Resultados para: <span class="text-primary">"{{ request('search') }}"</span>
                </h3>
                <p class="text-muted">
                    {{ $products->total() }} produto(s) encontrado(s)
                    <a href="{{ route('home') }}" class="ms-3 text-decoration-none text-primary">
                        <i class="bi bi-x-circle"></i> Limpar busca
                    </a>
                </p>
            @else
                <h3 class="mb-2">
                    <i class="bi bi-grid-3x3-gap text-primary"></i> 
                    Todos os produtos
                </h3>
                <p class="text-muted">Explore nossa seleção completa de produtos</p>
            @endif
        </div>
    </div>

    <!-- Produtos Grid -->
    @if($products->count() > 0)
        <div class="row">
            @foreach($products as $product)
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 border-0 shadow-sm product-card">
                        <!-- Imagem do Produto -->
                        <div class="position-relative">
                            <img src="{{ $product->image }}" 
                                 class="card-img-top product-image" 
                                 alt="{{ $product->name }}"
                                 style="height: 180px; object-fit: cover;">
                            
                            @if($product->total_stock <= 5 && $product->total_stock > 0)
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    Últimas unidades!
                                </span>
                            @elseif($product->total_stock == 0)
                                <span class="position-absolute top-0 end-0 badge bg-danger m-2">
                                    Esgotado
                                </span>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column p-3">
                            <!-- Nome do Produto -->
                            <h6 class="card-title mb-2" style="height: 48px; overflow: hidden; line-height: 1.4;">
                                {{ Str::limit($product->name, 45) }}
                            </h6>

                            <!-- Preço -->
                            <div class="mb-2">
                                @if($product->has_variations && $product->stocks->count() > 1)
                                    @php
                                        $minPrice = $product->stocks->min('price_adjustment') + $product->price;
                                        $maxPrice = $product->stocks->max('price_adjustment') + $product->price;
                                    @endphp
                                    
                                    @if($minPrice != $maxPrice)
                                        <span class="price h6">
                                            R$ {{ number_format($minPrice, 2, ',', '.') }} - 
                                            R$ {{ number_format($maxPrice, 2, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="price h5 mb-0">R$ {{ number_format($minPrice, 2, ',', '.') }}</span>
                                    @endif
                                @else
                                    <span class="price h5 mb-0">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                                @endif
                            </div>

                            <!-- Informações de Estoque -->
                            <div class="mb-3">
                                @if($product->has_variations)
                                    <small class="text-muted">
                                        <i class="bi bi-palette"></i> 
                                        {{ $product->stocks->count() }} variações disponíveis
                                    </small>
                                @endif
                                
                                @if($product->total_stock > 0)
                                    <div class="small text-success">
                                        <i class="bi bi-check-circle"></i> Em estoque ({{ $product->total_stock }})
                                    </div>
                                @else
                                    <div class="small text-danger">
                                        <i class="bi bi-x-circle"></i> Indisponível
                                    </div>
                                @endif
                            </div>

                            <!-- Botões de Ação -->
                            <div class="mt-auto">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('products.show', $product) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        Ver detalhes
                                    </a>
                                    
                                    @if($product->total_stock > 0)
                                        @if($product->has_variations && $product->stocks->count() > 1)
                                            <!-- Para produtos com variações, redireciona para página do produto -->
                                        @else
                                            <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="stock_id" value="{{ $product->stocks->first()->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-success btn-sm w-100">
                                                    <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                                                </button>
                                            </form>
                                            @guest
                                                <small class="text-muted">
                                                    <i class="bi bi-info-circle"></i> 
                                                    <a href="{{ route('login') }}" class="text-decoration-none">Faça login</a> para finalizar a compra
                                                </small>
                                            @endguest
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Admin Actions -->
                        @auth
                            @if(auth()->user()->is_admin)
                                <div class="card-footer bg-light border-0 p-2">
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('products.edit', $product) }}" 
                                           class="btn btn-outline-warning btn-sm flex-fill">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('products.destroy', $product) }}" 
                                              method="POST" 
                                              class="flex-fill"
                                              onsubmit="return confirm('Tem certeza que deseja desativar este produto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $products->appends(request()->query())->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-5">
            @if(request('search'))
                <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                <h3 class="mt-3 text-muted">Nenhum produto encontrado</h3>
                <p class="text-muted">
                    Não encontramos produtos para "<strong>{{ request('search') }}</strong>"
                </p>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="bi bi-grid-3x3-gap"></i> Ver todos os produtos
                </a>
            @else
                <i class="bi bi-box text-muted" style="font-size: 4rem;"></i>
                <h3 class="mt-3 text-muted">Nenhum produto disponível</h3>
                <p class="text-muted">Ainda não há produtos cadastrados na loja</p>
                @auth
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Cadastrar Primeiro Produto
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<style>
    .product-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
    .carousel-cupons {
        height: 200px;
    }
    .carousel-item {
        height: 200px;
    }
</style>

<script>
    // Handle add to cart forms
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Adicionando...';
            
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na requisição');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateCartCount();
                    
                    // Show success feedback
                    button.innerHTML = '<i class="bi bi-check"></i> Adicionado!';
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-success');
                    
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.classList.remove('btn-outline-success');
                        button.classList.add('btn-success');
                        button.disabled = false;
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Erro ao adicionar produto');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Show error feedback
                button.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Erro';
                button.classList.remove('btn-success');
                button.classList.add('btn-danger');
                
                // Show error message with SweetAlert2
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: error.message || 'Erro ao adicionar produto ao carrinho',
                    confirmButtonText: 'OK'
                });
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-danger');
                    button.classList.add('btn-success');
                    button.disabled = false;
                }, 2000);
            });
        });
    });
    
    // Auto-start carousel
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.getElementById('cuponsCarousel');
        if (carousel) {
            new bootstrap.Carousel(carousel, {
                interval: 5000,
                wrap: true
            });
        }
    });
</script>
@endpush 