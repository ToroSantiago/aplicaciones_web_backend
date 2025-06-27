@extends('layouts.admin')

@section('title', 'Estadísticas de Ventas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-chart-line me-2"></i>Estadísticas de Ventas</h1>
        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver a ventas
        </a>
    </div>

    <!-- Filtro de fechas -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ventas.estadisticas') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="fecha_inicio" class="form-label">Fecha inicio</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" 
                               value="{{ $fechaInicio instanceof \Carbon\Carbon ? $fechaInicio->format('Y-m-d') : $fechaInicio }}">
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_fin" class="form-label">Fecha fin</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" 
                               value="{{ $fechaFin instanceof \Carbon\Carbon ? $fechaFin->format('Y-m-d') : $fechaFin }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <h3 class="card-title">${{ number_format($ventasTotales, 2, ',', '.') }}</h3>
                            <p class="card-text">Ventas Totales</p>
                        </div>
                        <div class="col-4 text-end">
                            <i class="fas fa-dollar-sign fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <h3 class="card-title">{{ $cantidadVentas }}</h3>
                            <p class="card-text">Cantidad de Ventas</p>
                        </div>
                        <div class="col-4 text-end">
                            <i class="fas fa-shopping-cart fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Productos más vendidos -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top 10 Productos Más Vendidos</h5>
                </div>
                <div class="card-body">
                    @if($productosMasVendidos->isEmpty())
                        <p class="text-muted">No hay datos disponibles para el período seleccionado.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Volumen</th>
                                        <th>Vendidos</th>
                                        <th>Ingresos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productosMasVendidos as $producto)
                                        <tr>
                                            <td>
                                                <strong>{{ $producto->nombre }}</strong><br>
                                                <small class="text-muted">{{ $producto->marca }}</small>
                                            </td>
                                            <td>{{ $producto->volumen }}ml</td>
                                            <td>
                                                <span class="badge bg-info">{{ $producto->total_vendido }}</span>
                                            </td>
                                            <td>${{ number_format($producto->ingresos_totales, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Clientes top -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Top 10 Mejores Clientes</h5>
                </div>
                <div class="card-body">
                    @if($clientesTop->isEmpty())
                        <p class="text-muted">No hay datos disponibles para el período seleccionado.</p>
                    @else
                        <div class="table-responsive"></div>