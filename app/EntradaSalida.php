<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class EntradaSalida extends Model
{
	protected $table = 'entradasalida';
	protected $primaryKey = 'idEntradaSalida';
	protected $fillable = [
		'idEntradaSalida',
		'idMapaProcesos',
		'nombre',
		'tipo',
		'estado'
	];

	private static $ENTRADA = 'E';
	private static $SALIDA = 'S';

	private static $TXT_ENTRADA = 'ENTRADA';
	private static $TXT_SALIDA = 'SALIDA';

	public static function registrarEntrada($datos, $idMapaProcesos){
		$datos = array_merge($datos, array('tipo' => EntradaSalida::$ENTRADA));
		$datos = array_merge($datos, array('idMapaProcesos' => $idMapaProcesos));
		return static::registrar($datos);
	}
	public static function registrarSalida($datos, $idMapaProcesos){
		$datos = array_merge($datos, array('tipo' => EntradaSalida::$SALIDA));
		$datos = array_merge($datos, array('idMapaProcesos' => $idMapaProcesos));
		return static::registrar($datos);
	}
	private static function registrar($datos){
		return EntradaSalida::create([
			'idMapaProcesos' => $datos['idMapaProcesos'],
			'nombre' => $datos['nombre'],
			'tipo' => $datos['tipo'],
			'estado' => '1'
		]);
	}
	public static function modificarEntradaSalida($idMapaProcesos, $mapaProcesos){
		$entradaSalida = EntradaSalida::find($idMapaProcesos);
		$entradaSalida->nombre = $mapaProcesos['nombre'];
		$entradaSalida->estado = $mapaProcesos['estado'];
		$entradaSalida->save();
		return $entradaSalida;
	}

	public static function listarEntradas($idMapaProcesos){
		$where = array('idMapaProcesos' => $idMapaProcesos, 'tipo' => static::$ENTRADA);
		return static::listar($where);
	}
	public static function listarSalidas($idMapaProcesos){
		$where = array('idMapaProcesos' => $idMapaProcesos ,'tipo' => static::$SALIDA);
		return static::listar($where);
	}
	public static function listar($datos){
		$datos = array_merge($datos, ['estado' => '1']);
		return EntradaSalida::where($datos)->get();
	}
}
