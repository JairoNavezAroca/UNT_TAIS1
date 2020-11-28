<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DetalleSeguimientoActividades extends Model
{
	protected $table = 'detalleseguimientoactividades';
	protected $primaryKey = 'idDetalleSeguimientoActividades';
	protected $fillable = [
		'idDetalleSeguimientoActividades',
		'idSeguimientoActividades',
		'actividad',
		'rol',
		'flujo',
		'tiempo',
		'orden',
		'estado',
		'idProceso'
	];
	protected $casts = [
		'fechareg' => 'datetime:d/m/Y h:i a',
	];

	public static function listar($idSeguimientoActividades, $idProceso){
		return DetalleSeguimientoActividades::where([
			'idSeguimientoActividades' => $idSeguimientoActividades,
			'idProceso' => $idProceso
			])
			->orderBy('orden', 'asc')
			->get();
	}

	public static function registrar($datos, $idSeguimientoActividades, $idProceso, $orden){
		$detalleseguimientoactividades = DetalleSeguimientoActividades::create([
			'idSeguimientoActividades' => $idSeguimientoActividades,
			'actividad' => $datos['actividad'],
			'rol' => $datos['rol'],
			'flujo' => $datos['flujo'],
			'tiempo' => $datos['tiempo'],
			'orden' => $orden,
			'estado' => '1',
			'idProceso' => $idProceso
		]);
		return $detalleseguimientoactividades;
	}
	public static function desactivar($idSeguimientoActividades){
		DetalleSeguimientoActividades::where([
			'idSeguimientoActividades' => $idSeguimientoActividades
			])
			->update([
				'estado' => '0',
				'orden' => null
			]);
	}
	public static function modificar($datos, $orden){
		$detalleseguimientoactividades = DetalleSeguimientoActividades::find($datos['idDetalleSeguimientoActividades']);
		//$detalleseguimientoactividades->idSeguimientoActividades = $datos['idSeguimientoActividades'];
		$detalleseguimientoactividades->actividad = $datos['actividad'];
		$detalleseguimientoactividades->rol = $datos['rol'];
		$detalleseguimientoactividades->flujo = $datos['flujo'];
		$detalleseguimientoactividades->tiempo = $datos['tiempo'];
		$detalleseguimientoactividades->estado = '1';
		$detalleseguimientoactividades->orden = $orden;
		$detalleseguimientoactividades->idProceso = $datos['idProceso'];
		$detalleseguimientoactividades->save();
		return $detalleseguimientoactividades;
	}

}
