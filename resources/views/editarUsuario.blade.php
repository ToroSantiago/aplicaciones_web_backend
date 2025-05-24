@extends('layouts.admin')

@section('title', 'Editar Usuario')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-user-edit me-2"></i>Editar Usuario</h1>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al listado
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Formulario de Edición - ID: {{ $usuario->id }}</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('usuarios.update', $usuario->id) }}" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Nombre de usuario:</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" 
                               id="username" name="username" value="{{ old('username', $usuario->username) }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Correo electrónico:</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                               id="nombre" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="apellido" class="form-label">Apellido:</label>
                        <input type="text" class="form-control @error('apellido') is-invalid @enderror" 
                               id="apellido" name="apellido" value="{{ old('apellido', $usuario->apellido) }}" required>
                        @error('apellido')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Rol:</label>
                        <input type="text" class="form-control" value="{{ $usuario->rol }}" disabled>
                        <input type="hidden" name="rol" value="{{ $usuario->rol }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Género:</label>
                        <input type="text" class="form-control" value="@if($usuario->genero == 'M') Masculino @elseif($usuario->genero == 'F') Femenino @else Otro @endif" disabled>
                        <input type="hidden" name="genero" value="{{ $usuario->genero }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Nueva Contraseña (opcional):</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
