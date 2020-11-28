<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB;

class CriterioPriorizacion extends Model
{
	protected $table = 'criteriospriorizacion';
	protected $primaryKey = 'idCriteriosPriorizacion';
	protected $fillable = [
		'idCriteriosPriorizacion',
		'idMapaProcesos',
		'nombre',
		'peso',
		'justificacion',
		'valmin',
		'valmax',
		'estado'
	];

	protected $casts = [
		'peso' => 'integer',
		'valmin' => 'integer',
		'valmax' => 'integer'
	];

	public static function registrar($datos, $idMapaProcesos){
		return CriterioPriorizacion::create([
			'idMapaProcesos' => $idMapaProcesos,
			'nombre' => $datos['nombre'],
			'peso' => $datos['peso'],
			'justificacion' => $datos['justificacion'],
			'valmin' => $datos['valmin'],
			'valmax' => $datos['valmax'],
			'estado' => '1'
		]);
	}
	public static function modificar($idCriteriosPriorizacion, $datos){
		$criterioPriorizacion = CriterioPriorizacion::find($idCriteriosPriorizacion);
		//$criterioPriorizacion->idMapaProcesos = $datos['idMapaProcesos'];
		$criterioPriorizacion->nombre = $datos['nombre'];
		$criterioPriorizacion->peso = $datos['peso'];
		$criterioPriorizacion->justificacion = $datos['justificacion'];
		$criterioPriorizacion->valmin = $datos['valmin'];
		$criterioPriorizacion->valmax = $datos['valmax'];
		$criterioPriorizacion->estado = $datos['estado'];
		$criterioPriorizacion->save();
		return $criterioPriorizacion;
	}
	public static function listar($idMapaProcesos){
		$datos = [
			'estado' => '1',
			'idMapaProcesos' => $idMapaProcesos
		];
		return CriterioPriorizacion::where($datos)->get();
	}
}
