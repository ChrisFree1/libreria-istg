<?php

namespace App\Http\Controllers\API\Reservas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibroReservaAPIController extends Controller
{
    private $database;


    // Funcion para conectar a la base de de datos de Firebase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();

    }


    public function regristrarReseveraApp(Request $request)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');
        
        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $carrera = $request->input('carrera');
            $area = $request->input('area');
            $cedula = $request->input('cedula');
            $correo = $request->input('correo');
            $fechaReserva = $request->input('fechaReserva');
            $libro = $request->input('libro');
            $nombre = $request->input('nombre');
            $autor = $request->input('autor');
            $comentario = $request->input('comentario');
            $estado = "Activo";

            // Validar si la fecha de reserva ya está en otra reserva
            $fechaReservaExistente = $this->verificarFechaReservaExistente($fechaReserva);

            if ($fechaReservaExistente) {
                // La fecha de reserva ya está ocupada
                return response()->json(['success' => false, 'message' => 'La fecha de reserva ya está ocupada'], 400);
            }

            // Crear la reserva en la base de datos
            $librosRecientesRef = $this->database
                ->getReference("libreria/lb_reserva")
                ->push();

            $librosRecientesRef->set([
                'carrera' => $carrera,
                'area' => $area,
                'cedula' => $cedula,
                'correo' => $correo,
                'comentario' => $comentario,
                'estado' => $estado,
                'autor' => $autor,
                'fechaReserva' => $fechaReserva,
                'libro' => $libro,
                'nombre' => $nombre,
            ]);

            // Actualizar el estado del libro a "Reservado"
            $nombreLibro = $request->input('libro');

            $librosReference = $this->database->getReference('libreria/libro');
            $librosSnapshot = $librosReference->getSnapshot();

            foreach ($librosSnapshot->getValue() as $key => $libroInfo) {
                if (
                    isset($libroInfo['nombre']) &&
                    $libroInfo['nombre'] === $nombreLibro &&
                    $libroInfo['estado'] === 'Activo' &&
                    isset($libroInfo['fisico']) && $libroInfo['fisico'] === 'true'
                ) {
                    $librosReference->getChild($key)->getChild('estado')->set('Reservado');
                    break; // Terminar el bucle una vez que se ha actualizado un libro
                }
            }

            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }

    private function verificarFechaReservaExistente($fechaReserva)
    {
        // Realizar la consulta a la base de datos para verificar si la fecha ya está reservada
        $reservasReference = $this->database->getReference('libreria/lb_reserva');
        $reservasSnapshot = $reservasReference->getSnapshot();

        foreach ($reservasSnapshot->getValue() as $reservaInfo) {
            if (isset($reservaInfo['fechaReserva']) && $reservaInfo['fechaReserva'] === $fechaReserva && isset($reservaInfo['estado']) && $reservaInfo['estado'] === 'Activo') {
                return true; // La fecha ya está reservada
            }
        }

        return false; // La fecha no está reservada
    }



     public function actualizarLibroReserva(Request $request, $id)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');

        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $carrera = $request->input('carrera');
            $area = $request->input('area');
            $cedula = $request->input('cedula');
            $correo = $request->input('correo');
            $autor = $request->input('autor');
            $fechaReserva = $request->input('fechaReserva');
            $libro = $request->input('libro');
            $nombre = $request->input('nombre');
            $comentario = $request->input('comentario');

            $usuariosRef = $this->database->getReference('libreria/lb_reserva');

            // Verificar si el ID (clave) existe en la base de datos
            $usuarioEncontrado = false;
            $usuarios = $usuariosRef->getValue();
            foreach ($usuarios as $clave => $usuario) {
                if ($clave === $id) {
                    $usuarioEncontrado = true;
                    break;
                }
            }

            if (!$usuarioEncontrado) {
                return response()->json(['success' => false, 'message' => 'No se encontró ningún usuario con el ID especificado.']);
            }

            // Obtener la información del usuario (reserva) antes de la actualización
            $usuarioAnterior = $usuariosRef->getChild($id)->getValue();

            // Verificar si el libro se actualiza y, en ese caso, actualizar el libro anterior
            if (isset($libro) && $libro !== $usuarioAnterior['libro']) {
                // Obtener el libro anterior asociado a la reserva
                $libroAnterior = $usuarioAnterior['libro'];

                // Actualizar el estado del libro anterior a "Activo"
                $librosReference = $this->database->getReference('libreria/libro');
                $librosSnapshot = $librosReference->getSnapshot();
                foreach ($librosSnapshot->getValue() as $key => $libroInfo) {
                    if (
                        isset($libroInfo['nombre']) && $libroInfo['nombre'] === $libroAnterior && $libroInfo['estado'] === 'Reservado' && isset($libroInfo['fisico']) && $libroInfo['fisico'] === 'true'
                    ) {
                        $librosReference->getChild($key)->getChild('estado')->set('Activo');
                        break;
                    }
                }

                // Actualizar el estado del nuevo libro a "Reservado"
                foreach ($librosSnapshot->getValue() as $key => $libroInfo) {
                    if (
                        isset($libroInfo['nombre']) &&
                        $libroInfo['nombre'] === $libro &&
                        isset($libroInfo['fisico']) && $libroInfo['fisico'] === 'true'
                    ) {
                        $librosReference->getChild($key)->getChild('estado')->set('Reservado');
                        break;
                    }
                }
            }

            // Continuar con el proceso de actualización de la reserva
            $usuariosRef->getChild($id)->update([
                'carrera' => $carrera,
                'area' => $area,
                'cedula' => $cedula,
                'correo' => $correo,
                'autor' => $autor,
                'fechaReserva' => $fechaReserva,
                'libro' => $libro,
                'nombre' => $nombre,
                'comentario' => $comentario,
            ]);

            return response()->json(['success' => true, 'message' => 'Reserva Actualizada con éxito.']);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }







    public function listarReservasPorCorreo(Request $request, $correo)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');

        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $reservas = $this->database->getReference('libreria/lb_reserva')->getValue();
            $reservasUsuario = [];

            $estado = "Activo";
            foreach ($reservas as $numeroDocumento => $reserva) {
                // Verificar si el correo coincide y que el estado sea "Activo"
                if (isset($reserva['correo']) && $reserva['correo'] === $correo && isset($reserva['estado']) && 
                     $reserva['estado'] === $estado ||
                     $reserva['estado'] === 'Entregado' || 
                     $reserva['estado'] === 'Reservado') {
                    $reservaConNumeroDocumento = $reserva;
                    $reservaConNumeroDocumento['numero_documento'] = $numeroDocumento;
                    $reservasUsuario[] = $reservaConNumeroDocumento;
                }
            }

            return response()->json($reservasUsuario);
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }


    public function obtenerReservaPorId(Request $request, $id)
    {
        $verifiedIdToken = $request->attributes->get('verified_id_token');

        if ($verifiedIdToken) {

            $reservaRef = $this->database->getReference('libreria/lb_reserva/' . $id)->getValue();

            if ($reservaRef !== null && isset($reservaRef['estado'])) {
                $estado = $reservaRef['estado'];
                
                
                if ($estado === 'Activo' || $estado === 'Entregado' || $estado === 'Devuelto' || $estado === 'Reservado') {

                    return response()->json($reservaRef);

                } else {

                    return response()->json(['error' => 'Reserva no encontrada'], 404);
                }

            } else {

                return response()->json(['error' => 'Reserva no encontrada'], 404);

            }

        } else {

            return response()->json(['error' => 'Token inválido o expirado'], 401);

        }
    }



    public function eliminarLibroReserva(Request $request, $id)
    {
        // Obtener el token verificado del middleware
        $verifiedIdToken = $request->attributes->get('verified_id_token');

        // Continuar con el proceso solo si el token está verificado
        if ($verifiedIdToken) {
            $estado = "Inactivo";
            $comentario = $request->input('comentario');

            $usuariosRef = $this->database->getReference('libreria/lb_reserva');

            // Verificar si el ID (clave) existe en la base de datos
            $usuarioEncontrado = false;
            $usuarios = $usuariosRef->getValue();
            foreach ($usuarios as $clave => $usuario) {
                if ($clave === $id) { 
                    // Obtener el nombre del libro de la reserva antes de eliminarla
                    $nombreLibro = $usuario['libro'];

                    // Realizar consulta al nodo "libro" para encontrar el libro por su nombre
                    $librosReference = $this->database->getReference('libreria/libro');
                    $librosSnapshot = $librosReference->getSnapshot();

                    // Recorrer los libros y actualizar el estado a "Activo" si el nombre coincide
                    foreach ($librosSnapshot->getValue() as $key => $libro) {
                        if (isset($libro['nombre']) && $libro['nombre'] === $nombreLibro && $libro['estado'] === 'Reservado' && isset($libro['fisico']) && $libro['fisico'] === 'true') {
                            $librosReference->getChild($key)->getChild('estado')->set('Activo');
                            break; // Terminar el bucle una vez que se ha actualizado un libro
                        }
                    }

                    // Actualizar la reserva
                    $usuariosRef->getChild($id)->update([
                        'estado' => $estado,
                        'comentario' => $comentario,
                    ]);

                    $usuarioEncontrado = true;
                    break;
                }
            }

            if ($usuarioEncontrado) {
                return response()->json(['success' => true, 'message' => 'Reserva eliminada con éxito.']);
            } else {
                return response()->json(['success' => false, 'message' => 'No se encontró ninguna reserva con el ID especificado.']);
            }
        } else {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }
    }


}
