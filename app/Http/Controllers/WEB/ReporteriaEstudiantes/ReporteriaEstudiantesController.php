<?php

namespace App\Http\Controllers\WEB\ReporteriaEstudiantes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Dompdf\Dompdf;
use Dompdf\Options;
class ReporteriaEstudiantesController extends Controller
{

        private $database;

    //funcion para conectar a la base de datos de FireBase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();

    }

     public function obtenerCantidadEstudiantesPorCarrera()
    {
        // Obtener la referencia a la ubicación en Firebase
        $usuariosRef = $this->database->getReference('libreria/usuario');

        // Obtener todos los usuarios
        $usuarios = $usuariosRef->getValue();

        // Inicializar un array para almacenar la cantidad de estudiantes por carrera
        $cantidadEstudiantesPorCarrera = [];

        // Contar la cantidad de estudiantes por carrera
        foreach ($usuarios as $usuario) {
            if (isset($usuario['carrera'])) {
                $carrera = $usuario['carrera'];

                if (!isset($cantidadEstudiantesPorCarrera[$carrera])) {
                    $cantidadEstudiantesPorCarrera[$carrera] = 1;
                } else {
                    $cantidadEstudiantesPorCarrera[$carrera]++;
                }
            }
        }

        // Devolver la respuesta en formato JSON
        return response()->json(['cantidadEstudiantesPorCarrera' => $cantidadEstudiantesPorCarrera]);
    }

    public function obtenerCantidadEstudiantesPorCarreraPdf()
    {
        // Obtener la referencia a la ubicación en Firebase
            $usuariosRef = $this->database->getReference('libreria/usuario');

            // Obtener todos los usuarios
            $usuarios = $usuariosRef->getValue();

            // Inicializar un array para almacenar la cantidad de estudiantes por carrera
            $cantidadEstudiantesPorCarrera = [];

            // Contar la cantidad de estudiantes por carrera
            foreach ($usuarios as $usuario) {
                if (isset($usuario['carrera'])) {
                    $carrera = $usuario['carrera'];

                    if (!isset($cantidadEstudiantesPorCarrera[$carrera])) {
                        $cantidadEstudiantesPorCarrera[$carrera] = 1;
                    } else {
                        $cantidadEstudiantesPorCarrera[$carrera]++;
                    }
                }
            }

            // Devolver la respuesta en formato JSON
            return $cantidadEstudiantesPorCarrera;
    }



    public function generarReporteCarrerasPDF()
    {
         $cantidadEstudiantesPorCarrera = $this->obtenerCantidadEstudiantesPorCarreraPdf();

            // Crear el contenido HTML para el reporte
            $html = '<h1>Reporte de Carreras y Cantidad de Estudiantes</h1>';
            $html .= '<table border="1" cellspacing="0" cellpadding="10">';
            $html .= '<tr><th>Carrera</th><th>Cantidad de Estudiantes</th></tr>';

            foreach ($cantidadEstudiantesPorCarrera as $carrera => $cantidad) {
                $html .= '<tr>';
                $html .= '<td>' . (is_string($carrera) ? htmlspecialchars($carrera) : $carrera) . '</td>';
                
                if (is_array($cantidad)) {
                    // Si $cantidad es un array, conviértelo a cadena antes de aplicar htmlspecialchars
                    $cantidad = implode(', ', $cantidad);
                }
                
                $html .= '<td>' . (is_string($cantidad) ? htmlspecialchars($cantidad) : $cantidad) . '</td>';
                $html .= '</tr>';
            }

            $html .= '</table>';
        // Configurar opciones de Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        // instantiate and use the dompdf class
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('reporte_carreras.pdf');
    }
}
