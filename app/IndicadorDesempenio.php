<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class IndicadorDesempenio extends Model
{
	protected $table = 'indicadorDesempenio';
	protected $primaryKey = 'idIndicadorDesempenio';
	protected $fillable = [
		'idIndicadorDesempenio',
		'tipo',
		'estado',
		'idMapaProcesos',
		'idProceso'
	];

	public static function obtener($datos){
		return IndicadorDesempenio::where([
			'idMapaProcesos' => $datos['idMapaProcesos'],
			'idProceso' => $datos['idProceso']
		])->first();
	}
	public static function registrar($datos){
		return IndicadorDesempenio::create([
			'tipo' => '-',
			'estado' => '1',
			'idMapaProcesos' => $datos['idMapaProcesos'],
			'idProceso' => $datos['idProceso']
		]);
	}
	public static function cambiarTipo($idIndicadorDesempenio, $tipo){
		if ($tipo == 'Proceso')
			$tipo = 'P';
		else if ($tipo == 'SubProceso')
			$tipo = 'S';
		IndicadorDesempenio::where(['idIndicadorDesempenio' => $idIndicadorDesempenio])
			->update(['tipo' => $tipo]);
		return $tipo;
	}

}
