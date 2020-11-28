<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Proceso extends Model
{
	protected $table = 'procesos';
	protected $primaryKey = 'idProceso';
	protected $fillable = [
		'idProceso',
		'idMapaProcesos',
		'nombre',
		'tipo',
		'priorizado',
		'estado',
		'idProcesoAnterior'
	];
	protected $casts = [
		'priorizado' => 'boolean'
	];

	private static $PRIMARIO = 'P';
	private static $APOYO = 'A';
	private static $ESTRATEGICO = 'E';

	private static $TXT_PRIMARIO = 'PRIMARIO';
	private static $TXT_APOYO = 'DE APOYO';
	private static $TXT_ESTRATEGICO = 'ESTRATÉGICO';

	public static function registrarPPrimario($datos, $idMapaProcesos){
		$datos = array_merge($datos, array('tipo' => static::$PRIMARIO));
		$datos = array_merge($datos, array('idMapaProcesos' => $idMapaProcesos));
		return static::registrar($datos);
	}
	public static function registrarPApoyo($datos, $idMapaProcesos){
		$datos = array_merge($datos, array('tipo' => static::$APOYO));
		$datos = array_merge($datos, array('idMapaProcesos' => $idMapaProcesos));
		return static::registrar($datos);
	}
	public static function registrarPEstrategico($datos, $idMapaProcesos){
		$datos = array_merge($datos, array('tipo' => static::$ESTRATEGICO));
		$datos = array_merge($datos, array('idMapaProcesos' => $idMapaProcesos));
		return static::registrar($datos);
	}

	private static function registrar($datos){
		return Proceso::create([
			'idMapaProcesos' => $datos['idMapaProcesos'],
			'nombre' => $datos['nombre'],
			'tipo' => $datos['tipo'],
			'priorizado' => false,
			'estado' => '1',
			'idProcesoAnterior' => $datos['idProcesoAnterior'] ?? null
		]);
	}
	public static function modificarProceso($idMapaProcesos, $mapaProcesos){
		$proceso = Proceso::find($idMapaProcesos);
		$proceso->nombre = $mapaProcesos['nombre'];
		$proceso->estado = $mapaProcesos['estado'];
		$proceso->idProcesoAnterior = $mapaProcesos['idProcesoAnterior'];
		$proceso->save();
		return $proceso;
	}

	public static function listarPPrimarios($idMapaProcesos){
		$where = array('idMapaProcesos' => $idMapaProcesos, 'tipo' => static::$PRIMARIO);
		return static::listar($where);
	}
	public static function listarPEstrategicos($idMapaProcesos){
		$where = array('idMapaProcesos' => $idMapaProcesos, 'tipo' => static::$ESTRATEGICO);
		return static::listar($where);
	}
	public static function listarPApoyo($idMapaProcesos){
		$where = array('idMapaProcesos' => $idMapaProcesos, 'tipo' => static::$APOYO);
		return static::listar($where);
	}
	public static function listarPriorizados($idMapaProcesos){
		$where = array('idMapaProcesos' => $idMapaProcesos, 'priorizado' => true);
		return static::listar($where);
	}
	public static function listarSubProcesos($idProceso){
		$where = array('idProcesoAnterior' => $idProceso);
		return static::listar($where);
	}
	public static function listar($datos){
		$datos = array_merge($datos, ['estado' => '1']);
		return Proceso::where($datos)->get();
	}

	public static function cambiarPriorizacion($idProceso, $priorizado){
		Proceso::where(['idProceso' => $idProceso])
		->update(['priorizado' => $priorizado]);
	}

}
