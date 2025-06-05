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
                                <th scope="col">Nombre</th>
                                <th scope="col">Marca</th>
                                <th scope="col">Volumen</th>
                                <th scope="col">Precio</th>
                                <th scope="col">Género</th>
                                <th scope="col">Stock</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($perfumes as $perfume)
                                <tr>
                                    <td>{{ $perfume->id }}</td>
                                    <td>{{ $perfume->nombre }}</td>
                                    <td>{{ $perfume->marca }}</td>
                                    <td>{{ $perfume->volumen }} ml</td>
                                    <td>${{ $perfume->precio }}</td>
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
                                        @if($perfume->stock)
                                            <span class="badge bg-success">Disponible</span>
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
                <!-- Aquí iría la paginación -->
            @endif
        </div>
        <div class="card-footer text-muted">
            Total de registros: {{ $perfumes->count() }}
        </div>
    </div>
@endsection