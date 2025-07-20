@extends('layout.app')

@section('title', $product->name . ' - Mini ERP')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-6">
            <!-- Product Image -->
            <div class="text-center">
                @if($product->image)
                    <img src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" 
                         class="img-fluid rounded shadow" 
                         alt="{{ $product->name }}"
                         style="max-height: 400px;">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                         style="height: 400px;">
                        <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="ps-md-4">
                <!-- Product Details -->
                <h1 class="h3">{{ $product->name }}</h1>
                
                @if($product->description)
                    <p class="text-muted mb-4">{{ $product->description }}</p>
                @endif

                <!-- Price -->
                <div class="mb-4">
                    <span class="h2 price" id="product-price">
                        R$ {{ number_format($product->price, 2, ',', '.') }}
                    </span>
                </div>

                <!-- Stock Status -->
                <div class="mb-4">
                    @if($product->total_stock > 0)
                        <span class="badge bg-success fs-6">
                            <i class="bi bi-check-circle"></i> {{ $product->total_stock }} em estoque
                        </span>
                    @else
                        <span class="badge bg-danger fs-6">
                            <i class="bi bi-x-circle"></i> Produto esgotado
                        </span>
                    @endif
                </div>

                <!-- Purchase Form -->
                @auth
                    @if(!auth()->user()->is_admin)
                        @if($product->total_stock > 0)
                            <form action="{{ route('cart.add') }}" method="POST" id="add-to-cart-form">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                
                                <!-- Variations -->
                                @if($product->has_variations && $product->stocks->count() > 1)
                                    <div class="mb-4">
                                        <label class="form-label">Escolha a variação:</label>
                                        <select class="form-select" name="stock_id" id="variation-select" required>
                                            <option value="">Selecione uma opção</option>
                                            @foreach($product->stocks as $stock)
                                                @if($stock->quantity > 0)
                                                    <option value="{{ $stock->id }}" 
                                                            data-price="{{ $stock->final_price }}"
                                                            data-stock="{{ $stock->quantity }}">
                                                        {{ $stock->full_variation_name }} 
                                                        ({{ $stock->quantity }} disponível)
                                                        @if($stock->price_adjustment != 0)
                                                            - R$ {{ number_format($stock->final_price, 2, ',', '.') }}
                                                        @endif
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="stock_id" value="{{ $product->stocks->first()->id }}">
                                @endif

                                <!-- Quantity -->
                                <div class="mb-4">
                                    <label class="form-label">Quantidade:</label>
                                    <div class="input-group" style="max-width: 150px;">
                                        <button type="button" class="btn btn-outline-secondary" id="decrease-qty">-</button>
                                        <input type="number" class="form-control text-center" 
                                               name="quantity" id="quantity" value="1" min="1" 
                                               max="{{ $product->stocks->max('quantity') }}">
                                        <button type="button" class="btn btn-outline-secondary" id="increase-qty">+</button>
                                    </div>
                                    <small class="text-muted" id="stock-info">
                                        Máximo: {{ $product->stocks->max('quantity') }} unidades
                                    </small>
                                </div>

                                <!-- Add to Cart Button -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                                    </button>
                                </div>
                            </form>
                        @endif
                    @endif
                @else
                    @if($product->total_stock > 0)
                        <form action="{{ route('cart.add') }}" method="POST" id="add-to-cart-form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            
                            <!-- Variations -->
                            @if($product->has_variations && $product->stocks->count() > 1)
                                <div class="mb-4">
                                    <label class="form-label">Escolha a variação:</label>
                                    <select class="form-select" name="stock_id" id="variation-select" required>
                                        <option value="">Selecione uma opção</option>
                                        @foreach($product->stocks as $stock)
                                            @if($stock->quantity > 0)
                                                <option value="{{ $stock->id }}" 
                                                        data-price="{{ $stock->final_price }}"
                                                        data-stock="{{ $stock->quantity }}">
                                                    {{ $stock->full_variation_name }} 
                                                    ({{ $stock->quantity }} disponível)
                                                    @if($stock->price_adjustment != 0)
                                                        - R$ {{ number_format($stock->final_price, 2, ',', '.') }}
                                                    @endif
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="stock_id" value="{{ $product->stocks->first()->id }}">
                            @endif

                            <!-- Quantity -->
                            <div class="mb-4">
                                <label class="form-label">Quantidade:</label>
                                <div class="input-group" style="max-width: 150px;">
                                    <button type="button" class="btn btn-outline-secondary" id="decrease-qty">-</button>
                                    <input type="number" class="form-control text-center" 
                                           name="quantity" id="quantity" value="1" min="1" 
                                           max="{{ $product->stocks->max('quantity') }}">
                                    <button type="button" class="btn btn-outline-secondary" id="increase-qty">+</button>
                                </div>
                                <small class="text-muted" id="stock-info">
                                    Máximo: {{ $product->stocks->max('quantity') }} unidades
                                </small>
                            </div>

                            <!-- Add to Cart Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                                </button>
                            </div>
                        </form>
                    @endif
                @endauth

                <!-- Admin Actions -->
                <div class="mt-4 pt-4 border-top">
                    <div class="d-flex gap-2">
                        @auth
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-warning">
                                    <i class="bi bi-pencil"></i> Editar Produto
                                </a>
                            @endif
                        @endauth
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Details -->
    @if($product->has_variations)
        <div class="row mt-5">
            <div class="col-12">
                <h4>Estoque por Variação</h4>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Variação</th>
                                <th>Estoque</th>
                                <th>Preço</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->stocks as $stock)
                                <tr>
                                    <td>{{ $stock->full_variation_name }}</td>
                                    <td>{{ $stock->quantity }}</td>
                                    <td>R$ {{ number_format($stock->final_price, 2, ',', '.') }}</td>
                                    <td>
                                        @if($stock->quantity > 0)
                                            <span class="badge bg-success">Disponível</span>
                                        @else
                                            <span class="badge bg-danger">Esgotado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Update price and stock when variation changes
    document.getElementById('variation-select')?.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const price = option.dataset.price;
        const stock = option.dataset.stock;
        
        if (price) {
            document.getElementById('product-price').textContent = 
                'R$ ' + parseFloat(price).toLocaleString('pt-BR', {minimumFractionDigits: 2});
        }
        
        if (stock) {
            const quantityInput = document.getElementById('quantity');
            quantityInput.max = stock;
            quantityInput.value = Math.min(quantityInput.value, stock);
            
            document.getElementById('stock-info').textContent = `Máximo: ${stock} unidades`;
        }
    });

    // Quantity controls
    document.getElementById('decrease-qty')?.addEventListener('click', function() {
        const input = document.getElementById('quantity');
        if (input.value > 1) {
            input.value = parseInt(input.value) - 1;
        }
    });

    document.getElementById('increase-qty')?.addEventListener('click', function() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.max);
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
        }
    });

    // Form submission
    document.getElementById('add-to-cart-form')?.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Adicionando...';
        
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-cart-plus"></i> Adicionar ao Carrinho';
        }, 3000);
    });
</script>
@endpush 