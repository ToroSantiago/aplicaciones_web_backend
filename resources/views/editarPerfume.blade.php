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
                    <div class="col-md-6">
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
                    
                    <div class="col-md-6">
                        <label for="imagen_url" class="form-label">URL de Imagen (Cloudinary):</label>
                        <input type="url" class="form-control @error('imagen_url') is-invalid @enderror" 
                               id="imagen_url" name="imagen_url" value="{{ old('imagen_url', $perfume->imagen_url) }}" 
                               placeholder="https://res.cloudinary.com/...">
                        <small class="text-muted">Opcional: URL de la imagen desde Cloudinary</small>
                        @error('imagen_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if($perfume->imagen_url)
                            <div class="mt-2">
                                <p class="mb-1">Imagen actual:</p>
                                <img src="{{ $perfume->imagen_url }}" alt="{{ $perfume->nombre }}" 
                                     class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        @endif
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
                                    @php
                                        $variantes = $perfume->variantes->keyBy('volumen');
                                    @endphp
                                    
                                    <!-- Variante 75ml -->
                                    <tr>
                                        <td>
                                            <strong>75 ml</strong>
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control @error('variante_75_precio') is-invalid @enderror" 
                                                   name="variante_75_precio" 
                                                   value="{{ old('variante_75_precio', $variantes[75]->precio ?? 0) }}" 
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
                                                   value="{{ old('variante_75_stock', $variantes[75]->stock ?? 0) }}" 
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
                                                   value="{{ old('variante_100_precio', $variantes[100]->precio ?? 0) }}" 
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
                                                   value="{{ old('variante_100_stock', $variantes[100]->stock ?? 0) }}" 
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
                                                   value="{{ old('variante_200_precio', $variantes[200]->precio ?? 0) }}" 
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
                                                   value="{{ old('variante_200_stock', $variantes[200]->stock ?? 0) }}" 
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