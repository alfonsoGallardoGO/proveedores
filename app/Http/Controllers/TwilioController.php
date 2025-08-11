<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\SupplierWhatsapp;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TwilioController extends Controller
{
    public function sendWhatsApp(Request $request)
    {


        $request->validate([
            'receiptId'       => 'required|string',
            'tranid'          => 'nullable|string',
            'proveedorId'     => 'required|string',
            'proveedor'       => 'required|string',
            'fecha'           => 'required|string',
            'numeroWhatsapp'  => 'required|string',
            
        ]);

        $orderNumber ="PRUE";
        $telefono = $request->numeroWhatsapp;

        $fechaDb = \Carbon\Carbon::createFromFormat('d/m/Y', $request->fecha)->format('Y-m-d');


        $pdfRuta = null;
        $pdfBase64 = null;

        if ($request->filled('pdfBase64')) {
            $base64 = $request->pdfBase64;

            // Si viene con encabezado data URI lo quitamos
            if (str_starts_with($base64, 'data:')) {
                $base64 = preg_replace('/^data:application\/pdf;base64,/', '', $base64);
            }
            $base64 = str_replace(' ', '+', $base64);

            $binary = base64_decode($base64, true);

            if ($binary !== false) {
                // Guardar en storage/public/whatsapp/receipts
                $filename = 'receipt_'.Str::uuid().'.pdf';
                $path = "whatsapp/receipts/{$filename}";
                Storage::disk('public')->makeDirectory('whatsapp/receipts');
                Storage::disk('public')->put($path, $binary);

                $pdfRuta = $path;
                $pdfBase64 = $base64;
            }
        }


        SupplierWhatsapp::create([
            'reception_id'         => $request->receiptId,
            'external_supplier_id' => $request->proveedorId,
            'supplier_name'        => $request->proveedor,
            'date'                 => $fechaDb,
            'phone'                => $request->numeroWhatsapp,
            'pdf_base_64'          => null,
            'pdf_rute'             => null,
        ]);

        // $sid = env('TWILIO_SID');
        // $token = env('TWILIO_TOKEN');
        // $from = env('TWILIO_WHATSAPP_FROM');
        // $templateSid = env('TWILIO_TEMPLATE_SID');
    
        // $client = new Client($sid, $token);

        // try {
        //     $message = $client->messages->create(
        //         "whatsapp:$telefono",
        //         [
        //             'from' => $from,
        //             'contentSid' => $templateSid,
        //             'contentVariables' => json_encode([
        //                 '1' => $orderNumber,
        //             ]),
        //         ]
        //     );

        //     return response()->json([
        //         'success' => true,
        //         'sid' => $message->sid,
        //         'message' => 'Mensaje enviado correctamente',
        //     ]);
        // } catch (\Exception $e) {
        //     \Log::error('Error al enviar WhatsApp: ' . $e->getMessage());

        //     return response()->json([
        //         'success' => false,
        //         'error' => $e->getMessage(),
        //     ], 500);
        // }
    }
}



