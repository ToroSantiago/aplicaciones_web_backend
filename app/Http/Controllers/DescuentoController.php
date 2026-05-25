<?php

namespace App\Http\Controllers;

use App\Models\Descuento;
use App\Models\Perfume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DescuentoController extends Controller
{
    /**
     * Listado con filtro opcional ?estado=vigente|futuro|expirado|inactivo.
     */
    public function index(Request $request)
    {
        $query = Descuento::with('variantes.perfume')
            ->orderByDesc('fecha_inicio');

        $estado = $request->query('estado');
        $hoy = now()->toDateString();

        match ($estado) {
            'vigente'  => $query->where('activo', true)
                                ->whereDate('fecha_inicio', '<=', $hoy)
                                ->whereDate('fecha_fin', '>=', $hoy),
            'futuro'   => $query->where('activo', true)
                                ->whereDate('fecha_inicio', '>', $hoy),
            'expirado' => $query->where('activo', true)
                                ->whereDate('fecha_fin', '<', $hoy),
            'inactivo' => $query->where('activo', false),
            default    => null,
        };

        $descuentos = $query->paginate(15)->withQueryString();

        return view('descuentos.index', compact('descuentos', 'estado'));
    }

    public function create()
    {
        $perfumes = Perfume::with('variantes')->orderBy('nombre')->get();
        return view('descuentos.create', compact('perfumes'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $descuento = Descuento::create([
                'nombre'       => $data['nombre'],
                'porcentaje'   => $data['porcentaje'],
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_fin'    => $data['fecha_fin'],
                'activo'       => $data['activo'] ?? true,
            ]);

            $descuento->variantes()->sync($data['variante_ids']);
        });

        return redirect()->route('descuentos.index')
            ->with('success', 'Descuento creado correctamente.');
    }

    public function show(Descuento $descuento)
    {
        $descuento->load('variantes.perfume');
        return view('descuentos.show', compact('descuento'));
    }

    public function edit(Descuento $descuento)
    {
        $descuento->load('variantes');
        $perfumes = Perfume::with('variantes')->orderBy('nombre')->get();
        $variantesAsignadas = $descuento->variantes->pluck('id')->toArray();

        return view('descuentos.edit', compact('descuento', 'perfumes', 'variantesAsignadas'));
    }

    public function update(Request $request, Descuento $descuento)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data, $descuento) {
            $descuento->update([
                'nombre'       => $data['nombre'],
                'porcentaje'   => $data['porcentaje'],
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_fin'    => $data['fecha_fin'],
                'activo'       => $data['activo'] ?? false, // checkbox no marcado = false
            ]);

            $descuento->variantes()->sync($data['variante_ids']);
        });

        return redirect()->route('descuentos.index')
            ->with('success', 'Descuento actualizado correctamente.');
    }

    public function destroy(Descuento $descuento)
    {
        $descuento->delete();

        return redirect()->route('descuentos.index')
            ->with('success', 'Descuento eliminado correctamente.');
    }

    /**
     * Validación compartida por store y update.
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'nombre'         => 'required|string|max:255',
            'porcentaje'     => 'required|numeric|gt:0|lte:100',
            'fecha_inicio'   => 'required|date',
            'fecha_fin'      => 'required|date|after_or_equal:fecha_inicio',
            'activo'         => 'sometimes|boolean',
            'variante_ids'   => 'required|array|min:1',
            'variante_ids.*' => 'integer|exists:perfume_variantes,id',
        ], [
            'variante_ids.required' => 'Tenés que seleccionar al menos una variante.',
            'fecha_fin.after_or_equal' => 'La fecha fin no puede ser anterior a la fecha inicio.',
            'porcentaje.gt' => 'El porcentaje debe ser mayor a 0.',
            'porcentaje.lte' => 'El porcentaje no puede superar 100.',
        ]);
    }
}
