@extends('layouts.admin')

@section('title', 'Administrar Descuentos')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-tags me-2"></i>Gestión de Descuentos</h1>
        <a href="{{ route('descuentos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Descuento
        </a>
    </div>

    {{-- Filtros por estado --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="btn-group btn-group-sm" role="group" aria-label="Filtro estado">
                @php
                    $estados = [
                        null       => 'Todos',
                        'vigente'  => 'Vigentes',
                        'futuro'   => 'Futuros',
                        'expirado' => 'Expirados',
                        'inactivo' => 'Inactivos',
                    ];
                @endphp
                @foreach($estados as $key => $label)
                    <a href="{{ route('descuentos.index', $key ? ['estado' => $key] : []) }}"
                       class="btn btn-outline-secondary {{ ($estado ?? null) === $key ? 'active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Lista de Descuentos</h5>
        </div>
        <div class="card-body">
            @if($descuentos->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>No hay descuentos para mostrar.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>%</th>
                                <th>Vigencia</th>
                                <th>Estado</th>
                                <th>Variantes</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($descuentos as $d)
                                <tr>
                                    <td>{{ $d->id }}</td>
                                    <td>{{ $d->nombre }}</td>
                                    <td><span class="badge bg-danger">-{{ rtrim(rtrim(number_format($d->porcentaje, 2, ',', '.'), '0'), ',') }}%</span></td>
                                    <td>
                                        <small>
                                            {{ $d->fecha_inicio->format('d/m/Y') }} —
                                            {{ $d->fecha_fin->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        @switch($d->estado)
                                            @case('vigente')
                                                <span class="badge bg-success">Vigente</span>
                                                @break
                                            @case('futuro')
                                                <span class="badge bg-info">Futuro</span>
                                                @break
                                            @case('expirado')
                                                <span class="badge bg-secondary">Expirado</span>
                                                @break
                                            @case('inactivo')
                                                <span class="badge bg-dark">Inactivo</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $d->variantes->count() }} variante(s)</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('descuentos.show', $d->id) }}" class="btn btn-outline-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('descuentos.edit', $d->id) }}" class="btn btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteDescuento{{ $d->id }}"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        {{-- Modal confirmación borrado --}}
                                        <div class="modal fade" id="deleteDescuento{{ $d->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>Confirmar eliminación
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Eliminar el descuento <strong>{{ $d->nombre }}</strong>?
                                                        Se desvinculará de las variantes asignadas.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <form action="{{ route('descuentos.destroy', $d->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-trash me-1"></i>Eliminar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $descuentos->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
