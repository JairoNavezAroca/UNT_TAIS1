<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Caracterizacion extends Model
{
	protected $table = 'caracterizacion';
	protected $primaryKey = 'idCaracterizacion';
	protected $fillable = [
		'idCaracterizacion',
		'tipo',
		'estado',
		'idMapaProcesos',
		'idProceso'
	];

	public static function obtener($datos){
		return Caracterizacion::where([
			'idMapaProcesos' => $datos['idMapaProcesos'],
			'idProceso' => $datos['idProceso']
		])->first();
	}
	public static function registrar($datos){
		return Caracterizacion::create([
			'tipo' => '-',
			'estado' => '1',
			'idMapaProcesos' => $datos['idMapaProcesos'],
			'idProceso' => $datos['idProceso']
		]);
	}
	public static function cambiarTipo($idCaracterizacion, $tipo){
		if ($tipo == 'Proceso')
			$tipo = 'P';
		else if ($tipo == 'SubProceso')
			$tipo = 'S';
		Caracterizacion::where(['idCaracterizacion' => $idCaracterizacion])
			->update(['tipo' => $tipo]);
		return $tipo;
	}

}
