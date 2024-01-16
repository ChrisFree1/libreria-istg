<?php

namespace App\Http\Controllers\API\ListadoLibros;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListadoLibrosAPIController extends Controller
{
    private $database;

    //funcion para conectar a la base de datos de FireBase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();

    }


    public function listarCarrerasAreas(Request $request, $carrera, $area)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');


        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $consulta = $this->database->getReference('libreria/libro')->getValue();
            $librosResultado = [];

            foreach ($consulta as $resultado) {
                if (isset($resultado['estado'])  &&     $resultado['estado'] === 'Activo' &&
                    isset($resultado['carrera']) &&     $resultado['carrera'] === $carrera && 
                    isset($resultado['area'])    &&     $resultado['area'] === $area) {
                    $librosResultado[] = $resultado;
                }
            }
            return response()->json($librosResultado);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }


    public function listarCarrerasAreasAutor(Request $request, $carrera, $area, $autor)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');

        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $consulta = $this->database->getReference('libreria/libro')->getValue();
            $librosResultado = [];

            foreach ($consulta as $resultado) {
                if (isset($resultado['fisico'])     && $resultado['fisico'] === 'true' && 
                    isset($resultado['estado'])     && $resultado['estado'] === 'Activo' && 
                    isset($resultado['carrera'])    && $resultado['carrera'] === $carrera && 
                    isset($resultado['area'])       && $resultado['area'] === $area && 
                    isset($resultado['autor'])      && $resultado['autor'] === $autor ) {
                    $librosResultado[] = $resultado;
                }
            }

            return response()->json($librosResultado);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }
    // fisico 


    public function listarAutoresLibros(Request $request, $carrera, $area)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');
        

        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $libros = $this->database->getReference('libreria/libro')->getValue();
            $autores = [];

            foreach ($libros as $libro) {
                if (isset($libro['estado'])  && $libro['estado'] === 'Activo'&& 
                    isset($libro['carrera']) && $libro['carrera']=== $carrera && 
                    isset($libro['area'])    && $libro['area']=== $area) {
                    if (isset($libro['autor'])) {
                        $autores[] = ['autor' => $libro['autor']];
                    }
                }
            }

            return response()->json($autores);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }

}