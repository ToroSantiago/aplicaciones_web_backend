@extends('layouts.admin')

@section('title', 'Detalle de Venta')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-receipt me-2"></i>Detalle de Venta #{{ $venta->id }}</h1>
        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al listado
        </a>
    </div>

    <div class="row">
        <!-- Información de la venta -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información de la Venta</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">ID Venta:</dt>
                        <dd class="col-sm-7">#{{ $venta->id }}</dd>

                        <dt class="col-sm-5">Fecha:</dt>
                        <dd class="col-sm-7">{{ $venta->created_at->format('d/m/Y') }}</dd>

                        <dt class="col-sm-5">Hora:</dt>
                        <dd class="col-sm-7">{{ $venta->created_at->format('H:i:s') }}</dd>

                        <dt class="col-sm-5">Estado:</dt>
                        <dd class="col-sm-7">
                            @if($venta->estado == 'completada')
                                <span class="badge bg-success">Completada</span>
                            @elseif($venta->estado == 'pendiente')
                                <span class="badge bg-warning">Pendiente</span>
                            @else
                                <span class="badge bg-danger">Cancelada</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5">Método de Pago:</dt>
                        <dd class="col-sm-7">{{ ucfirst($venta->metodo_pago ?? 'No especificado') }}</dd>

                        <dt class="col-sm-5">Total Items:</dt>
                        <dd class="col-sm-7">{{ $venta->cantidad_total_items }}</dd>

                        <dt class="col-sm-5">Total:</dt>
                        <dd class="col-sm-7"><h5 class="text-primary mb-0">${{ number_format($venta->total, 2, ',', '.') }}</h5></dd>
                    </dl>

                    @if($venta->observaciones)
                        <hr>
                        <h6>Observaciones:</h6>
                        <p class="text-muted">{{ $venta->observaciones }}</p>
                    @endif

                    <!-- Acciones -->
                    <hr>
                    <h6>Cambiar Estado:</h6>
                    <div class="btn-group d-flex" role="group">
                        @if($venta->estado != 'completada')
                            <form action="{{ route('ventas.updateStatus', $venta->id) }}" method="POST" class="flex-fill">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" value="completada">
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="fas fa-check"></i> Completar
                                </button>
                            </form>
                        @endif
                        @if($venta->estado != 'pendiente')
                            <form action="{{ route('ventas.updateStatus', $venta->id) }}" method="POST" class="flex-fill">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" value="pendiente">
                                <button type="submit" class="btn btn-warning btn-sm w-100">
                                    <i class="fas fa-clock"></i> Pendiente
                                </button>
                            </form>
                        @endif
                        @if($venta->estado != 'cancelada')
                            <form action="{{ route('ventas.updateStatus', $venta->id) }}" method="POST" class="flex-fill"
                                  onsubmit="return confirm('¿Está seguro de cancelar esta venta? Se devolverá el stock.')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" value="cancelada">
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del cliente -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Información del Cliente</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">ID:</dt>
                        <dd class="col-sm-8">#{{ $venta->usuario->id }}</dd>

                        <dt class="col-sm-4">Nombre:</dt>
                        <dd class="col-sm-8">{{ $venta->usuario->nombre }} {{ $venta->usuario->apellido }}</dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">{{ $venta->usuario->email }}</dd>

                        <dt class="col-sm-4">Usuario:</dt>
                        <dd class="col-sm-8">{{ $venta->usuario->username }}</dd>

                        <dt class="col-sm-4">Rol:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-info">{{ $venta->usuario->rol }}</span>
                        </dd>

                        <dt class="col-sm-4">Registrado:</dt>
                        <dd class="col-sm-8">{{ $venta->usuario->created_at->format('d/m/Y') }}</dd>
                    </dl>

                    <hr>
                    <a href="{{ route('ventas.index', ['cliente_id' => $venta->usuario->id]) }}" 
                       class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-history me-2"></i>Ver historial de compras
                    </a>
                </div>
            </div>
        </div>

        <!-- Resumen de productos -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Resumen</h5>
                </div>
                <div class="card-body">
                    <canvas id="resumenChart" height="200"></canvas>
                    
                    <hr>
                    
                    <h6>Productos por categoría:</h6>
                    @php
                        $productosPorGenero = $venta->detalles->groupBy(function($detalle) {
                            return $detalle->perfumeVariante->perfume->genero;
                        });
                    @endphp
                    
                    <ul class="list-unstyled mb-0">
                        @foreach($productosPorGenero as $genero => $detalles)
                            <li>
                                @if($genero == 'M')
                                    <i class="fas fa-mars text-primary"></i> Masculino: 
                                @elseif($genero == 'F')
                                    <i class="fas fa-venus text-danger"></i> Femenino: 
                                @else
                                    <i class="fas fa-venus-mars text-info"></i> Unisex: 
                                @endif
                                <strong>{{ $detalles->sum('cantidad') }}</strong> unidades
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalle de productos -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-shopping-basket me-2"></i>Productos de la Venta</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Producto</th>
                            <th>Marca</th>
                            <th>Volumen</th>
                            <th>Precio Unit.</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta->detalles as $detalle)
                            <tr>
                                <td>
                                    @if($detalle->perfumeVariante->perfume->imagen_url)
                                        <img src="{{ $detalle->perfumeVariante->perfume->imagen_url }}" 
                                             alt="{{ $detalle->perfumeVariante->perfume->nombre }}" 
                                             class="img-thumbnail" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <span class="text-muted">Sin imagen</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $detalle->perfumeVariante->perfume->nombre }}</strong>
                                </td>
                                <td>{{ $detalle->perfumeVariante->perfume->marca }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $detalle->perfumeVariante->volumen }}ml</span>
                                </td>
                                <td>${{ number_format($detalle->precio_unitario, 2, ',', '.') }}</td>
                                <td>{{ $detalle->cantidad }}</td>
                                <td>
                                    <strong>${{ number_format($detalle->subtotal, 2, ',', '.') }}</strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <td colspan="5" class="text-end"><strong>TOTAL:</strong></td>
                            <td><strong>{{ $venta->cantidad_total_items }}</strong></td>
                            <td><strong>${{ number_format($venta->total, 2, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Script para el gráfico -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('resumenChart').getContext('2d');
        const data = {
            labels: [
                @foreach($venta->detalles as $detalle)
                    '{{ $detalle->perfumeVariante->perfume->nombre }} ({{ $detalle->perfumeVariante->volumen }}ml)',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($venta->detalles as $detalle)
                        {{ $detalle->subtotal }},
                    @endforeach
                ],
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ]
            }]
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': $' + context.parsed.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection