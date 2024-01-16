<?php

namespace App\Http\Controllers\API\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsuarioAPIController extends Controller
{
    private $database;


    // Funcion para conectar a la base de de datos de Firebase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();

    }


     public function registrarUsuarioApp(Request $request)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');


        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $correo = $request->input('correo');
            $nombre = $request->input('nombre');
            $fecha_creacion = $request->input('fecha_creacion');
            $cedula = $request->input('cedula');
            $celular = $request->input('celular');
            $carrera = $request->input('carrera');


            $conosultarCorreo = $this->database->getReference('libreria/usuario')->getValue();

            foreach ($conosultarCorreo as $correoFirebase) {
                if (isset($correoFirebase['correo']) && $correoFirebase['correo'] === $correo) {
                    return response()->json(['success' => false, 'message' => 'El correo ya está registrado.']);
                }
            }

            $librosRecientesRef = $this->database
                ->getReference("libreria/usuario")
                ->push();

            $librosRecientesRef->set([
                'correo' => $correo,
                'nombre' => $nombre,
                'fecha_creacion' => $fecha_creacion,
                'cedula' => $cedula,
                'celular' => $celular,
                'carrera' => $carrera,

            ]);

            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }



    public function actualizarUsuarioPorCorreo(Request $request)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');


        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $correo = $request->input('correo');
            $fecha_modificacion = $request->input('fecha_modificacion');
            $cedula = $request->input('cedula');
            $celular = $request->input('celular');
            $carrera = $request->input('carrera');
            $nombre = $request->input('nombre');


            $usuariosRef = $this->database->getReference('libreria/usuario');

            $usuarios = $usuariosRef->getValue();

            $usuarioEncontrado = false;

            foreach ($usuarios as $clave => $usuario) {
                if (isset($usuario['correo']) && $usuario['correo'] === $correo) {
                    $usuariosRef->getChild($clave)->update([
                        'fecha_modificacion' => $fecha_modificacion,
                        'cedula' => $cedula,
                        'celular' => $celular,
                        'carrera' => $carrera,
                        'nombre' => $nombre,
                    ]);

                    $usuarioEncontrado = true;
                    break;
                }
            }

            if ($usuarioEncontrado) {
                return response()->json(['success' => true, 'message' => 'Usuario actualizado con éxito.']);
            } else {
                return response()->json(['success' => false, 'message' => 'No se encontró ningún usuario con el correo especificado.']);
            }
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }

    
    public function listarCamposPorCorreo(Request $request, $correo)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');
        

        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $cedulas = $this->database->getReference('libreria/usuario')->getValue();
            $cedulaUsuario = [];

            foreach ($cedulas as $cedulaU) {
                if (isset($cedulaU['correo']) && $cedulaU['correo'] === $correo) {
                    $cedulaUsuario[] = $cedulaU;
                }
            }

            return response()->json($cedulaUsuario);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }



}
