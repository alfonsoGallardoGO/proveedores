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

            if (str_starts_with($base64, 'data:')) {
                $base64 = preg_replace('/^data:application\/pdf;base64,/', '', $base64);
            }
            $base64 = str_replace(' ', '+', $base64);

            $binary = base64_decode($base64, true);

            // if ($binary !== false) {

            //     $filename = 'receipt_'.Str::uuid().'.pdf';
            //     $path = "whatsapp/receipts/{$filename}";
            //     Storage::disk('public')->makeDirectory('whatsapp/receipts');
            //     Storage::disk('public')->put($path, $binary);

            //     $pdfRuta = $path;
            //     $pdfBase64 = $base64;
            // }

            if ($binary !== false) {
                $filename = 'receipt_' . Str::uuid() . '.pdf';

                // Ruta física en /public/whatsapp/receipts
                $folderPath = public_path('whatsapp/receipts');

                // Crear carpeta si no existe
                if (!file_exists($folderPath)) {
                    mkdir($folderPath, 0775, true);
                }

                // Guardar archivo
                file_put_contents($folderPath . '/' . $filename, $binary);

                // Ruta relativa para guardar en BD
                $pdfRuta = "whatsapp/receipts/{$filename}";

                // Base64 original si quieres guardarlo también
                $pdfBase64 = $base64;

                // URL pública accesible desde navegador
                $pdfUrl = url($pdfRuta);
            }


        }


        SupplierWhatsapp::create([
            'reception_id'         => $request->receiptId,
            'external_supplier_id' => $request->proveedorId,
            'supplier_name'        => $request->proveedor,
            'date'                 => $fechaDb,
            'phone'                => $request->numeroWhatsapp,
            'pdf_base_64'          => null,
            'pdf_rute'             => $pdfRuta ?? null,
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



