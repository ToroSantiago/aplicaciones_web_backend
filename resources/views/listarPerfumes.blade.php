@extends('layouts.admin')

@section('title', 'Administrar Perfumes')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-spray-can me-2"></i>Gestión de Perfumes</h1>
        <a href="{{ route('perfumes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Perfume
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Lista de Perfumes</h5>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar..." id="searchInput">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($perfumes->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No hay perfumes registrados.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Imagen</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Marca</th>
                                <th scope="col">Género</th>
                                <th scope="col">Variantes</th>
                                <th scope="col">Rango de Precios</th>
                                <th scope="col">Stock Total</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($perfumes as $perfume)
                                <tr>
                                    <td>{{ $perfume->id }}</td>
                                    <td>
                                        @if($perfume->imagen_url)
                                            <img src="{{ $perfume->imagen_url }}" alt="{{ $perfume->nombre }}" 
                                                 class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <span class="text-muted">Sin imagen</span>
                                        @endif
                                    </td>
                                    <td>{{ $perfume->nombre }}</td>
                                    <td>{{ $perfume->marca }}</td>
                                    <td>
                                        @if($perfume->genero == 'M')
                                            <span class="badge bg-primary">Masculino</span>
                                        @elseif($perfume->genero == 'F')
                                            <span class="badge bg-danger">Femenino</span>
                                        @else
                                            <span class="badge bg-info">Unisex</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#variantesModal{{ $perfume->id }}">
                                            <i class="fas fa-boxes me-1"></i>Ver variantes
                                        </button>
                                    </td>
                                    <td>
                                        ${{ number_format($perfume->precio_minimo, 2, ',', '.') }} - 
                                        ${{ number_format($perfume->precio_maximo, 2, ',', '.') }}
                                    </td>
                                    <td>
                                        @if($perfume->stock_total > 0)
                                            <span class="badge bg-success">{{ $perfume->stock_total }} unidades</span>
                                        @else
                                            <span class="badge bg-danger">Agotado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Acciones">
                                            <a href="{{ route('perfumes.show', $perfume->id) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('perfumes.edit', $perfume->id) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('perfumes.destroy', $perfume->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Estás seguro de eliminar este perfume?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Modales de variantes - FUERA de la tabla -->
                @foreach($perfumes as $perfume)
                    <div class="modal fade" id="variantesModal{{ $perfume->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Variantes de {{ $perfume->nombre }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Volumen</th>
                                                <th>Precio</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($perfume->variantes && $perfume->variantes->count() > 0)
                                                @foreach($perfume->variantes->sortBy('volumen') as $variante)
                                                    <tr>
                                                        <td>{{ $variante->volumen }} ml</td>
                                                        <td>${{ number_format($variante->precio, 2, ',', '.') }}</td>
                                                        <td>
                                                            @if($variante->stock > 0)
                                                                <span class="badge bg-success">{{ $variante->stock }} unidades</span>
                                                            @else
                                                                <span class="badge bg-danger">Agotado</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">
                                                        No hay variantes disponibles
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <!-- Fin de modales -->
                
            @endif
        </div>
        <div class="card-footer text-muted">
            Total de registros: {{ $perfumes->count() }}
        </div>
    </div>
@endsection