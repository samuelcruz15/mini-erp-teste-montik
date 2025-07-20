@extends('layout.app')

@section('title', 'Gerenciar Cupons - Mini ERP')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-tags"></i> Gerenciar Cupons</h2>
        <a href="{{ route('coupons.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Novo Cupom
        </a>
    </div>

    @if($cupons->count() > 0)
        <div class="card border-0 shadow">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Código</th>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Desconto</th>
                                <th>Valor Mín.</th>
                                <th>Validade</th>
                                <th>Uso</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cupons as $cupon)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark border" style="font-family: monospace;">
                                            {{ $cupon->code }}
                                        </span>
                                    </td>
                                    <td>{{ $cupon->name }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($cupon->type === 'percentage') bg-info @else bg-warning @endif">
                                            {{ $cupon->type === 'percentage' ? 'Porcentagem' : 'Valor Fixo' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($cupon->type === 'percentage')
                                            {{ number_format($cupon->discount, 2) }}%
                                        @else
                                            R$ {{ number_format($cupon->discount, 2, ',', '.') }}
                                        @endif
                                    </td>
                                    <td>R$ {{ number_format($cupon->minimum_amount, 2, ',', '.') }}</td>
                                    <td>
                                        <small>
                                            {{ $cupon->valid_from->format('d/m/Y') }} até {{ $cupon->valid_until->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <small>
                                            {{ $cupon->used_count }} / {{ $cupon->usage_limit ?? '∞' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($cupon->active) bg-success @else bg-secondary @endif">
                                            {{ $cupon->active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('coupons.edit', $cupon) }}"
                                               class="btn btn-outline-warning" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('coupons.destroy', $cupon) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" 
                                                        title="Excluir"
                                                        onclick="return confirm('Tem certeza que deseja excluir este cupom?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $cupons->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <i class="bi bi-tags text-muted" style="font-size: 4rem;"></i>
            <h3 class="mt-3 text-muted">Nenhum cupom encontrado</h3>
            <p class="text-muted">Comece criando cupons de desconto para sua loja</p>
            <a href="{{ route('coupons.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Criar Primeiro Cupom
            </a>
        </div>
    @endif
</div>
@endsection 