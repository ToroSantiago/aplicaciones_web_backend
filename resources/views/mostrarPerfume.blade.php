@extends('layouts.admin')

@section('title', 'Detalles del Perfume')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-info-circle me-2"></i>Detalles del Perfume</h1>
        <a href="{{ route('perfumes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al listado
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Información del Perfume - ID: {{ $perfume->id }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <h5>Imagen del producto</h5>
                    <div class="text-center">
                        @if($perfume->imagen_url)
                            <img src="{{ $perfume->imagen_url }}" alt="{{ $perfume->nombre }}" 
                                 class="img-fluid rounded" style="max-height: 300px;">
                        @else
                            <div class="bg-light p-5 rounded">
                                <i class="fas fa-image fa-3x text-muted"></i>
                                <p class="text-muted mt-2">Sin imagen disponible</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-4">
                    <h5>Datos básicos</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 40%">Nombre:</th>
                            <td>{{ $perfume->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Marca:</th>
                            <td>{{ $perfume->marca }}</td>
                        </tr>
                        <tr>
                            <th>Descripción:</th>
                            <td>{{ $perfume->descripcion }}</td>
                        </tr>
                        <tr>
                            <th>Género:</th>
                            <td>
                                @if($perfume->genero == 'M')
                                    <span class="badge bg-primary">Masculino</span>
                                @elseif($perfume->genero == 'F')
                                    <span class="badge bg-danger">Femenino</span>
                                @else
                                    <span class="badge bg-info">Unisex</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de creación:</th>
                            <td>{{ $perfume->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Última actualización:</th>
                            <td>{{ $perfume->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-4">
                    <h5>Resumen de Inventario</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 40%">Precio mínimo:</th>
                            <td>${{ number_format($perfume->precio_minimo, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Precio máximo:</th>
                            <td>${{ number_format($perfume->precio_maximo, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Stock total:</th>
                            <td>
                                @if($perfume->stock_total > 0)
                                    <span class="badge bg-success">{{ $perfume->stock_total }} unidades</span>
                                @else
                                    <span class="badge bg-danger">Agotado</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Estado general:</th>
                            <td>
                                @if($perfume->stock_total > 50)
                                    <i class="fas fa-check-circle text-success"></i> Stock suficiente
                                @elseif($perfume->stock_total > 0)
                                    <i class="fas fa-exclamation-triangle text-warning"></i> Stock bajo
                                @else
                                    <i class="fas fa-times-circle text-danger"></i> Sin stock
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Sección de Variantes -->
            <div class="mt-4">
                <h5><i class="fas fa-boxes me-2"></i>Variantes del Perfume</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Volumen</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($perfume->variantes->sortBy('volumen') as $variante)
                                <tr>
                                    <td><strong>{{ $variante->volumen }} ml</strong></td>
                                    <td>${{ number_format($variante->precio, 2, ',', '.') }}</td>
                                    <td>{{ $variante->stock }} unidades</td>
                                    <td>
                                        @if($variante->stock > 10)
                                            <span class="badge bg-success">Disponible</span>
                                        @elseif($variante->stock > 0)
                                            <span class="badge bg-warning">Stock bajo</span>
                                        @else
                                            <span class="badge bg-danger">Agotado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4 gap-2">
                <a href="{{ route('perfumes.edit', $perfume->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Editar
                </a>
                
                <form action="{{ route('perfumes.destroy', $perfume->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" 
                           onclick="return confirm('¿Estás seguro de eliminar este perfume?')">
                        <i class="fas fa-trash me-1"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection