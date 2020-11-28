<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Log;
use App\Empresa;
use App\UnidadNegocio;
use App\_Mensaje;
use App\MapaProcesos;
use App\EntradaSalida;
use App\Proceso;
use App\RelacionProceso;
use App\CriterioPriorizacion;
use App\EvaluacionCriterios;
use App\Caracterizacion;
use App\DetalleCaracterizacion;
use App\DiagramaFlujo;
use App\DetalleDiagramaFlujo;
use App\SeguimientoActividades;
use App\DetalleSeguimientoActividades;
use App\IndicadorDesempenio;
use App\DetalleIndicadorDesempenio;
use App\MapaEstrategico;
use App\DetalleMapaEstrategico;
use App\TableroControl;
use App\DetalleTableroControl;
use App\DataFuente;

class MapaProcesosController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}
	protected function vista(Request $request){
		Log::log([
			'descripcion' => 'Vista: Mapa de Procesos'
		]);
		return view("mapaprocesos");
	}

	protected function listaempresas(Request $request){
		Log::log([
			'descripcion' => 'Listar Empresas',
			'tabla' => 'Empresas'
		]);
		$empresa = Empresa::listar();
		return _Mensaje::enviar('', $empresa);
	}
	protected function listaunidades(Request $request){
		$idEmpresa = $request->get('idEmpresa');
		Log::log([
			'descripcion' => 'Listar Unidades de Negocio',
			'tabla' => 'UnidadNegocio',
			'idTabla' => $idEmpresa
		]);
		$unidadnegocio = UnidadNegocio::listar($idEmpresa);
		return _Mensaje::enviar('', $unidadnegocio);
	}

	protected function setupd(Request $request){
		$mapaProcesos = $request->get('mapa');
		$idMapaProcesos = $mapaProcesos['idMapaProcesos'] ?? null;
		//dd($idMapaProcesos);
		DB::beginTransaction();
		try {
			if ($idMapaProcesos == null)
				$res = MapaProcesos::registrar($mapaProcesos);
			else
				$res = MapaProcesos::modificar($idMapaProcesos, $mapaProcesos);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => (($idMapaProcesos == null)?'Registrar':'Modificar').' Mapa de Procesos: '.($mensaje ??'Correcto'),
			'tabla' => 'MapaProcesos',
			'idTabla' => ($idMapaProcesos ?? ($res->idMapaProcesos ?? null))
		]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function listar(Request $request){
		Log::log([
			'descripcion' => 'Listar Mapas de Procesos',
			'tabla' => 'MapaProcesos'
		]);
		$mapaprocesos = MapaProcesos::listar();
		//dd($mapaprocesos);
		return _Mensaje::enviar('', $mapaprocesos);
	}

	protected function cargar(Request $request, $idMapaProcesos){
		$mapaprocesos = MapaProcesos::obtener($idMapaProcesos);
		if ($mapaprocesos === null){
			$mensaje = 'Error, datos no encontrados';
			$msj_log = ' (id: '.$idMapaProcesos.', no encontrado)';
		}
		Log::log([
			'descripcion' => 'Vista: Mapa de Procesos - Ver'.($msj_log ?? ''),
			'tabla' => 'MapaProcesos',
			'idTabla' => (is_numeric($idMapaProcesos) === true)?$idMapaProcesos:null
		]);
		if ($mapaprocesos === null){
			return redirect("mapaprocesos")->with('mensaje', $mensaje);
		}
		return view("mapaprocesosver", ['mapaprocesos' => $mapaprocesos]);
	}


	protected function entradas(Request $request){
		return $this::entrada_salida_setupdel($request, 'Entrada');
	}
	protected function salidas(Request $request){
		return $this::entrada_salida_setupdel($request, 'Salida');
	}
	private function entrada_salida_setupdel(Request $request, $tipo){
		$datosmapa = $request->get('datosmapa');
		$idMapaProcesos = $datosmapa['idMapaProcesos'] ?? null;
		$obj = $request->get('obj');
		$idEntradaSalida = $obj['idEntradaSalida'] ?? null;
		DB::beginTransaction();
		try {
			if ($idEntradaSalida == null){
				if($tipo == 'Entrada')
					$res = EntradaSalida::registrarEntrada($obj, $idMapaProcesos);
				else if($tipo == 'Salida')
					$res = EntradaSalida::registrarSalida($obj, $idMapaProcesos);
			}
			else
				$res = EntradaSalida::modificarEntradaSalida($idEntradaSalida, $obj);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		$est = (($idEntradaSalida == null)?'Registrar':'Modificar');
		$est = ($res->estado == '0')?'Eliminar':$est;
		Log::log([
			'descripcion' => $est." $tipo: ".($mensaje ??'Correcto'),
			'tabla' => 'EntradaSalida',
			'idTabla' => ($idEntradaSalida ?? ($res->idEntradaSalida ?? null))
		]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}

	protected function pprimarios(Request $request){
		return $this::procesos_primarios_apoyo_estrategicos($request, 'Primario');
	}
	protected function papoyo(Request $request){
		return $this::procesos_primarios_apoyo_estrategicos($request, 'de Apoyo');
	}
	protected function pestrategicos(Request $request){
		return $this::procesos_primarios_apoyo_estrategicos($request, 'Estratégico');
	}
	private function procesos_primarios_apoyo_estrategicos(Request $request, $tipo){
		//dd($request);
		$datosmapa = $request->get('datosmapa');
		$idMapaProcesos = $datosmapa['idMapaProcesos'] ?? null;
		$obj = $request->get('obj');
		$idProceso = $obj['idProceso'] ?? null;
		DB::beginTransaction();
		try {
			if ($idProceso == null){
				if($tipo == 'Primario')
					$res = Proceso::registrarPPrimario($obj, $idMapaProcesos);
				else if($tipo == 'de Apoyo')
					$res = Proceso::registrarPApoyo($obj, $idMapaProcesos);
				else if($tipo == 'Estratégico')
					$res = Proceso::registrarPEstrategico($obj, $idMapaProcesos);
			}
			else
				$res = Proceso::modificarProceso($idProceso, $obj);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		$est = (($idProceso == null)?'Registrar':'Modificar');
		$est = ($res->estado == '0')?'Eliminar':$est;
		Log::log([
			'descripcion' => $est." Proceso $tipo: ".($mensaje ??'Correcto'),
			'tabla' => 'EntradaSalida',
			'idTabla' => ($idProceso ?? ($res->idProceso ?? null))
		]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function relaciones_listar(Request $request){
		$datosmapa = $request->get('datosmapa');
		$idMapaProcesos = $datosmapa['idMapaProcesos'] ?? null;
		$res = RelacionProceso::listar($idMapaProcesos);
		Log::log([
			'descripcion' => "Listar Relaciones: ".($mensaje ??'Correcto'),
			'tabla' => 'RelacionProceso',
			'idTabla' => ($idMapaProcesos ?? null)
		]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function relaciones(Request $request){
		$datosmapa = $request->get('datosmapa');
		$idMapaProcesos = $datosmapa['idMapaProcesos'] ?? null;
		$obj = $request->get('obj');
		$idRelacionProceso = $obj['idRelacionProceso'] ?? null;
		DB::beginTransaction();
		try {
			if ($idRelacionProceso == null)
				$res = RelacionProceso::registrar($obj, $idMapaProcesos);
			else
				$res = RelacionProceso::modificar($idRelacionProceso, $obj);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		$est = (($idRelacionProceso == null)?'Registrar':'Modificar');
		$est = ($res->estado == '0')?'Eliminar':$est;
		Log::log([
			'descripcion' => $est." Relacion: ".($mensaje ??'Correcto'),
			'tabla' => 'EntradaSalida',
			'idTabla' => ($idRelacionProceso ?? ($res->idRelacionProceso ?? null))
		]);
		// devuelvo todos los proceso debido a que estan cruzados con la tabla procesos
		$res = RelacionProceso::listar($idMapaProcesos);
		//
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}

	protected function criterios(Request $request){
		$datosmapa = $request->get('datosmapa');
		$idMapaProcesos = $datosmapa['idMapaProcesos'] ?? null;
		$obj = $request->get('obj');
		$idCriteriosPriorizacion = $obj['idCriteriosPriorizacion'] ?? null;
		DB::beginTransaction();
		try {
			if ($idCriteriosPriorizacion == null)
				$res = CriterioPriorizacion::registrar($obj, $idMapaProcesos);
			else
				$res = CriterioPriorizacion::modificar($idCriteriosPriorizacion, $obj);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		$est = (($idCriteriosPriorizacion == null)?'Registrar':'Modificar');
		$est = ($res->estado == '0')?'Eliminar':$est;
		Log::log([
			'descripcion' => $est." Criterio: ".($mensaje ??'Correcto'),
			'tabla' => 'CriteriosPriorizacion',
			'idTabla' => ($idCriteriosPriorizacion ?? ($res->idCriteriosPriorizacion ?? null))
		]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function priorizacion(Request $request){
		$datosmapa = $request->get('datosmapa');
		$idMapaProcesos = $datosmapa['idMapaProcesos'] ?? null;
		$listaProcesos = $request->get('listaProcesos');
		$cantidadpriorizar = $request->get('cantidadpriorizar');
		DB::beginTransaction();
		try {
			$res = MapaProcesos::actualizarCantidadPriorizar($datosmapa, $cantidadpriorizar);
			$cantidadpriorizar = $res->cantidadPriorizar;
			$arr = [];
			foreach ($listaProcesos as $proceso) {
				$idProceso = $proceso['idProceso'];
				$_sumapuntaje_multiplicado = 0;
				foreach ($proceso['criterios'] as $criterio) {
					$idCriteriosPriorizacion = $criterio['idCriteriosPriorizacion'];
					if ($criterio['seleccionado'] != 0){
						$_sumapuntaje_multiplicado += (int)($criterio['seleccionado']);
						$puntaje = $criterio['seleccionado'] / $criterio['peso'];
						try {
							EvaluacionCriterios::registrar(
								$idMapaProcesos,
								$idProceso,
								$idCriteriosPriorizacion,
								$puntaje
							);
						}
						catch (\Exception $e) {
							if ($e->getCode() == 23000)
								EvaluacionCriterios::modificar(
									$idMapaProcesos,
									$idProceso,
									$idCriteriosPriorizacion,
									$puntaje
								);
							else
								throw new \Exception("Error", 1);
						}
					}
				}
				$arr[] = [
					'ptj' => $_sumapuntaje_multiplicado,
					'idProceso' => $idProceso
				];
			}
			for ($i=0; $i < count($arr); $i++) {
				for ($j=$i+1; $j < count($arr); $j++) {
					if ($arr[$i]['ptj'] <= $arr[$j]['ptj']){
						$temp = $arr[$j];
						$arr[$j] = $arr[$i];
						$arr[$i] = $temp;
					}
				}
			}
			for ($i=0; $i < count($arr); $i++) {
				if ($i < $cantidadpriorizar)
					Proceso::cambiarPriorizacion($arr[$i]['idProceso'], true);
				else
					Proceso::cambiarPriorizacion($arr[$i]['idProceso'], false);
			}
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => "Registrar / Modificar puntajes de la priorizacion y cantidad a priorizar: ".($mensaje ??'Correcto'),
			'tabla' => 'EvaluacionCriterios'
		]);
		$res = [
			'mapaprocesos' => $res ?? null,
			'listapriorizados' => Proceso::listarPriorizados($idMapaProcesos),
			'matriz' => EvaluacionCriterios::listar($idMapaProcesos)
		];
		return _Mensaje::enviar($mensaje ?? '', $res);
	}
	protected function finalizarpriorizar(Request $request){
		$datosmapa = $request->get('datosmapa');
		$idMapaProcesos = $datosmapa['idMapaProcesos'] ?? null;
		DB::beginTransaction();
		try {
			$res = MapaProcesos::finalizarpriorizar($idMapaProcesos);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' =>"Finalizar priorizacion: ".($mensaje ??'Correcto'),
			'tabla' => 'MapaProcesos',
			'idTabla' => ($res->idMapaProcesos ?? null)
		]);
		$res = [
			'mapaprocesos' => $res ?? null,
			'listapriorizados' => Proceso::listarPriorizados($idMapaProcesos)
		];
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);

	}


	protected function caracterizacion_maestro(Request $request){
		$datosmapa = $request->get('datosmapa');
		$idMapaProcesos = $datosmapa['idMapaProcesos'] ?? null;
		$obj = $request->get('obj');
		$idProceso = $obj['procesoSeleccionado'] ?? null;
		DB::beginTransaction();
		try {
			$res = Caracterizacion::obtener([
				'idMapaProcesos' => $idMapaProcesos,
				'idProceso' => $idProceso
			]);
			if ($res == null)
				$res = Caracterizacion::registrar([
					'idMapaProcesos' => $idMapaProcesos,
					'idProceso' => $obj['procesoSeleccionado']
				]);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => 'Listar SubProcesos de un Proceso',
			'tabla' => 'Procesos',
			'idTabla' => $idProceso
		]);
		$res = [
			'caracterizacion' => $res,
			'subprocesos' => Proceso::listarSubProcesos($idProceso)
		];
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function caracterizacion_detalle(Request $request){
		$objcaracterizacion = $request->get('objcaracterizacion');
		$idCaracterizacion = $objcaracterizacion['idCaracterizacion'] ?? null;
		$obj = $request->get('obj');
		$idProceso = $obj['subProcesoSeleccionado'];
		DB::beginTransaction();
		try {
			$res = DetalleCaracterizacion::listar($idCaracterizacion, $idProceso);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => 'Listar detalle caracterizacion',
			'tabla' => 'DetalleCaracterizacion',
			'idTabla' => $idProceso
		]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function caracterizacion(Request $request){
		$objcaracterizacion = $request->get('objcaracterizacion');
		$idCaracterizacion = $objcaracterizacion['idCaracterizacion'] ?? null;
		$obj = $request->get('obj');
		$idProceso = $obj['subProcesoSeleccionado'];
		DB::beginTransaction();
		try {
			if ($obj['procesoSeleccionado'] == $obj['subProcesoSeleccionado'])
				$res2 = Caracterizacion::cambiarTipo($idCaracterizacion, 'Proceso');
			else
				$res2 = Caracterizacion::cambiarTipo($idCaracterizacion, 'SubProceso');
			$res = DetalleCaracterizacion::registrar([
				'idCaracterizacion' => $idCaracterizacion,
				'nombreArchivo' => $obj['archivo'],
				'descripcion' => $obj['detalle'],
				'idProceso' => $idProceso
			]);
			$this->moverarchivosubido($obj['archivo']);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => "Registrar Caracterizacion: ".($mensaje ??'Correcto'),
			'tabla' => 'DetalleCaracterizacion',
			'idTabla' => ($res->idDetalleCaracterizacion ?? null)
		]);
		$res = [
			'detalle' => $res,
			'tipo' => $res2
		];
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}



	protected function diagramaflujo_maestro(Request $request){
		$datosmapa = $request->get('datosmapa');
		$idMapaProcesos = $datosmapa['idMapaProcesos'] ?? null;
		$obj = $request->get('obj');
		$idProceso = $obj['procesoSeleccionado'] ?? null;
		$situacion = $obj['tipoSeleccionado'] ?? null;
		DB::beginTransaction();
		try {
			$res = DiagramaFlujo::obtener([
				'idMapaProcesos' => $idMapaProcesos,
				'idProceso' => $idProceso,
				'situacion' => $situacion
			]);
			if ($res == null)
				$res = DiagramaFlujo::registrar([
					'idMapaProcesos' => $idMapaProcesos,
					'idProceso' => $idProceso,
					'situacion' => $situacion
				]);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => 'Listar SubProcesos de un Proceso',
			'tabla' => 'Procesos',
			'idTabla' => $idProceso
		]);
		$res = [
			'diagramaflujo' => $res,
			'subprocesos' => Proceso::listarSubProcesos($idProceso)
		];
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function diagramaflujo_detalle(Request $request){
		$objdiagramaflujo = $request->get('objdiagramaflujo');
		$idDiagramaFlujo = $objdiagramaflujo['idDiagramaFlujo'] ?? null;
		$obj = $request->get('obj');
		$idProceso = $obj['subProcesoSeleccionado'];
		DB::beginTransaction();
		try {
			$res = DetalleDiagramaFlujo::listar($idDiagramaFlujo, $idProceso);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => 'Listar detalle caracterizacion',
			'tabla' => 'DetalleDiagramaFlujo',
			'idTabla' => $idProceso
		]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function diagramaflujo(Request $request){
		$objdiagramaflujo = $request->get('objdiagramaflujo');
		$idDiagramaFlujo = $objdiagramaflujo['idDiagramaFlujo'] ?? null;
		$obj = $request->get('obj');
		$idProceso = $obj['subProcesoSeleccionado'];
		DB::beginTransaction();
		try {
			if ($obj['procesoSeleccionado'] == $obj['subProcesoSeleccionado'])
				$res2 = DiagramaFlujo::cambiarTipo($idDiagramaFlujo, 'Proceso');
			else
				$res2 = DiagramaFlujo::cambiarTipo($idDiagramaFlujo, 'SubProceso');
			$res = DetalleDiagramaFlujo::registrar([
				'idDiagramaFlujo' => $idDiagramaFlujo,
				'nombreArchivo' => $obj['archivo'],
				'descripcion' => $obj['detalle'],
				'idProceso' => $idProceso
			]);
			$this->moverarchivosubido($obj['archivo']);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => "Registrar Diagrama de flujo: ".($mensaje ??'Correcto'),
			'tabla' => 'DetalleDiagramaFlujo',
			'idTabla' => ($res->idDetalleDiagramaFlujo ?? null)
		]);
		$res = [
			'detalle' => $res,
			'tipo' => $res2
		];
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}


	protected function diagramaseguimientoactividades_maestro(Request $request){
			$datosmapa = $request->get('datosmapa');
			$idMapaProcesos = $datosmapa['idMapaProcesos'] ?? null;
			$obj = $request->get('obj');
			$idProceso = $obj['procesoSeleccionado'] ?? null;
			$situacion = $obj['tipoSeleccionado'] ?? null;
			DB::beginTransaction();
			try {
				$res = SeguimientoActividades::obtener([
					'idMapaProcesos' => $idMapaProcesos,
					'idProceso' => $idProceso,
					'situacion' => $situacion
				]);
				if ($res == null)
					$res = SeguimientoActividades::registrar([
						'idMapaProcesos' => $idMapaProcesos,
						'idProceso' => $idProceso,
						'situacion' => $situacion
					]);
				DB::commit();
			}
			catch (\Exception $e) {
				$res = null;
				dd($e);
				$mensaje = 'Hubo un error, intente nuevamente más tarde';
				DB::rollback();
			}
			Log::log([
				'descripcion' => 'Listar SubProcesos de un Proceso',
				'tabla' => 'Procesos',
				'idTabla' => $idProceso
			]);
			$res = [
				'diagramaseguimientoactividades' => $res,
				'subprocesos' => Proceso::listarSubProcesos($idProceso)
			];
			return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function diagramaseguimientoactividades_detalle(Request $request){
			$objdiagramaseguimientoactividades = $request->get('objdiagramaseguimientoactividades');
			$idSeguimientoActividades = $objdiagramaseguimientoactividades['idSeguimientoActividades'] ?? null;
			$obj = $request->get('obj');
			$idProceso = $obj['subProcesoSeleccionado'];
			DB::beginTransaction();
			try {
				$res = DetalleSeguimientoActividades::listar($idSeguimientoActividades, $idProceso);
				DB::commit();
			}
			catch (\Exception $e) {
				$res = null;
				dd($e);
				$mensaje = 'Hubo un error, intente nuevamente más tarde';
				DB::rollback();
			}
			Log::log([
				'descripcion' => 'Listar detalle caracterizacion',
				'tabla' => 'DetalleSeguimientoActividades',
				'idTabla' => $idProceso
			]);
			return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function diagramaseguimientoactividades(Request $request){
			$objdiagramaseguimientoactividades = $request->get('objdiagramaseguimientoactividades');
			$idSeguimientoActividades = $objdiagramaseguimientoactividades['idSeguimientoActividades'] ?? null;
			$obj = $request->get('obj');
			$idProceso = $obj['subProcesoSeleccionado'];
			$arreglo = $request->get('arreglo');
			DB::beginTransaction();
			try {
				if ($obj['procesoSeleccionado'] == $obj['subProcesoSeleccionado'])
					$res = SeguimientoActividades::cambiarTipo($idSeguimientoActividades, 'Proceso');
				else
					$res = SeguimientoActividades::cambiarTipo($idSeguimientoActividades, 'SubProceso');
				DetalleSeguimientoActividades::desactivar($idSeguimientoActividades);
				for ($i = 0; $i < count($arreglo); $i++) {
					$item = $arreglo[$i];
					if (isset($item['idDetalleSeguimientoActividades']))
						$item = DetalleSeguimientoActividades::modificar($item, $i);
					else
						$item = DetalleSeguimientoActividades::registrar($item, $idSeguimientoActividades, $idProceso, $i);
					$arreglo[$i] = $item;
				}
				DB::commit();
			}
			catch (\Exception $e) {
				$res = null;
				dd($e);
				$mensaje = 'Hubo un error, intente nuevamente más tarde';
				DB::rollback();
			}
			Log::log([
				'descripcion' => "Registrar Seguimiento de actividades: ".($mensaje ??'Correcto'),
				'tabla' => 'DetalleSeguimientoActividades',
				'idTabla' => ($res->idDetalleSeguimientoActividades ?? null)
			]);
			$res = [
				'arreglo' => $arreglo,
				'tipo' => $res
			];
			return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}

	protected function historico(Request $request){
		DB::beginTransaction();
		try {
			$descripcion = $request->descripcion ?? '';
			$idMapaProcesos = $request->idMapaProcesos;
			//datos
			$entradaSalida = EntradaSalida::where(['idMapaProcesos' => $idMapaProcesos])->get();
			$procesos = Proceso::where(['idMapaProcesos' => $idMapaProcesos])->get();
			$relaciones = RelacionProceso::where(['idMapaProcesos' => $idMapaProcesos])->get();
			$criterios = CriterioPriorizacion::where(['idMapaProcesos' => $idMapaProcesos])->get();
			$matriz = EvaluacionCriterios::where(['idMapaProcesos' => $idMapaProcesos])->get();
			//
			$mapaProcesos = MapaProcesos::get($idMapaProcesos);
			$mapaProcesos->estado = MapaProcesos::getCodigoHistorico();
			$mapaProcesos->descripcion = $descripcion;
			$mapaProcesos->save();
			// nuevo
			$mapaProcesos = MapaProcesos::create([
				'idUnidadNegocio' => $mapaProcesos->idUnidadNegocio,
				'idEmpresa' => $mapaProcesos->idEmpresa,
				'descripcion' => '-',
				'tipo' => '',///////////////////////////////////////////////////////QUE ES?
				'priorizado' => false,
				'cantidadPriorizar' => 0,
				'estado' => MapaProcesos::getCodigoActivo(),
				'idUsuario' => Auth::user()->idUsuario
			]);
			$idMapaProcesos = $mapaProcesos->idMapaProcesos;
			//
			foreach ($entradaSalida as $item) {
				EntradaSalida::create([
					'idMapaProcesos' => $idMapaProcesos,
					'nombre' => $item->nombre,
					'tipo' => $item->tipo,
					'estado' => '1'
				]);
			}
			foreach ($procesos as $item) {
				Proceso::create([
					'idMapaProcesos' => $idMapaProcesos,
					'nombre' => $item->nombre,
					'tipo' => $item->tipo,
					'priorizado' => false,
					'estado' => '1',
					'idProcesoAnterior' => $item->idProcesoAnterior ?? null
				]);
			}
			foreach ($relaciones as $item) {
				RelacionProceso::create([
					'idProceso_desde' => $item->idProceso_desde,
					'idProceso_hasta' => $item->idProceso_hasta,
					'idMapaProcesos' => $idMapaProcesos,
					'estado' => '1'
				]);
			}
			foreach ($criterios as $item) {
				CriterioPriorizacion::create([
					'idMapaProcesos' => $idMapaProcesos,
					'nombre' => $item->nombre,
					'peso' => $item->peso,
					'justificacion' => $item->justificacion,
					'valmin' => $item->valmin,
					'valmax' => $item->valmax,
					'estado' => '1'
				]);
			}
			foreach ($matriz as $item) {
				EvaluacionCriterios::create([
					'idMapaProcesos' => $idMapaProcesos,
					'idProceso' => $item->idProceso,
					'idCriteriosPriorizacion' => $item->idCriteriosPriorizacion,
					'puntaje' => $item->puntaje
				]);
			}
			//
			DB::commit();
		}
		catch (\Exception $e) {
			return _Mensaje::enviar('Error', null);
			dd($e);
			DB::rollback();
		}
		return _Mensaje::enviar('', null);
		//dd(json_decode($this->mapaprocesos($request)));
	}

	protected function mapaprocesos(Request $request){
		$idMapaProcesos = $request->get('idMapaProcesos');
		/*
		Log::log([
			'descripcion' => 'Listar Entradas',
			'tabla' => 'EntradaSalida',
			'idTabla' => $idMapaProcesos
		]);
		Log::log([
			'descripcion' => 'Listar Salidas',
			'tabla' => 'EntradaSalida',
			'idTabla' => $idMapaProcesos
		]);
		Log::log([
			'descripcion' => 'Listar Procesos Primarios',
			'tabla' => 'Procesos',
			'idTabla' => $idMapaProcesos
		]);
		Log::log([
			'descripcion' => 'Listar Procesos Estratégicos',
			'tabla' => 'Procesos',
			'idTabla' => $idMapaProcesos
		]);
		Log::log([
			'descripcion' => 'Listar Procesos de Apoyo',
			'tabla' => 'Procesos',
			'idTabla' => $idMapaProcesos
		]);
		Log::log([
			'descripcion' => 'Listar Relaciones entre procesos',
			'tabla' => 'Procesos',
			'idTabla' => $idMapaProcesos
		]);
		Log::log([
			'descripcion' => 'Listar Criterios de priorizacion',
			'tabla' => 'CriteriosPriorizacion',
			'idTabla' => $idMapaProcesos
		]);
		*/
		Log::log([
			'descripcion' => 'Listar datos del Mapa de Procesos',
			'tabla' => 'MapaProcesos',
			'idTabla' => $idMapaProcesos
		]);
		$res = [
			'entradas' => EntradaSalida::listarEntradas($idMapaProcesos),
			'salidas' => EntradaSalida::listarSalidas($idMapaProcesos),
			'primarios' => Proceso::listarPPrimarios($idMapaProcesos),
			'estrategico' => Proceso::listarPEstrategicos($idMapaProcesos),
			'apoyo' => Proceso::listarPApoyo($idMapaProcesos),
			'relaciones' => RelacionProceso::listar($idMapaProcesos),
			//
			'criterios' => CriterioPriorizacion::listar($idMapaProcesos),
			'matriz' => EvaluacionCriterios::listar($idMapaProcesos),
			'priorizados' => Proceso::listarPriorizados($idMapaProcesos)
		];
		return _Mensaje::enviar('', $res);
	}





	protected function indicadores_maestro(Request $request){
		$datosmapa = $request->get('datosmapa');
		$idMapaProcesos = $datosmapa['idMapaProcesos'] ?? null;
		$obj = $request->get('obj');
		$idProceso = $obj['procesoSeleccionado'] ?? null;
		DB::beginTransaction();
		try {
			$res = IndicadorDesempenio::obtener([
				'idMapaProcesos' => $idMapaProcesos,
				'idProceso' => $idProceso
			]);
			if ($res == null)
				$res = IndicadorDesempenio::registrar([
					'idMapaProcesos' => $idMapaProcesos,
					'idProceso' => $obj['procesoSeleccionado']
				]);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => 'Listar SubProcesos de un Proceso',
			'tabla' => 'Procesos',
			'idTabla' => $idProceso
		]);
		$res = [
			'indicadores' => $res,
			'subprocesos' => Proceso::listarSubProcesos($idProceso)
		];
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function indicadores_detalle(Request $request){
		$objindicador = $request->get('objindicador');
		$idIndicadorDesempenio = $objindicador['idIndicadorDesempenio'] ?? null;
		$obj = $request->get('obj');
		$idProceso = $obj['subProcesoSeleccionado'];
		DB::beginTransaction();
		try {
			$res = DetalleIndicadorDesempenio::listar($idIndicadorDesempenio, $idProceso);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => 'Listar detalle caracterizacion',
			'tabla' => 'DetalleIndicadorDesempenio',
			'idTabla' => $idProceso
		]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function indicadores(Request $request){
		$obj = $request->get('obj');
		$objmaestro = $request->get('objmaestro');
		$datosmapa = $request->get('datosmapa');
		$objindicador = $request->get('objindicador');
		//dd($request->get('objindicador'));
		//dd($request->get('datosmapa'));
		//dd($request->get('objmaestro'));
		$idIndicadorDesempenio = $objindicador['idIndicadorDesempenio'] ?? null;
		$idDetalleIndicadorDesempenio = $obj['idDetalleIndicadorDesempenio'] ?? null;
		$idProceso = $objmaestro['subProcesoSeleccionado'];
		DB::beginTransaction();
		try {
			if ($objmaestro['procesoSeleccionado'] == $objmaestro['subProcesoSeleccionado'])
				$res2 = IndicadorDesempenio::cambiarTipo($idIndicadorDesempenio, 'Proceso');
			else
				$res2 = IndicadorDesempenio::cambiarTipo($idIndicadorDesempenio, 'SubProceso');
			if ($idDetalleIndicadorDesempenio == null)
				$res = DetalleIndicadorDesempenio::registrar($idIndicadorDesempenio, $obj, $idProceso);
			else
				$res = DetalleIndicadorDesempenio::modificar($idDetalleIndicadorDesempenio, $obj);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => "Registrar Caracterizacion: ".($mensaje ??'Correcto'),
			'tabla' => 'DetalleIndicadorDesempenio',
			'idTabla' => ($res->idDetalleIndicadorDesempenio ?? null)
		]);
		$res = [
			'detalle' => $res,
			'tipo' => $res2
		];
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}

	protected function mapaestrategico_maestro(Request $request){
		$datosmapa = $request->get('datosmapa');
		$idMapaProcesos = $datosmapa['idMapaProcesos'] ?? null;
		$obj = $request->get('obj');
		$idProceso = $obj['procesoSeleccionado'] ?? null;
		DB::beginTransaction();
		try {
			$res = MapaEstrategico::obtener([
				'idMapaProcesos' => $idMapaProcesos,
				'idProceso' => $idProceso
			]);
			if ($res == null)
				$res = MapaEstrategico::registrar([
					'idMapaProcesos' => $idMapaProcesos,
					'idProceso' => $obj['procesoSeleccionado']
				]);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => 'Listar SubProcesos de un Proceso',
			'tabla' => 'Procesos',
			'idTabla' => $idProceso
		]);
		$res = [
			'mapaestrategico' => $res,
			'subprocesos' => Proceso::listarSubProcesos($idProceso)
		];
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function mapaestrategico_detalle(Request $request){
		$objmapaest = $request->get('objmapaest');
		$idMapaEstrategico = $objmapaest['idMapaEstrategico'] ?? null;
		$obj = $request->get('obj');
		$idProceso = $obj['subProcesoSeleccionado'];
		DB::beginTransaction();
		try {
			$res = DetalleMapaEstrategico::listar($idMapaEstrategico, $idProceso);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => 'Listar detalle caracterizacion',
			'tabla' => 'DetalleMapaEstrategico',
			'idTabla' => $idProceso
		]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function mapaestrategico_orden(Request $request){
		$idMapaEstrategico = $request->get('objmapaest')['idMapaEstrategico'];
		$orden_aspectos = $request->get('orden_aspectos');
		DB::beginTransaction();
			try {
				$res = MapaEstrategico::darOrden($idMapaEstrategico, $orden_aspectos);
				DB::commit();
			}
			catch (\Exception $e) {
				$res = null;
				dd($e);
				$mensaje = 'Hubo un error, intente nuevamente más tarde';
				DB::rollback();
			}
			Log::log([
				'descripcion' => 'Orden en mapa estratégico',
				'tabla' => 'DetalleMapaEstrategico',
				'idTabla' => $res->idMapaEstrategico
			]);
			return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function mapaestrategico(Request $request){
		$obj = $request->get('obj');
		$objmaestro = $request->get('objmaestro');
		$datosmapa = $request->get('datosmapa');
		$objmapaest = $request->get('objmapaest');
		//dd($obj);
		//dd($objmaestro);
		//dd($datosmapa);
		//dd($objmapaest);
		$idMapaEstrategico = $objmapaest['idMapaEstrategico'] ?? null;
		$idDetalleMapaEstrategico = $obj['idDetalleMapaEstrategico'] ?? null;
		$idProceso = $objmaestro['subProcesoSeleccionado'];
		DB::beginTransaction();
		try {
			if ($objmaestro['procesoSeleccionado'] == $objmaestro['subProcesoSeleccionado'])
				$res2 = MapaEstrategico::cambiarTipo($idMapaEstrategico, 'Proceso');
			else
				$res2 = MapaEstrategico::cambiarTipo($idMapaEstrategico, 'SubProceso');
			if ($idDetalleMapaEstrategico == null)
				$res = DetalleMapaEstrategico::registrar($idMapaEstrategico, $obj, $idProceso);
			else
				$res = DetalleMapaEstrategico::modificar($idDetalleMapaEstrategico, $obj);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => "Registrar Caracterizacion: ".($mensaje ??'Correcto'),
			'tabla' => 'DetalleMapaEstrategico',
			'idTabla' => ($res->idDetalleMapaEstrategico ?? null)
		]);
		$res = [
			'detalle' => $res,
			'tipo' => $res2
		];
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}

	protected function tablerocontrol_maestro(Request $request){
		$datosmapa = $request->get('datosmapa');
		$idMapaProcesos = $datosmapa['idMapaProcesos'] ?? null;
		$obj = $request->get('obj');
		$idProceso = $obj['procesoSeleccionado'] ?? null;
		DB::beginTransaction();
		try {
			$res = TableroControl::obtener([
				'idMapaProcesos' => $idMapaProcesos,
				'idProceso' => $idProceso
			]);
			if ($res == null)
				$res = TableroControl::registrar([
					'idMapaProcesos' => $idMapaProcesos,
					'idProceso' => $obj['procesoSeleccionado']
				]);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => 'Listar SubProcesos de un Proceso',
			'tabla' => 'Procesos',
			'idTabla' => $idProceso
		]);
		$res = [
			'tablerocontrol' => $res,
			'subprocesos' => Proceso::listarSubProcesos($idProceso)
		];
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function tablerocontrol_detalle(Request $request){
		$objtablero = $request->get('objtablero');
		$idTableroControl = $objtablero['idTableroControl'] ?? null;
		$obj = $request->get('obj');
		$idProceso = $obj['subProcesoSeleccionado'];
		DB::beginTransaction();
		try {
			$res = DetalleTableroControl::listar($idTableroControl, $idProceso);
			$res2 = DetalleIndicadorDesempenio::listar_por_proceso($idProceso);
			$res3 = DetalleMapaEstrategico::listar_por_proceso($idProceso);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => 'Listar detalle caracterizacion',
			'tabla' => 'DetalleTableroControl',
			'idTabla' => $idProceso
		]);
		$res = [
			'tablerocontrol' => $res,
			'indicador_desempenio' => $res2,
			'mapa_estrategico' => $res3
		];
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function tablerocontrol(Request $request){
		$obj = $request->get('obj');
		$objmaestro = $request->get('objmaestro');
		$datosmapa = $request->get('datosmapa');
		$objtablero = $request->get('objtablero');
		//dd($obj);
		//dd($objmaestro);
		//dd($datosmapa);
		//dd($objtablero);
		$idTableroControl = $objtablero['idTableroControl'] ?? null;
		$idDetalleTableroControl = $obj['idDetalleITableroControl'] ?? null;
		$idProceso = $objmaestro['subProcesoSeleccionado'];
		DB::beginTransaction();
		try {
			if ($objmaestro['procesoSeleccionado'] == $objmaestro['subProcesoSeleccionado'])
				$res2 = TableroControl::cambiarTipo($idTableroControl, 'Proceso');
			else
				$res2 = TableroControl::cambiarTipo($idTableroControl, 'SubProceso');
			if ($idDetalleTableroControl == null)
				$res = DetalleTableroControl::registrar($idTableroControl, $obj, $idProceso);
			else
				$res = DetalleTableroControl::modificar($idDetalleTableroControl, $obj);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => "Registrar Caracterizacion: ".($mensaje ??'Correcto'),
			'tabla' => 'DetalleTableroControl',
			'idTabla' => ($res->idDetalleTableroControl ?? null)
		]);
		$res = [
			'detalle' => $res,
			'tipo' => $res2
		];
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function tablerocontrol_datafuente(Request $request){
		$obj = $request->get('obj') ?? null;
		$objtablero = $request->get('objtablero');
		//dd($obj);
		//dd($objtablero['idDetalleTableroControl']);
		$idDetalleTableroControl = $objtablero['idDetalleTableroControl'];
		DB::beginTransaction();
		try {
			if ($obj != null){
				for ($i = 0; $i < count($obj); $i++) {
					$obj[$i]['variables_resultado'] = json_encode($obj[$i]['variables_resultado']);
					if (isset($obj[$i]['idDataFuente']))
						$res = DataFuente::modificar($obj[$i]['idDataFuente'], $obj[$i]);
					else
						$res = DataFuente::registrar($obj[$i], $idDetalleTableroControl);
				}
			}
			$res = DataFuente::obtener($idDetalleTableroControl);
			DB::commit();
		}
		catch (\Exception $e) {
			$res = null;
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => "Registrar Data Fuente: ".($mensaje ??'Correcto'),
			'tabla' => 'DataFuente'
		]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);

	}


	private function moverarchivosubido($ruta){
		$rutaFotoTemporal = 'imgtemp/';
		$rutaFoto = 'archivos/';
		rename($rutaFotoTemporal.$ruta, $rutaFoto.$ruta);
	}
	protected function subirfoto(Request $request){
		$rutaFotoTemporal = 'imgtemp/';
		$rutaFoto = 'archivos/';
		if(count($_FILES) == 0)
			return;
		$nombreArchivo = $_FILES["file"]['name'];
		$extension = explode(".", $nombreArchivo);
		$extension = $extension[count($extension) - 1];
		$nombreArchivoFinal =
			date("y"). //años
			date("m"). //mes
			date("d"). //dia
			date("H"). //hora 24h
			date("i"). //minutos
			date("s"). //segundos
			date("_").
			substr(microtime(),2,4).
			'.'.$extension;
		$rutaArchivoSubido = $rutaFotoTemporal.$nombreArchivoFinal;
		move_uploaded_file($_FILES["file"]['tmp_name'], $rutaArchivoSubido);
		Log::log([
			'descripcion' => 'Subió imagen: '.$rutaArchivoSubido
		]);
		return $nombreArchivoFinal;
	}

}
