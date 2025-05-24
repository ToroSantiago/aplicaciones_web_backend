@extends('layouts.admin')

@section('title', 'Editar Perfume')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-edit me-2"></i>Editar Perfume</h1>
        <a href="{{ route('perfumes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al listado
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Formulario de Edición - ID: {{ $perfume->id }}</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('perfumes.update', $perfume->id) }}" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                               id="nombre" name="nombre" value="{{ old('nombre', $perfume->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="marca" class="form-label">Marca:</label>
                        <input type="text" class="form-control @error('marca') is-invalid @enderror" 
                               id="marca" name="marca" value="{{ old('marca', $perfume->marca) }}" required>
                        @error('marca')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                              id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $perfume->descripcion) }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="volumen" class="form-label">Volumen (ml):</label>
                        <input type="number" class="form-control @error('volumen') is-invalid @enderror" 
                               id="volumen" name="volumen" value="{{ old('volumen', $perfume->volumen) }}" required>
                        @error('volumen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="precio" class="form-label">Precio:</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control @error('precio') is-invalid @enderror" 
                                   id="precio" name="precio" value="{{ old('precio', $perfume->precio) }}" required>
                            @error('precio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="genero" class="form-label">Género:</label>
                        <select class="form-select @error('genero') is-invalid @enderror" 
                            id="genero" name="genero" required>
                            <option value="M" {{ old('genero', $perfume->genero) == 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('genero', $perfume->genero) == 'F' ? 'selected' : '' }}>Femenino</option>
                            <option value="U" {{ old('genero', $perfume->genero) == 'U' ? 'selected' : '' }}>Unisex</option>
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
                               id="stock" name="stock" value="1" {{ old('stock', $perfume->stock) ? 'checked' : '' }}>
                        <label class="form-check-label" for="stock">¿Hay stock disponible?</label>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('perfumes.index') }}" class="btn btn-secondary">
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