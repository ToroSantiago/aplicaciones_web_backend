@extends('layouts.admin')

@section('title', 'Crear Perfume')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-plus-circle me-2"></i>Crear Perfume</h1>
        <a href="{{ route('perfumes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al listado
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Formulario de Creación</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('perfumes.store') }}" class="needs-validation" novalidate>
                @csrf
                
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
                        <label for="marca" class="form-label">Marca:</label>
                        <input type="text" class="form-control @error('marca') is-invalid @enderror" 
                               id="marca" name="marca" value="{{ old('marca') }}" required>
                        @error('marca')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                              id="descripcion" name="descripcion" rows="3" required>{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="volumen" class="form-label">Volumen (ml):</label>
                        <input type="number" class="form-control @error('volumen') is-invalid @enderror" 
                               id="volumen" name="volumen" value="{{ old('volumen') }}" required>
                        @error('volumen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="precio" class="form-label">Precio:</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control @error('precio') is-invalid @enderror" 
                                   id="precio" name="precio" value="{{ old('precio') }}" required>
                            @error('precio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="genero" class="form-label">Género:</label>
                        <select class="form-select @error('genero') is-invalid @enderror" 
                                id="genero" name="genero" required>
                            <option value="" disabled {{ old('genero') ? '' : 'selected' }}>Seleccionar género</option>
                            <option value="M" {{ old('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                            <option value="U" {{ old('genero') == 'U' ? 'selected' : '' }}>Unisex</option>
                        </select>
                        @error('genero')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input type="hidden" name="stock" value="0">
                        <input class="form-check-input" type="checkbox" role="switch" 
                               id="stock" name="stock" value="1" {{ old('stock', '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="stock">¿Hay stock disponible?</label>
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