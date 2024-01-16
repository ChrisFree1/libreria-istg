<?php

namespace App\Http\Controllers\WEB\Reserva;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Exception\FirebaseException;
use Session;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class LibroReservaController extends Controller
{
    private $database;

    //funcion para conectar a la base de datos de FireBase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();

    }




    public function guardarReserva(Request $request)
    {              
        $estado = "Activo";

        // Obtener la fecha y hora de reserva formateada
        $fechaReserva = Carbon::createFromFormat('Y-m-d H:i', $request->fechaReserva . ' ' . $request->horaReserva);
        $fechaFormateada = $fechaReserva->format('d/m/Y H:i');

        // Obtener los datos del formulario
        $datos = [
            'libro' => $request->input('libro'),
            'autor' => $request->input('autor'),
            'fechaReserva' => $fechaFormateada,
            'carrera' => $request->input('carrera'),
            'correo' => $request->input('correo'),
            'nombre' => $request->input('nombre'),
            'cedula' => $request->input('cedula'),
            'estado' => $estado,
            'comentario' => $request->input('comentario'),
        ];
        
        $firebase = $this->database->getReference('libreria/lb_reserva');
        $libroRef = $firebase->push()->set($datos);

        // Obtener el nombre del libro de la reserva
        $nombreLibro = $request->input('libro');

        // Realizar consulta al nodo "libro" para encontrar el libro por su nombre
        $librosReference = $this->database->getReference('libreria/libro');
        $librosSnapshot = $librosReference->getSnapshot();

        // Recorrer los libros y actualizar el estado a "Reservado" si el nombre coincide
        foreach ($librosSnapshot->getValue() as $key => $libro) {
            if (isset($libro['nombre']) && $libro['nombre'] === $nombreLibro && $libro['estado'] === 'Activo') {
                $librosReference->getChild($key)->getChild('estado')->set('Reservado');
                break; // Terminar el bucle una vez que se ha actualizado un libro
            }
        }

        return redirect('/crear-libro-reserva');
        // Redirigir a la página de inicio u otra página
    }




