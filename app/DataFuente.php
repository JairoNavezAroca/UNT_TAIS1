<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DataFuente extends Model
{
	protected $table = 'DataFuente';
	protected $primaryKey = 'idDataFuente';
	protected $fillable = [
		'idDataFuente',
		'idDetalleTableroControl',
		'fecha',
		'descripcion',
		'variables_resultado',
		'considerar',
		'estado'
	];

	public static function obtener($idDetalleTableroControl){
		return DataFuente::where([
			'idDetalleTableroControl' => $idDetalleTableroControl,
			'estado' => '1'
		])->get();
	}

	public static function registrar($datos, $idDetalleTableroControl){
		return DataFuente::create([
			'idDetalleTableroControl' => $idDetalleTableroControl,
			'fecha' => $datos['fecha'],
			'descripcion' => $datos['descripcion'],
			'variables_resultado' => $datos['variables_resultado'],
			'considerar' => $datos['considerar'],
			'estado' => '1'
		]);
	}

	public static function modificar($idDataFuente, $datos){
		$_DataFuente = DataFuente::find($idDataFuente);
		$_DataFuente->fecha = $datos['fecha'];
		$_DataFuente->descripcion = $datos['descripcion'];
		$_DataFuente->variables_resultado = $datos['variables_resultado'];
		$_DataFuente->considerar = intval($datos['considerar']);
		$_DataFuente->estado = $datos['estado'];
		$_DataFuente->save();
		return $_DataFuente;
	}

}
