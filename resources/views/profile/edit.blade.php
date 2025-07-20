@extends('layout.app')

@section('title', 'Editar Informações Pessoais')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-user-edit"></i> Editar Informações Pessoais
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                       id="full_name" name="full_name" 
                                       value="{{ old('full_name', $profile?->full_name ?? $user->name) }}" required>
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telefone *</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" 
                                       value="{{ old('phone', $profile?->phone) }}" 
                                       placeholder="(11) 99999-9999" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" class="form-control @error('cpf') is-invalid @enderror" 
                                       id="cpf" name="cpf" 
                                       value="{{ old('cpf', $profile?->cpf) }}" 
                                       placeholder="000.000.000-00">
                                @error('cpf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">Data de Nascimento</label>
                                <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                       id="birth_date" name="birth_date" 
                                       value="{{ old('birth_date', $profile?->birth_date?->format('Y-m-d')) }}">
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gênero</label>
                                <select class="form-select @error('gender') is-invalid @enderror" 
                                        id="gender" name="gender">
                                    <option value="">Selecione...</option>
                                    <option value="M" {{ old('gender', $profile?->gender) == 'M' ? 'selected' : '' }}>
                                        Masculino
                                    </option>
                                    <option value="F" {{ old('gender', $profile?->gender) == 'F' ? 'selected' : '' }}>
                                        Feminino
                                    </option>
                                    <option value="O" {{ old('gender', $profile?->gender) == 'O' ? 'selected' : '' }}>
                                        Outro
                                    </option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" 
                                       value="{{ $user->email }}" disabled>
                                <small class="text-muted">Para alterar o email, entre em contato com o suporte.</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Alterações
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
            // Phone mask
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        if (value.length > 0) {
            if (value.length <= 2) {
                value = '(' + value;
            } else if (value.length <= 7) {
                value = '(' + value.substring(0, 2) + ') ' + value.substring(2);
            } else {
                value = '(' + value.substring(0, 2) + ') ' + value.substring(2, 7) + '-' + value.substring(7);
            }
        }
        e.target.value = value;
    });

            // CPF mask
    const cpfInput = document.getElementById('cpf');
    cpfInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        if (value.length > 0) {
            if (value.length <= 3) {
                value = value;
            } else if (value.length <= 6) {
                value = value.substring(0, 3) + '.' + value.substring(3);
            } else if (value.length <= 9) {
                value = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + value.substring(6);
            } else {
                value = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + value.substring(6, 9) + '-' + value.substring(9);
            }
        }
        e.target.value = value;
    });
});
</script>
@endpush
@endsection 