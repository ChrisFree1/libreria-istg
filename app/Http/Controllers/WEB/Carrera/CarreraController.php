<?php

namespace App\Http\Controllers\WEB\Carrera;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class CarreraController extends Controller
{
    private $database;

    //funcion para conectar a la base de datos de FireBase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();
    }

    public function guardarCarrera(Request $request)
    {


        $estado = "Activo";
        // Obtener los datos del formulario
        $datos = [
            'nombre_carrera' => $request->input('carrera'),
            'estado' => $estado,
        ];

        $firebase = $this->database->getReference('libreria/carrera');

        $libroRef = $firebase->push()->set($datos);




        return redirect('/registar-carrera');

        // Redirigir a la página de inicio u otra página


    }
    public function listarRegistros()
    {
        $referencia = $this->database->getReference('libreria/carrera');
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

        return view('Carrera.listarCarrera', compact('registrosPaginados', 'nombresClave'));
    }



    public function eliminarRegistroCarrera($clave)
    {
        $referencia = $this->database->getReference('libreria/carrera/' . $clave);
        $referencia->update([
            'estado' => 'Inactivo'
        ]);

        return redirect('/listado-carreras');
    }


    public function actualizarCarrera(Request $request, $clave)
    {


        $referencia = $this->database->getReference('libreria/carrera/' . $clave);
        $estado = "Activo";

        $referencia->set([
            'nombre_carrera' => $request->input('carrera'),
            'estado' => $estado,
        ]);

        return redirect('/listado-carreras');
    }



    public function datosCarrera($clave)
    {
        $referencia = $this->database->getReference('libreria/carrera/' . $clave);
        $registro = $referencia->getValue();

        return view('Carrera.editarCarrera', [
            'registro' => $registro,
            'clave' => $clave
        ]);
    }

    public function buscarCarrera(Request $request)
{
    // Obtén el valor del parámetro de búsqueda desde la solicitud
    $query = $request->input('carrera');

    // Obtén resultados de Firebase utilizando la función buscarEnFirebase
    $results = $this->buscarEnFirebase($query);

    // Obtén todos los registros de Firebase
    $registros = $this->database->getReference('libreria/carrera')->getValue();

    // Número de registros por página
    $perPage = 20;

    // Página actual
    $currentPage = Paginator::resolveCurrentPage() ?: 1;

    // Crear una instancia de Collection
    $collection = new Collection($results);

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

    return view('Carrera.listarCarrera',compact('registrosPaginados'));
}

private function buscarEnFirebase($query)
{
    // Obtén todos los registros de Firebase
    $categoriaRef = $this->database->getReference('libreria/carrera')->getValue();
    $results = [];

    foreach ($categoriaRef as $key => $value) {
        // Buscar en el campo 'nombre_carrera' y considerar solo los registros con estado "Activo"
        if (
            isset($value['nombre_carrera']) &&
            isset($value['estado']) &&
            stripos($value['nombre_carrera'], $query) !== false &&
            $value['estado'] == 'Activo'
        ) {
            $results[] = [
                'key' => $key,
                'nombre_carrera' => $value['nombre_carrera'],
                'estado' => $value['estado'],
            ];
        }
    }

    return $results;
}

}