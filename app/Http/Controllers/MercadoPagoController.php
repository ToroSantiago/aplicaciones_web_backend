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
        Log::info('Creando preferencia de pago');
        $this->authenticate();
        Log::info('Autenticado con éxito');

        try {
            // Validar datos del request
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.id' => 'required',
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

            Log::info('Payload enviado a MP', $requestData);

            // Crear la preferencia
            $client = new PreferenceClient();
            $preference = $client->create($requestData);

            return response()->json([
                'success' => true,
                'id' => $preference->id,
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Datos de validación incorrectos',
                'details' => $e->errors()
            ], 422);
        } catch (MPApiException $error) {
            Log::error('Error de MercadoPago API: ' . $error->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error en la API de MercadoPago',
                'details' => $error->getApiResponse()->getContent(),
            ], 500);
        } catch (Exception $e) {
            Log::error('Error general: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function success(Request $request)
    {
        Log::info('Pago exitoso', $request->all());

        return view('mercadopago.success', [
            'payment_id' => $request->get('payment_id'),
            'status' => $request->get('status'),
            'external_reference' => $request->get('external_reference'),
        ]);
    }

    public function failed(Request $request)
    {
        Log::info('Pago fallido', $request->all());

        return view('mercadopago.failed', [
            'payment_id' => $request->get('payment_id'),
            'status' => $request->get('status'),
            'external_reference' => $request->get('external_reference'),
        ]);
    }

    public function webhook(Request $request)
    {
        Log::info('Webhook recibido', $request->all());

        // Procesar notificaciones de MercadoPago
        // Aquí es donde actualizarías el estado de la orden

        return response()->json(['status' => 'ok']);
    }

    // Autenticación con Mercado Pago
    protected function authenticate()
    {
        $mpAccessToken = config('services.mercadopago.token');
        if (!$mpAccessToken) {
            throw new Exception("El token de acceso de Mercado Pago no está configurado.");
        }
        MercadoPagoConfig::setAccessToken($mpAccessToken);
    }

    // Función para crear la estructura de preferencia
    protected function createPreferenceRequest($items, $payer): array
    {
        foreach ($items as &$item) {
            unset($item['id']);
            $item['currency_id'] = 'ARS';
        }

        $paymentMethods = [
            "excluded_payment_methods" => [],
            "installments" => 12,
            "default_installments" => 1
        ];

        // Usar URLs configuradas en el .env
        $backUrls = [
            'success' => config('services.mercadopago.success_url'),
            'failure' => config('services.mercadopago.failure_url')
        ];

        $externalReference = 'ORDER_' . time() . '_' . uniqid();

        return [
            "items" => $items,
            "payer" => $payer,
            "payment_methods" => $paymentMethods,
            "back_urls" => $backUrls,
            "statement_descriptor" => "TIENDAONLINE",
            "external_reference" => $externalReference,
            "expires" => false,
            "auto_return" => 'approved',
        ];
    }
}
