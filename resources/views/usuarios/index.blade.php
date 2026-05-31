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
            <div class="row align-items-center g-2">
                <div class="col-12 col-md">
                    <h5 class="mb-0">Lista de Usuarios</h5>
                </div>
                <div class="col-12 col-md-auto">
                    {{-- action="" envía al URL actual — evita mixed-content
                         warning del navegador cuando route() genera http://
                         detrás del proxy HTTPS de Vercel. --}}
                    <form method="GET" action="" class="d-flex gap-2 flex-wrap">
                        {{-- Filtro por rol --}}
                        <select name="rol" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Todos los roles</option>
                            @foreach(['Cliente', 'Empleado', 'Administrador'] as $opt)
                                <option value="{{ $opt }}" {{ ($rol ?? '') === $opt ? 'selected' : '' }}>
                                    {{ $opt }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Buscador --}}
                        <div class="input-group input-group-sm">
                            <input type="text"
                                   name="q"
                                   class="form-control"
                                   placeholder="Buscar por nombre, email, username..."
                                   value="{{ $q ?? '' }}"
                                   id="searchInput">
                            <button class="btn btn-primary" type="submit" aria-label="Buscar usuarios">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(!empty($q) || !empty($rol))
                                <a href="{{ route('usuarios.index') }}"
                                   class="btn btn-outline-secondary"
                                   title="Limpiar filtros">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($usuarios->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    @if(!empty($q) || !empty($rol))
                        No se encontraron usuarios con los filtros aplicados.
                        <a href="{{ route('usuarios.index') }}">Limpiar filtros</a>.
                    @else
                        No hay usuarios registrados.
                    @endif
                </div>
            @else
                <div class="table-responsive d-none d-md-block">
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
                                        @elseif($usuario->rol == 'Empleado')
                                            <span class="badge bg-primary">Empleado</span>
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
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $usuario->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="modal fade"
                                            id="deleteModal{{ $usuario->id }}"
                                            tabindex="-1"
                                            aria-hidden="true">

                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            Confirmar eliminación
                                                        </h5>

                                                        <button type="button"
                                                                class="btn-close"
                                                                data-bs-dismiss="modal">
                                                        </button>
                                                    </div>

                                                    <div class="modal-body">
                                                        ¿Seguro que querés eliminar a
                                                        <strong>{{ $usuario->username }}</strong>?
                                                    </div>

                                                    <div class="modal-footer">

                                                        <button type="button"
                                                                class="btn btn-secondary"
                                                                data-bs-dismiss="modal">
                                                            Cancelar
                                                        </button>

                                                        <form action="{{ route('usuarios.destroy', $usuario->id) }}"
                                                            method="POST">

                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                    class="btn btn-danger">
                                                                Eliminar
                                                            </button>

                                                        </form>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Versión Mobile -->
        <div class="d-md-none">

            @foreach ($usuarios as $usuario)

                <div class="card mb-3 shadow-sm">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-0">
                                    {{ $usuario->nombre }} {{ $usuario->apellido }}
                                </h6>
                            </div>

                            <span class="badge bg-secondary">
                                #{{ $usuario->id }}
                            </span>
                        </div>

                        <hr>

                        <div class="mb-2">
                            <small class="text-muted d-block">
                                Email
                            </small>

                            <strong>
                                {{ $usuario->email }}
                            </strong>
                        </div>

                        <div class="row mb-3">

                            <div class="col-6">
                                <small class="text-muted d-block">
                                    Rol
                                </small>

                                @if($usuario->rol == 'Administrador')
                                    <span class="badge bg-success">
                                        Administrador
                                    </span>
                                @elseif($usuario->rol == 'Empleado')
                                    <span class="badge bg-primary">
                                        Empleado
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        Cliente
                                    </span>
                                @endif
                            </div>

                            <div class="col-6">
                                <small class="text-muted d-block">
                                    Estado email
                                </small>

                                @if($usuario->email_verified_at)
                                    <span class="badge bg-success">
                                        Verificado
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        Sin verificar
                                    </span>
                                @endif
                            </div>

                        </div>

                        <div class="d-grid gap-2">

                            <a href="{{ route('usuarios.show', $usuario->id) }}"
                            class="btn btn-outline-info btn-sm">
                                <i class="fas fa-eye me-2"></i>
                                Ver detalles
                            </a>

                            <a href="{{ route('usuarios.edit', $usuario->id) }}"
                            class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-2"></i>
                                Editar
                            </a>

                            <button type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal{{ $usuario->id }}">
                                <i class="fas fa-trash me-2"></i>
                                Eliminar
                            </button>

                        </div>

                    </div>
                </div>

            @endforeach

        </div>

        {{-- Paginación (no renderiza nada si no hay resultados) --}}
        @if(!$usuarios->isEmpty())
            <div class="d-flex justify-content-center my-3">
                {{ $usuarios->links() }}
            </div>
        @endif

        <div class="card-footer text-muted">
            @if(!empty($q) || !empty($rol))
                Mostrando {{ $usuarios->count() }} de {{ $usuarios->total() }} usuarios filtrados
                @if(!empty($q)) para "<strong>{{ $q }}</strong>"@endif
                @if(!empty($rol)) con rol <strong>{{ $rol }}</strong>@endif
            @else
                Mostrando {{ $usuarios->count() }} de {{ $usuarios->total() }} usuarios
            @endif
        </div>
    </div>
@endsection