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

        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');
        $templateSid = config('services.twilio.template_sid');



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

