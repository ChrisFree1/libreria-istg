<?php

namespace App\Http\Controllers\WEB\Categoria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;


class CategoriaController extends Controller
{
    private $database;

    //funcion para conectar a la base de datos de FireBase

    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();

    }




    public function guardarCategoria(Request $request)
    {

        $estado= "Activo";
        $datos = [
            'nombre_categoria' => $request->input('categoria'),
            'estado' => $estado,
        ];
        
        $firebase = $this->database->getReference('libreria/categoria');
        
        $libroRef = $firebase->push()->set($datos);
        

        

        return redirect('/registar-categoria');
        // Redirigir a la página de inicio u otra página

        
    }   


    public function eliminarRegistroCategoria($clave)
    {
        $referencia = $this->database->getReference('libreria/categoria/' . $clave);
        $referencia->update([
            'estado' => 'Inactivo'
        ]);

        return redirect('/listado-categorias');
    }

    public function actualizarCategoria(Request $request, $clave)
    {


        $referencia = $this->database->getReference('libreria/categoria/' . $clave);
        $estado = "Activo";

        $referencia->set([
            'nombre_categoria' => $request->input('categoria'),
            'estado' => $estado,
        ]);

        return redirect('/listado-categorias');
    }



    public function datosCategoria($clave)
    {
        $referencia = $this->database->getReference('libreria/categoria/' . $clave);
        $registro = $referencia->getValue();





        return view('Categoria.editarCategoria', ['registro' => $registro,
                                        'clave' => $clave]);
    }



    private function buscarEnFirebase($query)
    {
        $categoriaRef = $this->database->getReference('libreria/categoria')->getValue();


        $results = [];

        foreach ($categoriaRef as $key => $value) {
            if (isset($value['nombre_categoria']) && isset($value['estado']) && stripos($value['nombre_categoria'], $query) !== false) {

                $results[] = [
                    'key' => $key,
                    'nombre_categoria' => $value['nombre_categoria'],
                    'estado' => $value['estado'],
                ];

            }
        }

        return $results;

    }


    public function listarCategoria()
    {
        $referencia = $this->database->getReference('libreria/categoria');
        $registros = $referencia->getValue();

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
        $registros = new LengthAwarePaginator(
            $collection->forPage($currentPage, $perPage),
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
            ]
        );

        return view('Categoria.listarCategoria', compact('registros'));
    }


    public function buscarCategoria(Request $request)
    {
        // Obtén el valor del parámetro de búsqueda desde la solicitud
        $query = $request->input('categoria');

        // Obtén una referencia a la ubicación de Firebase
        $categoriaRef = $this->database->getReference('libreria/categoria');

        // Inicializa el filtro sin condición
        $filtro = $categoriaRef;

        // Aplica el filtrado solo si $query no es nulo
        if ($query !== null) {
            // Aplica el filtrado directamente en Firebase
            $filtro = $filtro->orderByChild('nombre_categoria')->startAt($query)->endAt($query . "\uf8ff");
        }

        // Obtiene los resultados
        $resultados = $filtro->getValue();

        // Filtra solo los resultados con estado "Activo"
        $resultadosActivos = array_filter($resultados, function ($resultado) {
            return isset($resultado['estado']) && $resultado['estado'] == 'Activo';
        });

        // Número de registros por página
        $perPage = 20;

        // Página actual
        $currentPage = Paginator::resolveCurrentPage() ?: 1;

        // Crear una instancia de Collection
        $collection = new Collection($resultadosActivos);

        // Crear un paginador Laravel
        $registros = new LengthAwarePaginator(
            $collection->forPage($currentPage, $perPage),
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
            ]
        );

        // Devuelve la vista con los resultados paginados
        return view('Categoria.listarCategoria', compact('registros'));
    }

}