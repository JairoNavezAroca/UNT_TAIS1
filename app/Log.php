<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB;

class Log extends Model
{
	protected $table = 'log';
	protected $primaryKey = 'idLog';
	protected $fillable = [
		'idLog',
		'idUsuario',
		'descripcion',
		'tabla',
		'idTabla',
		'uri',
		'ip',
		'puerto'
	];

	public static function log($datos){
		//if($_SERVER['REMOTE_ADDR'] == '179.7.136.224' || $_SERVER['REMOTE_ADDR'] == '179.7.136.224')
		try {
			//dd($_SERVER);
			Log::create([
				'idUsuario' => Auth::user()->idUsuario ?? null,
				'descripcion' => $datos['descripcion'] ?? null,
				'tabla' => $datos['tabla'] ?? null,
				'idTabla' => $datos['idTabla'] ?? null,
				'uri' => $_SERVER['REQUEST_URI'],
				'ip' => $_SERVER['REMOTE_ADDR'],
				'puerto' => $_SERVER['REMOTE_PORT']
			]);
		} catch (\Exception $e) {
			dd($e);
		}
	}

}
