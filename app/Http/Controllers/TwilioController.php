<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class TwilioController extends Controller
{
    public function sendWhatsApp(Request $request)
    {
        $request->validate([
            'telefono' => 'required|string',
            'order_number' => 'required|string',
        ]);

        $telefono = $request->telefono;
        $orderNumber = $request->order_number;

        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_WHATSAPP_FROM');
        $templateSid = env('TWILIO_TEMPLATE_SID');



        $client = new Client($sid, $token);

        try {
            $message = $client->messages->create(
                "whatsapp:$telefono",
                [
                    'from' => $from,
                    'contentSid' => $templateSid,
                    'contentVariables' => json_encode([
                        '1' => $orderNumber,
                    ]),
                ]
            );

            return response()->json([
                'success' => true,
                'sid' => $message->sid,
                'message' => 'Mensaje enviado correctamente',
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Error al enviar WhatsApp: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

