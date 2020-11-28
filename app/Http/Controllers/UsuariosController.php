<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Log;
use App\Usuario;
use App\_Mensaje;

class UsuariosController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
		//dd($_SERVER);
	}
	protected function vista(Request $request){
		Log::log([
			'descripcion' => 'Vista: Usuarios'
		]);
		return view("usuarios");
	}
	protected function listar(Request $request){
		Log::log([
			'descripcion' => 'Listar Usuarios',
			'tabla' => 'Usuario'
		]);
		$usuarios = Usuario::listar();
		return _Mensaje::enviar('', $usuarios);
	}
	protected function habilita(Request $request){
		$idUsuario = $request->get('idUsuario');
		return $this->cambiaEstado($idUsuario, 'Habilitar', function($idUsuario){
			return Usuario::habilita($idUsuario);
		});
	}
	protected function deshabilita(Request $request){
		$idUsuario = $request->get('idUsuario');
		return $this->cambiaEstado($idUsuario, 'Deshabilitar', function($idUsuario){
			return Usuario::deshabilita($idUsuario);
		});
	}
	protected function cambiaEstado($idUsuario, $descip, $funn){
		DB::beginTransaction();
		try {
			if(Auth::user()->idUsuario != $idUsuario)
				$res = $funn($idUsuario);
			else
				$mensaje = 'Usted no puede cambiar su estado a si mismo';
			DB::commit();
		} catch (\Exception $e) {
			//dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => $descip.' Usuario: '.($mensaje ?? 'Correcto'),
			'tabla' => 'Usuario',
			'idTabla' => $idUsuario
		]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function setupd(Request $request){
		$usuario = $request->get('usuario');
		$idUsuario = $usuario['idUsuario'] ?? null;
		DB::beginTransaction();
		try {
			//dd(Auth::user()->rol == $usuario['rol']);
			if ($idUsuario == null)
				$res = Usuario::registrar($usuario);
			else if ($idUsuario == Auth::user()->idUsuario && Auth::user()->rol != $usuario['rol'])
				$mensaje = 'Usted no puede cambiar su rol a si mismo';
			else
				$res = Usuario::modificar($idUsuario, $usuario);
			DB::commit();
		} catch (\Exception $e) {
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => (($idUsuario == null)?'Registrar':'Modificar').' Usuario: '.($mensaje ??'Correcto'),
			'tabla' => 'Usuario',
			'idTabla' => ($idUsuario ?? ($res->idUsuario ?? null))
		]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
}
