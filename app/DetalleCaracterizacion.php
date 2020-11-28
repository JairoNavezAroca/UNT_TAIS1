<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DetalleCaracterizacion extends Model
{
	protected $table = 'detallecaracterizacion';
	protected $primaryKey = 'idDetalleCaracterizacion';
	protected $fillable = [
		'idDetalleCaracterizacion',
		'idCaracterizacion',
		'nombreArchivo',
		'descripcion',
		'idProceso'
	];
	protected $casts = [
		'fechareg' => 'datetime:d/m/Y h:i a',
	];

	public static function listar($idCaracterizacion, $idProceso){
		return DetalleCaracterizacion::where([
			'idCaracterizacion' => $idCaracterizacion,
			'idProceso' => $idProceso
			])
			->select(
				'idDetalleCaracterizacion',
				'idCaracterizacion',
				'nombreArchivo',
				'descripcion',
				'idProceso',
				'created_at as fechareg'
			)
			->orderBy('idDetalleCaracterizacion', 'desc')
			->get();
	}

	public static function registrar($datos){
		$detallecaracterizacion = DetalleCaracterizacion::create([
			'idCaracterizacion' => $datos['idCaracterizacion'],
			'nombreArchivo' => $datos['nombreArchivo'],
			'descripcion' => $datos['descripcion'],
			'idProceso' => $datos['idProceso']
		]);
		$varrr = new \DateTime($detallecaracterizacion->created_at);
		$detallecaracterizacion->fechareg = $varrr->format('d/m/Y h:i a');
		return $detallecaracterizacion;
	}

}
