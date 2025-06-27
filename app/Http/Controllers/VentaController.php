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
        $query = Venta::with(['usuario', 'detalles.perfumeVariante.perfume']);

        // Filtro por cliente
        if ($request->filled('cliente_id')) {
            $query->where('usuario_id', $request->cliente_id);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Búsqueda general
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->whereHas('usuario', function($query) use ($buscar) {
                    $query->where('nombre', 'LIKE', "%{$buscar}%")
                          ->orWhere('apellido', 'LIKE', "%{$buscar}%")
                          ->orWhere('email', 'LIKE', "%{$buscar}%");
                })
                ->orWhereHas('detalles.perfumeVariante.perfume', function($query) use ($buscar) {
                    $query->where('nombre', 'LIKE', "%{$buscar}%")
                          ->orWhere('marca', 'LIKE', "%{$buscar}%");
                });
            });
        }

        $ventas = $query->recientes()->paginate(20);
        $clientes = Usuario::where('rol', 'Cliente')->orderBy('nombre')->get();

        return view('ventas.index', compact('ventas', 'clientes'));
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
     * Update the status of the sale
     */
    public function updateStatus(Request $request, Venta $venta)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,completada,cancelada'
        ]);

        DB::transaction(function() use ($request, $venta) {
            $estadoAnterior = $venta->estado;
            $venta->estado = $request->estado;
            $venta->save();

            // Si se cancela la venta, devolver el stock
            if ($request->estado === 'cancelada' && $estadoAnterior !== 'cancelada') {
                foreach ($venta->detalles as $detalle) {
                    $variante = $detalle->perfumeVariante;
                    $variante->stock += $detalle->cantidad;
                    $variante->save();
                }
            }
            // Si se reactiva una venta cancelada, volver a descontar el stock
            elseif ($estadoAnterior === 'cancelada' && $request->estado !== 'cancelada') {
                foreach ($venta->detalles as $detalle) {
                    $variante = $detalle->perfumeVariante;
                    $variante->stock -= $detalle->cantidad;
                    $variante->save();
                }
            }
        });

        return redirect()->back()->with('success', 'Estado de la venta actualizado correctamente');
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