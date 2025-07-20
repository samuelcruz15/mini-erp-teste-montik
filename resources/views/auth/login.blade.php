@extends('layout.app')

@section('title', 'Login - Mini ERP')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow">
                <div class="card-header bg-gradient-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="bi bi-shield-lock"></i> Acesso ao Sistema
                    </h4>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Lembrar de mim
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Entrar
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer bg-light">
                    <div class="text-center">
                        <small class="text-muted">
                            <strong>Contas de Teste:</strong><br>
                            <span class="badge bg-danger me-2">Admin:</span> admin@minierp.com / admin123<br>
                            <span class="badge bg-info">Cliente:</span> cliente@exemplo.com / cliente123
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar para Loja
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 