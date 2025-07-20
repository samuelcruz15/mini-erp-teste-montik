@extends('layout.app')

@section('title', 'Meus Endereços')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Meus Endereços</h2>
                <a href="{{ route('addresses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Endereço
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($addresses->count() > 0)
                <div class="row">
                    @foreach($addresses as $address)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ $address->name }}</h6>
                                    @if($address->is_default)
                                        <span class="badge bg-success">Padrão</span>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>CEP:</strong> {{ $address->cep }}<br>
                                        <strong>Endereço:</strong> {{ $address->street }}, {{ $address->number }}<br>
                                        @if($address->complement)
                                            <strong>Complemento:</strong> {{ $address->complement }}<br>
                                        @endif
                                        <strong>Bairro:</strong> {{ $address->neighborhood }}<br>
                                        <strong>Cidade:</strong> {{ $address->city }}/{{ $address->state }}
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <div class="btn-group w-100" role="group">
                                        @if(!$address->is_default)
                                            <form action="{{ route('addresses.set-default', $address) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-star"></i> Padrão
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('addresses.edit', $address) }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                    onclick="return confirm('Tem certeza que deseja remover este endereço?')">
                                                <i class="fas fa-trash"></i> Remover
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Nenhum endereço cadastrado</h4>
                    <p class="text-muted">Adicione seu primeiro endereço para facilitar suas compras.</p>
                    <a href="{{ route('addresses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Adicionar Endereço
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 