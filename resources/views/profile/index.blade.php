@extends('layout.app')

@section('title', 'Minha Conta')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Minha Conta</h2>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Informações Pessoais -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-user"></i> Informações Pessoais
                            </h5>
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                        <div class="card-body">
                            @if($profile)
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <strong>Nome:</strong> {{ $profile->full_name }}
                                    </div>
                                    <div class="col-12 mb-2">
                                        <strong>Email:</strong> {{ $user->email }}
                                    </div>
                                    <div class="col-12 mb-2">
                                        <strong>Telefone:</strong> {{ $profile->formatted_phone }}
                                    </div>
                                    @if($profile->cpf)
                                        <div class="col-12 mb-2">
                                            <strong>CPF:</strong> {{ $profile->formatted_cpf }}
                                        </div>
                                    @endif
                                    @if($profile->birth_date)
                                        <div class="col-12 mb-2">
                                            <strong>Data de Nascimento:</strong> {{ $profile->birth_date->format('d/m/Y') }}
                                            @if($profile->age)
                                                ({{ $profile->age }} anos)
                                            @endif
                                        </div>
                                    @endif
                                    @if($profile->gender)
                                        <div class="col-12 mb-2">
                                            <strong>Gênero:</strong> 
                                            @switch($profile->gender)
                                                @case('M')
                                                    Masculino
                                                    @break
                                                @case('F')
                                                    Feminino
                                                    @break
                                                @case('O')
                                                    Outro
                                                    @break
                                            @endswitch
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-user-plus fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">Nenhuma informação pessoal cadastrada</p>
                                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Cadastrar Informações
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Endereços -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-map-marker-alt"></i> Meus Endereços
                            </h5>
                            <a href="{{ route('addresses.create') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus"></i> Novo Endereço
                            </a>
                        </div>
                        <div class="card-body">
                            @if($addresses->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($addresses->take(3) as $address)
                                        <div class="list-group-item px-0">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        {{ $address->name }}
                                                        @if($address->is_default)
                                                            <span class="badge bg-success ms-2">Padrão</span>
                                                        @endif
                                                    </h6>
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
                                                </div>
                                                <div class="ms-2">
                                                    <a href="{{ route('addresses.edit', $address) }}" 
                                                       class="btn btn-outline-secondary btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($addresses->count() > 3)
                                        <div class="text-center mt-2">
                                            <a href="{{ route('addresses.index') }}" class="btn btn-outline-primary btn-sm">
                                                Ver todos os endereços ({{ $addresses->count() }})
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-map-marker-alt fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">Nenhum endereço cadastrado</p>
                                    <a href="{{ route('addresses.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Adicionar Endereço
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Links Rápidos -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-link"></i> Links Rápidos
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <a href="{{ route('my-orders') }}" class="btn btn-outline-info w-100">
                                        <i class="fas fa-shopping-bag"></i> Meus Pedidos
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="{{ route('cart.index') }}" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-shopping-cart"></i> Carrinho
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="{{ route('addresses.index') }}" class="btn btn-outline-success w-100">
                                        <i class="fas fa-map-marker-alt"></i> Gerenciar Endereços
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-user-edit"></i> Editar Perfil
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 