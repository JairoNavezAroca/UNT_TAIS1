<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ProveedorCliente extends Model
{
	protected $table = 'proveedorcliente';
	protected $primaryKey = 'idProveedorCliente';
	protected $fillable = [
		'idProveedorCliente',
		'idEmpresa',
		'idVersionCadena',
		'tipo',
		'nivel',
		'estado',
		'idEmpresaAnterior'
	];

	public static function registrarProveedor($datos){
		$datos = array_merge($datos, array('tipo' => 'P'));
		return static::registrar($datos);
	}
	public static function registrarCliente($datos){
		$datos = array_merge($datos, array('tipo' => 'C'));
		return static::registrar($datos);
	}
	public static function registrar($datos){
		return ProveedorCliente::create([
			'idEmpresaAnterior' => $datos['idEmpresaAnterior'],
			'idEmpresa' => $datos['idEmpresa'],
			'nivel' => $datos['nivel'],
			'idVersionCadena' => $datos['idVersionCadena'],
			'tipo' => $datos['tipo'],
			'estado' => '1'
		]);
	}

	//actualizarDatos => actualiza los datos de la version anterior a la nueva
	public static function actualizarDatos($idVersionAnterior, $idVersionNueva){
		$versionAnterior = ProveedorCliente::where([
			'idVersionCadena' => $idVersionAnterior,
			'estado' => '1'
		])->get();
		foreach ($versionAnterior as $va) {
			static::registrar([
				'idEmpresaAnterior' => $va->idEmpresaAnterior,
				'idEmpresa' => $va->idEmpresa,
				'nivel' => $va->nivel,
				'tipo' => $va->tipo,
				'idVersionCadena' => $idVersionNueva,
				'idEmpresaAnterior' => $va->idEmpresaAnterior
			]);
		}
	}


	public static function getProveedores($idVersionCadena){
		return static::getProveedorCliente($idVersionCadena, 'P');
	}
	public static function getClientes($idVersionCadena){
		return static::getProveedorCliente($idVersionCadena, 'C');
	}
	public static function getProveedorCliente($idVersionCadena, $tipo){
		return ProveedorCliente::where([
			'idVersionCadena' => $idVersionCadena,
			'tipo' => $tipo,
			'estado' => '1'
		])
		->get();
	}


	public static function eliminar($datos){
		return ProveedorCliente::where([
			'idEmpresa' => $datos['idEmpresa'],
			'tipo' => $datos['tipo'],
			'nivel' => $datos['nivel'],
			'idEmpresaAnterior' => $datos['idEmpresaAnterior'],
			'idVersionCadena' => $datos['idVersionCadena']
		])
		->update([
			'estado' => '0'
		]);
	}
}
