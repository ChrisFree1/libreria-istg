<?php

namespace App\Http\Controllers\WEB\Area;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class AreaController extends Controller
{
    private $database;

    //funcion para conectar a la base de datos de FireBase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();
    }


    public function guardarArea(Request $request)

    {

        $estado = "Activo";
        // Obtener los datos del formulario
        $datos = [
            'nombre_area' => $request->input('area'),
            'estado' => $estado,
        ];

        $firebase = $this->database->getReference('libreria/area');

        $libroRef = $firebase->push()->set($datos);



        return redirect('/registrar-area');
    }


    public function listarArea()
    {
        $referencia = $this->database->getReference('libreria/area');
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

        return view('Area.listarArea', [
            'registros' => $registrosPaginados,
            'nombresClave' => $nombresClave,
        ]);
    }


    public function eliminarRegistroArea($clave)
    {
        $referencia = $this->database->getReference('libreria/area/' . $clave);
        $referencia->update([
            'estado' => 'Inactivo'
        ]);

        return redirect('/listado-areas');
    }


    public function actualizarArea(Request $request, $clave)
    {


        $referencia = $this->database->getReference('libreria/area/' . $clave);
        $estado = "Activo";

        $referencia->set([
            'nombre_area' => $request->input('area'),
            'estado' => $estado,
        ]);

        return redirect('/listado-areas');
    }



    public function datosAreas($clave)
    {
        $referencia = $this->database->getReference('libreria/area/' . $clave);
        $registro = $referencia->getValue();


        return view('Area.editarArea', [
            'registro' => $registro,
            'clave' => $clave
        ]);
    }

    public function buscarArea(Request $request)
    {
        // Obtén el valor del parámetro de búsqueda desde la solicitud
        $query = $request->input('area');

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

        return view('Area.listarArea', [
            'registros' => $registrosPaginados,
        ]);
    }

    private function buscarEnFirebase($query)
    {
        // Obtén todos los registros de Firebase
        $categoriaRef = $this->database->getReference('libreria/area')->getValue();
        $results = [];

        foreach ($categoriaRef as $key => $value) {
            // Buscar en el campo 'nombre_area' y considerar solo los registros con estado "Activo"
            if (
                isset($value['nombre_area']) &&
                isset($value['estado']) &&
                stripos($value['nombre_area'], $query) !== false &&
                $value['estado'] == 'Activo'
            ) {
                $results[] = [
                    'key' => $key,
                    'nombre_area' => $value['nombre_area'],
                    'estado' => $value['estado'],
                ];
            }
        }

        return $results;
    }
}