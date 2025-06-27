@extends('layouts.admin')

@section('title', 'Administrar Ventas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-shopping-cart me-2"></i>Gestión de Ventas</h1>
        <a href="{{ route('ventas.estadisticas') }}" class="btn btn-info">
            <i class="fas fa-chart-line me-2"></i>Ver Estadísticas
        </a>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('ventas.index') }}" id="filtrosForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="cliente_id" class="form-label">Cliente</label>
                        <select name="cliente_id" id="cliente_id" class="form-select">
                            <option value="">Todos los clientes</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre }} {{ $cliente->apellido }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="estado" class="form-label">Estado</label>
                        <select name="estado" id="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="fecha_desde" class="form-label">Desde</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" 
                               value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="fecha_hasta" class="form-label">Hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" 
                               value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="buscar" class="form-label">Buscar</label>
                        <input type="text" name="buscar" id="buscar" class="form-control" 
                               placeholder="Cliente o producto..." value="{{ request('buscar') }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de ventas -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Lista de Ventas</h5>
        </div>
        <div class="card-body">
            @if($ventas->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No hay ventas registradas con los filtros aplicados.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Items</th>
                                <th scope="col">Total</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Método Pago</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ventas as $venta)
                                <tr>
                                    <td>{{ $venta->id }}</td>
                                    <td>{{ $venta->fecha_formateada }}</td>
                                    <td>
                                        <strong>{{ $venta->cliente_nombre_completo }}</strong><br>
                                        <small class="text-muted">{{ $venta->usuario->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $venta->cantidad_total_items }} items</span>
                                    </td>
                                    <td>
                                        <strong>${{ number_format($venta->total, 2, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        @if($venta->estado == 'completada')
                                            <span class="badge bg-success">Completada</span>
                                        @elseif($venta->estado == 'pendiente')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @else
                                            <span class="badge bg-danger">Cancelada</span>
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($venta->metodo_pago ?? 'N/A') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('ventas.show', $venta->id) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                    data-bs-toggle="dropdown" 
                                                    aria-expanded="false"
                                                    title="Cambiar estado">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if($venta->estado != 'completada')
                                                    <li>
                                                        <form action="{{ route('ventas.updateStatus', $venta->id) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="estado" value="completada">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fas fa-check text-success me-2"></i>Completar
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if($venta->estado != 'pendiente')
                                                    <li>
                                                        <form action="{{ route('ventas.updateStatus', $venta->id) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="estado" value="pendiente">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fas fa-clock text-warning me-2"></i>Pendiente
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if($venta->estado != 'cancelada')
                                                    <li>
                                                        <form action="{{ route('ventas.updateStatus', $venta->id) }}" 
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('¿Cancelar esta venta? Se devolverá el stock.')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="estado" value="cancelada">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fas fa-times text-danger me-2"></i>Cancelar
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $ventas->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
        <div class="card-footer text-muted">
            Mostrando {{ $ventas->firstItem() ?? 0 }} - {{ $ventas->lastItem() ?? 0 }} de {{ $ventas->total() }} registros
        </div>
    </div>
@endsection