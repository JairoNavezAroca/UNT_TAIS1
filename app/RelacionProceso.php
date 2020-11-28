<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class RelacionProceso extends Model
{
	protected $table = 'relacionproceso';
	protected $primaryKey = 'idRelacionProceso';
	protected $fillable = [
		'idRelacionProceso',
		'idProceso_desde',
		'idProceso_hasta',
		'idMapaProcesos',
		'estado'
	];

	public static function registrar($datos, $idMapaProcesos){
		return RelacionProceso::create([
			'idProceso_desde' => $datos['idProceso_desde'],
			'idProceso_hasta' => $datos['idProceso_hasta'],
			'idMapaProcesos' => $idMapaProcesos,
			'estado' => '1'
		]);
	}
	public static function modificar($idRelacionProceso, $datos){
		$relacionProceso = RelacionProceso::find($idRelacionProceso);
		$relacionProceso->idProceso_desde = $datos['idProceso_desde'];
		$relacionProceso->idProceso_hasta = $datos['idProceso_hasta'];
		$relacionProceso->estado = $datos['estado'];
		$relacionProceso->save();
		return $relacionProceso;
	}

	public static function listar($idMapaProcesos){
		$datos = [
			'relacionproceso.estado' => '1',
			'relacionproceso.idMapaProcesos' => $idMapaProcesos
		];
		return RelacionProceso::where($datos)
			->select(
				'relacionproceso.idRelacionProceso',
				'relacionproceso.idProceso_desde',
				'relacionproceso.idProceso_hasta',
				'relacionproceso.idMapaProcesos',
				'relacionproceso.estado',
				'p1.nombre as proceso_desde',
				'p2.nombre as proceso_hasta',
				'relacionproceso.created_at',
				'relacionproceso.updated_at'
			)
			->join('procesos as p1', 'p1.idProceso', '=', 'relacionproceso.idProceso_desde')
			->join('procesos as p2', 'p2.idProceso', '=', 'relacionproceso.idProceso_hasta')
			->get();
	}
}
