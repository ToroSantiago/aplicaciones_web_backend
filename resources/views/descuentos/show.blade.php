@extends('layouts.admin')

@section('title', 'Detalle de Descuento')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-tag me-2"></i>Descuento #{{ $descuento->id }}</h1>
        <div>
            <a href="{{ route('descuentos.edit', $descuento->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i>Editar
            </a>
            <a href="{{ route('descuentos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Datos</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Nombre:</dt>
                        <dd class="col-sm-7">{{ $descuento->nombre }}</dd>

                        <dt class="col-sm-5">Porcentaje:</dt>
                        <dd class="col-sm-7">
                            <span class="badge bg-danger fs-6">
                                -{{ rtrim(rtrim(number_format($descuento->porcentaje, 2, ',', '.'), '0'), ',') }}%
                            </span>
                        </dd>

                        <dt class="col-sm-5">Vigencia:</dt>
                        <dd class="col-sm-7">
                            {{ $descuento->fecha_inicio->format('d/m/Y') }} —
                            {{ $descuento->fecha_fin->format('d/m/Y') }}
                        </dd>

                        <dt class="col-sm-5">Estado:</dt>
                        <dd class="col-sm-7">
                            @switch($descuento->estado)
                                @case('vigente') <span class="badge bg-success">Vigente</span> @break
                                @case('futuro') <span class="badge bg-info">Futuro</span> @break
                                @case('expirado') <span class="badge bg-secondary">Expirado</span> @break
                                @case('inactivo') <span class="badge bg-dark">Inactivo</span> @break
                            @endswitch
                        </dd>

                        <dt class="col-sm-5">Activo:</dt>
                        <dd class="col-sm-7">{{ $descuento->activo ? 'Sí' : 'No' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-7 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Variantes alcanzadas ({{ $descuento->variantes->count() }})</h5>
                </div>
                <div class="card-body">

                @if($descuento->variantes->isEmpty())
                    <div class="alert alert-warning mb-0">
                        No hay variantes asignadas.
                    </div>
                @else

                    {{-- Desktop --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Perfume</th>
                                    <th>Volumen</th>
                                    <th>Precio original</th>
                                    <th>Precio con descuento</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($descuento->variantes as $v)
                                    @php
                                        $precioConDescuento = round(
                                            $v->precio * (1 - $descuento->porcentaje / 100),
                                            2
                                        );
                                    @endphp

                                    <tr>
                                        <td>
                                            <strong>{{ $v->perfume->nombre }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $v->perfume->marca }}
                                            </small>
                                        </td>

                                        <td>{{ $v->volumen }}ml</td>

                                        <td>
                                            <s class="text-muted">
                                                ${{ number_format($v->precio, 2, ',', '.') }}
                                            </s>
                                        </td>

                                        <td>
                                            <strong class="text-success">
                                                ${{ number_format($precioConDescuento, 2, ',', '.') }}
                                            </strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile --}}
                    <div class="d-md-none">
                        @foreach($descuento->variantes as $v)
                            @php
                                $precioConDescuento = round(
                                    $v->precio * (1 - $descuento->porcentaje / 100),
                                    2
                                );
                            @endphp

                            <div class="card mb-3 border-start border-4">
                                <div class="card-body">

                                    <h6 class="mb-1">
                                        {{ $v->perfume->nombre }}
                                    </h6>

                                    <small class="text-muted d-block mb-3">
                                        {{ $v->perfume->marca }}
                                    </small>

                                    <div class="row g-2">

                                        <div class="col-6">
                                            <small class="text-muted">Volumen</small>
                                            <div>
                                                <span class="badge bg-secondary">
                                                    {{ $v->volumen }}ml
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <small class="text-muted">Descuento</small>
                                            <div>
                                                <span class="badge bg-danger">
                                                    -{{ rtrim(rtrim(number_format($descuento->porcentaje, 2, ',', '.'), '0'), ',') }}%
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <small class="text-muted">Precio original</small>
                                            <div>
                                                <s>
                                                    ${{ number_format($v->precio, 2, ',', '.') }}
                                                </s>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <small class="text-muted">Precio final</small>
                                            <div>
                                                <strong class="text-success fs-5">
                                                    ${{ number_format($precioConDescuento, 2, ',', '.') }}
                                                </strong>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
@endsection
