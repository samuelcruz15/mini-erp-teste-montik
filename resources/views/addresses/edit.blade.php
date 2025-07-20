@extends('layout.app')

@section('title', 'Editar Endereço')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Editar Endereço</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('addresses.update', $address) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nome do Endereço *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $address->name) }}" 
                                       placeholder="Ex: Casa, Trabalho" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="cep" class="form-label">CEP *</label>
                                <input type="text" class="form-control @error('cep') is-invalid @enderror" 
                                       id="cep" name="cep" value="{{ old('cep', $address->cep) }}" 
                                       placeholder="00000-000" required>
                                @error('cep')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="street" class="form-label">Rua/Avenida *</label>
                                <input type="text" class="form-control @error('street') is-invalid @enderror" 
                                       id="street" name="street" value="{{ old('street', $address->street) }}" required>
                                @error('street')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="number" class="form-label">Número *</label>
                                <input type="text" class="form-control @error('number') is-invalid @enderror" 
                                       id="number" name="number" value="{{ old('number', $address->number) }}" required>
                                @error('number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="complement" class="form-label">Complemento</label>
                                <input type="text" class="form-control @error('complement') is-invalid @enderror" 
                                       id="complement" name="complement" value="{{ old('complement', $address->complement) }}" 
                                       placeholder="Apto, Sala, etc.">
                                @error('complement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="neighborhood" class="form-label">Bairro *</label>
                                <input type="text" class="form-control @error('neighborhood') is-invalid @enderror" 
                                       id="neighborhood" name="neighborhood" value="{{ old('neighborhood', $address->neighborhood) }}" required>
                                @error('neighborhood')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="city" class="form-label">Cidade *</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" value="{{ old('city', $address->city) }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="state" class="form-label">Estado *</label>
                                <select class="form-select @error('state') is-invalid @enderror" 
                                        id="state" name="state" required>
                                    <option value="">Selecione...</option>
                                    <option value="AC" {{ old('state', $address->state) == 'AC' ? 'selected' : '' }}>Acre</option>
                                    <option value="AL" {{ old('state', $address->state) == 'AL' ? 'selected' : '' }}>Alagoas</option>
                                    <option value="AP" {{ old('state', $address->state) == 'AP' ? 'selected' : '' }}>Amapá</option>
                                    <option value="AM" {{ old('state', $address->state) == 'AM' ? 'selected' : '' }}>Amazonas</option>
                                    <option value="BA" {{ old('state', $address->state) == 'BA' ? 'selected' : '' }}>Bahia</option>
                                    <option value="CE" {{ old('state', $address->state) == 'CE' ? 'selected' : '' }}>Ceará</option>
                                    <option value="DF" {{ old('state', $address->state) == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                                    <option value="ES" {{ old('state', $address->state) == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                                    <option value="GO" {{ old('state', $address->state) == 'GO' ? 'selected' : '' }}>Goiás</option>
                                    <option value="MA" {{ old('state', $address->state) == 'MA' ? 'selected' : '' }}>Maranhão</option>
                                    <option value="MT" {{ old('state', $address->state) == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                                    <option value="MS" {{ old('state', $address->state) == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                                    <option value="MG" {{ old('state', $address->state) == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                                    <option value="PA" {{ old('state', $address->state) == 'PA' ? 'selected' : '' }}>Pará</option>
                                    <option value="PB" {{ old('state', $address->state) == 'PB' ? 'selected' : '' }}>Paraíba</option>
                                    <option value="PR" {{ old('state', $address->state) == 'PR' ? 'selected' : '' }}>Paraná</option>
                                    <option value="PE" {{ old('state', $address->state) == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                                    <option value="PI" {{ old('state', $address->state) == 'PI' ? 'selected' : '' }}>Piauí</option>
                                    <option value="RJ" {{ old('state', $address->state) == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                                    <option value="RN" {{ old('state', $address->state) == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                                    <option value="RS" {{ old('state', $address->state) == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                                    <option value="RO" {{ old('state', $address->state) == 'RO' ? 'selected' : '' }}>Rondônia</option>
                                    <option value="RR" {{ old('state', $address->state) == 'RR' ? 'selected' : '' }}>Roraima</option>
                                    <option value="SC" {{ old('state', $address->state) == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                                    <option value="SP" {{ old('state', $address->state) == 'SP' ? 'selected' : '' }}>São Paulo</option>
                                    <option value="SE" {{ old('state', $address->state) == 'SE' ? 'selected' : '' }}>Sergipe</option>
                                    <option value="TO" {{ old('state', $address->state) == 'TO' ? 'selected' : '' }}>Tocantins</option>
                                </select>
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" 
                                       {{ old('is_default', $address->is_default) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default">
                                    Definir como endereço padrão
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('addresses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Atualizar Endereço
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
            // CEP mask
    const cepInput = document.getElementById('cep');
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
                    document.getElementById('street').value = data.data.logradouro;
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
});
</script>
@endpush
@endsection 