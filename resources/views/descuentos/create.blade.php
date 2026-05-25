@extends('layouts.admin')

@section('title', 'Crear Descuento')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-plus-circle me-2"></i>Crear Descuento</h1>
        <a href="{{ route('descuentos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al listado
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Datos de la campaña</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('descuentos.store') }}">
                @csrf
                @include('descuentos._form')

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('descuentos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Crear descuento
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
