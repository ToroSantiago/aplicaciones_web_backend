@extends('layouts.admin')

@section('title', 'Crear Usuario')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-user-plus me-2"></i>Crear Usuario</h1>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al listado
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Formulario de Creación</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('usuarios.store') }}" class="needs-validation" novalidate>
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" 
                               id="username" name="username" value="{{ old('username') }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                               id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="apellido" class="form-label">Apellido:</label>
                        <input type="text" class="form-control @error('apellido') is-invalid @enderror" 
                               id="apellido" name="apellido" value="{{ old('apellido') }}" required>
                        @error('apellido')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        <small class="form-text text-muted">
                            Mínimo 8 caracteres, una mayúscula, una minúscula, un número y un símbolo.
                        </small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña:</label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="rol" class="form-label">Rol:</label>
                        <select class="form-select @error('rol') is-invalid @enderror" id="rol" name="rol" required>
                            <option value="" disabled {{ old('rol') ? '' : 'selected' }}>Seleccionar rol</option>
                            <option value="Cliente" {{ old('rol') == 'Cliente' ? 'selected' : '' }}>Cliente</option>
                            <option value="Administrador" {{ old('rol') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                        </select>
                        @error('rol')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="genero" class="form-label">Género (Opcional):</label>
                        <select class="form-select @error('genero') is-invalid @enderror" id="genero" name="genero">
                            <option value="" {{ old('genero') ? '' : 'selected' }}>Prefiero no decir</option>
                            <option value="M" {{ old('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                            <option value="O" {{ old('genero') == 'O' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('genero')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-eraser me-1"></i>Limpiar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection