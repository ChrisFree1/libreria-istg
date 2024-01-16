<?php

namespace App\Http\Controllers\API\AreaCarrera;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AreaCarreraAPIController extends Controller
{
    
    private $database;


    // Funcion para conectar a la base de de datos de Firebase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();

    }


    public function listarAreasPorCarrera(Request $request, $nombreCarrera)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');
        

        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $carreras = $this->database->getReference('libreria/area_carrera')->getValue();

            $carrerasActivas = [];

            foreach ($carreras as $carrera) {
                if (isset($carrera['prefijo']) && $carrera['prefijo'] === $nombreCarrera && $carrera['estado'] === 'Activo') {
                    $carrerasActivas[] = $carrera;
                }
            }

            return response()->json($carrerasActivas);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }

}
