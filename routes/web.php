<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () { return view('welcome'); });

//Route::get('/', function () { return view('layout'); });
//Route::get('/login', function () { return view('login'); });
//Route::post('/validar', function () { return view('layout'); })->name('validar');
//Route::get('/logout', function () { return view('login'); })->name('logout');

Route::get('/', 'LoginController@inicio')->name('inicio');
Route::get('/login', 'LoginController@login')->name('login');
Route::post('/validar', 'LoginController@validar')->name('validar');
Route::get('/logout', 'LoginController@logout')->name('logout');



//////Route::get('/empresas', 'AdministradorController@empresas')->name('empresas');
//////Route::get('/cadenas', 'AdministradorController@cadenas')->name('cadenas');
//////Route::get('/usuarios', 'AdministradorController@usuarios')->name('usuarios');

//Route::get('/usuarios', function () { return view('usuarios'); })->name('usuarios');
Route::get('/usuarios', 'UsuariosController@vista')->name('usuarios');
Route::post('/usuarios/listar', 'UsuariosController@listar');
Route::post('/usuarios/deshabilita', 'UsuariosController@deshabilita');
Route::post('/usuarios/habilita', 'UsuariosController@habilita');
Route::post('/usuarios/setupd', 'UsuariosController@setupd');

//Route::get('/empresas', function () { return view('empresas'); })->name('empresas');
Route::get('/empresas', 'EmpresasController@vista')->name('empresas');
Route::post('/empresas/subirfoto', 'EmpresasController@subirfoto');
Route::delete('/empresas/subirfoto', 'EmpresasController@subirfoto');
Route::post('/empresas/setupd', 'EmpresasController@setupd');
Route::post('/empresas/listar', 'EmpresasController@listar');
Route::post('/empresas/eliminar', 'EmpresasController@eliminar');
Route::post('/unidadnegocio/setupdel', 'UnidadNegocioController@setupdel');
Route::post('/unidadnegocio/listar', 'UnidadNegocioController@listar');

//Route::get('/cadenas', function () { return view('cadenas'); })->name('cadenas');
Route::get('/cadenas', 'CadenaSuministrosController@vista')->name('cadenas');
Route::post('/cadenas/listaempresas', 'CadenaSuministrosController@listaempresas');
Route::post('/cadenas/listaunidades', 'CadenaSuministrosController@listaunidades');
Route::post('/cadenas/setupd', 'CadenaSuministrosController@setupd');
Route::post('/cadenas/listar', 'CadenaSuministrosController@listar');

//Route::get('/cadenasver', function () { return view('cadenasver'); })->name('cadenasver');
Route::get('/cadenasver/cargar/{idCadenaSuministro}', 'CadenaSuministrosController@cargar');
Route::post('/cadenasver/listaempresas', 'CadenaSuministrosController@listaempresas2');
Route::post('/cadenasver/proveedores/agregar', 'CadenaSuministrosController@agregarproveedores');
Route::post('/cadenasver/clientes/agregar', 'CadenaSuministrosController@agregarclientes');
Route::post('/cadenasver/proveedores/listar', 'CadenaSuministrosController@listarproveedores');
Route::post('/cadenasver/clientes/listar', 'CadenaSuministrosController@listarclientes');
Route::post('/cadenasver/eliminar', 'CadenaSuministrosController@eliminar');
Route::post('/cadenasver/versiones', 'CadenaSuministrosController@versiones');


//Route::get('/mapaprocesos', function () { return view('mapaprocesos'); })->name('mapaprocesos');
Route::get('/mapaprocesos', 'MapaProcesosController@vista')->name('mapaprocesos');
Route::post('/mapaprocesos/listaempresas', 'MapaProcesosController@listaempresas');
Route::post('/mapaprocesos/listaunidades', 'MapaProcesosController@listaunidades');
Route::post('/mapaprocesos/setupd', 'MapaProcesosController@setupd');
Route::post('/mapaprocesos/listar', 'MapaProcesosController@listar');

