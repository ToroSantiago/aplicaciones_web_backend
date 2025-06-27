<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\PerfumeVariante;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VentaApiController extends Controller
{
    /**
     * Crear una nueva venta (mejorado del método compra existente)
     * POST /api/ventas
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente' => 'required|array',
            'cliente.email' => 'required|email',
            'cliente.nombre' => 'required|string',
            'cliente.apellido' => 'required|string',
            'cliente.password' => 'sometimes|string|min:8', // Solo si es nuevo cliente
            'items' => 'required|array|min:1',
            'items.*.perfume_id' => 'required|integer',
            'items.*.volumen' => 'required|integer|in:75,100,200',
            'items.*.cantidad' => 'required|integer|min:1',
            'metodo_pago' => 'nullable|string',
            'observaciones' => 'nullable|string'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // Buscar o crear el cliente
                $cliente = Usuario::where('email', $request->cliente['email'])->first();
                
                if (!$cliente) {
                    // Crear nuevo cliente
                    $cliente = Usuario::create([
                        'email' => $request->cliente['email'],
                        'username' => $request->cliente['email'],
                        'nombre' => $request->cliente['nombre'],
                        'apellido' => $request->cliente['apellido'],
                        'password' => Hash::make($request->cliente['password'] ?? 'password123'),
                        'rol' => 'Cliente'
                    ]);
                }

                // Calcular total y validar stock
                $total = 0;
                $detallesVenta = [];

                foreach ($request->items as $item) {
                    $variante = PerfumeVariante::where('perfume_id', $item['perfume_id'])
                                               ->where('volumen', $item['volumen'])
                                               ->first();

                    if (!$variante) {
                        throw new \Exception("No se encontró la variante de {$item['volumen']}ml para el perfume ID {$item['perfume_id']}");
                    }

                    if ($variante->stock < $item['cantidad']) {
                        $perfume = $variante->perfume;
                        throw new \Exception("Stock insuficiente para {$perfume->nombre} {$item['volumen']}ml. Stock disponible: {$variante->stock}");
                    }

                    $subtotal = $variante->precio * $item['cantidad'];
                    $total += $subtotal;

                    $detallesVenta[] = [
                        'variante' => $variante,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $variante->precio,
                        'subtotal' => $subtotal
                    ];
                }

                // Crear la venta
                $venta = Venta::create([
                    'usuario_id' => $cliente->id,
                    'total' => $total,
                    'estado' => 'completada',
                    'metodo_pago' => $request->metodo_pago,
                    'observaciones' => $request->observaciones
                ]);

                // Crear los detalles y actualizar stock
                foreach ($detallesVenta as $detalle) {
                    // Crear detalle de venta
                    VentaDetalle::create([
                        'venta_id' => $venta->id,
                        'perfume_variante_id' => $detalle['variante']->id,
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'subtotal' => $detalle['subtotal']
                    ]);

                    // Actualizar stock
                    $detalle['variante']->decrement('stock', $detalle['cantidad']);
                }

                // Cargar relaciones para la respuesta
                $venta->load(['usuario', 'detalles.perfumeVariante.perfume']);

                return response()->json([
                    'success' => true,
                    'message' => 'Venta realizada con éxito',
                    'data' => [
                        'venta_id' => $venta->id,
                        'total' => $venta->total,
                        'fecha' => $venta->created_at->format('Y-m-d H:i:s'),
                        'cliente' => [
                            'id' => $cliente->id,
                            'nombre' => $cliente->nombre . ' ' . $cliente->apellido,
                            'email' => $cliente->email
                        ],
                        'items' => $venta->detalles->map(function ($detalle) {
                            return [
                                'perfume' => $detalle->perfumeVariante->perfume->nombre,
                                'marca' => $detalle->perfumeVariante->perfume->marca,
                                'volumen' => $detalle->perfumeVariante->volumen,
                                'cantidad' => $detalle->cantidad,
                                'precio_unitario' => $detalle->precio_unitario,
                                'subtotal' => $detalle->subtotal
                            ];
                        })
                    ]
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la venta',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obtener historial de ventas de un cliente por email
     * GET /api/ventas/cliente/{email}
     */
    public function ventasPorCliente($email)
    {
        try {
            $cliente = Usuario::where('email', $email)->first();

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            $ventas = Venta::with(['detalles.perfumeVariante.perfume'])
                          ->where('usuario_id', $cliente->id)
                          ->recientes()
                          ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'cliente' => [
                        'id' => $cliente->id,
                        'nombre' => $cliente->nombre . ' ' . $cliente->apellido,
                        'email' => $cliente->email
                    ],
                    'ventas' => $ventas->map(function ($venta) {
                        return [
                            'id' => $venta->id,
                            'fecha' => $venta->created_at->format('Y-m-d H:i:s'),
                            'total' => $venta->total,
                            'estado' => $venta->estado,
                            'metodo_pago' => $venta->metodo_pago,
                            'cantidad_items' => $venta->cantidad_total_items,
                            'items' => $venta->detalles->map(function ($detalle) {
                                return [
                                    'perfume' => $detalle->perfumeVariante->perfume->nombre,
                                    'marca' => $detalle->perfumeVariante->perfume->marca,
                                    'volumen' => $detalle->perfumeVariante->volumen,
                                    'cantidad' => $detalle->cantidad,
                                    'precio_unitario' => $detalle->precio_unitario,
                                    'subtotal' => $detalle->subtotal
                                ];
                            })
                        ];
                    })
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el historial de ventas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener detalle de una venta específica
     * GET /api/ventas/{id}
     */
    public function show($id)
    {
        try {
            $venta = Venta::with(['usuario', 'detalles.perfumeVariante.perfume'])
                          ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $venta->id,
                    'fecha' => $venta->created_at->format('Y-m-d H:i:s'),
                    'total' => $venta->total,
                    'estado' => $venta->estado,
                    'metodo_pago' => $venta->metodo_pago,
                    'observaciones' => $venta->observaciones,
                    'cliente' => [
                        'id' => $venta->usuario->id,
                        'nombre' => $venta->cliente_nombre_completo,
                        'email' => $venta->usuario->email
                    ],
                    'items' => $venta->detalles->map(function ($detalle) {
                        return [
                            'id' => $detalle->id,
                            'perfume' => $detalle->perfumeVariante->perfume->nombre,
                            'marca' => $detalle->perfumeVariante->perfume->marca,
                            'imagen' => $detalle->perfumeVariante->perfume->imagen_url,
                            'volumen' => $detalle->perfumeVariante->volumen,
                            'cantidad' => $detalle->cantidad,
                            'precio_unitario' => $detalle->precio_unitario,
                            'subtotal' => $detalle->subtotal
                        ];
                    })
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Venta no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la venta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método de compatibilidad con el endpoint existente de compra
     * POST /api/perfumes/compra
     */
    public function compraLegacy(Request $request)
    {
        // Transformar el request al formato del nuevo método
        $nuevoRequest = new Request([
            'cliente' => [
                'email' => $request->email ?? 'cliente@example.com',
                'nombre' => $request->nombre ?? 'Cliente',
                'apellido' => $request->apellido ?? 'Anónimo'
            ],
            'items' => $request->items,
            'metodo_pago' => $request->metodo_pago ?? 'efectivo'
        ]);

        return $this->store($nuevoRequest);
    }
}