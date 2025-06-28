<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\PerfumeVariante;
use Illuminate\Support\Facades\DB;

class MercadoPagoController extends Controller
{
    public function createPaymentPreference(Request $request)
    {
        Log::info('=== INICIO CREACIÓN PREFERENCIA DE PAGO ===');
        Log::info('Request recibido: ' . json_encode($request->all()));

        // Iniciar transacción para asegurar consistencia
        DB::beginTransaction();

        try {
            // Configurar MercadoPago
            $this->authenticate();
            Log::info('Autenticado con éxito');

            // Validar datos del request
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|integer', // ID de la variante
                'items.*.title' => 'required|string',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'payer.name' => 'required|string',
                'payer.surname' => 'required|string',
                'payer.email' => 'required|email',
            ]);

            // Obtener items y payer del request
            $items = $request->input('items');
            $payer = $request->input('payer');

            // Verificar stock antes de crear la preferencia
            $stockValidation = $this->validateAndReserveStock($items);
            if (!$stockValidation['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => $stockValidation['message']
                ], 400);
            }

            // Crear la solicitud de preferencia
            $requestData = $this->createPreferenceRequest($items, $payer);

            Log::info('Payload enviado a MP: ' . json_encode($requestData));

            $client = new PreferenceClient();
            $preference = $client->create($requestData);

            Log::info('Preferencia creada exitosamente: ' . $preference->id);

            // Si todo salió bien, confirmar la transacción
            DB::commit();

            // Guardar la referencia externa para poder restaurar el stock si falla el pago
            $this->saveOrderReference($requestData['external_reference'], $items);

            return response()->json([
                'success' => true,
                'id' => $preference->id,
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point,
                'external_reference' => $requestData['external_reference']
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'error' => 'Datos de validación incorrectos',
                'details' => $e->errors()
            ], 422);
        } catch (MPApiException $error) {
            DB::rollBack();
            Log::error('Error de MercadoPago API: ' . $error->getMessage());
            if ($error->getApiResponse()) {
                Log::error('Respuesta API: ' . json_encode($error->getApiResponse()->getContent()));
            }
            return response()->json([
                'success' => false,
                'error' => 'Error en la API de MercadoPago',
                'details' => $error->getMessage(),
            ], 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error general: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validar y reservar stock
     */
    private function validateAndReserveStock($items)
    {
        try {
            foreach ($items as $item) {
                // El ID que viene del frontend es el variante_id
                $variante = PerfumeVariante::find($item['id']);
                
                if (!$variante) {
                    return [
                        'success' => false,
                        'message' => "No se encontró la variante con ID {$item['id']}"
                    ];
                }

                // Verificar si hay suficiente stock
                if ($variante->stock < $item['quantity']) {
                    return [
                        'success' => false,
                        'message' => "Stock insuficiente para {$item['title']}. Stock disponible: {$variante->stock}"
                    ];
                }

                // Descontar el stock
                $variante->stock -= $item['quantity'];
                $variante->save();

                Log::info("Stock actualizado para variante {$variante->id}: nuevo stock = {$variante->stock}");
            }

            return ['success' => true];

        } catch (Exception $e) {
            Log::error('Error al validar/reservar stock: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al procesar el stock'
            ];
        }
    }

    /**
     * Guardar referencia de la orden para poder restaurar stock si falla
     */
    private function saveOrderReference($externalReference, $items)
    {
        // Aquí podrías guardar en una tabla temporal o en caché
        // Para este ejemplo, solo lo loguearemos
        Log::info('Orden creada con referencia: ' . $externalReference);
        Log::info('Items de la orden: ' . json_encode($items));
        
        // Si tienes Redis o una tabla de órdenes temporales, guarda aquí
        // Cache::put('order_' . $externalReference, $items, 3600); // 1 hora
    }

    public function success(Request $request)
    {
        Log::info('Pago exitoso: ' . json_encode($request->all()));

        // Aquí puedes confirmar la venta en tu base de datos
        $externalReference = $request->get('external_reference');
        if ($externalReference) {
            Log::info('Confirmando venta para referencia: ' . $externalReference);
            // Aquí podrías marcar la orden como completada
        }

        // Redirigir al frontend
        $frontendUrl = env('MP_SUCCESS_URL', 'https://essenzaroyalefrontend.vercel.app/success');
        return redirect($frontendUrl . '?' . http_build_query($request->all()));
    }

    public function failed(Request $request)
    {
        Log::info('Pago fallido: ' . json_encode($request->all()));

        // IMPORTANTE: Restaurar el stock cuando falla el pago
        $externalReference = $request->get('external_reference');
        if ($externalReference) {
            Log::info('Intentando restaurar stock para referencia: ' . $externalReference);
            // Aquí deberías implementar la lógica para restaurar el stock
            // $this->restoreStock($externalReference);
        }

        // Redirigir al frontend
        $frontendUrl = env('MP_FAILURE_URL', 'https://essenzaroyalefrontend.vercel.app/failed');
        return redirect($frontendUrl . '?' . http_build_query($request->all()));
    }

    public function webhook(Request $request)
    {
        Log::info('Webhook recibido: ' . json_encode($request->all()));

        try {
            // Verificar el tipo de notificación
            $type = $request->get('type');
            $data = $request->get('data');

            if ($type === 'payment' && isset($data['id'])) {
                // Aquí podrías verificar el estado del pago
                // y actualizar tu base de datos acordemente
                Log::info('Notificación de pago recibida: ' . $data['id']);
            }

            return response()->json(['status' => 'ok']);
        } catch (Exception $e) {
            Log::error('Error procesando webhook: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    // ... resto de los métodos (authenticate, createPreferenceRequest) sin cambios
    
    protected function authenticate()
    {
        $mpAccessToken = config('services.mercadopago.token');
        if (!$mpAccessToken) {
            throw new Exception("El token de acceso de Mercado Pago no está configurado.");
        }
        
        MercadoPagoConfig::setAccessToken($mpAccessToken);
        Log::info('Token configurado correctamente');
    }

    protected function createPreferenceRequest($items, $payer): array
    {
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = [
                'id' => (string)$item['id'], // Mantener el ID para referencia
                'title' => $item['title'],
                'quantity' => (int) $item['quantity'],
                'unit_price' => (float) $item['unit_price'],
                'currency_id' => 'ARS',
            ];
        }

        $paymentMethods = [
            "excluded_payment_methods" => [],
            "excluded_payment_types" => [],
            "installments" => 12,
        ];

        $backUrls = [
            'success' => config('services.mercadopago.success_url'),
            'failure' => config('services.mercadopago.failure_url'),
            'pending' => config('services.mercadopago.failure_url'),
        ];

        $externalReference = 'ORDER_' . time() . '_' . uniqid();

        return [
            "items" => $formattedItems,
            "payer" => [
                "name" => $payer['name'],
                "surname" => $payer['surname'],
                "email" => $payer['email'],
            ],
            "payment_methods" => $paymentMethods,
            "back_urls" => $backUrls,
            "statement_descriptor" => "ESSENZA ROYALE",
            "external_reference" => $externalReference,
            "expires" => false,
            "auto_return" => 'approved',
        ];
    }
}