<?php

namespace App\Http\Controllers;

use App\Services\NetsuiteRestletService;
use Illuminate\Http\Request;

class RestletController extends Controller
{
    protected $netsuiteRestletService;

    public function __construct(NetsuiteRestletService $netsuiteRestletService)
    {
        $this->netsuiteRestletService = $netsuiteRestletService;
    }

    public function getRestletResponse(Request $request, int $scriptId, int $deployId)
    {
        // La ruta del RESTlet, incluyendo script y deploy IDs
        // Si el script y deploy son fijos, puedes codificarlos aquí.
        // Si son dinámicos, podrías pasarlos como parámetros a esta función.
        $restletPath = "/restlet.nl?script={$scriptId}&deploy={$deployId}";

        // Si tu RESTlet espera datos en el cuerpo (POST/PUT), podrías pasarlos así:
        // $data = $request->json()->all(); // Asume que el cliente envía JSON

        try {
            // Llama al servicio para ejecutar el RESTlet
            // Para una solicitud GET, $data estará vacío
            $response = $this->netsuiteRestletService->callRestlet('GET', $restletPath);

            if ($response === null) {
                // Si el servicio devuelve null, significa que hubo un error interno
                return response()->json([
                    'error' => 'No se pudo obtener la respuesta del RESTlet. Revisa los logs de Laravel.'
                ], 500);
            }

            // Si la respuesta no es nula, se asume que contiene los datos del RESTlet
            return response()->json($response);

        } catch (\Exception $e) {
            // Captura cualquier excepción que no fue manejada por el servicio
            //Log::error('Error en RestletController: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ocurrió un error inesperado al procesar la solicitud.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}