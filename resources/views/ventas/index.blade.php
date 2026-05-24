@extends('layouts.admin')

@section('title', 'Administrar Ventas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-shopping-cart me-2"></i>Gestión de Ventas</h1>
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
            <!-- Tabla Desktop -->
            <div class="d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Método Pago</th>
                                <th>Acciones</th>
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
                                        <span class="badge bg-secondary">
                                            {{ $venta->cantidad_total_items }} items
                                        </span>
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
                                        <a href="{{ route('ventas.show', $venta->id) }}"
                                        class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Cards Mobile -->
            <div class="d-block d-md-none">

                @foreach ($ventas as $venta)

                    <div class="card mb-3 shadow-sm border-0">

                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-start mb-2">

                                <div>
                                    <h5 class="mb-1">
                                        Venta #{{ $venta->id }}
                                    </h5>

                                    <small class="text-muted">
                                        {{ $venta->fecha_formateada }}
                                    </small>
                                </div>

                                <div>
                                    @if($venta->estado == 'completada')
                                        <span class="badge bg-success">Completada</span>
                                    @elseif($venta->estado == 'pendiente')
                                        <span class="badge bg-warning">Pendiente</span>
                                    @else
                                        <span class="badge bg-danger">Cancelada</span>
                                    @endif
                                </div>

                            </div>

                            <div class="mb-2">
                                <strong>Cliente:</strong><br>
                                {{ $venta->cliente_nombre_completo }}
                            </div>

                            <div class="mb-2">
                                <strong>Email:</strong><br>
                                <small>{{ $venta->usuario->email }}</small>
                            </div>

                            <div class="row text-center mb-3">

                                <div class="col-4">
                                    <small class="text-muted d-block">
                                        Items
                                    </small>

                                    <span class="badge bg-secondary">
                                        {{ $venta->cantidad_total_items }}
                                    </span>
                                </div>

                                <div class="col-4">
                                    <small class="text-muted d-block">
                                        Total
                                    </small>

                                    <strong>
                                        ${{ number_format($venta->total, 2, ',', '.') }}
                                    </strong>
                                </div>

                                <div class="col-4">
                                    <small class="text-muted d-block">
                                        Pago
                                    </small>

                                    <small>
                                        {{ ucfirst($venta->metodo_pago ?? 'N/A') }}
                                    </small>
                                </div>

                            </div>

                            <div class="d-grid">

                                <a href="{{ route('ventas.show', $venta->id) }}"
                                class="btn btn-outline-info">

                                    <i class="fas fa-eye me-2"></i>
                                    Ver detalles

                                </a>

                            </div>

                        </div>

                    </div>

                @endforeach

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