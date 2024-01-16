<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WEB\Libro\LibroController;
use App\Http\Controllers\WEB\Reserva\LibroReservaController;
use App\Http\Controllers\WEB\Carrera\CarreraController;
use App\Http\Controllers\WEB\Area\AreaController;
use App\Http\Controllers\WEB\AreaCarrera\AreaCarreraController;
use App\Http\Controllers\WEB\Categoria\CategoriaController;
use App\Http\Controllers\WEB\Excel\ExcelImportController;
use App\Http\Controllers\WEB\Parametro\ParametroController;
use App\Http\Controllers\WEB\FirebaseAnalytics\FirebaseAnalyticsController;
use App\Http\Controllers\WEB\ReporteriaEstudiantes\ReporteriaEstudiantesController;

Route::get('/', function () { return view('welcome');});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('user');

// Route::get('/home/customer', [App\Http\Controllers\HomeController::class, 'customer'])->middleware('user','fireauth');

Route::get('/email/verify', [App\Http\Controllers\Auth\ResetController::class, 'verify_email'])->name('verify')->middleware('fireauth');

Route::post('login/{provider}/callback', 'Auth\LoginController@handleCallback');

Route::resource('/home/profile', App\Http\Controllers\Auth\ProfileController::class)->middleware('user','fireauth');

Route::resource('/password/reset', App\Http\Controllers\Auth\ResetController::class);




