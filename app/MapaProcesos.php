<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB;

class MapaProcesos extends Model
{
	protected $table = 'mapaprocesos';
	protected $primaryKey = 'idMapaProcesos';
	protected $fillable = [
		'idMapaProcesos',
		'idUnidadNegocio',
		'idEmpresa',
		'descripcion',
		'tipo',
		'priorizado',
		'cantidadPriorizar',
		'estado',
		'idUsuario'
	];
	protected $casts = [
		'fechacreacion' => 'datetime:d/m/Y h:i a',
		'cantidadPriorizar' => 'integer',
		'priorizado' => 'boolean'
	];

	private static $MAPA_ACTIVO = 'A';
	private static $MAPA_ELIMINADO = 'E';
	private static $MAPA_EN_HISTORICO = 'H';

	private static $TXT_MAPA_ACTIVO = 'ACTIVO';
	private static $TXT_MAPA_ELIMINADO = 'ELIMINADO';
	private static $TXT_MAPA_EN_HISTORICO = 'EN HISTÓRICO';

	public static function registrar($mapaProcesos){
		$_mapaProcesos = MapaProcesos::create([
			'idUnidadNegocio' => $mapaProcesos['idUnidadNegocio'],
			'idEmpresa' => $mapaProcesos['idEmpresa'],
			'descripcion' => '-',
			'tipo' => '',///////////////////////////////////////////////////////QUE ES?
			'priorizado' => false,
			'cantidadPriorizar' => 0,
			'estado' => MapaProcesos::$MAPA_ACTIVO,
			'idUsuario' => Auth::user()->idUsuario
		]);
		return $_mapaProcesos;
	}

	public static function modificar($idMapaProcesos, $mapaProcesos){
		dd();
		return $_cadenasuministro;
	}

	public static function listar($_where = null){
		$where = array();
		$where['un.estado'] = '1';
		$where['mapaprocesos.idUsuario'] = Auth::user()->idUsuario;
		if ($_where != null)
			$where = array_merge($where, $_where);
		$listaMapaProcesos = MapaProcesos::where($where)->whereRaw('mapaprocesos.estado != ?', MapaProcesos::$MAPA_ELIMINADO)
			->select(
				'mapaprocesos.idMapaProcesos',
				'un.idUnidadNegocio',
				'un.nombre as nombreunidadnegocio',
				'em.idEmpresa',
				'em.nombre as nombreempresa',
				'mapaprocesos.descripcion',
				'mapaprocesos.tipo',
				'mapaprocesos.priorizado as priorizado',
				DB::raw("(CASE
					WHEN mapaprocesos.priorizado = 1 THEN 'SI'
					ELSE 'NO' END) AS priorizado2"),
				DB::raw("(CASE
					WHEN mapaprocesos.estado = '".MapaProcesos::$MAPA_ACTIVO."' THEN '".MapaProcesos::$TXT_MAPA_ACTIVO."'
					WHEN mapaprocesos.estado = '".MapaProcesos::$MAPA_ELIMINADO."' THEN '".MapaProcesos::$TXT_MAPA_ELIMINADO."'
					WHEN mapaprocesos.estado = '".MapaProcesos::$MAPA_EN_HISTORICO."' THEN '".MapaProcesos::$TXT_MAPA_EN_HISTORICO."'
					ELSE '' END) AS estado"),
				'mapaprocesos.estado as estado2',
				'mapaprocesos.cantidadPriorizar',
				'mapaprocesos.created_at as fechacreacion'
			)
			->join('unidadnegocio as un', 'un.idUnidadNegocio', '=', 'mapaprocesos.idUnidadNegocio')
			->join('empresa as em', 'em.idEmpresa', '=', 'un.idEmpresa')
			->orderByDesc('mapaprocesos.idMapaProcesos')
			->get();
			//->toSql();
		return $listaMapaProcesos;
	}

	public static function obtener($idMapaProcesos){
		$where = array('mapaprocesos.idMapaProcesos' => $idMapaProcesos);
		return static::listar($where)->first();
	}

	public static function actualizarCantidadPriorizar($datosmapa, $cantidadpriorizar){
		$mapaProcesos = MapaProcesos::find($datosmapa['idMapaProcesos']);
		$mapaProcesos->cantidadPriorizar = $cantidadpriorizar;
		$mapaProcesos->save();
		return $mapaProcesos;
	}

	public static function finalizarpriorizar($idMapaProcesos){
		$mapaProcesos = MapaProcesos::find($idMapaProcesos);
		$mapaProcesos->priorizado = true;
		$mapaProcesos->save();
		return $mapaProcesos;
	}


	public static function getCodigoActivo(){
		return static::$MAPA_ACTIVO;
	}
	public static function getCodigoHistorico(){
		return static::$MAPA_EN_HISTORICO;
	}
	public static function get($idMapaProcesos){
		return MapaProcesos::find($idMapaProcesos);
	}
}
