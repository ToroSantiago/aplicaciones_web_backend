<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $ventas = $this->buildVentasQuery($request)
            ->recientes()
            ->paginate(20)
            ->withQueryString();

        $clientes = Usuario::where('rol', 'Cliente')->orderBy('nombre')->get();

        return view('ventas.index', compact('ventas', 'clientes'));
    }

    /**
     * Exportar las ventas filtradas a CSV.
     *
     * Usa el mismo set de filtros que index() para que el admin baje
     * exactamente lo que está viendo en pantalla. Stream + chunks para
     * que escale sin cargar todo en memoria.
     */
    public function exportCsv(Request $request)
    {
        $query = $this->buildVentasQuery($request)->recientes();

        $nombreArchivo = 'ventas-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');

            // BOM UTF-8: Excel español lo necesita para mostrar bien los acentos.
            fwrite($out, "\xEF\xBB\xBF");

            // Cabecera. Usamos ';' como separator porque es el default que Excel
            // español espera al abrir con doble click. Para Excel inglés
            // también funciona si se importa desde "Datos → Desde texto".
            fputcsv($out, [
                'ID',
                'Fecha',
                'Cliente',
                'Email',
                'Items',
                'Total',
                'Estado',
                'Método de pago',
            ], ';');

            // Procesamos en lotes de 500 para no agotar memoria si hay muchas ventas.
            $query->chunk(500, function ($ventas) use ($out) {
                foreach ($ventas as $venta) {
                    fputcsv($out, [
                        $venta->id,
                        $venta->created_at->format('d/m/Y H:i'),
                        $venta->cliente_nombre_completo,
                        $venta->usuario?->email ?? '',
                        $venta->cantidad_total_items,
                        // Excel español espera coma como decimal.
                        number_format((float) $venta->total, 2, ',', '.'),
                        ucfirst($venta->estado),
                        $venta->metodo_pago ?? '',
                    ], ';');
                }
            });

            fclose($out);
        }, $nombreArchivo, [
            'Content-Type'  => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    /**
     * Construye la query base de ventas aplicando los filtros del request.
     * Compartido entre index() y exportCsv() para que ambos respeten
     * exactamente los mismos criterios.
     */
    private function buildVentasQuery(Request $request)
    {
        $query = Venta::with(['usuario', 'detalles.perfumeVariante.perfume']);

        if ($request->filled('cliente_id')) {
            $query->where('usuario_id', $request->cliente_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->whereHas('usuario', function ($query) use ($buscar) {
                    $query->where('nombre', 'ILIKE', "%{$buscar}%")
                          ->orWhere('apellido', 'ILIKE', "%{$buscar}%")
                          ->orWhere('email', 'ILIKE', "%{$buscar}%");
                })
                ->orWhereHas('detalles.perfumeVariante.perfume', function ($query) use ($buscar) {
                    $query->where('nombre', 'ILIKE', "%{$buscar}%")
                          ->orWhere('marca', 'ILIKE', "%{$buscar}%");
                });
            });
        }

        return $query;
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta)
    {
        $venta->load(['usuario', 'detalles.perfumeVariante.perfume']);
        return view('ventas.show', compact('venta'));
    }

    /**
     * Update the status of the sale.
     *
     * Reglas de stock por estado:
     *   - 'completada' → stock DESCONTADO
     *   - 'pendiente'  → stock INTACTO (todavía no se cobró / no se entregó)
     *   - 'cancelada'  → stock INTACTO (devuelto si lo habíamos descontado)
     *
     * Cualquier transición que cruce el "umbral completada" debe ajustar el
     * stock acordemente. Esto cubre los 6 casos posibles (incluyendo el que
     * la lógica vieja no manejaba: pendiente → completada, que es lo que
     * pasa cuando un admin marca a mano una venta MP que el webhook no
     * llegó a confirmar).
     */
    public function updateStatus(Request $request, Venta $venta)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,completada,cancelada'
        ]);

        $estadoNuevo = $request->estado;
        $estadoAnterior = $venta->estado;

        if ($estadoAnterior === $estadoNuevo) {
            return redirect()->back()->with('success', 'La venta ya estaba en ese estado.');
        }

        DB::transaction(function () use ($estadoAnterior, $estadoNuevo, $venta) {
            // ¿Hay que tocar stock? Solo cuando una de las dos puntas es 'completada'.
            $teniaStockDescontado  = $estadoAnterior === 'completada';
            $tendraStockDescontado = $estadoNuevo === 'completada';

            if ($teniaStockDescontado && ! $tendraStockDescontado) {
                // completada → (pendiente | cancelada): devolver stock
                $this->ajustarStock($venta, sumar: true);
            } elseif (! $teniaStockDescontado && $tendraStockDescontado) {
                // (pendiente | cancelada) → completada: descontar stock
                $this->ajustarStock($venta, sumar: false);
            }
            // pendiente <-> cancelada: no se toca el stock.

            $venta->update(['estado' => $estadoNuevo]);
        });

        return redirect()->back()->with('success', 'Estado de la venta actualizado correctamente.');
    }

    /**
     * Suma o resta del stock de cada variante de la venta. Usa lockForUpdate
     * para evitar race conditions con compras concurrentes.
     */
    private function ajustarStock(Venta $venta, bool $sumar): void
    {
        $venta->load('detalles');
        $varianteIds = $venta->detalles->pluck('perfume_variante_id')->all();

        $variantes = \App\Models\PerfumeVariante::whereIn('id', $varianteIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($venta->detalles as $detalle) {
            $variante = $variantes[$detalle->perfume_variante_id] ?? null;
            if (! $variante) {
                continue;
            }
            if ($sumar) {
                $variante->increment('stock', $detalle->cantidad);
            } else {
                $variante->decrement('stock', $detalle->cantidad);
            }
        }
    }

    /**
     * Mostrar estadísticas de ventas
     */
    public function estadisticas(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? now()->startOfMonth();
        $fechaFin = $request->fecha_fin ?? now()->endOfDay();

        // Ventas totales
        $ventasTotales = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin])
                              ->where('estado', 'completada')
                              ->sum('total');

        // Cantidad de ventas
        $cantidadVentas = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin])
                               ->where('estado', 'completada')
                               ->count();

        // Productos más vendidos
        $productosMasVendidos = DB::table('venta_detalles')
            ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
            ->join('perfume_variantes', 'venta_detalles.perfume_variante_id', '=', 'perfume_variantes.id')
            ->join('perfumes', 'perfume_variantes.perfume_id', '=', 'perfumes.id')
            ->whereBetween('ventas.created_at', [$fechaInicio, $fechaFin])
            ->where('ventas.estado', 'completada')
            ->select(
                'perfumes.nombre',
                'perfumes.marca',
                'perfume_variantes.volumen',
                DB::raw('SUM(venta_detalles.cantidad) as total_vendido'),
                DB::raw('SUM(venta_detalles.subtotal) as ingresos_totales')
            )
            ->groupBy('perfumes.id', 'perfumes.nombre', 'perfumes.marca', 'perfume_variantes.volumen')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get();

        // Clientes top
        $clientesTop = DB::table('ventas')
            ->join('usuarios', 'ventas.usuario_id', '=', 'usuarios.id')
            ->whereBetween('ventas.created_at', [$fechaInicio, $fechaFin])
            ->where('ventas.estado', 'completada')
            ->select(
                'usuarios.id',
                'usuarios.nombre',
                'usuarios.apellido',
                'usuarios.email',
                DB::raw('COUNT(ventas.id) as cantidad_compras'),
                DB::raw('SUM(ventas.total) as total_gastado')
            )
            ->groupBy('usuarios.id', 'usuarios.nombre', 'usuarios.apellido', 'usuarios.email')
            ->orderBy('total_gastado', 'desc')
            ->limit(10)
            ->get();

        return view('ventas.estadisticas', compact(
            'ventasTotales',
            'cantidadVentas',
            'productosMasVendidos',
            'clientesTop',
            'fechaInicio',
            'fechaFin'
        ));
    }
}