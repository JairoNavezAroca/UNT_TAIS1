<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DiagramaFlujo extends Model
{
	protected $table = 'diagramaflujo';
	protected $primaryKey = 'idDiagramaFlujo';
	protected $fillable = [
		'idDiagramaFlujo',
		'tipo',
		'situacion',
		'estado',
		'idMapaProcesos',
		'idProceso'
	];

	private static $SITUACION_ACTUAL = 'A';
	private static $REDISENO = 'R';

	public static function obtener($datos){
		return DiagramaFlujo::where([
			'idMapaProcesos' => $datos['idMapaProcesos'],
			'idProceso' => $datos['idProceso'],
			'situacion' => $datos['situacion']
		])->first();
	}
	public static function registrar($datos){
		return DiagramaFlujo::create([
			'tipo' => '-',
			'estado' => '1',
			'idMapaProcesos' => $datos['idMapaProcesos'],
			'idProceso' => $datos['idProceso'],
			'situacion' => $datos['situacion']
		]);
	}
	public static function cambiarTipo($idDiagramaFlujo, $tipo){
		if ($tipo == 'Proceso')
			$tipo = 'P';
		else if ($tipo == 'SubProceso')
			$tipo = 'S';
		DiagramaFlujo::where(['idDiagramaFlujo' => $idDiagramaFlujo])
			->update(['tipo' => $tipo]);
		return $tipo;
	}

}
