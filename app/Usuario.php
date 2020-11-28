<?php
namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

use App\Log;

class Usuario extends Authenticatable
{
	use Notifiable;
	protected $table = 'usuario';
	protected $primaryKey = 'idUsuario';
	protected $fillable = [
		'idUsuario',
		'dni',
		'nombres',
		'apellidos',
		'usuario',
		'contrasena',
		'rol',
		'estado'
	];

	protected $hidden = [
		'contrasena', 'remember_token'
	];

	public static function obtenerUsuario($usuario){
		return Usuario::where('usuario', $usuario)->first();
	}
	public static function setFormato($usuario){
		//dd($usuario);
		if ($usuario->rol == 'A' || $usuario->rol_ == 'Administrador')
			$usuario->rol_ = 'Administrador';
		else if ($usuario->rol == 'T' || $usuario->rol_ == 'Trabajador')
			$usuario->rol_ = 'Trabajador';
		else
			$usuario->rol_ = '-';
		if ($usuario->estado == 'H' || $usuario->estado_ == 'Habilitado')
			$usuario->estado_ = 'Habilitado';
		else if ($usuario->estado == 'D' || $usuario->estado_ == 'Deshabilitado')
			$usuario->estado_ = 'Deshabilitado';
		else
			$usuario->estado_ = '-';
		return $usuario;
	}

	public static function listar(){
		$usuario = Usuario::get();
		$uss = [];
		foreach ($usuario as $u) {
			$uss[] = static::setFormato($u);
		}
		return $uss;
	}
	public static function deshabilita($idUsuario){
		$usuario = Usuario::find($idUsuario);
		$usuario->estado = 'D';
		$usuario->save();
		return static::setFormato($usuario);
	}
	public static function habilita($idUsuario){
		$usuario = Usuario::find($idUsuario);
		$usuario->estado = 'H';
		$usuario->save();
		return static::setFormato($usuario);
	}

	public static function registrar($usuario){
		$usuario = Usuario::create([
			'nombres' => $usuario['nombres'],
			'apellidos' => $usuario['apellidos'],
			'usuario' => $usuario['usuario'],
			'dni' => $usuario['dni'],
			'rol' => $usuario['rol'],
			'contrasena' => Hash::make($usuario['dni']),
			'estado' => 'H'
		]);
		return static::setFormato($usuario);
	}
	public static function modificar($idUsuario, $usuario){
		$_usuario = Usuario::find($idUsuario);
		$_usuario->nombres = $usuario['nombres'];
		$_usuario->apellidos = $usuario['apellidos'];
		$_usuario->usuario = $usuario['usuario'];
		$_usuario->dni = $usuario['dni'];
		$_usuario->rol = $usuario['rol'];
		$_usuario->save();
		return static::setFormato($_usuario);
	}
}
