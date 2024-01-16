<?php

namespace App\Http\Controllers\API\Sugerencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SugerenciasAPIController extends Controller
{
    private $database;


    // Funcion para conectar a la base de de datos de Firebase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();

    }

    public function registrarSugerenciaApp(Request $request)
    {
        $verifiedIdToken = $request->attributes->get('verified_id_token');


        if ($verifiedIdToken) {
            $correo = $request->input('correo');
            $fechaSugerencia = $request->input('fechaSugerencia');
            $comentario = $request->input('comentario');
            $estado = "Activo";

            $librosRecientesRef = $this->database
                ->getReference("libreria/sugerencia")
                ->push();

            $librosRecientesRef->set([
                'estado'=>$estado,
                'correo' => $correo,
                'comentario' => $comentario,
                'fechaSugerencia' => $fechaSugerencia,
            ]);

            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }

    }
}
