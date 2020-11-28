<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class VersionCadena extends Model
{
	protected $table = 'versioncadena';
	protected $primaryKey = 'idVersionCadena';
	protected $fillable = [
		'idVersionCadena',
		'idCadenaSuministro',
		'descripcion',
		'estado'
	];
	protected $casts = [
		'creado' => 'datetime:d/m/Y h:i a',
		'modificado' => 'datetime:d/m/Y h:i a'
	];

	public static function registrarNueva($idCadenaSuministro, $descripcion = null){
		$versioncadena = VersionCadena::create([
			'idCadenaSuministro' => $idCadenaSuministro,
			'descripcion' => $descripcion ?? 'Se creó la cadena de suministro',
			'estado' => '1'
		]);
		return $versioncadena;
	}
	public static function desactivarUltima($idCadenaSuministro){
		VersionCadena::where([
			'idCadenaSuministro' => $idCadenaSuministro,
			'estado' => '1'
		])
		->update([
			'estado' => '0'
		]);
	}

	public static function getUltima($idCadenaSuministro){
		return VersionCadena::select('idVersionCadena')
		->where([
			'idCadenaSuministro' => $idCadenaSuministro,
			'estado' => '1'
		])
		->orderBy('idCadenaSuministro', 'desc')
		->first()
		->idVersionCadena;
	}

	public static function listarVersiones($idCadenaSuministro){
		return VersionCadena::select(
			'idVersionCadena',
			'idCadenaSuministro',
			'descripcion',
			'estado',
			'created_at as creado',
			'updated_at as modificado'
		)
		->where([
			'idCadenaSuministro' => $idCadenaSuministro
		])
		->orderBy('idVersionCadena', 'desc')
		->get();
	}


}
