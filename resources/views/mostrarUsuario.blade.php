@extends('layouts.admin')

@section('title', 'Detalles del Usuario')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-user me-2"></i>Detalles del Usuario</h1>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al listado
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Información del Usuario - ID: {{ $usuario->id }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Datos personales</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Nombre:</th>
                            <td>{{ $usuario->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Apellido:</th>
                            <td>{{ $usuario->apellido }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $usuario->email }}</td>
                        </tr>
                        <tr>
                            <th>Usuario:</th>
                            <td>{{ $usuario->username }}</td>
                        </tr>
                        <tr>
                            <th>Género:</th>
                            <td>
                                @if($usuario->genero === 'M')
                                    <span class="badge bg-primary">Masculino</span>
                                @elseif($usuario->genero === 'F')
                                    <span class="badge bg-primary">Femenino</span>
                                @else
                                    <span class="badge bg-primary">Otro</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h5>Información adicional</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Rol:</th>
                            <td>
                                @if($usuario->rol === 'Administrador')
                                    <span class="badge bg-danger">Administrador</span>
                                @else
                                    <span class="badge bg-success">Cliente</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Email verificado:</th>
                            <td>
                                @if($usuario->email_verified_at)
                                    <span class="badge bg-success">Sí</span>
                                @else
                                    <span class="badge bg-warning text-dark">No verificado</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de creación:</th>
                            <td>{{ $usuario->created_at }}</td>
                        </tr>
                        <tr>
                            <th>Última actualización:</th>
                            <td>{{ $usuario->updated_at }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4 gap-2">
                <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Editar
                </a>

                <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                        <i class="fas fa-trash me-1"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
