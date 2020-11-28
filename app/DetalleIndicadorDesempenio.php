<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DetalleIndicadorDesempenio extends Model
{
	protected $table = 'detalleIndicadorDesempenio';
	protected $primaryKey = 'idDetalleIndicadorDesempenio';
	protected $fillable = [
		'idDetalleIndicadorDesempenio',
		'idIndicadorDesempenio',
		'idProceso',
		'nombre',
		'puesto',
		'medir',
		'mecanismo',
		'tolerancia',
		'quehacer',
		'formula',
		'estado'
	];

	public static function listar($idIndicadorDesempenio, $idProceso){
		return DetalleIndicadorDesempenio::where([
			'idIndicadorDesempenio' => $idIndicadorDesempenio,
			'idProceso' => $idProceso,
			'estado' => '1'
			])
			->select(
				'idDetalleIndicadorDesempenio',
				'idIndicadorDesempenio',
				'idProceso',
				'nombre',
				'puesto',
				'medir',
				'mecanismo',
				'tolerancia',
				'quehacer',
				'formula',
				'estado'
			)
			->orderBy('idDetalleIndicadorDesempenio', 'desc')
			->get();
	}

	public static function registrar($idIndicadorDesempenio, $obj, $idProceso){
		$detalleindicadordesempenio = DetalleIndicadorDesempenio::create([
			'idIndicadorDesempenio' => $idIndicadorDesempenio,
			'idProceso' => $idProceso,
			'nombre' => $obj['nombre'],
			'puesto' => $obj['puesto'],
			'medir' => $obj['medir'],
			'mecanismo' => $obj['mecanismo'],
			'tolerancia' => $obj['tolerancia'],
			'quehacer' => $obj['quehacer'],
			'formula' => json_encode($obj['formula']),
			'estado' => '1'
		]);
		return $detalleindicadordesempenio;
	}

	public static function modificar($idDetalleIndicadorDesempenio, $obj){
		$detalleindicadordesempenio = DetalleIndicadorDesempenio::find($idDetalleIndicadorDesempenio);
		$detalleindicadordesempenio->nombre = $obj['nombre'];
		$detalleindicadordesempenio->puesto = $obj['puesto'];
		$detalleindicadordesempenio->medir = $obj['medir'];
		$detalleindicadordesempenio->mecanismo = $obj['mecanismo'];
		$detalleindicadordesempenio->tolerancia = $obj['tolerancia'];
		$detalleindicadordesempenio->quehacer = $obj['quehacer'];
		$detalleindicadordesempenio->formula = json_encode($obj['formula']);
		$detalleindicadordesempenio->estado = $obj['estado'];
		$detalleindicadordesempenio->save();
		return $detalleindicadordesempenio;
	}

	public static function listar_por_proceso($idProceso){
		return DetalleIndicadorDesempenio::where([
			'idProceso' => $idProceso,
			'estado' => '1'
			])
			->get();
	}
}
