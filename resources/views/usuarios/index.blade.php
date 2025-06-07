@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-users me-2"></i>Gestión de Usuarios</h1>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Lista de Usuarios</h5>
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
            @if($usuarios->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No hay usuarios registrados.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Username</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Email</th>
                                <th scope="col">Rol</th>
                                <th scope="col">Email verificado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usuarios as $usuario)
                                <tr>
                                    <td>{{ $usuario->id }}</td>
                                    <td>{{ $usuario->username }}</td>
                                    <td>{{ $usuario->nombre }} {{ $usuario->apellido }}</td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>
                                        @if($usuario->rol == 'Administrador')
                                            <span class="badge bg-success">Administrador</span>
                                        @else
                                            <span class="badge bg-secondary">Cliente</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($usuario->email_verified_at)
                                            <span class="badge bg-success">Verificado</span>
                                        @else
                                            <span class="badge bg-danger">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Acciones">
                                            <a href="{{ route('usuarios.show', $usuario->id) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('usuarios.edit', $usuario->id) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('usuarios.destroy', $usuario->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
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
            @endif
        </div>
        <div class="card-footer text-muted">
            Total de registros: {{ $usuarios->count() }}
        </div>
    </div>
@endsection