{{--
    Partial reutilizado por create y edit.
    Variables esperadas:
      - $descuento (Descuento o null en create)
      - $perfumes (Collection<Perfume> con variantes cargadas)
      - $variantesAsignadas (array<int> de IDs ya marcados; vacío en create)
--}}
@php
    $descuento ??= null;
    $variantesAsignadas ??= [];
@endphp

<div class="row mb-3">
    <div class="col-md-6">
        <label for="nombre" class="form-label">Nombre de la campaña:</label>
        <input type="text" class="form-control @error('nombre') is-invalid @enderror"
               id="nombre" name="nombre"
               value="{{ old('nombre', $descuento->nombre ?? '') }}" required>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label for="porcentaje" class="form-label">Porcentaje (%):</label>
        <input type="number" step="0.01" min="0.01" max="100"
               class="form-control @error('porcentaje') is-invalid @enderror"
               id="porcentaje" name="porcentaje"
               value="{{ old('porcentaje', $descuento->porcentaje ?? '') }}" required>
        @error('porcentaje') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <div class="form-check form-switch">
            <input type="hidden" name="activo" value="0">
            <input class="form-check-input" type="checkbox" role="switch"
                   id="activo" name="activo" value="1"
                   {{ old('activo', $descuento->activo ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="activo">Activo</label>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <label for="fecha_inicio" class="form-label">Fecha inicio:</label>
        <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror"
               id="fecha_inicio" name="fecha_inicio"
               value="{{ old('fecha_inicio', isset($descuento) && $descuento->fecha_inicio ? $descuento->fecha_inicio->format('Y-m-d') : '') }}" required>
        @error('fecha_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label for="fecha_fin" class="form-label">Fecha fin:</label>
        <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror"
               id="fecha_fin" name="fecha_fin"
               value="{{ old('fecha_fin', isset($descuento) && $descuento->fecha_fin ? $descuento->fecha_fin->format('Y-m-d') : '') }}" required>
        @error('fecha_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<h5 class="mb-2">Variantes a las que aplica</h5>
<p class="text-muted small mb-2">Marcá una o más. Podés usar "Todas" para tildar todas las variantes de un perfume de un solo click.</p>

@error('variante_ids')
    <div class="alert alert-danger">{{ $message }}</div>
@enderror

<div class="card">
    <div class="card-body p-2" style="max-height: 480px; overflow-y: auto;">
        @php
            $oldSeleccion = old('variante_ids', $variantesAsignadas);
            // Normalizamos a array de ints para el `in_array` posterior.
            $oldSeleccion = is_array($oldSeleccion) ? array_map('intval', $oldSeleccion) : [];
        @endphp

        @forelse($perfumes as $perfume)
            <div class="border rounded mb-2 p-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong>{{ $perfume->nombre }} <span class="text-muted">— {{ $perfume->marca }}</span></strong>
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="toggleVariantes({{ $perfume->id }}, this)">
                        Todas / Ninguna
                    </button>
                </div>
                <div class="row g-2">
                    @foreach($perfume->variantes as $v)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input perfume-{{ $perfume->id }}"
                                       type="checkbox"
                                       id="variante-{{ $v->id }}"
                                       name="variante_ids[]"
                                       value="{{ $v->id }}"
                                       {{ in_array((int)$v->id, $oldSeleccion, true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="variante-{{ $v->id }}">
                                    {{ $v->volumen }}ml — ${{ number_format($v->precio, 2, ',', '.') }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="alert alert-warning mb-0">No hay perfumes cargados todavía.</div>
        @endforelse
    </div>
</div>

<script>
    function toggleVariantes(perfumeId, btn) {
        const boxes = document.querySelectorAll('.perfume-' + perfumeId);
        const allChecked = Array.from(boxes).every(b => b.checked);
        boxes.forEach(b => b.checked = !allChecked);
    }
</script>
