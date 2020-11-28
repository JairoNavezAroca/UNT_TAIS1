<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB;

class CadenaSuministro extends Model
{
	protected $table = 'cadenasuministro';
	protected $primaryKey = 'idCadenaSuministro';
	protected $fillable = [
		'idCadenaSuministro',
		'idUnidadNegocio',
		'idEmpresa',
		'detalle',
		'estado',
		'idUsuario'
	];
	protected $casts = [
		'ultimafecha' => 'datetime:d/m/Y h:i a'
	];

	public static function registrar($cadenasuministro){
		$_cadenasuministro = CadenaSuministro::create([
			'idUnidadNegocio' => $cadenasuministro['idUnidadNegocio'],
			'idEmpresa' => $cadenasuministro['idEmpresa'],
			'detalle' => $cadenasuministro['detalle'],
			'estado' => '1',
			'idUsuario' => Auth::user()->idUsuario
		]);
		return $_cadenasuministro;
	}

	public static function modificar($idCadenaSuministro, $cadenasuministro){
		$_cadenasuministro = CadenaSuministro::find($idCadenaSuministro);
		$_cadenasuministro->detalle = $cadenasuministro['detalle'];
		$_cadenasuministro->save();
		return $_cadenasuministro;
	}

	public static function listar($_where = null){
		$where = array();
		$where['un.estado'] = '1';
		$where['cadenasuministro.estado'] = '1';
		$where['cadenasuministro.idUsuario'] = Auth::user()->idUsuario;
		if ($_where != null)
			$where = array_merge($where, $_where);
		$unidadnegocio = CadenaSuministro::where($where)
			->select(
				'cadenasuministro.idCadenaSuministro',
				'cadenasuministro.detalle',
				'un.idUnidadNegocio',
				'un.nombre as nombreunidadnegocio',
				'em.idEmpresa',
				'em.nombre as nombreempresa',
				DB::raw("(select created_at from versioncadena where idCadenaSuministro = cadenasuministro.idCadenaSuministro limit 1) as ultimafecha")
			)
			->join('unidadnegocio as un', 'un.idUnidadNegocio', '=', 'cadenasuministro.idUnidadNegocio')
			->join('empresa as em', 'em.idEmpresa', '=', 'un.idEmpresa')
			->get();
		return $unidadnegocio;
	}


	public static function obtener($idCadenaSuministro){
		$where = array('cadenasuministro.idCadenaSuministro' => $idCadenaSuministro);
		return static::listar($where)->first();
	}
}