public function listarRegistrosLbReserva()
{
    $referencia = $this->database->getReference('libreria/lb_reserva');
    $registros = $referencia->getValue();
    $nombresClave = $referencia->getChildKeys();

    // Filtrar solo los registros con estado "Activo"
    $registrosActivos = array_filter($registros, function ($registro) {
        return isset($registro['estado']) && $registro['estado'] == 'Activo';
    });

    // Número de registros por página
    $perPage = 20;

    // Página actual
    $currentPage = Paginator::resolveCurrentPage() ?: 1;

    // Crear una instancia de Collection
    $collection = new Collection($registrosActivos);

    // Crear un paginador Laravel
    $registrosPaginados = new LengthAwarePaginator(
        $collection->forPage($currentPage, $perPage),
        $collection->count(),
        $perPage,
        $currentPage,
        [
            'path' => Paginator::resolveCurrentPath(),
        ]
    );

    return view('LibroReservas.listadoLibroReserva', [
        'registros' => $registrosPaginados,
        'nombresClave' => $nombresClave,
    ]);
}



    public function listarCarreraFormulario()
    {
        $libros = $this->database->getReference('libreria/libro')->getValue();
        $carreras = $this->database->getReference('libreria/carrera')->getValue();

        $librosActivos = [];

        foreach ($libros as $libro) {
            if (isset($libro['fisico']) && $libro['fisico'] === 'true'  && $libro['estado'] === 'Activo') {
                $librosActivos[] = $libro['nombre']; 
            }
        }
 

        return view('LibroReservas.crearLibroReserva', ['carreras' => $carreras, 'librosActivos' => $librosActivos]);      

    }




    public function eliminarRegistroLibroReserva($clave)
    {
        $referencia = $this->database->getReference('libreria/lb_reserva/' . $clave);
        $reserva = $referencia->getValue();
        $nombreLibro = $reserva['libro'];

        $referencia->update([
            'estado' => 'Inactivo'
        ]);



        $librosReference = $this->database->getReference('libreria/libro');
        $librosSnapshot = $librosReference->getSnapshot();

        foreach ($librosSnapshot->getValue() as $key => $libro) {
            if (isset($libro['nombre']) && $libro['nombre'] === $nombreLibro && $libro['estado'] === 'Reservado' || $libro['estado'] === 'Entregado') {
                $librosReference->getChild($key)->getChild('estado')->set('Activo');
                break; // Terminar el bucle una vez que se ha actualizado un libro
            }
        }
        return redirect('/listado-reservas');
    }

    public function entregarRegistroLibroReserva($clave)
    {
        // Obtener la reserva
        $referencia = $this->database->getReference('libreria/lb_reserva/' . $clave);
        $reserva = $referencia->getValue();

        // Obtener el nombre del libro de la reserva
        $nombreLibro = $reserva['libro'];

        // Actualizar el estado de la reserva a "Entregado"
        $referencia->update([
            'estado' => 'Entregado'
        ]);

        // Consultar y actualizar el estado del libro en el nodo "libro"
        $librosReference = $this->database->getReference('libreria/libro');
        $librosSnapshot = $librosReference->getSnapshot();

        foreach ($librosSnapshot->getValue() as $key => $libro) {
            if (isset($libro['nombre']) && $libro['nombre'] === $nombreLibro && $libro['estado'] === 'Reservado') {
                $librosReference->getChild($key)->getChild('estado')->set('Entregado');
                break; // Terminar el bucle una vez que se ha actualizado un libro
            }
        }

        return redirect('/listado-reservas');
    }

    public function libroReservaDevuelta($clave)
    {
        // Obtener la reserva
        $referencia = $this->database->getReference('libreria/lb_reserva/' . $clave);
        $reserva = $referencia->getValue();

        // Obtener el nombre del libro de la reserva
        $nombreLibro = $reserva['libro'];

        // Actualizar el estado de la reserva a "Entregado"
        $referencia->update([
            'estado' => 'Devuelto'
        ]);

        // Consultar y actualizar el estado del libro en el nodo "libro"
        $librosReference = $this->database->getReference('libreria/libro');
        $librosSnapshot = $librosReference->getSnapshot();

        foreach ($librosSnapshot->getValue() as $key => $libro) {
            if (isset($libro['nombre']) && $libro['nombre'] === $nombreLibro && $libro['estado'] === 'Entregado') {
                $librosReference->getChild($key)->getChild('estado')->set('Activo');
                break; // Terminar el bucle una vez que se ha actualizado un libro
            }
        }

        return redirect('/listado-reservas');
    }

    
    public function combosEditarRegistrosReservas($clave)
    {
        $referencia = $this->database->getReference('libreria/lb_reserva/' . $clave);
        $registro = $referencia->getValue();
        $carreras = $this->database->getReference('libreria/carrera')->getValue();
        $libros = $this->database->getReference('libreria/libro')->getValue();

        $librosActivos = [];

        foreach ($libros as $libro) {
            if (isset($libro['fisico']) && $libro['fisico'] === 'true'  && $libro['estado'] === 'Activo') {
                $librosActivos[] = $libro['nombre']; 
            }
        }
 
        $fechaReserva = Carbon::createFromFormat('d/m/Y H:i', $registro['fechaReserva']);

        $registro['fechaReserva'] = $fechaReserva->format('Y-m-d');
        $registro['horaReserva'] = $fechaReserva->format('H:i');

        return view('LibroReservas.editarLibroReserva', ['registro' => $registro, 
                                       'clave' => $clave,
                                        'carreras' => $carreras,
                                        'librosActivos'=>$librosActivos]);
    }
    
    public function combosVerRegistrosReservas($clave)
    {
        $referencia = $this->database->getReference('libreria/lb_reserva/' . $clave);
        $registro = $referencia->getValue();
        $carreras = $this->database->getReference('libreria/carrera')->getValue();

        $libros = $this->database->getReference('libreria/libro')->getValue();

        $librosActivos = [];

        foreach ($libros as $libro) {
            if (isset($libro['fisico']) && $libro['fisico'] === 'true'  && $libro['estado'] === 'Activo') {
                $librosActivos[] = $libro['nombre']; 
            }
        }
                $fechaReserva = Carbon::createFromFormat('d/m/Y H:i', $registro['fechaReserva']);

        $registro['fechaReserva'] = $fechaReserva->format('Y-m-d');
        $registro['horaReserva'] = $fechaReserva->format('H:i');
        return view('LibroReservas.verLibroReserva', ['registro' => $registro, 
                                       'clave' => $clave,
                                        'carreras' => $carreras,
                                        'librosActivos' => $librosActivos]);
    }

    public function actualizarRegistroReserva(Request $request, $clave)
    {
        $referencia = $this->database->getReference('libreria/lb_reserva/' . $clave);

        // Obtener la información de la reserva antes de la actualización
        $reservaAnterior = $referencia->getValue();
        $fechaReserva = Carbon::createFromFormat('Y-m-d H:i', $request->fechaReserva . ' ' . $request->horaReserva);
        $fechaFormateada = $fechaReserva->format('d/m/Y H:i');
        $referencia->update([
            'libro' => $request->input('libro'),
            'autor' => $request->input('autor'),
            'fechaReserva' => $fechaFormateada,
            'carrera' => $request->input('carrera'),
            'nombre' => $request->input('nombre'),
            'cedula' => $request->input('cedula'),
            'correo' => $request->input('correo'),            
            'comentario' => $request->input('comentario'),
        ]);

        $libroInput = $request->input('libro');
        if ($libroInput !== null && $libroInput !== $reservaAnterior['libro']) {
            // Obtener el libro anterior asociado a la reserva
            $libroAnterior = $reservaAnterior['libro'];

            // Actualizar el estado del libro anterior a "Activo"
            $librosReference = $this->database->getReference('libreria/libro');
            $librosSnapshot = $librosReference->getSnapshot();
            foreach ($librosSnapshot->getValue() as $key => $libroInfo) {
                if (
                    isset($libroInfo['nombre']) &&
                    $libroInfo['nombre'] === $libroAnterior &&
                    $libroInfo['estado'] === 'Reservado' &&
                    isset($libroInfo['fisico']) && $libroInfo['fisico'] === 'true'
                ) {
                    $librosReference->getChild($key)->getChild('estado')->set('Activo');
                    break;
                }
            }

            // Actualizar el estado del nuevo libro a "Reservado"
            foreach ($librosSnapshot->getValue() as $key => $libroInfo) {
                if (
                    isset($libroInfo['nombre']) &&
                    $libroInfo['nombre'] === $request->input('libro') &&
                    isset($libroInfo['fisico']) && $libroInfo['fisico'] === 'true'
                ) {
                    $librosReference->getChild($key)->getChild('estado')->set('Reservado');
                    break;
                }
            }
        }

        return redirect('/listado-reservas');
    }


    public function generarListadoPDF()
    {
        $referencia = $this->database->getReference('libreria/lb_reserva');
        $registros = $referencia->getValue();
        $nombresClave = $referencia->getChildKeys();

        $html = '<h1>Listado de Reservas</h1>';
        $html .= '<table border="1" cellspacing="0" cellpadding="10">';
        $html .= '<tr><th>Carrera</th><th>Cédula</th><th>Fecha Reserva</th><th>Libro</th><th>Nombre</th><th>Estado</th></tr>';
        
        foreach ($registros as $clave => $registro) {
            if (isset($registro['estado']) && $registro['estado'] === 'Activo' || $registro['estado'] === 'Reservado' || $registro['estado'] === 'Entregado' || $registro['estado'] === 'Devuelto') {
                $html .= '<tr>';
                $html .= '<td>' . $registro['carrera'] . '</td>';
                $html .= '<td>' . $registro['cedula'] . '</td>';
                $html .= '<td>' . $registro['fechaReserva'] . '</td>';
                $html .= '<td>' . $registro['libro'] . '</td>';
                $html .= '<td>' . $registro['nombre'] . '</td>';
                $html .= '<td>' . $registro['estado'] . '</td>';

                $html .= '</tr>';
            }
        }
        
        $html .= '</table>';

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('listado_libros_reservas.pdf');

        // You may also save the PDF file if needed
        // $output = $dompdf->output();
        // file_put_contents('listado_libros.pdf', $output);
    }


    public function generarListadoExcel()
    {
        $referencia = $this->database->getReference('libreria/lb_reserva');
        $registros = $referencia->getValue();
        $nombresClave = $referencia->getChildKeys();

        // Crear un nuevo libro de trabajo (spreadsheet)
        $spreadsheet = new Spreadsheet();

        // Obtener la hoja activa
        $sheet = $spreadsheet->getActiveSheet();

        // Agregar encabezados
        $sheet->setCellValue('A1', 'Carrera');
        $sheet->setCellValue('B1', 'Cédula');
        $sheet->setCellValue('C1', 'Fecha Reserva');
        $sheet->setCellValue('D1', 'Libro');
        $sheet->setCellValue('E1', 'Nombre');
        $sheet->setCellValue('F1', 'Estado');


        // Llenar el contenido de la hoja con los datos de los libros
        $row = 2;
        foreach ($registros as $clave => $registro) {
            if (isset($registro['estado']) && $registro['estado'] === 'Activo' || $registro['estado'] === 'Reservado' || $registro['estado'] === 'Entregado' || $registro['estado'] === 'Devuelto') {
                $sheet->setCellValue('A' . $row, $registro['carrera']);
                $sheet->setCellValue('B' . $row, $registro['cedula']);
                $sheet->setCellValue('C' . $row, $registro['fechaReserva']);
                $sheet->setCellValue('D' . $row, $registro['libro']);
                $sheet->setCellValue('E' . $row, $registro['nombre']);
                $sheet->setCellValue('F' . $row, $registro['estado']);

                $row++;
            }
        }

        // Crear un escritor para el archivo Excel
        $writer = new Xlsx($spreadsheet);

        // Establecer las cabeceras para la descarga
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="listado_libros.xlsx"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    public function buscarReserva(Request $request)
{
    // Obtén el valor del parámetro de búsqueda desde la solicitud
    $query = $request->input('query');

    // Obtén resultados de Firebase utilizando la función buscarEnFirebaseReserva
    $resultados = $this->buscarEnFirebaseReserva($query);

    // Número de registros por página
    $perPage = 20;

    // Página actual
    $currentPage = Paginator::resolveCurrentPage() ?: 1;

    // Crear una instancia de Collection
    $collection = new Collection($resultados);

    // Crear un paginador Laravel
    $registrosPaginados = new LengthAwarePaginator(
        $collection->forPage($currentPage, $perPage),
        $collection->count(),
        $perPage,
        $currentPage,
        [
            'path' => Paginator::resolveCurrentPath(),
        ]
    );

    return view('LibroReservas.listadoLibroReserva', ['registros' => $registrosPaginados]);
}

private function buscarEnFirebaseReserva($query)
{
    $categoriaRef = $this->database->getReference('libreria/lb_reserva')->getValue();
    $results = [];

    foreach ($categoriaRef as $key => $value) {
        // Buscar en varios campos
        if (
            (isset($value['carrera']) && stripos($value['carrera'], $query) !== false) ||
            (isset($value['cedula']) && stripos($value['cedula'], $query) !== false) ||
            (isset($value['estado']) && stripos($value['estado'], $query) !== false) ||
            (isset($value['fechaReserva']) && stripos($value['fechaReserva'], $query) !== false) ||
            (isset($value['libro']) && stripos($value['libro'], $query) !== false) ||
            (isset($value['nombre']) && stripos($value['nombre'], $query) !== false)
        ) {
            $results[] = $value;
        }
    }

    return $results;
}

    public function liberarReservas()
    {
        // Obtén las reservas que están en estado 'Activo' y cuya fecha haya vencido
        $referencia = $this->database->getReference('libreria/lb_reserva');
        $registros = $referencia->getValue();

        foreach ($registros as $key => $registro) {
            $fechaReserva = Carbon::createFromFormat('d/m/Y H:i', $registro['fechaReserva']); // Asegúrate de importar la clase Carbon

            if ($registro['estado'] === 'Activo' && $fechaReserva->isPast()) {
                $referencia->getChild($key)->getChild('estado')->set('Inactivo');
            }
        }

        return redirect('/listado-reservas');
    }


}