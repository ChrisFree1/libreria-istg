<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\API\LibroReciente\LibroRecienteAPIController;
use App\Http\Controllers\API\Libro\LibroAPIController;
use App\Http\Controllers\API\AreaCarrera\AreaCarreraAPIController;
use App\Http\Controllers\API\Usuario\UsuarioAPIController;
use App\Http\Controllers\API\Reservas\LibroReservaAPIController;
use App\Http\Controllers\API\Carreras\CarrerasAPIController;
use App\Http\Controllers\API\ListadoLibros\ListadoLibrosAPIController;
use App\Http\Controllers\API\Parametros\ParametrosAPIController;
use App\Http\Controllers\API\Sugerencia\SugerenciasAPIController;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// -------------------------------------------- WEB SERVICE PARA LISTAR TODOS LOS LIBROS -------------------------

Route::middleware(['firebaseAuth'])->group(function () {

Route::get('/listar-libros', [LibroAPIController::class, 'listarLibros']); 


// -------------------------------------------- WEB SERVICE PARA LISTAR LIBROS POR AUTOR -------------------------

Route::get('/listar-libros-autor/{resultado_autor}', [LibroAPIController::class, 'libroAutor']);


// -------------------------------------------- WEB SERVICE PARA LISTAR LIBROS POR EL TITULO -------------------------

Route::get('/listar-libros-titulo/{resultado_titulo}', [LibroAPIController::class, 'libroTitulo']);



// -------------------------------------------- WEB SERVICE PARA LISTAR LIBROS INACTIVOS ( ¡ADMINISTRADOR! ) -------------------------

Route::get('/listar-libros-inactivos', [LibroAPIController::class, 'libroInactivo']); // Libro Inactivos


// -------------------------------------------- WEB SERVICE PARA LISTAR LIBROS POR FECHA PUBLICACION -------------------------

Route::get('/listar-libros-fecha-publicacion/{resultado_fechaPublicacion}', [LibroAPIController::class, 'libroFechaPublicacion']);// Libro por Fecha Publicación


// -------------------------------------------- WEB SERVICE PARA LIBROS RECIENTES  -------------------------

Route::post('registrar-libros-recientes/{uid}', [LibroRecienteAPIController::class, 'regristrarLibroReciente']);

Route::get('/listar-libros-recientes/{uid}', [LibroRecienteAPIController::class, 'listarLibrosRecientesPorUid']); 


// -------------------------------------------- WEB SERVICE PARA LISTAR LIBROS FISICOS -------------------------

Route::get('/listar-libros-fisicos', [LibroAPIController::class, 'listarLibrosFisicos']); 


// -------------------------------------------- WEB SERVICE PARA LISTAR LIBROS FISICOS Y DOCUMENTOS -------------------------

Route::get('/listar-libros-fisicos-documento', [LibroAPIController::class, 'libroFisicosTodasCarreras']); 

// -------------------------------------------- WEB SERVICE PARA LISTAR LIBROS NO FISICOS -------------------------

Route::get('/listar-libros-no-fisicos', [LibroAPIController::class, 'listarLibrosNoFisicos']); 

// ------------------------------------------- WEB SERVICE PARA LISTAR TODAS LAS CARRERAS -------------------------

Route::get('/listar-carreras', [CarrerasAPIController::class, 'listarCarreras']); 


// ------------------------------------------- WEB SERVICE PARA LISTAR TODAS CARRERAS CON AUTOR -------------------------

Route::get('/listar-campo-carrera/{carrera}', [CarrerasAPIController::class, 'listarLibrosFisicosCarreras']); 


// ------------------------------------------- WEB SERVICE PARA LISTAR CARRERAS AREAS -----------------------------

Route::get('/listar-carreras-areas/{carrera}/{area}', [ListadoLibrosAPIController::class, 'listarCarrerasAreas']); 


// ------------------------------------------  WEB SERVICE PARA LISTAR CARRERAS AREAS POR AUTOR --------------------

Route::get('/listar-carreras-areas-autor/{carrera}/{area}/{autor}', [ListadoLibrosAPIController::class, 'listarCarrerasAreasAutor']); 


// ------------------------------------------- WEB SERVICE PARA OBTENER LOS PREFIJOS POR CARRERA --------------------

Route::get('/listar-prefijos-carreras/{carrera}', [CarrerasAPIController::class, 'listarPrefijoCarrera']); 


// ------------------------------------------- WEB SERVICE PARA OBTENER AUTORES POR CARRERA Y AREA -------------------

Route::get('/listar-autores-por-carreras-areas/{carrera}/{area}', [ListadoLibrosAPIController::class, 'listarAutoresLibros']); 


// ------------------------------------------  CARRERA POR AREA WEB SERVICE -------------------------------------------

Route::get('/listar-carreas-por-areas/{prefijo}', [AreaCarreraAPIController::class, 'listarAreasPorCarrera']); 


// ------------------------------------------- CRUD USUARIOS WEB SERVICE -----------------------------------------------------

Route::post('registrar-usuarios', [UsuarioAPIController::class, 'registrarUsuarioApp']);

Route::put('/actualizar-usuario', [UsuarioAPIController::class, 'actualizarUsuarioPorCorreo']);

Route::get('/listar-usuario-creado/{correo}', [UsuarioAPIController::class, 'listarCamposPorCorreo']); 


// ------------------------------------- CRUD RESERVAS WEB SERVICE -----------------------------------------------------

Route::post('registrar-reserva-app', [LibroReservaAPIController::class, 'regristrarReseveraApp']);

Route::get('/listar-reserva-creada/{correo}', [LibroReservaAPIController::class, 'listarReservasPorCorreo']); 

Route::put('/actualizar-reserva-usuario/{id}', [LibroReservaAPIController::class, 'actualizarLibroReserva']);

Route::get('/eliminar-reserva-usuario/{id}', [LibroReservaAPIController::class, 'eliminarLibroReserva']);

Route::get('/listar-reserva-creada-documento/{id}', [LibroReservaAPIController::class, 'obtenerReservaPorId']); 


// --------------------------------- CRUD DE PARAMETROS WEB SERVICE ------------------------------------

Route::get('/listar-valor-parametro/{codigo}', [ParametrosAPIController::class, 'listarValorParametro']); 

Route::get('/listar-parametro', [ParametrosAPIController::class, 'listadoParametro']); 

Route::post('/registrar-parametro', [ParametrosAPIController::class, 'registrarParametro']); 

Route::put('/actualizar-parametro', [ParametrosAPIController::class, 'actualizarParametro']); 

Route::get('/eliminar-parametro/{id}', [ParametrosAPIController::class, 'eliminarParametro']); 


// ------------------------------- WEB SERVICE PARA REGISTRAR SUGERENCIA ---------------------------------------

Route::post('registrar-sugerencia-app', [SugerenciasAPIController::class, 'registrarSugerenciaApp']);


});
