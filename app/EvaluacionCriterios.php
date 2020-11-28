<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB;

class EvaluacionCriterios extends Model
{
	protected $table = 'evaluacioncriterios';
	//protected $primaryKey = 'idEvaluacionCriterios';
	protected $fillable = [
		'idMapaProcesos',
		'idProceso',
		'idCriteriosPriorizacion',
		'puntaje'
	];

	protected $casts = [
		'puntaje' => 'integer'
	];

	public static function registrar($idMapaProcesos, $idProceso, $idCriteriosPriorizacion, $puntaje){
		return EvaluacionCriterios::create([
			'idMapaProcesos' => $idMapaProcesos,
			'idProceso' => $idProceso,
			'idCriteriosPriorizacion' => $idCriteriosPriorizacion,
			'puntaje' => $puntaje
		]);
	}
	public static function modificar($idMapaProcesos, $idProceso, $idCriteriosPriorizacion, $puntaje){
		EvaluacionCriterios::where([
			'idMapaProcesos' => $idMapaProcesos,
			'idProceso' => $idProceso,
			'idCriteriosPriorizacion' => $idCriteriosPriorizacion
		])
		->whereRaw('puntaje <> ?', [$puntaje])
		->update(['puntaje' => $puntaje]);
	}
	public static function listar($idMapaProcesos){
		$datos = [
			'idMapaProcesos' => $idMapaProcesos
		];
		return EvaluacionCriterios::where($datos)
			->select(
				'idMapaProcesos',
				'idProceso',
				'idCriteriosPriorizacion',
				'puntaje'
				)
			->get();
	}
}
