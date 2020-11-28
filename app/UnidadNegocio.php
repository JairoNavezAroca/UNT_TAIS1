<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class UnidadNegocio extends Model
{
	protected $table = 'unidadnegocio';
	protected $primaryKey = 'idUnidadNegocio';
	protected $fillable = [
		'idUnidadNegocio',
		'idEmpresa',
		'nombre',
		'descripcion',
		'estado'
	];

	public static function listar($idEmpresa){
		$unidadnegocio = UnidadNegocio::where([
				'estado' => '1',
				'idEmpresa' => $idEmpresa
			])
			->get();
		return $unidadnegocio;
	}

	public static function registrar($idEmpresa, $unidadnegocio){
		$unidadnegocio = UnidadNegocio::create([
			'idEmpresa' => $idEmpresa,
			'nombre' => $unidadnegocio['nombre'],
			'descripcion' => $unidadnegocio['descripcion'],
			'estado' => '1'
		]);
		return $unidadnegocio;
	}

	public static function modificar($idUnidadNegocio, $unidadnegocio){
		$_unidadnegocio = UnidadNegocio::find($idUnidadNegocio);
		$_unidadnegocio->nombre = $unidadnegocio['nombre'];
		$_unidadnegocio->descripcion = $unidadnegocio['descripcion'];
		$_unidadnegocio->save();
		return $_unidadnegocio;
	}

	public static function eliminar($idUnidadNegocio){
		$_unidadnegocio = UnidadNegocio::find($idUnidadNegocio);
		$_unidadnegocio->estado = '0';
		$_unidadnegocio->save();
		return;
		//return $_unidadnegocio;
	}

}
