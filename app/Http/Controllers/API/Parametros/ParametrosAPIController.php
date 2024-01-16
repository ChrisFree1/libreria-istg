<?php

namespace App\Http\Controllers\API\Parametros;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParametrosAPIController extends Controller
{
    private $database;


    // Funcion para conectar a la base de de datos de Firebase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();

    }

    public function listarValorParametro(Request $request, $codigo)
    {
        $verifiedIdToken = $request->attributes->get('verified_id_token');
        

        if ($verifiedIdToken) {
            $consulta = $this->database->getReference('libreria/parametro')->getValue();
            $valor = null;

            foreach ($consulta as $resultado) {
                if (isset($resultado['estado']) && $resultado['estado'] === 'Activo' && $resultado['codigo'] === $codigo) {
                    $valor = $resultado['valor'];
                    break; 
                }
            }

            if ($valor !== null) {
                return response()->json(['valor' => $valor]);
            } else {
                return response()->json(['error' => 'Código no encontrado'], 404);
            }
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }


    public function listadoParametro(Request $request)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');

        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $consulta = $this->database->getReference('libreria/parametro')->getValue();
            $librosResultado = [];

            foreach ($consulta as $resultado) {
                if (isset($resultado['estado']) && $resultado['estado'] === 'Activo' ) {
                    $librosResultado[] = $resultado;
                }
            }

            return response()->json($librosResultado);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }

    public function registrarParametro(Request $request)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');


        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $codigo = $request->input('codigo');
            $descripcion = $request->input('descripcion');
            $nomnbre_parametro = $request->input('nombre_parametro');
            $valor = $request->input('valor');
            $estado="Activo";



            $conosultarCorreo = $this->database->getReference('libreria/parametro')->getValue();

            foreach ($conosultarCorreo as $correoFirebase) {
                if (isset($correoFirebase['codigo']) && $correoFirebase['codigo'] === $codigo) {
                    return response()->json(['success' => false, 'message' => 'El codigo ya está registrado.']);
                }
            }

            $librosRecientesRef = $this->database
                ->getReference("libreria/parametro")
                ->push();

            $librosRecientesRef->set([
                        'codigo' => $codigo,
                        'descripcion'=> $descripcion,
                        'nombre_parametro'=> $nomnbre_parametro,
                        'valor'=>$valor,
                        'estado'=> $estado

            ]);

            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }


    public function actualizarParametro(Request $request)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');


        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $codigo = $request->input('codigo');
            $descripcion = $request->input('descripcion');
            $nomnbre_parametro = $request->input('nombre_parametro');
            $valor = $request->input('valor');
            $estado="Activo";


            $usuariosRef = $this->database->getReference('libreria/parametro');

            $usuarios = $usuariosRef->getValue();

            $usuarioEncontrado = false;

            foreach ($usuarios as $clave => $usuario) {
                if (isset($usuario['codigo']) && $usuario['codigo'] === $codigo) {
                    $usuariosRef->getChild($clave)->update([
                        'codigo' => $codigo,
                        'descripcion'=> $descripcion,
                        'nombre_parametro'=> $nomnbre_parametro,
                        'valor'=>$valor,
                        'estado'=> $estado
                    ]);

                    $usuarioEncontrado = true;
                    break;
                }
            }

            if ($usuarioEncontrado) {
                return response()->json(['success' => true, 'message' => 'Parametro actualizado con éxito.']);
            } else {
                return response()->json(['success' => false, 'message' => 'No se encontró ningún parametro con el nombre especificado.']);
            }
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }


    public function eliminarParametro(Request $request, $id)
    {
        $verifiedIdToken = $request->attributes->get('verified_id_token');

        if ($verifiedIdToken) {
            $estado = "Inactivo";

            $parametroRef = $this->database->getReference('libreria/parametro');

            // Verificar si el ID (clave) existe en la base de datos
            $parametroEncontrado = false;
            $parametros = $parametroRef->getValue();
            foreach ($parametros as $clave => $parametro) {
                if ($clave === $id) { 

                    $parametroRef->getChild($id)->update([
                        'estado' => $estado,
                    ]);
             

           

                    $parametroEncontrado = true;
                    break;
                }
            }

            if ($parametroEncontrado) {
                return response()->json(['success' => true, 'message' => 'Parametro eliminado con éxito.']);
            } else {
                return response()->json(['success' => false, 'message' => 'No se encontró ningún parametro con el ID especificado.']);
            }
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }

}
