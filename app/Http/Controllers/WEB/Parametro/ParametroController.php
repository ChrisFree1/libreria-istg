<?php

namespace App\Http\Controllers\WEB\Parametro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;




class ParametroController extends Controller
{
    private $database;

    //funcion para conectar a la base de datos de FireBase
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();
    }

    public function guardarParametro(Request $request)
    {

        $codigo = $request->input('codigo');
        $nombre_parametro = $request->input('parametro');
        $descripcion = $request->input('descripcion');
        $valor = $request->input('valor');
        $estado = 'Activo';


        // Obtener los datos del formulario
        $datos = [
            'codigo' => $codigo,
            'nombre_parametro' => $nombre_parametro,
            'descripcion' => $descripcion,
            'valor' => $valor,
            'estado' => $estado,
        ];



        $firebase = $this->database->getReference('libreria/parametro');

        $libroRef = $firebase->push()->set($datos);





        return redirect('/registrar-parametro');
    }


    public function listarParametro()
    {
        $referencia = $this->database->getReference('libreria/parametro');
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
        $registrosPaginados = new LengthAwarePaginator(
            $collection->forPage($currentPage, $perPage),
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
            ]
        );

        return view('Parametro.listarParametro', compact('registrosPaginados'));
    }





    public function eliminarRegistroParametro($clave)
    {
        $referencia = $this->database->getReference('libreria/parametro/' . $clave);
        $referencia->update([
            'estado' => 'Inactivo'
        ]);

        return redirect('/listado-parametros');
    }


    public function actualizarParametro(Request $request, $clave)
    {


        $referencia = $this->database->getReference('libreria/parametro/' . $clave);
        $estado = "Activo";

        $referencia->set([
            'codigo' => $request->input('codigo'),
            'nombre_parametro' => $request->input('parametro'),
            'descripcion' => $request->input('descripcion'),
            'valor' => $request->input('valor'),
            'estado' => $estado,
        ]);

        return redirect('/listado-parametros');
    }



    public function datosParametros($clave)
    {
        $referencia = $this->database->getReference('libreria/parametro/' . $clave);
        $registro = $referencia->getValue();
        return view('Parametro.editarParametro', [
            'registro' => $registro,
            'clave' => $clave,
        ]);
    }

    public function buscarParametro(Request $request)
    {
        // Obtén el valor del parámetro de búsqueda desde la solicitud
        $query = $request->input('parametro');

        // Obtén una referencia a la ubicación de Firebase
        $parametrosRef = $this->database->getReference('libreria/parametro');

        // Inicializa el filtro sin condición
        $filtro = $parametrosRef;

        // Aplica el filtrado solo si $query no es nulo
        if ($query !== null) {
            // Aplica el filtrado directamente en Firebase
            $filtro = $filtro->orderByChild('codigo')->startAt($query)->endAt($query . "\uf8ff");
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
        $registrosPaginados = new LengthAwarePaginator(
            $collection->forPage($currentPage, $perPage),
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
            ]
        );

        // Devuelve la vista con los resultados paginados
        return view('Parametro.listarParametro', compact('registrosPaginados'));
    }







    public function verificarCodigoUnico(Request $request)
    {
        $codigo = $request->input('codigo');

        // Obtener la referencia a la ubicación en Firebase
        $parametrosRef = $this->database->getReference('libreria/parametro');

        // Utilizar el método orderByChild para filtrar por código
        $query = $parametrosRef->orderByChild('codigo')->equalTo($codigo);

        // Obtener los resultados
        $resultados = $query->getValue() ?? [];

        // Verificar si hay algún resultado
        $existe = !empty($resultados);

        // Devolver la respuesta en formato JSON
        return response()->json(['existe' => $existe]);
    }
}