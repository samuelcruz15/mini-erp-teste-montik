@extends('layout.app')

@section('title', 'Editar Cupom - Mini ERP')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil"></i> Editar Cupom de Desconto
                    </h4>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('coupons.update', $cupon->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="code" class="form-label">Código do Cupom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code', $cupon->code) }}" 
                                       placeholder="Ex: DESCONTO10" style="text-transform: uppercase;" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Use apenas letras e números, sem espaços</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nome do Cupom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $cupon->name) }}" 
                                       placeholder="Ex: Desconto de 10%" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="type" class="form-label">Tipo de Desconto <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="percentage" {{ old('type', $cupon->type) === 'percentage' ? 'selected' : '' }}>
                                        Porcentagem (%)
                                    </option>
                                    <option value="fixed" {{ old('type', $cupon->type) === 'fixed' ? 'selected' : '' }}>
                                        Valor Fixo (R$)
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="discount" class="form-label">Valor do Desconto <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('discount') is-invalid @enderror" 
                                       id="discount" name="discount" step="0.01" min="0" 
                                       value="{{ old('discount', $cupon->discount) }}" required>
                                @error('discount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="discount-help">Digite o valor</small>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="minimum_amount" class="form-label">Valor Mínimo (R$) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('minimum_amount') is-invalid @enderror" 
                                       id="minimum_amount" name="minimum_amount" step="0.01" min="0" 
                                       value="{{ old('minimum_amount', $cupon->minimum_amount) }}" required>
                                @error('minimum_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Valor mínimo do carrinho para usar o cupom</small>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="valid_from" class="form-label">Válido a partir de <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('valid_from') is-invalid @enderror" 
                                       id="valid_from" name="valid_from" 
                                       value="{{ old('valid_from', $cupon->valid_from->format('Y-m-d')) }}" required>
                                @error('valid_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="valid_until" class="form-label">Válido até <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                       id="valid_until" name="valid_until" 
                                       value="{{ old('valid_until', $cupon->valid_until->format('Y-m-d')) }}" required>
                                @error('valid_until')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="usage_limit" class="form-label">Limite de Uso</label>
                                <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                                       id="usage_limit" name="usage_limit" min="1" 
                                       value="{{ old('usage_limit', $cupon->usage_limit) }}" 
                                       placeholder="Deixe vazio para ilimitado">
                                @error('usage_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Quantas vezes pode ser usado</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="active" 
                                       name="active" value="1" {{ old('active', $cupon->active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">
                                    <strong>Cupom ativo</strong>
                                    <br><small class="text-muted">Cupons inativos não podem ser utilizados</small>
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('coupons.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Atualizar Cupom
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
    // Update discount help text based on type
    document.getElementById('type').addEventListener('change', function() {
        const discountHelp = document.getElementById('discount-help');
        const discountInput = document.getElementById('discount');
        
        if (this.value === 'percentage') {
            discountHelp.textContent = 'Digite a porcentagem (ex: 10 para 10%)';
            discountInput.placeholder = 'Ex: 10';
            discountInput.max = '100';
        } else if (this.value === 'fixed') {
            discountHelp.textContent = 'Digite o valor em reais (ex: 15.00)';
            discountInput.placeholder = 'Ex: 15.00';
            discountInput.removeAttribute('max');
        } else {
            discountHelp.textContent = 'Digite o valor';
            discountInput.placeholder = '';
            discountInput.removeAttribute('max');
        }
    });

    // Auto-uppercase code
    document.getElementById('code').addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });

    // Set minimum valid_until date
    document.getElementById('valid_from').addEventListener('change', function() {
        document.getElementById('valid_until').min = this.value;
    });

    // Initialize discount help text on page load
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const discountHelp = document.getElementById('discount-help');
        const discountInput = document.getElementById('discount');
        
        if (typeSelect.value === 'percentage') {
            discountHelp.textContent = 'Digite a porcentagem (ex: 10 para 10%)';
            discountInput.placeholder = 'Ex: 10';
            discountInput.max = '100';
        } else if (typeSelect.value === 'fixed') {
            discountHelp.textContent = 'Digite o valor em reais (ex: 15.00)';
            discountInput.placeholder = 'Ex: 15.00';
            discountInput.removeAttribute('max');
        }
    });
</script>
@endpush 