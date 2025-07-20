@extends('layout.app')

@section('title', 'Novo Produto - Mini ERP')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-plus-circle"></i> Novo Produto
                    </h4>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST" id="product-form">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="price" class="form-label">Preço Base (R$) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" step="0.01" min="0" value="{{ old('price') }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label">URL da Imagem</label>
                            <input type="url" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" placeholder="https://exemplo.com/imagem.jpg" 
                                   value="{{ old('image') }}">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Cole aqui o link da imagem do produto</small>
                        </div>

                        <!-- Variations Toggle -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="has_variations" 
                                       name="has_variations" value="1" {{ old('has_variations') ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_variations">
                                    <strong>Este produto possui variações</strong>
                                    <br><small class="text-muted">Ex: Tamanhos, cores, modelos, etc.</small>
                                </label>
                            </div>
                        </div>

                        <!-- Default Stock (for products without variations) -->
                        <div id="default-stock" class="mb-4" style="display: {{ old('has_variations') ? 'none' : 'block' }};">
                            <label for="default_quantity" class="form-label">Quantidade em Estoque <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('default_quantity') is-invalid @enderror" 
                                   id="default_quantity" name="default_quantity" min="0" value="{{ old('default_quantity', 0) }}">
                            @error('default_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Variations Section -->
                        <div id="variations-section" style="display: {{ old('has_variations') ? 'block' : 'none' }};">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Variações do Produto</h5>
                                <button type="button" class="btn btn-success btn-sm" id="add-variation">
                                    <i class="bi bi-plus"></i> Adicionar Variação
                                </button>
                            </div>

                            <div id="variations-container">
                                @if(old('variations'))
                                    @foreach(old('variations') as $index => $variation)
                                        <div class="variation-item border rounded p-3 mb-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Variação {{ $index + 1 }}</h6>
                                                <button type="button" class="btn btn-danger btn-sm remove-variation">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="form-label">Tipo</label>
                                                    <input type="text" class="form-control" 
                                                           name="variations[{{ $index }}][name]" 
                                                           placeholder="Ex: Tamanho" 
                                                           value="{{ $variation['name'] }}" required>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <label class="form-label">Valor</label>
                                                    <input type="text" class="form-control" 
                                                           name="variations[{{ $index }}][value]" 
                                                           placeholder="Ex: P, M, G" 
                                                           value="{{ $variation['value'] }}" required>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <label class="form-label">Estoque</label>
                                                    <input type="number" class="form-control" 
                                                           name="variations[{{ $index }}][quantity]" 
                                                           min="0" value="{{ $variation['quantity'] }}" required>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <label class="form-label">Ajuste Preço (R$)</label>
                                                    <input type="number" class="form-control" 
                                                           name="variations[{{ $index }}][price_adjustment]" 
                                                           step="0.01" value="{{ $variation['price_adjustment'] ?? 0 }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('home') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Criar Produto
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
    let variationIndex = {{ count(old('variations', [])) }};

    // Toggle variations section
    document.getElementById('has_variations').addEventListener('change', function() {
        const defaultStock = document.getElementById('default-stock');
        const variationsSection = document.getElementById('variations-section');
        
        if (this.checked) {
            defaultStock.style.display = 'none';
            variationsSection.style.display = 'block';
            document.getElementById('default_quantity').required = false;
        } else {
            defaultStock.style.display = 'block';
            variationsSection.style.display = 'none';
            document.getElementById('default_quantity').required = true;
        }
    });

    // Add variation
    document.getElementById('add-variation').addEventListener('click', function() {
        const container = document.getElementById('variations-container');
        const variationHtml = `
            <div class="variation-item border rounded p-3 mb-3 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Variação ${variationIndex + 1}</h6>
                    <button type="button" class="btn btn-danger btn-sm remove-variation">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Tipo</label>
                        <input type="text" class="form-control" 
                               name="variations[${variationIndex}][name]" 
                               placeholder="Ex: Tamanho" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Valor</label>
                        <input type="text" class="form-control" 
                               name="variations[${variationIndex}][value]" 
                               placeholder="Ex: P, M, G" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Estoque</label>
                        <input type="number" class="form-control" 
                               name="variations[${variationIndex}][quantity]" 
                               min="0" value="0" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Ajuste Preço (R$)</label>
                        <input type="number" class="form-control" 
                               name="variations[${variationIndex}][price_adjustment]" 
                               step="0.01" value="0">
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', variationHtml);
        variationIndex++;
    });

    // Remove variation
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-variation')) {
            e.target.closest('.variation-item').remove();
        }
    });

    // Form validation
    document.getElementById('product-form').addEventListener('submit', function(e) {
        const hasVariations = document.getElementById('has_variations').checked;
        const variationsContainer = document.getElementById('variations-container');
        
        if (hasVariations && variationsContainer.children.length === 0) {
            e.preventDefault();
            alert('Adicione pelo menos uma variação ou desmarque a opção "Este produto possui variações".');
            return false;
        }
        
        // Ensure has_variations is always sent
        if (!hasVariations) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'has_variations';
            hiddenInput.value = '0';
            this.appendChild(hiddenInput);
        }
    });
</script>
@endpush 