<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Exception;
use Illuminate\Support\Facades\Log;

class MercadoPagoController extends Controller
{
    public function createPaymentPreference(Request $request)
    {
        Log::info('=== INICIO CREACIÓN PREFERENCIA DE PAGO ===');
        Log::info('Request recibido: ' . json_encode($request->all()));

        try {
            // Configurar MercadoPago
            $this->authenticate();
            Log::info('Autenticado con éxito');

            // Validar datos del request
            $request->validate([
                'items' => 'required|array|min:1',
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

            // Crear la solicitud de preferencia
            $requestData = $this->createPreferenceRequest($items, $payer);

            Log::info('Payload enviado a MP: ' . json_encode($requestData));

            $client = new PreferenceClient();
            $preference = $client->create($requestData);

            Log::info('Preferencia creada exitosamente: ' . $preference->id);

            return response()->json([
                'success' => true,
                'id' => $preference->id,
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'error' => 'Datos de validación incorrectos',
                'details' => $e->errors()
            ], 422);
        } catch (MPApiException $error) {
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
            Log::error('Error general: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function success(Request $request)
    {
        Log::info('Pago exitoso: ' . json_encode($request->all()));

        // Aquí puedes redirigir al frontend con los parámetros
        $frontendUrl = env('MP_SUCCESS_URL', 'https://essenzaroyalefrontend.vercel.app/success');
        return redirect($frontendUrl . '?' . http_build_query($request->all()));
    }

    public function failed(Request $request)
    {
        Log::info('Pago fallido: ' . json_encode($request->all()));

        // Aquí puedes redirigir al frontend con los parámetros
        $frontendUrl = env('MP_FAILURE_URL', 'https://essenzaroyalefrontend.vercel.app/failed');
        return redirect($frontendUrl . '?' . http_build_query($request->all()));
    }

    public function webhook(Request $request)
    {
        Log::info('Webhook recibido: ' . json_encode($request->all()));

        // Aquí procesarías las notificaciones de MercadoPago
        // Por ejemplo, actualizar el estado de la orden en tu base de datos

        return response()->json(['status' => 'ok']);
    }

    // Autenticación con Mercado Pago
    protected function authenticate()
    {
        $mpAccessToken = config('services.mercadopago.token');
        if (!$mpAccessToken) {
            throw new Exception("El token de acceso de Mercado Pago no está configurado.");
        }
        
        // Configurar el SDK v3
        MercadoPagoConfig::setAccessToken($mpAccessToken);
        
        // Opcional: configurar entorno (sandbox/production)
        // MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
        
        Log::info('Token configurado correctamente');
    }

    // Función para crear la estructura de preferencia
    protected function createPreferenceRequest($items, $payer): array
    {
        // Preparar items con el formato correcto
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = [
                'title' => $item['title'],
                'quantity' => (int) $item['quantity'],
                'unit_price' => (float) $item['unit_price'],
                'currency_id' => 'ARS', // Moneda Argentina
            ];
        }

        // Configurar métodos de pago
        $paymentMethods = [
            "excluded_payment_methods" => [],
            "excluded_payment_types" => [],
            "installments" => 12,
        ];

        // URLs de retorno
        $backUrls = [
            'success' => config('services.mercadopago.success_url'),
            'failure' => config('services.mercadopago.failure_url'),
            'pending' => config('services.mercadopago.failure_url'), // Opcional
        ];

        // Referencia externa única
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