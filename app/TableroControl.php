<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class TableroControl extends Model
{
	protected $table = 'tableroControl';
	protected $primaryKey = 'idTableroControl';
	protected $fillable = [
		'idTableroControl',
		'idMapaProcesos',
		'idProceso',
		'tipo',
		'estado'
	];

	public static function obtener($datos){
		return TableroControl::where([
			'idMapaProcesos' => $datos['idMapaProcesos'],
			'idProceso' => $datos['idProceso']
		])->first();
	}
	public static function registrar($datos){
		return TableroControl::create([
			'idMapaProcesos' => $datos['idMapaProcesos'],
			'idProceso' => $datos['idProceso'],
			'tipo' => '-',
			'estado' => '1'
		]);
	}
	public static function cambiarTipo($idTableroControl, $tipo){
		if ($tipo == 'Proceso')
			$tipo = 'P';
		else if ($tipo == 'SubProceso')
			$tipo = 'S';
		TableroControl::where(['idTableroControl' => $idTableroControl])
			->update(['tipo' => $tipo]);
		return $tipo;
	}
	public static function darOrden($idTableroControl, $orden_aspectos){
		$_TableroControl = TableroControl::find($idTableroControl);
		$_TableroControl->orden_aspectos = $orden_aspectos;
		$_TableroControl->save();
		return $_TableroControl;
	}

}
