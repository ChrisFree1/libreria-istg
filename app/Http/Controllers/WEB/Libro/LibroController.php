<?php

namespace App\Http\Controllers\WEB\Libro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class LibroController extends Controller
{
    private $database;

    //Constructor conectar a la base de datos de FireBase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();
    }



    // Funcion para guardar los registros de los libros
    public function guardarRegistroLibro(Request $request)
    {
        $estado = "Activo";

        $datos = [
            'nombre' => $request->input('nombre'),
            'autor' => $request->input('autor'),
            'nombre_editorial' => $request->input('nombre_editorial'),
            'resenia' => $request->input('resenia'),
            'anio_publicacion' => $request->input('anio_publicacion'),
            'link_drive' => $request->input('link_drive'),
            'carrera' => $request->input('carrera'),
            'area' => $request->input('area'),
            'categoria' => $request->input('categoria'),
            'fisico' => $request->input('fisico'),
            'documento' => $request->input('documento'),
            'estado' => $estado,
            'eliminado' => $estado,
        ];

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            $firebase_storage_path = 'Images/';

            // Genera un nombre único para la imagen
            $name = time() . '.' . $imagen->getClientOriginalExtension();

            // Sube la imagen a Firebase Storage
            $uploadedFile = fopen($imagen->path(), 'r');
            app('firebase.storage')->getBucket()->upload($uploadedFile, [
                'name' => $firebase_storage_path . $name,
            ]);

            // Asigna la URL de la imagen a los datos del libro
            $imagenUrl = $firebase_storage_path . $name;
            $baserUrl = 'https://firebasestorage.googleapis.com/v0/b/appbookistgdev.appspot.com/o/';
            $urlFirebase = $baserUrl .  urlencode($imagenUrl) . '?alt=media';
            $datos['imagen'] = $urlFirebase;

            // Elimina el archivo temporal en Laravel
            unlink($imagen->path());
        }

        $firebase = $this->database->getReference('libreria/libro');
        $libroRef = $firebase->push()->set($datos);

        return redirect('/registro-libro');
    }


    public function listarRegistrosLibros()
    {
        $referencia = $this->database->getReference('libreria/libro');
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

        return view('Libro.listadoLibro', [
            'registros' => $registrosPaginados,
            'nombresClave' => $nombresClave,
        ]);
    }


    //Funcion para traer listados de las Carreras, Areas, Categorias en un combo
    public function listarCombosFormulario()
    {
        $carreras = $this->database->getReference('libreria/carrera')->getValue();
        $areas = $this->database->getReference('libreria/area')->getValue();
        $categorias = $this->database->getReference('libreria/categoria')->getValue();

        return view('Libro.ingresoLibro', [
            'carreras' => $carreras,
            'areas' => $areas,
            'categorias' => $categorias
        ]);
    }



    //Combos que son utilizados para listar en la plantilla editarLibro
    public function combosEditarRegistrosLibros($clave)
    {
        $referencia = $this->database->getReference('libreria/libro/' . $clave);
        $registro = $referencia->getValue();
        $carreras = $this->database->getReference('libreria/carrera')->getValue();
        $areas = $this->database->getReference('libreria/area')->getValue();
        $categorias = $this->database->getReference('libreria/categoria')->getValue();



        return view('Libro.editarRegistroLibro', [
            'registro' => $registro,
            'clave' => $clave,
            'carreras' => $carreras,
            'areas' => $areas,
            'categorias' => $categorias
        ]);
    }



    //Actualizar registro de un libro por medio de la clave del documento
    public function actualizarRegistroLibro(Request $request, $clave)
    {
        $estado = "Activo";
        $referencia = $this->database->getReference('libreria/libro/' . $clave);

        $datos = [
            'nombre' => $request->input('nombre'),
            'autor' => $request->input('autor'),
            'nombre_editorial' => $request->input('nombre_editorial'),
            'resenia' => $request->input('resenia'),
            'anio_publicacion' => $request->input('anio_publicacion'),
            'link_drive' => $request->input('link_drive'),
            'carrera' => $request->input('carrera'),
            'area' => $request->input('area'),
            'categoria' => $request->input('categoria'),
            'usuario_creacion' => $request->input('usuario_creacion'),
            'fecha_creacion' => $request->input('fecha_creacion'),
            'fisico' => $request->input('fisico'),
            'documento' => $request->input('documento'),
            'estado' => $estado,
            'eliminado' => $estado, 
        ];

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            $firebase_storage_path = 'Images/';

            // Genera un nombre único para la imagen
            $name = time() . '.' . $imagen->getClientOriginalExtension();

            // Sube la imagen a Firebase Storage
            $uploadedFile = fopen($imagen->path(), 'r');
            app('firebase.storage')->getBucket()->upload($uploadedFile, [
                'name' => $firebase_storage_path . $name,
            ]);

            // Asigna la URL de la imagen a los datos del libro
            $imagenUrl = $firebase_storage_path . $name;
            $datos['imagen'] = $imagenUrl;

            // Elimina el archivo temporal en Laravel
            unlink($imagen->path());
        } elseif (!$request->hasFile('imagen') && empty($request->input('imagen'))) {
            // Si no se proporciona una nueva imagen y el campo 'imagen' está vacío,
            // conservamos la URL existente de la imagen en los datos del libro
            $datos['imagen'] = $referencia->getChild('imagen')->getValue();
        }

        $referencia->update($datos);

        return redirect('/listado-libro');
    }




    //Funcion para eliminar de manera logica los registros
    public function eliminarRegistroLibro($clave)
    {
        $referencia = $this->database->getReference('libreria/libro/' . $clave);
        $referencia->update([
            'estado' => 'Inactivo'
        ]);

        return redirect('/listado-libro');
    }

    public function combosVerRegistrosLibro($clave)
    {
        $referencia = $this->database->getReference('libreria/libro/' . $clave);
        $registro = $referencia->getValue();
        $carreras = $this->database->getReference('libreria/carrera')->getValue();
        $areas = $this->database->getReference('libreria/area')->getValue();
        $categorias = $this->database->getReference('libreria/categoria')->getValue();



        return view('Libro.VerLibro', [
            'registro' => $registro,
            'clave' => $clave,
            'carreras' => $carreras,
            'areas' => $areas,
            'categorias' => $categorias
        ]);
    }
    public function generarListadoPDF()
    {
        $referencia = $this->database->getReference('libreria/libro');
        $registros = $referencia->getValue();
        $nombresClave = $referencia->getChildKeys();

        $html = '<h1>Listado de Libros</h1>';
        $html .= '<table border="1" cellspacing="0" cellpadding="10">';
        $html .= '<tr><th>Titulo</th><th>Año Publicación</th><th>Area</th><th>Carrera</th><th>Editorial</th></tr>';

        foreach ($registros as $clave => $registro) {
            if (isset($registro['estado']) && $registro['estado'] === 'Activo' || $registro['estado'] === 'Reservado') {
                $html .= '<tr>';
                $html .= '<td>' . $registro['nombre'] . '</td>';
                $html .= '<td>' . $registro['anio_publicacion'] . '</td>';
                $html .= '<td>' . $registro['area'] . '</td>';
                $html .= '<td>' . $registro['carrera'] . '</td>';
                $html .= '<td>' . $registro['nombre_editorial'] . '</td>';
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
        $dompdf->stream('listado_libros.pdf');

        // You may also save the PDF file if needed
        // $output = $dompdf->output();
        // file_put_contents('listado_libros.pdf', $output);
    }

    public function generarListadoExcel()
    {
        $referencia = $this->database->getReference('libreria/libro');
        $registros = $referencia->getValue();
        $nombresClave = $referencia->getChildKeys();

        // Crear un nuevo libro de trabajo (spreadsheet)
        $spreadsheet = new Spreadsheet();

        // Obtener la hoja activa
        $sheet = $spreadsheet->getActiveSheet();

        // Agregar encabezados
        $sheet->setCellValue('A1', 'Título');
        $sheet->setCellValue('B1', 'Año Publicación');
        $sheet->setCellValue('C1', 'Área');
        $sheet->setCellValue('D1', 'Carrera');
        $sheet->setCellValue('E1', 'Editorial');

        // Llenar el contenido de la hoja con los datos de los libros
        $row = 2;
        foreach ($registros as $clave => $registro) {
            if (isset($registro['estado']) && $registro['estado'] === 'Activo' || $registro['estado'] === 'Reservado') {
                $sheet->setCellValue('A' . $row, $registro['nombre']);
                $sheet->setCellValue('B' . $row, $registro['anio_publicacion']);
                $sheet->setCellValue('C' . $row, $registro['area']);
                $sheet->setCellValue('D' . $row, $registro['carrera']);
                $sheet->setCellValue('E' . $row, $registro['nombre_editorial']);
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

    public function buscarLibro(Request $request)
    {
        // Obtén el valor del parámetro de búsqueda desde la solicitud
        $query = $request->input('query');

        // Obtén resultados de Firebase utilizando la función buscarEnFirebase
        $resultados = $this->buscarEnFirebase($query);

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

        return view('Libro.listadoLibro', [
            'registros' => $registrosPaginados,
        ]);
    }




    private function buscarEnFirebase($query)
    {
        $categoriaRef = $this->database->getReference('libreria/libro')->getValue();
        $results = [];

        foreach ($categoriaRef as $key => $value) {
            // Buscar en varios campos
            if (
                (isset($value['nombre_area']) && stripos($value['nombre_area'], $query) !== false) ||
                (isset($value['nombre']) && stripos($value['nombre'], $query) !== false) ||
                (isset($value['anio_publicacion']) && stripos($value['anio_publicacion'], $query) !== false) ||
                (isset($value['area']) && stripos($value['area'], $query) !== false) ||
                (isset($value['carrera']) && stripos($value['carrera'], $query) !== false) ||
                (isset($value['autor']) && stripos($value['autor'], $query) !== false) ||
                (isset($value['nombre_editorial']) && stripos($value['nombre_editorial'], $query) !== false)
            ) {
                $results[] = $value; // Agregar todo el registro a los resultados
            }
        }

        return $results;
    }


        public function eliminarLibrosSeleccionados(Request $request)
        {
            try {
                // Validar la solicitud
                $request->validate([
                    'libros_seleccionados' => 'required|array',
                ]);

                // Obtener los IDs de los libros seleccionados
                $librosSeleccionados = $request->input('libros_seleccionados', []);

                // Iterar sobre los libros seleccionados y actualizar su estado
                foreach ($librosSeleccionados as $libroId) {
                    $libroRef = $this->database->getReference('libreria/libro/' . $libroId);
                    $libroRef->update([
                        'estado' => 'Inactivo'
                    ]);
                }

                return redirect()->back()->with('success', 'Libros eliminados correctamente');
            } catch (\Exception $e) {
                // Manejar la excepción
                return redirect()->back()->with('error', 'Error al eliminar libros: ' . $e->getMessage());
            }
        }


}