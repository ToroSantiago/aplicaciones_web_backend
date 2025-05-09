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
                <div class="col-md-6">
                    <h5>Datos básicos</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Nombre:</th>
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
                            <th>Fecha de creación:</th>
                            <td>{{ $perfume->created_at }}</td>
                        </tr>
                        <tr>
                            <th>Última actualización:</th>
                            <td>{{ $perfume->updated_at }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h5>Detalles del producto</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Volumen:</th>
                            <td>{{ $perfume->volumen }} ml</td>
                        </tr>
                        <tr>
                            <th>Precio:</th>
                            <td>${{ number_format($perfume->precio / 100, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Género:</th>
                            <td>
                                @if($perfume->genero == 'M')
                                    <span class="badge gender-M">Masculino</span>
                                @elseif($perfume->genero == 'F')
                                    <span class="badge gender-F">Femenino</span>
                                @else
                                    <span class="badge gender-U">Unisex</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Stock:</th>
                            <td>
                                @if($perfume->stock)
                                    <span class="badge badge-stock">Disponible</span>
                                @else
                                    <span class="badge badge-no-stock">Agotado</span>
                                @endif
                            </td>
                        </tr>
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