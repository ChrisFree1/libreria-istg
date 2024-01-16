<?php

namespace App\Http\Controllers\API\Carreras;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CarrerasAPIController extends Controller
{
    private $database;

    //funcion para conectar a la base de datos de FireBase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();

    }


    public function listarCarreras(Request $request)
    {
        $verifiedIdToken = $request->attributes->get('verified_id_token');

        if ($verifiedIdToken) {
            // Utiliza orderByChild para ordenar por "estado"
            $libros = $this->database
                ->getReference('libreria/carrera')
                ->orderByChild('estado')
                ->equalTo('Activo')
                ->getValue();

            // Transforma el array asociativo en un array indexado sin claves únicas
            $librosActivos = array_values($libros);

            return response()->json($librosActivos);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }



    public function listarLibrosFisicosCarreras(Request $request,$carrera)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');

        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $consulta = $this->database->getReference('libreria/libro')->getValue();
            $librosResultado = [];

            foreach ($consulta as $resultado) {
                if (isset($resultado['fisico']) && $resultado['fisico'] === 'true' && isset($resultado['estado']) && $resultado['estado'] === 'Activo' && 
                        isset($resultado['carrera']) && $resultado['carrera'] === $carrera ) {
                
                    
                    $librosResultado[] = $resultado;
                }
            }

            return response()->json($librosResultado);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }



    public function listarPrefijoCarrera(Request $request, $carrera)
    {
        $verifiedIdToken = $request->attributes->get('verified_id_token');

        if ($verifiedIdToken) {
            // Utiliza orderByChild y equalTo para filtrar por "nombre_carrera" y "estado"
            $carreras = $this->database
                ->getReference('libreria/area_carrera')
                ->orderByChild('nombre_carrera')
                ->equalTo($carrera)
                ->getValue();

            $resultado = [];

            foreach ($carreras as $carreraData) {
                // Filtra por "estado"
                if (isset($carreraData['estado']) && $carreraData['estado'] === 'Activo') {
                    // Suponiendo que el prefijo está almacenado en el campo 'prefijo'
                    if (isset($carreraData['prefijo'])) {
                             $resultado = [
                                'prefijo' => $carreraData['prefijo'],
                                // Si deseas incluir otros campos relevantes, agrégales aquí
                            ];
                            break;
                    }
                }
            }

            return response()->json($resultado);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }

}