//Route::get('/mapaprocesosver', function () { return view('mapaprocesosver'); })->name('mapaprocesosver');
Route::get('/mapaprocesosver/cargar/{idMapaProcesos}', 'MapaProcesosController@cargar');
Route::post('/mapaprocesosver/entradas/setupdel', 'MapaProcesosController@entradas');
Route::post('/mapaprocesosver/salidas/setupdel', 'MapaProcesosController@salidas');
Route::post('/mapaprocesosver/pprimarios/setupdel', 'MapaProcesosController@pprimarios');
Route::post('/mapaprocesosver/papoyo/setupdel', 'MapaProcesosController@papoyo');
Route::post('/mapaprocesosver/pestrategicos/setupdel', 'MapaProcesosController@pestrategicos');
Route::post('/mapaprocesosver/relaciones/listar', 'MapaProcesosController@relaciones_listar');
Route::post('/mapaprocesosver/relaciones/setupdel', 'MapaProcesosController@relaciones');
Route::post('/mapaprocesosver/criterios/setupdel', 'MapaProcesosController@criterios');
Route::post('/mapaprocesosver/priorizacion/setupdel', 'MapaProcesosController@priorizacion');
Route::post('/mapaprocesosver/finalizarpriorizar', 'MapaProcesosController@finalizarpriorizar');
Route::post('/mapaprocesosver/subirfoto', 'MapaProcesosController@subirfoto');
Route::delete('/mapaprocesosver/subirfoto', 'MapaProcesosController@subirfoto');
Route::post('/mapaprocesosver/caracterizacion/maestro', 'MapaProcesosController@caracterizacion_maestro');
Route::post('/mapaprocesosver/caracterizacion/detalle', 'MapaProcesosController@caracterizacion_detalle');
Route::post('/mapaprocesosver/caracterizacion/setupdel', 'MapaProcesosController@caracterizacion');
Route::post('/mapaprocesosver/diagramaflujo/maestro', 'MapaProcesosController@diagramaflujo_maestro');
Route::post('/mapaprocesosver/diagramaflujo/detalle', 'MapaProcesosController@diagramaflujo_detalle');
Route::post('/mapaprocesosver/diagramaflujo/setupdel', 'MapaProcesosController@diagramaflujo');
Route::post('/mapaprocesosver/diagramaseguimientoactividades/maestro', 'MapaProcesosController@diagramaseguimientoactividades_maestro');
Route::post('/mapaprocesosver/diagramaseguimientoactividades/detalle', 'MapaProcesosController@diagramaseguimientoactividades_detalle');
Route::post('/mapaprocesosver/diagramaseguimientoactividades/setupdel', 'MapaProcesosController@diagramaseguimientoactividades');
Route::post('/mapaprocesosver/historico', 'MapaProcesosController@historico');
Route::post('/mapaprocesosver/mapaprocesos', 'MapaProcesosController@mapaprocesos');

//mapa de procesos v2
Route::post('/mapaprocesosver/indicadores/maestro', 'MapaProcesosController@indicadores_maestro');
Route::post('/mapaprocesosver/indicadores/detalle', 'MapaProcesosController@indicadores_detalle');
Route::post('/mapaprocesosver/indicadores/setupdel', 'MapaProcesosController@indicadores');
Route::post('/mapaprocesosver/mapaestrategico/maestro', 'MapaProcesosController@mapaestrategico_maestro');
Route::post('/mapaprocesosver/mapaestrategico/detalle', 'MapaProcesosController@mapaestrategico_detalle');
Route::post('/mapaprocesosver/mapaestrategico/orden', 'MapaProcesosController@mapaestrategico_orden');
Route::post('/mapaprocesosver/mapaestrategico/setupdel', 'MapaProcesosController@mapaestrategico');
Route::post('/mapaprocesosver/tablerocontrol/maestro', 'MapaProcesosController@tablerocontrol_maestro');
Route::post('/mapaprocesosver/tablerocontrol/detalle', 'MapaProcesosController@tablerocontrol_detalle');
Route::post('/mapaprocesosver/tablerocontrol/setupdel', 'MapaProcesosController@tablerocontrol');
Route::post('/mapaprocesosver/tablerocontrol/datafuente', 'MapaProcesosController@tablerocontrol_datafuente');


//Auth::routes();
//Route::get('/home', 'HomeController@index')->name('home');
