@extends('layout.app')

@section('title', 'Finalizar Compra - Mini ERP')

@section('content')
<div class="container py-4">
    @guest
        <!-- Usuário não logado -->
        <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Login Necessário</h4>
            <p>Para finalizar sua compra, você precisa estar logado no sistema.</p>
            <hr>
            <div class="d-flex gap-2">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right"></i> Fazer Login
                </a>
                <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar ao Carrinho
                </a>
            </div>
        </div>
    @else
        <h2><i class="bi bi-credit-card"></i> Finalizar Compra</h2>

        <div class="row">
            <div class="col-lg-8">
                <form action="{{ route('orders.store') }}" method="POST" id="checkout-form">
                    @csrf
                    
                    <!-- Customer Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Dados do Cliente</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_name" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                           id="customer_name" name="customer_name" 
                                           value="{{ old('customer_name', $userProfile?->full_name ?? auth()->user()->name) }}" required>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="customer_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                           id="customer_email" name="customer_email" 
                                           value="{{ old('customer_email', auth()->user()->email) }}" required readonly>
                                    @error('customer_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Este campo é preenchido automaticamente com seu email de login</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="customer_phone" class="form-label">Telefone</label>
                                    <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" 
                                           id="customer_phone" name="customer_phone" 
                                           value="{{ old('customer_phone', $userProfile?->phone) }}">
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Selection -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Endereço de Entrega</h5>
                            <a href="{{ route('addresses.create') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-plus"></i> Novo Endereço
                            </a>
                        </div>
                        <div class="card-body">
                            @if($addresses->count() > 0)
                                <!-- Seleção de endereços salvos -->
                                <div class="mb-3">
                                    <label class="form-label">Escolher endereço salvo:</label>
                                    <div class="row">
                                        @foreach($addresses as $address)
                                            <div class="col-md-6 mb-2">
                                                <div class="card border-2 {{ $address->is_default ? 'border-primary' : 'border-light' }}">
                                                    <div class="card-body p-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" 
                                                                   name="selected_address" id="address_{{ $address->id }}" 
                                                                   value="{{ $address->id }}" 
                                                                   {{ $address->is_default ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="address_{{ $address->id }}">
                                                                <strong>{{ $address->name }}</strong>
                                                                @if($address->is_default)
                                                                    <span class="badge bg-primary ms-1">Padrão</span>
                                                                @endif
                                                                <br>
                                                                <small class="text-muted">
                                                                    {{ $address->street }}, {{ $address->number }}
                                                                    @if($address->complement)
                                                                        - {{ $address->complement }}
                                                                    @endif
                                                                    <br>
                                                                    {{ $address->neighborhood }} - {{ $address->city }}/{{ $address->state }}
                                                                    <br>
                                                                    CEP: {{ $address->cep }}
                                                                </small>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="text-center mb-3">
                                    <span class="text-muted">ou</span>
                                </div>
                            @endif

                            <!-- Formulário para novo endereço -->
                            <div class="card border-dashed">
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="address_option" 
                                               id="new_address" value="new" {{ $addresses->count() == 0 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="new_address">
                                            <strong>Usar novo endereço</strong>
                                        </label>
                                    </div>

                                    <div id="new-address-form" class="{{ $addresses->count() > 0 ? 'd-none' : '' }}">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="shipping_zipcode" class="form-label">CEP <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control @error('shipping_zipcode') is-invalid @enderror" 
                                                           id="cep" name="shipping_zipcode" value="{{ old('shipping_zipcode') }}" 
                                                           placeholder="00000-000" maxlength="9">
                                                    <button type="button" class="btn btn-outline-secondary" id="check-zipcode">
                                                        <i class="bi bi-search"></i>
                                                    </button>
                                                </div>
                                                @error('shipping_zipcode')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="col-md-8 mb-3">
                                                <label for="shipping_address" class="form-label">Endereço <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('shipping_address') is-invalid @enderror" 
                                                       id="address" name="shipping_address" value="{{ old('shipping_address') }}">
                                                @error('shipping_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="col-md-3 mb-3">
                                                <label for="number" class="form-label">Número <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('number') is-invalid @enderror" 
                                                       id="number" name="number" value="{{ old('number') }}">
                                                @error('number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="col-md-9 mb-3">
                                                <label for="complement" class="form-label">Complemento</label>
                                                <input type="text" class="form-control @error('complement') is-invalid @enderror" 
                                                       id="complement" name="complement" value="{{ old('complement') }}">
                                                @error('complement')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="col-md-4 mb-3">
                                                <label for="neighborhood" class="form-label">Bairro <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('neighborhood') is-invalid @enderror" 
                                                       id="neighborhood" name="neighborhood" value="{{ old('neighborhood') }}">
                                                @error('neighborhood')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="shipping_city" class="form-label">Cidade <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('shipping_city') is-invalid @enderror" 
                                                       id="city" name="shipping_city" value="{{ old('shipping_city') }}">
                                                @error('shipping_city')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="col-md-2 mb-3">
                                                <label for="shipping_state" class="form-label">UF <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('shipping_state') is-invalid @enderror" 
                                                       id="state" name="shipping_state" value="{{ old('shipping_state') }}" 
                                                       maxlength="2" style="text-transform: uppercase;">
                                                @error('shipping_state')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('cart.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar ao Carrinho
                        </a>
                        
                        <button type="submit" class="btn btn-success btn-lg" id="submit-order">
                            <i class="bi bi-check-circle"></i> Confirmar Pedido
                        </button>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Resumo do Pedido</h5>
                    </div>
                    
                    <div class="card-body">
                        <!-- Order Items -->
                        <div class="mb-3">
                            @foreach($cart as $item)
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $item['name'] }}</h6>
                                        @if($item['variation_info'] != 'Produto padrão')
                                            <small class="text-muted">{{ $item['variation_info'] }}</small>
                                        @endif
                                        <div class="text-muted small">
                                            {{ $item['quantity'] }}x R$ {{ number_format($item['price'], 2, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <strong>R$ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Totals -->
                        <div class="border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Subtotal:</span>
                                <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <div class="border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Frete:</span>
                                <span>R$ {{ number_format($shipping, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        @if($cuponDiscount > 0)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between text-success">
                                    <span>Desconto ({{ $appliedCupon['code'] }}):</span>
                                    <span>-R$ {{ number_format($cuponDiscount, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="pt-2">
                            <div class="d-flex justify-content-between">
                                <strong>Total:</strong>
                                <strong class="text-success">R$ {{ number_format($total, 2, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endguest
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
            // CEP mask
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 8) {
                value = value.substring(0, 8);
            }
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5);
            }
            e.target.value = value;
        });

        // Busca CEP automaticamente
        cepInput.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length === 8) {
                fetch(`{{ route('cart.check-zipcode') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ cep: cep })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('address').value = data.data.logradouro;
                        document.getElementById('neighborhood').value = data.data.bairro;
                        document.getElementById('city').value = data.data.localidade;
                        document.getElementById('state').value = data.data.uf;
                    } else {
                        console.error('Erro ao buscar CEP:', data.message);
                    }
                })
                .catch(error => console.error('Erro ao buscar CEP:', error));
            }
        });
    }

            // New address form display control
    const addressOptions = document.querySelectorAll('input[name="address_option"], input[name="selected_address"]');
    const newAddressForm = document.getElementById('new-address-form');
    const newAddressRadio = document.getElementById('new_address');

    function toggleNewAddressForm() {
        const selectedAddress = document.querySelector('input[name="selected_address"]:checked');
        
        if (newAddressRadio && newAddressRadio.checked) {
            newAddressForm.classList.remove('d-none');
            // Make fields required
            const fields = newAddressForm.querySelectorAll('input');
            fields.forEach(field => {
                field.required = true;
            });
        } else {
            newAddressForm.classList.add('d-none');
            // Remover obrigatoriedade dos campos
            const fields = newAddressForm.querySelectorAll('input');
            fields.forEach(field => {
                field.required = false;
            });
        }
    }

    addressOptions.forEach(option => {
        option.addEventListener('change', toggleNewAddressForm);
    });

    // Inicializar estado
    toggleNewAddressForm();

            // Form validation
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        const selectedAddress = document.querySelector('input[name="selected_address"]:checked');
        const newAddressSelected = document.getElementById('new_address')?.checked;

        if (!selectedAddress && !newAddressSelected) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Endereço Necessário',
                text: 'Por favor, selecione um endereço salvo ou preencha um novo endereço.'
            });
            return false;
        }

        if (newAddressSelected) {
            const requiredFields = ['shipping_zipcode', 'shipping_address', 'number', 'neighborhood', 'shipping_city', 'shipping_state'];
            const missingFields = requiredFields.filter(field => {
                const element = document.getElementById(field.replace('shipping_', ''));
                return !element || !element.value.trim();
            });

            if (missingFields.length > 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos Obrigatórios',
                    text: 'Por favor, preencha todos os campos obrigatórios do endereço.'
                });
                return false;
            }
        }

        // If a saved address was selected, ensure new address fields are not sent
        if (selectedAddress) {
            const newAddressFields = ['cep', 'address', 'number', 'complement', 'neighborhood', 'city', 'state'];
            newAddressFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.name = ''; // Remove name to not send
                }
            });
        }
    });
});
</script>
@endpush
@endsection 