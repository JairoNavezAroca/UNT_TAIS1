<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Foundation\Auth\AuthenticatesUsers;
//use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Usuario;
use App\Log;

class LoginController extends Controller
{
	//use AuthenticatesUsers;

	protected $redirectTo = '/';

	public function __construct()
	{
		//gest redirecciona si esta autenticado
		$this->middleware('guest')->except(['logout', 'inicio']);
		//auth redirecciona si no está autenticado
		$this->middleware('auth')->only('inicio');
	}

	protected function inicio(Request $request){
		Log::log([
			'descripcion' => 'Vista: Inicio'
		]);
		return view("layout");
	}
	protected function login(Request $request){
		Log::log([
			'descripcion' => 'Vista: Login'
		]);
		return view("login");
	}

	protected function validar(Request $request){
		$contrasena = $request->get('contrasena');
		$usuario_txt = $request->get('usuario');
		$usuario = Usuario::obtenerUsuario($usuario_txt);
		if ($usuario == null)
			$resultado = "Usuario no encontrado";
		else{
			//dd(Auth::user());
			if (Hash::check($contrasena, $usuario->contrasena)){
				if ($usuario->estado == 'H'){
					$resultado = null;
				}
				else if ($usuario->estado == 'D'){
					$resultado = 'Usuario desactivado, comuniquese con el adminsitrador';
				}
				else {
					$resultado = 'Error inesperado, intente nuevamente más tarde';
				}
			}
			else
				$resultado = "Contraseña incorrecta";
		}
		if ($resultado == null){
			Auth::login($usuario, true);
			Log::log([
				'descripcion' => 'Inició sesión'
			]);
			return redirect('/');
		}
		Log::log([
			'idUsuario' => $usuario->idUsuario ?? null,
			'descripcion' => $usuario_txt.' - '.$resultado
		]);
		return redirect('login')->with(['mensaje' => $resultado]);
	}

	protected function logout(Request $request){
		//dd(Session::get('us'));
		//dd(Auth::user());
		//Session::put('us', $us);
		Log::log(['descripcion' => 'Cerró sesión']);
		Auth::logout();
		return redirect('/');
	}
}
