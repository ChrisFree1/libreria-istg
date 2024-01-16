<?php

namespace App\Http\Controllers\API\LibroReciente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Database;

class LibroRecienteAPIController extends Controller
{
    private $database;



    // Funcion para conectar a la base de de datos de Firebase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();

    }

public function regristrarLibroReciente(Request $request, $uid)
{
    // Obtener el token verificado del middleware
    $verifiedIdToken = $request->attributes->get('verified_id_token');
    
    // Continuar con el proceso solo si el token está verificado
    if ($verifiedIdToken) {
        $libro = $request->input('nombre_libro');
        $autor = $request->input('autor_libro');
        $imagen = $request->input('imagen');
        $fecha_lectura = $request->input('fecha_lectura');

        $librosRecientesRef = $this->database
            ->getReference("libreria/lb_reciente/$uid");

        // Obtener la lista actualizada
        $currentList = $librosRecientesRef
            ->orderByChild('nombre_libro')
            ->getSnapshot()
            ->getValue();

        // Buscar si el libro ya existe en la lista
        $existingLibroKey = null;
        foreach ($currentList as $key => $value) {
            if ($value['nombre_libro'] === $libro) {
                $existingLibroKey = $key;
                break;
            }
        }

        if ($existingLibroKey !== null) {
            // Si el libro ya existe, eliminarlo y agregarlo al principio
            $librosRecientesRef->getChild($existingLibroKey)->remove();
        }

        // Agregar el libro al principio
        $libroNuevo = $librosRecientesRef->push();

        $libroNuevo->set([
            'nombre_libro' => $libro,
            'autor_libro' => $autor,
            'imagen' => $imagen,
        ]);

        // Mensaje indicando que la operación fue realizada con éxito
        return response()->json(['message' => 'Operación completada exitosamente']);
    } else {
        return response()->json(['error' => 'Token inválido o expirado'], 401);
    }
}



    


    public function listarLibrosRecientesPorUid(Request $request, $uid)
    {

        $verifiedIdToken = $request->attributes->get('verified_id_token');

        if ($verifiedIdToken) {

            $librosRecientesRef = $this->database->getReference("libreria/lb_reciente/$uid")->getValue();

            if (is_array($librosRecientesRef) && count($librosRecientesRef) > 0) {
                $filteredLibrosRecientes = [];
                foreach ($librosRecientesRef as $libroReciente) {
                    $filteredLibrosRecientes[] = $libroReciente;
                }
                return response()->json($filteredLibrosRecientes);
            } else {

                return response()->json([]);
            }
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }









}
