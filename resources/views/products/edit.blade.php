@extends('layout.app')

@section('title', 'Editar Produto - Mini ERP')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil"></i> Editar Produto: {{ $product->name }}
                    </h4>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('products.update', $product) }}" method="POST" id="product-form">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="price" class="form-label">Preço Base (R$) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" step="0.01" min="0" value="{{ old('price', $product->price) }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="image" class="form-label">URL da Imagem</label>
                                <input type="url" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" placeholder="https://exemplo.com/imagem.jpg" 
                                       value="{{ old('image', $product->image) }}">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($product->image)
                                    <div class="mt-2">
                                        <small class="text-muted">Imagem atual:</small><br>
                                        <img src="{{ $product->image }}" 
                                             alt="{{ $product->name }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 150px; max-height: 150px;">
                                    </div>
                                @endif
                                <small class="text-muted">Cole aqui o link da imagem do produto</small>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="hidden" name="active" value="0">
                                    <input class="form-check-input" type="checkbox" id="active" 
                                           name="active" value="1" {{ old('active', $product->active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">
                                        <strong>Produto ativo</strong>
                                        <br><small class="text-muted">Produtos inativos não aparecem na loja</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Variations Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="bi bi-palette"></i> Variações e Estoque
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input type="hidden" name="has_variations" value="0">
                                    <input class="form-check-input" type="checkbox" id="has_variations" 
                                           name="has_variations" value="1" 
                                           {{ old('has_variations', $product->has_variations) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_variations">
                                        <strong>Este produto possui variações</strong>
                                        <br><small class="text-muted">Ex: tamanhos, cores, modelos, etc.</small>
                                    </label>
                                </div>

                                <!-- Existing Stocks -->
                                @if($product->stocks->count() > 0)
                                    <div class="mb-3">
                                        <h6>Estoque Atual:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Variação</th>
                                                        <th>Quantidade</th>
                                                        <th>Ajuste de Preço</th>
                                                        <th>Preço Final</th>
                                                        <th width="120">Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($product->stocks as $stock)
                                                        <tr data-stock-id="{{ $stock->id }}">
                                                            <td>{{ $stock->full_variation_name }}</td>
                                                            <td>
                                                                <span class="stock-quantity">{{ $stock->quantity }}</span>
                                                                <input type="number" class="form-control form-control-sm d-none stock-quantity-input" 
                                                                       value="{{ $stock->quantity }}" min="0">
                                                            </td>
                                                            <td>
                                                                <span class="stock-price-adjustment">
                                                                    @if($stock->price_adjustment > 0)
                                                                        +R$ {{ number_format($stock->price_adjustment, 2, ',', '.') }}
                                                                    @elseif($stock->price_adjustment < 0)
                                                                        -R$ {{ number_format(abs($stock->price_adjustment), 2, ',', '.') }}
                                                                    @else
                                                                        --
                                                                    @endif
                                                                </span>
                                                                <input type="number" class="form-control form-control-sm d-none stock-price-adjustment-input" 
                                                                       value="{{ $stock->price_adjustment }}" step="0.01">
                                                            </td>
                                                            <td class="price">R$ {{ number_format($stock->final_price, 2, ',', '.') }}</td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <button type="button" class="btn btn-outline-primary edit-stock-btn" 
                                                                            data-stock-id="{{ $stock->id }}" title="Editar">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-outline-success save-stock-btn d-none" 
                                                                            data-stock-id="{{ $stock->id }}" title="Salvar">
                                                                        <i class="bi bi-check"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-outline-secondary cancel-stock-btn d-none" 
                                                                            data-stock-id="{{ $stock->id }}" title="Cancelar">
                                                                        <i class="bi bi-x"></i>
                                                                    </button>
                                                                    @if(!$stock->orderItems()->exists())
                                                                        <button type="button" class="btn btn-outline-danger remove-stock-btn" 
                                                                                data-stock-id="{{ $stock->id }}" title="Remover">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                <!-- Default Stock (for products without variations) -->
                                <div id="default-stock" class="{{ $product->has_variations ? 'd-none' : '' }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="default_quantity" class="form-label">Quantidade em Estoque <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="default_quantity" 
                                                   name="default_quantity" min="0" 
                                                   value="{{ $product->has_variations ? '' : $product->stocks->first()->quantity ?? 0 }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Variation Stock Section -->
                                <div id="variations-section" class="{{ !$product->has_variations ? 'd-none' : '' }}">
                                    <div class="mb-3">
                                        <label class="form-label">Adicionar Nova Variação:</label>
                                        <div class="row" id="variation-inputs">
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" placeholder="Nome da variação (ex: Tamanho)" id="variation_name">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" placeholder="Valor (ex: M)" id="variation_value">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control" placeholder="Quantidade" id="variation_quantity" min="0">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control" placeholder="Ajuste preço" id="variation_price_adjustment" step="0.01">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-primary" id="add-variation">
                                                    <i class="bi bi-plus"></i> Adicionar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('home') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Atualizar Produto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle variations
    document.getElementById('has_variations').addEventListener('change', function() {
        const defaultStock = document.getElementById('default-stock');
        const variationsSection = document.getElementById('variations-section');
        
        if (this.checked) {
            defaultStock.classList.add('d-none');
            variationsSection.classList.remove('d-none');
        } else {
            defaultStock.classList.remove('d-none');
            variationsSection.classList.add('d-none');
        }
    });

    // Add variation functionality
    document.getElementById('add-variation').addEventListener('click', function() {
        const name = document.getElementById('variation_name').value;
        const value = document.getElementById('variation_value').value;
        const quantity = document.getElementById('variation_quantity').value;
        const priceAdjustment = document.getElementById('variation_price_adjustment').value || 0;

        if (!name || !value || !quantity) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção!',
                text: 'Por favor, preencha todos os campos obrigatórios da variação.',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Send AJAX request to add variation
        fetch('{{ route("products.add-variation", $product) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                variation_name: name,
                variation_value: value,
                quantity: parseInt(quantity),
                price_adjustment: parseFloat(priceAdjustment)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: 'Variação adicionada com sucesso!',
                    confirmButtonText: 'OK'
                });
                location.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: data.message || 'Erro ao adicionar variação',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Erro ao adicionar variação',
                confirmButtonText: 'OK'
            });
        });
    });

    // Stock editing functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-stock-btn')) {
            const btn = e.target.closest('.edit-stock-btn');
            const stockId = btn.dataset.stockId;
            const row = btn.closest('tr');
            
            // Show edit inputs
            row.querySelector('.stock-quantity').classList.add('d-none');
            row.querySelector('.stock-quantity-input').classList.remove('d-none');
            row.querySelector('.stock-price-adjustment').classList.add('d-none');
            row.querySelector('.stock-price-adjustment-input').classList.remove('d-none');
            
            // Show save/cancel buttons
            btn.classList.add('d-none');
            row.querySelector('.save-stock-btn').classList.remove('d-none');
            row.querySelector('.cancel-stock-btn').classList.remove('d-none');
        }
        
        if (e.target.closest('.save-stock-btn')) {
            const btn = e.target.closest('.save-stock-btn');
            const stockId = btn.dataset.stockId;
            const row = btn.closest('tr');
            
            const quantity = row.querySelector('.stock-quantity-input').value;
            const priceAdjustment = row.querySelector('.stock-price-adjustment-input').value;
            
            // Send AJAX request to update stock
            fetch(`/admin/stocks/${stockId}/update`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    quantity: parseInt(quantity),
                    price_adjustment: parseFloat(priceAdjustment)
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update display values
                    row.querySelector('.stock-quantity').textContent = quantity;
                    row.querySelector('.stock-price-adjustment').innerHTML = formatPriceAdjustment(priceAdjustment);
                    row.querySelector('.price').textContent = `R$ ${data.final_price}`;
                    
                    // Hide edit mode
                    exitEditMode(row);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Estoque atualizado com sucesso!',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: data.message || 'Erro ao atualizar estoque',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao atualizar estoque',
                    confirmButtonText: 'OK'
                });
            });
        }
        
        if (e.target.closest('.cancel-stock-btn')) {
            const btn = e.target.closest('.cancel-stock-btn');
            const row = btn.closest('tr');
            exitEditMode(row);
        }
        
        if (e.target.closest('.remove-stock-btn')) {
            const btn = e.target.closest('.remove-stock-btn');
            const stockId = btn.dataset.stockId;
            
            Swal.fire({
                icon: 'warning',
                title: 'Confirmar Remoção',
                text: 'Tem certeza que deseja remover esta variação?',
                showCancelButton: true,
                confirmButtonText: 'Sim, remover',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/stocks/${stockId}/remove`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            btn.closest('tr').remove();
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: 'Variação removida com sucesso!',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: data.message || 'Erro ao remover variação',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Erro ao remover variação',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        }
    });
    
    function exitEditMode(row) {
        // Hide edit inputs
        row.querySelector('.stock-quantity').classList.remove('d-none');
        row.querySelector('.stock-quantity-input').classList.add('d-none');
        row.querySelector('.stock-price-adjustment').classList.remove('d-none');
        row.querySelector('.stock-price-adjustment-input').classList.add('d-none');
        
        // Show edit button
        row.querySelector('.edit-stock-btn').classList.remove('d-none');
        row.querySelector('.save-stock-btn').classList.add('d-none');
        row.querySelector('.cancel-stock-btn').classList.add('d-none');
    }
    
    function formatPriceAdjustment(priceAdjustment) {
        const value = parseFloat(priceAdjustment);
        if (value > 0) {
            return `+R$ ${value.toFixed(2).replace('.', ',')}`;
        } else if (value < 0) {
            return `-R$ ${Math.abs(value).toFixed(2).replace('.', ',')}`;
        } else {
            return '--';
        }
    }

    // Preview image
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Remove existing preview
                const existingPreview = document.getElementById('image-preview');
                if (existingPreview) {
                    existingPreview.remove();
                }
                
                // Create new preview
                const preview = document.createElement('div');
                preview.id = 'image-preview';
                preview.className = 'mt-2';
                preview.innerHTML = `
                    <small class="text-muted">Nova imagem:</small><br>
                    <img src="${e.target.result}" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                `;
                
                e.target.parentElement.appendChild(preview);
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush 