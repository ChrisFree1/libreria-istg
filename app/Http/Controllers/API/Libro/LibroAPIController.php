<?php

namespace App\Http\Controllers\API\Libro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibroAPIController extends Controller
{
    private $database;


    // Funcion para conectar a la base de de datos de Firebase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();

    }



    public function listarLibros(Request $request)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');

        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            // Utiliza orderByChild para ordenar por "estado"
            $libros = $this->database
                ->getReference('libreria/libro')
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





    public function listarLibrosFisicos(Request $request)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');

        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $consulta = $this->database->getReference('libreria/libro')->getValue();
            $librosResultado = [];

            foreach ($consulta as $resultado) {
                if (isset($resultado['fisico']) && $resultado['fisico'] === 'true' && isset($resultado['estado']) && $resultado['estado'] === 'Activo' ) {
                
                    
                    $librosResultado[] = $resultado;
                }
            }

            return response()->json($librosResultado);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }

    public function listarLibrosNoFisicos(Request $request)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');

        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $consulta = $this->database->getReference('libreria/libro')->getValue();
            $librosResultado = [];

            foreach ($consulta as $resultado) {
                if (isset($resultado['estado']) && $resultado['estado'] === 'Activo' && !isset($resultado['fisico'])  ) {
           
                    
                    $librosResultado[] = $resultado;
                }
            }

            return response()->json($librosResultado);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }



    public function libroFisicosTodasCarreras(Request $request)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');

        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $consulta = $this->database->getReference('libreria/libro')->getValue();
            $librosResultado = [];

            foreach ($consulta as $resultado) {
                if (
                    isset($resultado['documento']) && $resultado['documento'] === 'true' &&
                    isset($resultado['fisico']) && $resultado['fisico'] === 'true' &&
                    isset($resultado['estado']) && $resultado['estado'] === 'Activo'
                ) {
                  
                    $librosResultado[] = $resultado;
                }
            }

            return response()->json($librosResultado);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }




    // Funcion para listar libros Inactivos -- Administrativo

    public function libroInactivo(Request $request)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');


        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $libros = $this->database->getReference('libreria/libro')->getValue();

            $librosInactivos = [];

            foreach ($libros as $libro) {
                if (isset($libro['estado']) && $libro['estado'] === 'Inactivo') {
                    $librosInactivos[] = $libro;
                }
            }

            return response()->json($librosInactivos);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }



    //Funcion para listar libros por Autor
    public function libroAutor(Request $request, $resultadoAutor)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');


        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $libros = $this->database->getReference('libreria/libro')->getValue();

            $librosAutores = [];

            foreach ($libros as $libro) {
                if (isset($libro['autor']) && $libro['autor'] === $resultadoAutor  && $libro['estado'] === 'Activo') {
                    $librosAutores[] = $libro;
                }
            }

            return response()->json($librosAutores);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }



    public function libroTitulo(Request $request, $resultadoTitulo)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');

        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $libros = $this->database->getReference('libreria/libro')->getValue();

            $librosTitulos = [];

            foreach ($libros as $libro) {
                if (isset($libro['nombre']) && $libro['nombre'] === $resultadoTitulo && $libro['estado'] === 'Activo') {
                    $librosTitulos[] = $libro;
                }
            }

            return response()->json($librosTitulos);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }




    public function libroFechaPublicacion(Request $request, $resultadoFechaPublicacion)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');
        


        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $libros = $this->database->getReference('libreria/libro')->getValue();
            $librosFechaPublicacion = [];

            foreach ($libros as $libro) {
                if (isset($libro['anio_publicacion']) && $libro['anio_publicacion'] === $resultadoFechaPublicacion && $libro['estado'] === 'Activo') {
                    $librosFechaPublicacion[] = $libro;
                }
            }

            return response()->json($librosFechaPublicacion);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }


    
}
