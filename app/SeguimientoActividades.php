<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class SeguimientoActividades extends Model
{
	protected $table = 'seguimientoactividades';
	protected $primaryKey = 'idSeguimientoActividades';
	protected $fillable = [
		'idSeguimientoActividades',
		'tipo',
		'situacion',
		'estado',
		'idMapaProcesos',
		'idProceso'
	];

	private static $SITUACION_ACTUAL = 'A';
	private static $PROPUESTA = 'P';

	public static function obtener($datos){
		return SeguimientoActividades::where([
			'idMapaProcesos' => $datos['idMapaProcesos'],
			'idProceso' => $datos['idProceso'],
			'situacion' => $datos['situacion']
		])->first();
	}
	public static function registrar($datos){
		return SeguimientoActividades::create([
			'tipo' => '-',
			'estado' => '1',
			'idMapaProcesos' => $datos['idMapaProcesos'],
			'idProceso' => $datos['idProceso'],
			'situacion' => $datos['situacion']
		]);
	}
	public static function cambiarTipo($idSeguimientoActividades, $tipo){
		if ($tipo == 'Proceso')
			$tipo = 'P';
		else if ($tipo == 'SubProceso')
			$tipo = 'S';
		SeguimientoActividades::where(['idSeguimientoActividades' => $idSeguimientoActividades])
			->update(['tipo' => $tipo]);
		return $tipo;
	}

}
