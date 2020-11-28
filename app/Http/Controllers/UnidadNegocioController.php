<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Log;
use App\_Mensaje;
use App\UnidadNegocio;

class UnidadNegocioController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
		//dd($_SERVER);
	}
	protected function listar(Request $request){
		$idEmpresa = $request->get('idEmpresa');
		Log::log([
			'descripcion' => 'Listar Unidades de Negocio',
			'tabla' => 'UnidadNegocio',
			'idTabla' => $idEmpresa,
		]);
		$unidadnegocio = UnidadNegocio::listar($idEmpresa);
		return _Mensaje::enviar('', $unidadnegocio);
	}
	protected function setupdel(Request $request){
		//validacion, eliminar unidad de negocio si y solo si no tengan cadenas de suministros
		$unidadnegocio = $request->get('unidadnegocio');
		$idEmpresa = $request->get('idEmpresa');
		$idUnidadNegocio = $unidadnegocio['idUnidadNegocio'] ?? null;
		$unidadnegocio['eliminar'] = $unidadnegocio['eliminar'] ?? true; //true = no elimina
		//dd($unidadnegocio['eliminar'] !== true);
		DB::beginTransaction();
		try {
			if ($idUnidadNegocio == null)
				$res = UnidadNegocio::registrar($idEmpresa, $unidadnegocio);
			else if ($unidadnegocio['eliminar'] !== true)
				$res = UnidadNegocio::eliminar($idUnidadNegocio);
			else
				$res = UnidadNegocio::modificar($idUnidadNegocio, $unidadnegocio);
			DB::commit();
		} catch (\Exception $e) {
			$res = null;
			//dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => (($idUnidadNegocio == null)?'Registrar':(($unidadnegocio['eliminar'] !== true)?'Eliminar':'Modificar')).' Unidad de Negocio: '.($mensaje ??'Correcto'),
			'tabla' => 'UnidadNegocio',
			'idTabla' => ($idUnidadNegocio ?? ($res->idUnidadNegocio ?? null))
		]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}

}
