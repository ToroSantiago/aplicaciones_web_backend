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
                    <div class="col-md-6">
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
                    
                    <div class="col-md-6">
                        <label for="imagen_url" class="form-label">URL de Imagen (Cloudinary):</label>
                        <input type="url" class="form-control @error('imagen_url') is-invalid @enderror" 
                               id="imagen_url" name="imagen_url" value="{{ old('imagen_url') }}" 
                               placeholder="https://res.cloudinary.com/...">
                        <small class="text-muted">Opcional: URL de la imagen desde Cloudinary</small>
                        @error('imagen_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Sección de Variantes -->
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-boxes me-2"></i>Variantes del Perfume</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tamaño</th>
                                        <th>Precio ($)</th>
                                        <th>Stock (unidades)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Variante 75ml -->
                                    <tr>
                                        <td>
                                            <strong>75 ml</strong>
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control @error('variante_75_precio') is-invalid @enderror" 
                                                   name="variante_75_precio" 
                                                   value="{{ old('variante_75_precio', 0) }}" 
                                                   min="0" 
                                                   step="0.01" 
                                                   required>
                                            @error('variante_75_precio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control @error('variante_75_stock') is-invalid @enderror" 
                                                   name="variante_75_stock" 
                                                   value="{{ old('variante_75_stock', 0) }}" 
                                                   min="0" required>
                                            @error('variante_75_stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                    
                                    <!-- Variante 100ml -->
                                    <tr>
                                        <td>
                                            <strong>100 ml</strong>
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control @error('variante_100_precio') is-invalid @enderror" 
                                                   name="variante_100_precio" 
                                                   value="{{ old('variante_100_precio', 0) }}" 
                                                   min="0" 
                                                   step="0.01" 
                                                   required>
                                            @error('variante_100_precio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control @error('variante_100_stock') is-invalid @enderror" 
                                                   name="variante_100_stock" 
                                                   value="{{ old('variante_100_stock', 0) }}" 
                                                   min="0" required>
                                            @error('variante_100_stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                    
                                    <!-- Variante 200ml -->
                                    <tr>
                                        <td>
                                            <strong>200 ml</strong>
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control @error('variante_200_precio') is-invalid @enderror" 
                                                   name="variante_200_precio" 
                                                   value="{{ old('variante_200_precio', 0) }}" 
                                                   min="0" 
                                                   step="0.01" 
                                                   required>
                                            @error('variante_200_precio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control @error('variante_200_stock') is-invalid @enderror" 
                                                   name="variante_200_stock" 
                                                   value="{{ old('variante_200_stock', 0) }}" 
                                                   min="0" required>
                                            @error('variante_200_stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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