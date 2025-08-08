<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\SupplierWhatsapp;

class TwilioController extends Controller
{
    public function sendWhatsApp(Request $request)
    {
        $request->validate([
            'receiptId'       => 'required|string',
            'tranid'          => 'nullable|string',
            'proveedorId'     => 'required|string',
            'proveedor'       => 'required|string',
            'fecha'           => 'required|string', // viene como dd/mm/YYYY
            'numeroWhatsapp'  => 'required|string', // ejemplo: +5214434797316
            
        ]);

        

        $orderNumber ="PRUE";
        $telefono = $request->numeroWhatsapp;
        $reception_id = $request->receiptId;
        $external_supplier_id = $request->proveedorId;
        $supplier_name = $request->proveedor;

        SupplierWhatsapp::create([
            'reception_id'         => $reception_id,
            'external_supplier_id' => $external_supplier_id,
            // 'supplier_name'        => $supplier_name,
            // 'date'                 => $fechaDb,
            'phone'                => $telefono,
        ]);

        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
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



