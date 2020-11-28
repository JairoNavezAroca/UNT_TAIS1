<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class MapaEstrategico extends Model
{
	protected $table = 'mapaEstrategico';
	protected $primaryKey = 'idMapaEstrategico';
	protected $fillable = [
		'idMapaEstrategico',
		'tipo',
		'estado',
		'orden_aspectos',
		'idMapaProcesos',
		'idProceso'
	];

	public static function obtener($datos){
		return MapaEstrategico::where([
			'idMapaProcesos' => $datos['idMapaProcesos'],
			'idProceso' => $datos['idProceso']
		])->first();
	}
	public static function registrar($datos){
		return MapaEstrategico::create([
			'tipo' => '-',
			'estado' => '1',
			'orden_aspectos' => null,
			'idMapaProcesos' => $datos['idMapaProcesos'],
			'idProceso' => $datos['idProceso']
		]);
	}
	public static function cambiarTipo($idMapaEstrategico, $tipo){
		if ($tipo == 'Proceso')
			$tipo = 'P';
		else if ($tipo == 'SubProceso')
			$tipo = 'S';
		MapaEstrategico::where(['idMapaEstrategico' => $idMapaEstrategico])
			->update(['tipo' => $tipo]);
		return $tipo;
	}
	public static function darOrden($idMapaEstrategico, $orden_aspectos){
		$_MapaEstrategico = MapaEstrategico::find($idMapaEstrategico);
		$_MapaEstrategico->orden_aspectos = $orden_aspectos;
		$_MapaEstrategico->save();
		return $_MapaEstrategico;
	}

}
