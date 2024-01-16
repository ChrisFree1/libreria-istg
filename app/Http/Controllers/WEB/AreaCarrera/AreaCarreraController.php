<?php

namespace App\Http\Controllers\WEB\AreaCarrera;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class AreaCarreraController extends Controller
{
    private $database;

    //funcion para conectar a la base de datos de FireBase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();
    }

    public function listarCarreraFormulario()
    {
        $carreras = $this->database->getReference('libreria/carrera')->getValue();
        $areas = $this->database->getReference('libreria/area')->getValue();

        return view('AreaPorCarrera.crearAreaCarrera', ['carreras' => $carreras, 'areas' => $areas]);
    }

    public function guardarCarreraPorArea(Request $request)
    {

        $estado = "Activo";
        $datos = [
            'nombre_carrera' => $request->input('carrera'),
            'nombre_area' => $request->input('area'),
            'prefijo' => $request->input('prefijo'),
            'estado' => $estado,

        ];

        $firebase = $this->database->getReference('libreria/area_carrera');

        $libroRef = $firebase->push()->set($datos);




        return redirect('/listado-area-carreras');
        // Redirigir a la página de inicio u otra página


    }

    public function listarAreaCarrera()
    {
        $referencia = $this->database->getReference('libreria/area_carrera');
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

        return view('AreaPorCarrera.listarAreaCarrera', [
            'registros' => $registrosPaginados,
            'nombresClave' => $nombresClave,
        ]);
    }

    public function eliminarRegistroAreaCarrera($clave)
    {
        $referencia = $this->database->getReference('libreria/area_carrera/' . $clave);
        $referencia->update([
            'estado' => 'Inactivo'
        ]);

        return redirect('/listado-area-carreras');
    }




    public function datosAreaCarrera($clave)
    {
        $referencia = $this->database->getReference('libreria/area_carrera/' . $clave);
        $registro = $referencia->getValue();
        $carreras = $this->database->getReference('libreria/carrera')->getValue();
        $areas = $this->database->getReference('libreria/area')->getValue();

        // Crear un arreglo con índices numéricos para carreras y áreas
        $carrerasIndexed = array_values($carreras);
        $areasIndexed = array_values($areas);

        return view('AreaPorCarrera.editarAreaCarrera', [
            'clave' => $clave,
            'registro' => $registro,
            'carreras' => $carrerasIndexed,
            'areas' => $areasIndexed
        ]);
    }



    public function actualizarAreaCarrera(Request $request, $clave)
    {


        $referencia = $this->database->getReference('libreria/area_carrera/' . $clave);
        $estado = "Activo";

        $referencia->set([
            'nombre_carrera' => $request->input('carrera'),
            'nombre_area' => $request->input('area'),
            'prefijo' => $request->input('prefijo'),
            'estado' => $estado,
        ]);

        return redirect('/listado-area-carreras');
    }


    public function buscarAreaCarrera(Request $request)
    {
        // Obtén el valor del parámetro de búsqueda desde la solicitud
        $query = $request->input('query');

        // Obtén resultados de Firebase utilizando la función buscarEnFirebase
        $resultados = $this->buscarEnFirebaseCarrera($query);

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

        return view('AreaPorCarrera.listarAreaCarrera', [
            'registros' => $registrosPaginados,
        ]);
    }

    private function buscarEnFirebaseCarrera($query)
    {
        // Obtén todos los registros de Firebase
        $categoriaRef = $this->database->getReference('libreria/area_carrera')->getValue();
        $results = [];

        foreach ($categoriaRef as $key => $value) {
            // Buscar en el campo 'nombre_carrera' y considerar solo los registros con estado "Activo"
            if (
                (isset($value['nombre_carrera']) && stripos($value['nombre_carrera'], $query) !== false) ||
                (isset($value['prefijo']) && stripos($value['prefijo'], $query) !== false)
            ) {
                $results[] = $value;
            }
        }

        return $results;
    }
}