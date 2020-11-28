<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB;

class Empresa extends Model
{
	protected $table = 'empresa';
	protected $primaryKey = 'idEmpresa';
	protected $fillable = [
		'idEmpresa',
		'nombre',
		'ruc',
		'telefono',
		'direccion',
		'foto',
		'estado',
		'idUsuario'
	];
	public static function listar(){
		$empresa = Empresa::where([
				'estado' => '1',
				'idUsuario' => Auth::user()->idUsuario
			])
			->get();
		return $empresa;
	}

	public static function registrar($empresa){
		$empresa = Empresa::create([
			'nombre' => $empresa['nombre'],
			'ruc' => $empresa['ruc'],
			'telefono' => $empresa['telefono'],
			'direccion' => $empresa['direccion'],
			'foto' => $empresa['foto'],
			'estado' => '1',
			'idUsuario' => Auth::user()->idUsuario
		]);
		return $empresa;
	}

	public static function modificar($idEmpresa, $empresa){
		$_empresa = Empresa::find($idEmpresa);
		$_empresa->nombre = $empresa['nombre'];
		$_empresa->ruc = $empresa['ruc'];
		$_empresa->telefono = $empresa['telefono'];
		$_empresa->direccion = $empresa['direccion'];
		$_empresa->foto = $empresa['foto'];
		$_empresa->save();
		return $_empresa;
	}

	public static function eliminar($idEmpresa){
		$_empresa = Empresa::find($idEmpresa);
		$_empresa->estado = '0';
		$_empresa->save();
		return;
		//return $_empresa;
	}

	public static function getNombre($idEmpresa){
		$_empresa = Empresa::find($idEmpresa);
		return $_empresa->nombre;
	}
}
