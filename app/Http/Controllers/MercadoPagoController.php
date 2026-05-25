<?php

namespace App\Http\Controllers;

use App\Models\PerfumeVariante;
use App\Models\Usuario;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoController extends Controller
{
    /**
     * Crea la preferencia de pago en MP y deja la Venta en estado 'pendiente'.
     *
     * IMPORTANTE: ya NO descontamos stock acá. La fuente de verdad pasa a ser
     * el webhook (handleWebhook). Si el cliente abandona el pago, la venta
     * queda como 'pendiente' y el stock nunca se ve afectado. Cuando MP nos
     * confirma el pago aprobado, recién ahí descontamos.
     */
    public function createPaymentPreference(Request $request)
    {
        Log::info('=== INICIO CREACIÓN PREFERENCIA DE PAGO ===');
        Log::info('Request recibido: ' . json_encode($request->all()));

        try {
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|integer',           // ID de la variante
                'items.*.title' => 'required|string',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0', // ignorado: recalculamos server-side
                'payer.name' => 'required|string',
                'payer.surname' => 'required|string',
                'payer.email' => 'required|email',
            ]);

            $payer = $request->input('payer');
            $itemsRequest = $request->input('items');

            // Si el cliente vino con Bearer token, usamos ese usuario como
            // dueño de la venta — ignoramos el payer.email para la asignación.
            // El payer (name/surname/email) sigue siendo lo que mandamos a MP,
            // pero NO determina a quién se le acredita la compra.
            // Esto cierra el agujero de que cualquiera pueda asignar una venta
            // a otra persona simplemente mandando su email.
            $authUser = $request->user('sanctum');

            $externalReference = $this->generateExternalReference();

            // Toda la creación (validar stock + cliente + venta + detalles) en
            // una sola transacción. Si MP falla al crear la preferencia, hacemos
            // rollback y la venta nunca queda huérfana.
            [$venta, $itemsServidor] = DB::transaction(function () use ($itemsRequest, $payer, $authUser, $externalReference) {
                // 1. Validar stock disponible (sin descontar). Recalculamos
                //    precio_final server-side; nunca confiamos en unit_price del cliente.
                $itemsServidor = [];
                $total = 0;

                foreach ($itemsRequest as $item) {
                    $variante = PerfumeVariante::with('descuentos')->find($item['id']);

                    if (! $variante) {
                        throw new Exception("No se encontró la variante con ID {$item['id']}");
                    }

                    if ($variante->stock < $item['quantity']) {
                        throw new Exception("Stock insuficiente para {$item['title']}. Stock disponible: {$variante->stock}");
                    }

                    $precioUnitario = $variante->precio_final;
                    $subtotal = $precioUnitario * $item['quantity'];
                    $total += $subtotal;

                    $itemsServidor[] = [
                        'variante'        => $variante,
                        'cantidad'        => (int) $item['quantity'],
                        'precio_unitario' => $precioUnitario,
                        'subtotal'        => $subtotal,
                        'title'           => $item['title'],
                    ];
                }

                // 2. Resolver cliente.
                //    - Si hay usuario autenticado por Sanctum, ese es el dueño.
                //    - Si no (checkout anónimo), caemos al método legacy que
                //      busca por email del payer y, si no existe, lo crea.
                $cliente = $authUser ?? $this->resolveOrCreateCliente($payer);

                // 3. Crear Venta pendiente + Detalles
                $venta = Venta::create([
                    'usuario_id'         => $cliente->id,
                    'total'              => $total,
                    'estado'             => 'pendiente',
                    'metodo_pago'        => 'mercadopago',
                    'external_reference' => $externalReference,
                ]);

                foreach ($itemsServidor as $item) {
                    VentaDetalle::create([
                        'venta_id'            => $venta->id,
                        'perfume_variante_id' => $item['variante']->id,
                        'cantidad'            => $item['cantidad'],
                        'precio_unitario'     => $item['precio_unitario'],
                        'subtotal'            => $item['subtotal'],
                    ]);
                }

                return [$venta, $itemsServidor];
            });

            // 4. Crear preferencia en MP (fuera del lock de DB; si falla, no
            //    es crítico que la venta exista — la podemos cancelar después).
            $this->authenticate();
            $requestData = $this->buildPreferenceRequest($itemsServidor, $payer, $externalReference);
            Log::info('Payload enviado a MP: ' . json_encode($requestData));

            $client = new PreferenceClient();
            $preference = $client->create($requestData);

            Log::info("Preferencia MP creada: {$preference->id} (venta #{$venta->id}, ref {$externalReference})");

            return response()->json([
                'success'             => true,
                'id'                  => $preference->id,
                'init_point'          => $preference->init_point,
                'sandbox_init_point'  => $preference->sandbox_init_point,
                'external_reference'  => $externalReference,
                'venta_id'            => $venta->id,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'error'   => 'Datos de validación incorrectos',
                'details' => $e->errors(),
            ], 422);
        } catch (MPApiException $e) {
            Log::error('Error de MP API: ' . $e->getMessage());
            if ($e->getApiResponse()) {
                Log::error('Respuesta API: ' . json_encode($e->getApiResponse()->getContent()));
            }
            // Si la venta ya se creó pero MP rechazó la preferencia, la
            // cancelamos para no dejar registros huérfanos pendientes.
            if (isset($venta)) {
                $venta->update([
                    'estado' => 'cancelada',
                    'observaciones' => '[MP rechazó la creación de preferencia: ' . $e->getMessage() . ']',
                ]);
            }
            return response()->json([
                'success' => false,
                'error'   => 'Error en la API de MercadoPago',
                'details' => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            Log::error('Error general: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Webhook de MercadoPago.
     *
     * MP nos pega acá cada vez que cambia el estado de un pago. Acá decidimos
     * si descontar stock y completar la venta, o cancelarla.
     *
     * Debe ser idempotente: MP puede mandar la misma notificación varias veces.
     * Y debe devolver 200 incluso ante errores no recuperables, para que MP
     * no reintente para siempre. Solo devolvemos 5xx cuando MP debería reintentar.
     */
    public function webhook(Request $request)
    {
        Log::info('Webhook MP recibido: ' . json_encode($request->all()));

        try {
            // MP manda el id del pago en distintos campos según la versión:
            //   - body: { type: 'payment', data: { id: '123' } }
            //   - query: ?type=payment&data.id=123  o  ?topic=payment&id=123
            $type     = $request->input('type', $request->query('type', $request->query('topic')));
            $paymentId = $request->input('data.id', $request->query('data.id', $request->query('id')));

            if ($type !== 'payment' || ! $paymentId) {
                Log::info("Webhook MP ignorado (type={$type}, paymentId={$paymentId})");
                return response()->json(['status' => 'ignored'], 200);
            }

            // Consultar el pago a MP para conocer su estado real (no confiar
            // en lo que llega en el body por seguridad).
            $this->authenticate();
            $payment = (new PaymentClient())->get($paymentId);

            $externalReference = $payment->external_reference ?? null;
            $status            = $payment->status ?? null;

            Log::info("Pago MP {$paymentId}: status={$status}, external_reference={$externalReference}");

            if (! $externalReference) {
                Log::warning("Webhook MP: pago {$paymentId} sin external_reference");
                return response()->json(['status' => 'no_ref'], 200);
            }

            $venta = Venta::where('external_reference', $externalReference)->first();
            if (! $venta) {
                Log::warning("Webhook MP: no se encontró venta para ref {$externalReference}");
                return response()->json(['status' => 'venta_not_found'], 200);
            }

            // Idempotencia: si la venta ya está cerrada (completada o cancelada),
            // no procesamos de nuevo. Cualquier cambio posterior lo hace un admin.
            if (in_array($venta->estado, ['completada', 'cancelada'], true)) {
                Log::info("Webhook MP: venta #{$venta->id} ya está en estado '{$venta->estado}' — skip");
                return response()->json(['status' => 'already_processed'], 200);
            }

            if ($status === 'approved') {
                $this->confirmarVenta($venta);
            } elseif (in_array($status, ['rejected', 'cancelled', 'refunded', 'charged_back'], true)) {
                $venta->update(['estado' => 'cancelada']);
                Log::info("Webhook MP: venta #{$venta->id} marcada como cancelada (status={$status})");
            } else {
                // in_process, pending, authorized → mantener pendiente.
                Log::info("Webhook MP: venta #{$venta->id} queda pendiente (status={$status})");
            }

            return response()->json(['status' => 'ok'], 200);
        } catch (Exception $e) {
            Log::error('Error procesando webhook MP: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            // Devolvemos 200 igual para no entrar en loops de retry de MP.
            // El error queda en logs para investigar.
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }

    /**
     * Aplica el pago aprobado: revalida stock con lock pesimista, descuenta,
     * y marca la venta como completada. Si entre que se creó la preferencia y
     * el pago llegó, otro cliente compró el último frasco, dejamos la venta
     * como 'cancelada' con una observación para que el admin atienda al cliente.
     */
    private function confirmarVenta(Venta $venta): void
    {
        DB::transaction(function () use ($venta) {
            $venta->load('detalles');

            // Lockear las variantes para evitar race conditions con otras compras
            // concurrentes.
            $varianteIds = $venta->detalles->pluck('perfume_variante_id')->all();
            $variantes = PerfumeVariante::whereIn('id', $varianteIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // Revalidar stock
            foreach ($venta->detalles as $detalle) {
                $variante = $variantes[$detalle->perfume_variante_id] ?? null;
                if (! $variante || $variante->stock < $detalle->cantidad) {
                    $obs = "[Pago APROBADO pero stock insuficiente al confirmar " .
                           "(variante {$detalle->perfume_variante_id}, requiere {$detalle->cantidad}, " .
                           "disponible " . ($variante->stock ?? 0) . "). " .
                           "REVISAR Y CONTACTAR AL CLIENTE.]";
                    $venta->update([
                        'estado'        => 'cancelada',
                        'observaciones' => trim(($venta->observaciones ?? '') . "\n" . $obs),
                    ]);
                    Log::warning("Venta #{$venta->id}: stock insuficiente al confirmar pago MP — cancelada");
                    return;
                }
            }

            // Todo OK, descontar y marcar completada
            foreach ($venta->detalles as $detalle) {
                $variantes[$detalle->perfume_variante_id]->decrement('stock', $detalle->cantidad);
            }

            $venta->update(['estado' => 'completada']);
            Log::info("Venta #{$venta->id} completada (pago MP confirmado)");
        });
    }

    /**
     * Callback que ve el usuario al volver desde MP tras pago exitoso.
     * El descuento de stock NO ocurre acá — el webhook es el responsable.
     * Esta ruta solo redirige al frontend.
     */
    public function success(Request $request)
    {
        Log::info('Redirect MP success: ' . json_encode($request->all()));
        $frontendUrl = env('MP_SUCCESS_URL', 'https://essenzaroyalefrontend.vercel.app/success');
        return redirect($frontendUrl . '?' . http_build_query($request->all()));
    }

    /**
     * Callback de pago fallido. Tampoco toca stock (no había que devolver nada).
     */
    public function failed(Request $request)
    {
        Log::info('Redirect MP failed: ' . json_encode($request->all()));
        $frontendUrl = env('MP_FAILURE_URL', 'https://essenzaroyalefrontend.vercel.app/failed');
        return redirect($frontendUrl . '?' . http_build_query($request->all()));
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    protected function authenticate(): void
    {
        $mpAccessToken = config('services.mercadopago.token');
        if (! $mpAccessToken) {
            throw new Exception("El token de acceso de Mercado Pago no está configurado.");
        }

        MercadoPagoConfig::setAccessToken($mpAccessToken);
    }

    /**
     * Genera un external_reference único para vincular Venta ↔ pago MP.
     */
    private function generateExternalReference(): string
    {
        return 'ORDER_' . now()->format('YmdHis') . '_' . Str::random(8);
    }

    /**
     * Busca un Usuario (cliente) por email. Si no existe, lo crea con una
     * password aleatoria (no se la damos al cliente desde acá; podrá usar
     * el flujo de "olvidé mi contraseña" si quiere acceder al SPA).
     */
    private function resolveOrCreateCliente(array $payer): Usuario
    {
        $cliente = Usuario::where('email', $payer['email'])->first();
        if ($cliente) {
            return $cliente;
        }

        return Usuario::create([
            'email'    => $payer['email'],
            'username' => $payer['email'],
            'nombre'   => $payer['name'],
            'apellido' => $payer['surname'],
            // Password aleatoria — el comprador no la conoce. Mejor que el
            // anterior 'password123' fijo, que era un backdoor.
            'password' => Hash::make(Str::random(32)),
            'rol'      => Usuario::ROL_CLIENTE,
        ]);
    }

    /**
     * Arma el payload que mandamos a MP para crear la preferencia.
     */
    protected function buildPreferenceRequest(array $itemsServidor, array $payer, string $externalReference): array
    {
        $formattedItems = [];
        foreach ($itemsServidor as $item) {
            $formattedItems[] = [
                'id'          => (string) $item['variante']->id,
                'title'       => $item['title'],
                'quantity'    => (int) $item['cantidad'],
                'unit_price'  => (float) $item['precio_unitario'], // server-side
                'currency_id' => 'ARS',
            ];
        }

        return [
            'items'                => $formattedItems,
            'payer' => [
                'name'    => $payer['name'],
                'surname' => $payer['surname'],
                'email'   => $payer['email'],
            ],
            'payment_methods' => [
                'excluded_payment_methods' => [],
                'excluded_payment_types'   => [],
                'installments'             => 12,
            ],
            'back_urls' => [
                'success' => config('services.mercadopago.success_url'),
                'failure' => config('services.mercadopago.failure_url'),
                'pending' => config('services.mercadopago.failure_url'),
            ],
            // MP requiere HTTPS y URL accesible públicamente. En local no
            // funciona — testear vía deploy o ngrok.
            'notification_url'     => config('services.mercadopago.webhook_url')
                                       ?? route('mercadopago.webhook'),
            'statement_descriptor' => 'ESSENZA ROYALE',
            'external_reference'   => $externalReference,
            'expires'              => false,
            'auto_return'          => 'approved',
        ];
    }
}
