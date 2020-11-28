<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DetalleTableroControl extends Model
{
	protected $table = 'detalleTableroControl';
	protected $primaryKey = 'idDetalleTableroControl';
	protected $fillable = [
		'idDetalleTableroControl',
		'idTableroControl',
		'idProceso',
		'idDetalleIndicadorDesempenio',
		'idDetalleMapaEstrategico',
		'objetivo',
		'lineabase',
		'meta',
		'responsable',
		'iniciativas',
		'luz_roja_signo',
		'luz_roja_valor',
		'luz_verde_signo',
		'luz_verde_valor',
		'frecuenciamedicion',
		'estado'
	];
	protected $casts = [
		'fechareg' => 'datetime:d/m/Y h:i a',
	];

	public static function listar($idTableroControl, $idProceso){
		return DetalleTableroControl::where([
			'idTableroControl' => $idTableroControl,
			'idProceso' => $idProceso,
			'estado' => '1'
			])
			->select(
				'idDetalleTableroControl',
				'idTableroControl',
				'idProceso',
				'idDetalleIndicadorDesempenio',
				'idDetalleMapaEstrategico',
				'objetivo',
				'lineabase',
				'meta',
				'responsable',
				'iniciativas',
				'luz_roja_signo',
				'luz_roja_valor',
				'luz_verde_signo',
				'luz_verde_valor',
				'frecuenciamedicion',
				'estado'
			)
			->orderBy('idDetalleTableroControl', 'desc')
			->get();
	}

	public static function registrar($idTableroControl, $obj, $idProceso){
		$detallemapaestrategico = DetalleTableroControl::create([
			'idTableroControl' => $idTableroControl,
			'idProceso' => $idProceso,
			'idDetalleIndicadorDesempenio' => $obj['idDetalleIndicadorDesempenio'],
			'idDetalleMapaEstrategico' => $obj['idDetalleMapaEstrategico'],
			'objetivo' => $obj['objetivo'],
			'lineabase' => $obj['lineabase'],
			'meta' => $obj['meta'],
			'responsable' => $obj['responsable'],
			'iniciativas' => $obj['iniciativas'],
			'luz_roja_signo' => $obj['luz_roja_signo'],
			'luz_roja_valor' => $obj['luz_roja_valor'],
			'luz_verde_signo' => $obj['luz_verde_signo'],
			'luz_verde_valor' => $obj['luz_verde_valor'],
			'frecuenciamedicion' => $obj['frecuenciamedicion'],
			'estado' => '1'
		]);
		return $detallemapaestrategico;
	}

	public static function modificar($idDetalleTableroControl, $obj){
		$detallemapaestrategico = DetalleTableroControl::find($idDetalleTableroControl);
		$detallemapaestrategico->nombre = $obj['nombre'];
		$detallemapaestrategico->aspecto = $obj['aspecto'];
		$detallemapaestrategico->idAnterior = $obj['idAnterior'];
		$detallemapaestrategico->estado = $obj['estado'];
		$detallemapaestrategico->save();
		return $detallemapaestrategico;
	}

}
