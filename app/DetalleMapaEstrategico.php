<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DetalleMapaEstrategico extends Model
{
	protected $table = 'detalleMapaEstrategico';
	protected $primaryKey = 'idDetalleMapaEstrategico';
	protected $fillable = [
		'idDetalleMapaEstrategico',
		'idMapaEstrategico',
		'idProceso',
		'nombre',
		'aspecto',
		'idAnterior',
		'estado'
	];

	public static function listar($idMapaEstrategico, $idProceso){
		return DetalleMapaEstrategico::where([
			'idMapaEstrategico' => $idMapaEstrategico,
			'idProceso' => $idProceso,
			'estado' => '1'
			])
			->select(
				'idDetalleMapaEstrategico',
				'idMapaEstrategico',
				'idProceso',
				'nombre',
				'aspecto',
				'idAnterior',
				'estado'
			)
			->orderBy('idDetalleMapaEstrategico', 'desc')
			->get();
	}

	public static function registrar($idMapaEstrategico, $obj, $idProceso){
		$detallemapaestrategico = DetalleMapaEstrategico::create([
			'idMapaEstrategico' => $idMapaEstrategico,
			'idProceso' => $idProceso,
			'nombre' => $obj['nombre'],
			'aspecto' => $obj['aspecto'],
			'idAnterior' => $obj['idAnterior'],
			'estado' => '1'
		]);
		return $detallemapaestrategico;
	}

	public static function modificar($idDetalleMapaEstrategico, $obj){
		$detallemapaestrategico = DetalleMapaEstrategico::find($idDetalleMapaEstrategico);
		$detallemapaestrategico->nombre = $obj['nombre'];
		$detallemapaestrategico->aspecto = $obj['aspecto'];
		$detallemapaestrategico->idAnterior = $obj['idAnterior'];
		$detallemapaestrategico->estado = $obj['estado'];
		$detallemapaestrategico->save();
		return $detallemapaestrategico;
	}

	public static function listar_por_proceso($idProceso){
		return DetalleMapaEstrategico::where([
			'idProceso' => $idProceso,
			'estado' => '1'
			])
			->get();
	}
}
