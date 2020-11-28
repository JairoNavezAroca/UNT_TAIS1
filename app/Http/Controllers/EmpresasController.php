<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Log;
use App\Empresa;
use App\_Mensaje;

class EmpresasController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
		//dd($_SERVER);
	}
	protected function vista(Request $request){
		Log::log([
			'descripcion' => 'Vista: Empresas'
		]);
		return view("empresas");
	}
	protected function listar(Request $request){
		Log::log([
			'descripcion' => 'Listar Empresas',
			'tabla' => 'Empresa'
		]);
		$empresa = Empresa::listar();
		return _Mensaje::enviar('', $empresa);
	}

	private function moverfotosubida($ruta){
		$rutaFotoTemporal = 'imgtemp/';
		$rutaFoto = 'img/';
		rename($rutaFotoTemporal.$ruta, $rutaFoto.$ruta);
	}
	protected function subirfoto(Request $request){
		$rutaFotoTemporal = 'imgtemp/';
		$rutaFoto = 'img/';
		if(count($_FILES) == 0)
			return;
		$nombreArchivo = $_FILES["file"]['name'];
		$extension = explode(".", $nombreArchivo);
		$extension = $extension[count($extension) - 1];
		$nombreArchivoFinal =
			date("y"). //años
			date("m"). //mes
			date("d"). //dia
			date("H"). //hora 24h
			date("i"). //minutos
			date("s"). //segundos
			date("_").
			substr(microtime(),2,4).
			'.'.$extension;
		$rutaArchivoSubido = $rutaFotoTemporal.$nombreArchivoFinal;
		move_uploaded_file($_FILES["file"]['tmp_name'], $rutaArchivoSubido);
		Log::log([
			'descripcion' => 'Subió imagen: '.$rutaArchivoSubido
		]);
		return $nombreArchivoFinal;
	}
	protected function setupd(Request $request){
		$empresa = $request->get('empresa');
		$idEmpresa = $empresa['idEmpresa'] ?? null;
		$fotosubida = $empresa['foto_'] ?? [];
		//$fotosubida = ($fotosubida == '')?[]:'';
		//$fotosubida = $empresa['foto_'] ?? null;
		if (count($fotosubida) == 1){
			$sesubiofoto = true;
			$empresa['foto'] = $fotosubida[0]['upload']['data'];
		}
		else if ($empresa['foto'] == null)
			$empresa['foto'] = '';
		else
			$empresa['foto'] ?? '';
		//dd($empresa['foto'] ?? '');
		DB::beginTransaction();
		try {
			if ($idEmpresa == null)
				$res = Empresa::registrar($empresa);
			else
				$res = Empresa::modificar($idEmpresa, $empresa);
			if (count($fotosubida) == 1)
				$this->moverfotosubida($empresa['foto']);
			DB::commit();
		} catch (\Exception $e) {
			$res = null;
			//dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => (($idEmpresa == null)?'Registrar':'Modificar').' Empresa: '.($mensaje ??'Correcto'),
			'tabla' => 'Empresa',
			'idTabla' => ($idEmpresa ?? ($res->idEmpresa ?? null))
		]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function eliminar(Request $request){
		//validacion, eliminar empresas si y solo si no tengan unidades de negocio
		$idEmpresa = $request->get('idEmpresa');
		DB::beginTransaction();
		try {
			$res = Empresa::eliminar($idEmpresa);
			DB::commit();
		} catch (\Exception $e) {
			$res = null;
			//dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => 'Eliminar Empresa: '.($mensaje ??'Correcto'),
			'tabla' => 'Empresa',
			'idTabla' => ($idEmpresa ?? ($res->idEmpresa ?? null))
		]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}

}