Route::middleware(['fireauth'])->group(function () {

// --------------------------------- VENTANA PRINCIPAL --------------------------------------------

Route::get('/pantalla-principal', function () { return view('Mantenimientos.mantenimientos');});

Route::post('/cantidad-estudiantes-carrera',[ReporteriaEstudiantesController::class,'obtenerCantidadEstudiantesPorCarrera'])->name('cantidad-estudiantes-carrera');


// --------------------------------- VENTANA ESTADISTICAS --------------------------------------------
/*
Route::get('/analytics-dashboard', [FirebaseAnalyticsController::class, 'showAnalyticsDashboard']);

Resolver problemas con la funcion de la biblioteca 

Route::get('/estadisticas-app', [FirebaseAnalyticsController::class,'showAnalyticsDashboard']);
*/

Route::get('/estadisticas-app', function () { return view('Analiticas.estadistica');});

Route::get('/reporte-carreras-pdf', [ReporteriaEstudiantesController::class, 'generarReporteCarrerasPDF'])->name('reporte-carreras-pdf');


// ------------------------------------------------ CRUD REGISTROS LIBROS ----------------------------------------

Route::get('/listado-libros', [LibroController::class, 'listarRegistrosLibros'])->name('listado-libros');

Route::get('/listado-libro', [LibroController::class, 'listarLibro'])->name('listado-libro');

Route::post('/registro-libro', [LibroController::class,'guardarRegistroLibro'])->name('registrolibro');

Route::get('/registro-libro', [LibroController::class,'listarCombosFormulario']);

Route::get('/listado-libro', [LibroController::class,'listarRegistrosLibros']);

Route::get('/editar-registro-libro/{clave}',[LibroController::class,'combosEditarRegistrosLibros'])->name('editar-registro-libro');

Route::post('/actualizar-registro-libro/{clave}', [LibroController::class,'actualizarRegistroLibro'])->name('actualizar-registro-libro');

Route::get('/eliminar-registro-libro/{clave}', [LibroController::class, 'eliminarRegistroLibro'])->name('eliminar-registro-libro');

Route::get('/ver-registro-libro/{clave}',[LibroController::class,'combosVerRegistrosLibro'])->name('ver-registro-libro');

Route::get('/pdf', [LibroController::class,'generarListadoPDF'])->name('pdf');

Route::get('/generar-listado-excel', [LibroController::class,'generarListadoExcel'])->name('excel-libros');

Route::get('/buscar-libro',[LibroController::class,'buscarLibro'])->name('buscar-libro');

Route::post('/eliminar-libros-seleccionados',[LibroController::class,'eliminarLibrosSeleccionados'])->name('eliminar-libros-seleccionados');

// ----------------------------------------------- CRUD LIBROS RESERVAS ---------------------------------------------


Route::get('/listado-reservas', [LibroReservaController::class, 'listarRegistrosLbReserva']);

Route::get('/crear-libro-reserva', [LibroReservaController::class,'listarCarreraFormulario']);

Route::post('/registro-libro-reserva', [LibroReservaController::class,'guardarReserva'])->name('registroLibroReserva');

Route::get('/eliminar-registro-reserva/{clave}', [LibroReservaController::class, 'eliminarRegistroLibroReserva'])->name('eliminar-registro-reserva');

Route::get('/entregar-registro-reserva/{clave}', [LibroReservaController::class, 'entregarRegistroLibroReserva'])->name('entregar-registro-reserva');

Route::get('/devolver-registro-reserva/{clave}', [LibroReservaController::class, 'libroReservaDevuelta'])->name('devolver-registro-reserva');

Route::get('/editar-registro-reserva/{clave}',[LibroReservaController::class,'combosEditarRegistrosReservas'])->name('editar-registro-reserva');

Route::get('/ver-registro-reserva/{clave}',[LibroReservaController::class,'combosVerRegistrosReservas'])->name('ver-registro-reserva');

Route::post('/actualizar-registro-reserva/{clave}', [LibroReservaController::class,'actualizarRegistroReserva'])->name('actualizar-registro-reserva');

Route::get('/pdf-reservas', [LibroReservaController::class,'generarListadoPDF'])->name('pdf');

Route::get('/generar-listado-reserva-excel', [LibroReservaController::class,'generarListadoExcel'])->name('excel-reserva');

Route::get('/buscar-reserva',[LibroReservaController::class,'buscarReserva'])->name('buscar-reserva');

Route::get('/liberar-reservas',[LibroReservaController::class,'liberarReservas'])->name('liberar.reservas');

Route::get('/listarRegistrosLbReserva',[LibroReservaController::class,'listarRegistrosLbReserva'])->name('listarRegistrosLbReserva');

// ----------------------------------------------- CRUD AREAS ---------------------------------------------------------

Route::post('/registro-area', [AreaController::class,'guardarArea'])->name('registroArea');

Route::get('/listado-areas', [AreaController::class, 'listarArea']);

Route::get('/listar-area',[AreaController::class,'listarArea'])->name('listar-area');

Route::get('/editar-area/{clave}',[AreaController::class,'datosAreas'])->name('editar-area');

Route::get('/eliminar-area/{clave}', [AreaController::class, 'eliminarRegistroArea'])->name('eliminar-area');

Route::post('/actualizar-area/{clave}', [AreaController::class,'actualizarArea'])->name('actualizar-area');

Route::get('/registrar-area', function () { return view('Area.crearArea');});

Route::get('/buscar-area',[AreaController::class,'buscarArea'])->name('buscar-area');


// ----------------------------------------------- CRUD CARRERAS ---------------------------------------------------------

Route::post('/registro-carrera', [CarreraController::class,'guardarCarrera'])->name('registroCarrera');

Route::get('/listado-carreras', [CarreraController::class, 'listarRegistros']);

Route::get('/registar-carrera', function () { return view('Carrera.crearCarrera');});

Route::get('/eliminar-carrera/{clave}', [CarreraController::class, 'eliminarRegistroCarrera'])->name('eliminar-carrera');

Route::post('/actualizar-carrera/{clave}', [CarreraController::class,'actualizarCarrera'])->name('actualizar-carrera');

Route::get('/editar-carrera/{clave}',[CarreraController::class,'datosCarrera'])->name('editar-area');

Route::get('/buscar-carrera',[CarreraController::class,'buscarCarrera'])->name('buscar-carrera');

Route::get('/listar-carrera', [CarreraController::class, 'listarRegistros'])->name('listar-carrera');

// ----------------------------------------------- CRUD CARRERAS POR AREAS ---------------------------------------------------------

Route::get('/listado-area-carreras', [AreaCarreraController::class, 'listarAreaCarrera'])->name('listado-area-carrera');

Route::get('/crear-area-carrera', [AreaCarreraController::class,'listarCarreraFormulario']);

Route::post('/registro-area-carrera', [AreaCarreraController::class,'guardarCarreraPorArea'])->name('registroAreaCarrera');      

Route::get('/eliminar-area-carrera/{clave}', [AreaCarreraController::class, 'eliminarRegistroAreaCarrera'])->name('eliminar-area-carrera');

Route::get('/editar-area-carrera/{clave}',[AreaCarreraController::class,'datosAreaCarrera'])->name('editar-area-carrera');

Route::post('/actualizar-area-carrera/{clave}', [AreaCarreraController::class,'actualizarAreaCarrera'])->name('actualizar-area-carrera');

Route::get('/buscar-area-carrera',[AreaCarreraController::class,'buscarAreaCarrera'])->name('buscar-area-carrera');

Route::get('/listarAreaCarrera', [AreaCarreraController::class, 'listarAreaCarrera'])->name('listarAreaCarrera');

// ----------------------------------------------- CRUD CATEGORIAS ---------------------------------------------------------


Route::get('/listado-categorias', [CategoriaController::class, 'listarCategoria'])->name('listado-categorias');

Route::get('/registar-categoria', function () { return view('Categoria.crearCategoria');});

Route::post('/registro-categoria', [CategoriaController::class,'guardarCategoria'])->name('registroCategoria');

Route::get('/eliminar-categoria/{clave}', [CategoriaController::class, 'eliminarRegistroCategoria'])->name('eliminar-categoria');

Route::get('/editar-categoria/{clave}',[CategoriaController::class,'datosCategoria'])->name('editar-categoria');

Route::post('/actualizar-categoria/{clave}', [CategoriaController::class,'actualizarCategoria'])->name('actualizar-categoria');

Route::get('/buscar-categoria',[CategoriaController::class,'buscarCategoria'])->name('buscar-categoria');


// ------------------------------ INGRESO LIBRO EXCEL ------------------------------------------------

Route::get('/registar-libro-excel', function () { return view('LibroExcel.ingresoLibroExcel');});

Route::post('/import/excel', [ExcelImportController::class,'import'])->name('import.excel');

Route::get('/download-template', [ExcelImportController::class,'descargarTemplate'])->name('descargar.template');


// --------------------------------- CRUD PARAMETROS -------------------------------------------------------

Route::post('/registro-parametro', [ParametroController::class,'guardarParametro'])->name('registroParametro');

Route::get('/listado-parametros', [ParametroController::class, 'listarParametro'])->name('listado-parametros');

Route::get('/editar-parametro/{clave}',[ParametroController::class,'datosParametros'])->name('editar-parametro');

Route::get('/eliminar-parametro/{clave}', [ParametroController::class, 'eliminarRegistroParametro'])->name('eliminar-parametro');

Route::post('/actualizar-parametro/{clave}', [ParametroController::class,'actualizarParametro'])->name('actualizar-parametro');

Route::get('/registrar-parametro', function () { return view('Parametro.crearParametro');});

Route::get('/buscar-parametro',[ParametroController::class,'buscarParametro'])->name('buscar-parametro');

Route::post('/verificar-codigo-unico',[ParametroController::class,'verificarCodigoUnico'])->name('verificar-codigo-unico');

});
