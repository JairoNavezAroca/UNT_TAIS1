<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Log;
use App\Empresa;
use App\UnidadNegocio;
use App\CadenaSuministro;
use App\VersionCadena;
use App\_Mensaje;
use App\ProveedorCliente;

class CadenaSuministrosController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}
	protected function vista(Request $request){
		Log::log([
			'descripcion' => 'Vista: Cadena Suministros'
		]);
		return view("cadenas");
	}
	protected function listaempresas(Request $request){
		Log::log([
			'descripcion' => 'Listar Empresas',
			'tabla' => 'Empresas'
		]);
		$empresa = Empresa::listar();
		return _Mensaje::enviar('', $empresa);
	}
	protected function listaunidades(Request $request){
		$idEmpresa = $request->get('idEmpresa');
		Log::log([
			'descripcion' => 'Listar Unidades de Negocio',
			'tabla' => 'UnidadNegocio',
			'idTabla' => $idEmpresa
		]);
		$unidadnegocio = UnidadNegocio::listar($idEmpresa);
		return _Mensaje::enviar('', $unidadnegocio);
	}
	protected function setupd(Request $request){
		$cadenasuministro = $request->get('cadena');
		$idCadenaSuministro = $cadenasuministro['idCadenaSuministro'] ?? null;
		//dd($cadenasuministro);
		DB::beginTransaction();
		try {
			if ($idCadenaSuministro == null){
				$res = CadenaSuministro::registrar($cadenasuministro);
				$res2 = VersionCadena::registrarNueva($res->idCadenaSuministro);
			}
			else
				$res = CadenaSuministro::modificar($idCadenaSuministro, $cadenasuministro);
			DB::commit();
		} catch (\Exception $e) {
			$res = null;
			//dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => (($idCadenaSuministro == null)?'Registrar':'Modificar').' Cadena de Suministro: '.($mensaje ??'Correcto'),
			'tabla' => 'CadenaSuministro',
			'idTabla' => ($idCadenaSuministro ?? ($res->idCadenaSuministro ?? null))
		]);
		if ($idCadenaSuministro == null){
			Log::log([
				'descripcion' => 'Registrar Version de Cadena de Suministro: '.($mensaje ??'Correcto'),
				'tabla' => 'VersionCadena',
				'idTabla' => ($res2->idVersionCadena ?? null)
			]);
		}
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
	}
	protected function listar(Request $request){
		Log::log([
			'descripcion' => 'Listar Cadenas de Suministro',
			'tabla' => 'CadenaSuministro'
		]);
		$cadenasuministro = CadenaSuministro::listar();
		//dd($cadenasuministro);
		return _Mensaje::enviar('', $cadenasuministro);
	}


	protected function cargar(Request $request, $idCadenaSuministro){
		//return $idCadenaSuministro;
		$cadenasuministro = CadenaSuministro::obtener($idCadenaSuministro);
		if ($cadenasuministro === null){
			$mensaje = 'Error, datos no encontrados';
			$msj_log = ' (id: '.$idCadenaSuministro.', no encontrado)';
		}
		Log::log([
			'descripcion' => 'Vista: Cadena Suministros - Ver'.($msj_log ?? ''),
			'tabla' => 'CadenaSuministro',
			'idTabla' => (is_numeric($idCadenaSuministro) === true)?$idCadenaSuministro:null
		]);
		if ($cadenasuministro === null){
			return redirect("cadenas")->with('mensaje', $mensaje);
		}
		return view("cadenasver", ['cadenasuministro' => $cadenasuministro]);
	}
	protected function listaempresas2(Request $request){
		return $this->listaempresas($request);
	}


	protected function agregarclientes(Request $request){
		return static::agregarProveedorCliente($request, 'Cliente');
	}
	protected function agregarproveedores(Request $request){
		return static::agregarProveedorCliente($request, 'Proveedor');
	}
	protected function agregarProveedorCliente(Request $request, $tipo){
		$proveedorcliente = $request->get('proveedorcliente');
		$datoscadena = $request->get('datoscadena');
		DB::beginTransaction();
		try {
			$idCadenaSuministro = $datoscadena['idCadenaSuministro'];
			$idVersionAnterior = VersionCadena::getUltima($idCadenaSuministro);
			VersionCadena::desactivarUltima($idCadenaSuministro);
			$str = 'Se agregó el '.$tipo.': '.Empresa::getNombre($proveedorcliente['idEmpresa']);
			$versioncadena = VersionCadena::registrarNueva($idCadenaSuministro, $str);
			ProveedorCliente::actualizarDatos($idVersionAnterior, $versioncadena->idVersionCadena);
			if ($tipo == 'Proveedor')
				$res = ProveedorCliente::registrarProveedor([
					'idEmpresaAnterior' => $proveedorcliente['idEmpresaAnterior'],
					'idEmpresa' => $proveedorcliente['idEmpresa'],
					'nivel' => $proveedorcliente['nivel'],
					'idVersionCadena' => $versioncadena->idVersionCadena
				]);
			else if ($tipo == 'Cliente')
				$res = ProveedorCliente::registrarCliente([
					'idEmpresaAnterior' => $proveedorcliente['idEmpresaAnterior'],
					'idEmpresa' => $proveedorcliente['idEmpresa'],
					'nivel' => $proveedorcliente['nivel'],
					'idVersionCadena' => $versioncadena->idVersionCadena
				]);
			else
				throw new \Exception();
			DB::commit();
		} catch (\Exception $e) {
			//dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => "Registrar $tipo: ".($mensaje ??'Correcto'),
			'tabla' => 'ProveedorCliente',
			'idTabla' => ($res->idProveedorCliente ?? null)
			]);
		return _Mensaje::enviar($mensaje ?? '', $res ?? null);
		//dd($request);
	}

	protected function listarproveedores(Request $request){
		return $this->listarProveedorCliente($request, 'Proveedores');
	}
	protected function listarclientes(Request $request){
		return $this->listarProveedorCliente($request, 'Clientes');
	}

	protected function listarProveedorCliente(Request $request, $tipo){
		$idCadenaSuministro = $request->get('idCadenaSuministro');
		$cadenasuministro = CadenaSuministro::obtener($idCadenaSuministro);
		$idVersionCadena = VersionCadena::getUltima($idCadenaSuministro);
		//dd($idVersionCadena);
		$res = null;
		if ($tipo == 'Proveedores')
			$res = ProveedorCliente::getProveedores($idVersionCadena);
		else if ($tipo == 'Clientes')
			$res = ProveedorCliente::getClientes($idVersionCadena);
		Log::log([
			'descripcion' => 'Listar '.$tipo,
			'tabla' => 'ProveedoresCliente',
			'idTabla' => $idVersionCadena
		]);
		return _Mensaje::enviar('', $res);
	}

	protected function eliminar(Request $request){
		$datoscadena = $request->get('datoscadena');
		$item = $request->get('item');
		$idProveedorCliente = $item['idProveedorCliente'];
		$idEmpresa = $item['idEmpresa'];
		$tipo = $item['tipo'];
		$nivel = $item['nivel'];
		$idEmpresaAnterior = $item['idEmpresaAnterior'];
		$texto = ($item['tipo'] == 'C')?'Cliente':'Proveedor';
		DB::beginTransaction();
		try {
			$idCadenaSuministro = $datoscadena['idCadenaSuministro'];
			$idVersionAnterior = VersionCadena::getUltima($idCadenaSuministro);
			VersionCadena::desactivarUltima($idCadenaSuministro);
			$str = 'Se eliminó el '.$texto.': '.Empresa::getNombre($idEmpresa);
			$versioncadena = VersionCadena::registrarNueva($idCadenaSuministro, $str);
			ProveedorCliente::actualizarDatos($idVersionAnterior, $versioncadena->idVersionCadena);
			ProveedorCliente::eliminar([
				'idEmpresa' => $idEmpresa,
				'tipo' => $tipo,
				'nivel' => $nivel,
				'idEmpresaAnterior' => $idEmpresaAnterior,
				'idVersionCadena' => $versioncadena->idVersionCadena
			]);
			DB::commit();
		} catch (\Exception $e) {
			dd($e);
			$mensaje = 'Hubo un error, intente nuevamente más tarde';
			DB::rollback();
		}
		Log::log([
			'descripcion' => 'Eliminar  '.$texto,
			'tabla' => 'ProveedoresCliente',
			'idTabla' => $idProveedorCliente
		]);
		return _Mensaje::enviar($mensaje ?? '', null);
	}

	protected function versiones(Request $request){
		$datoscadena = $request->get('datoscadena');
		$idCadenaSuministro = $datoscadena['idCadenaSuministro'];
		Log::log([
			'descripcion' => 'Listar Versiones',
			'idTabla' => $idCadenaSuministro,
			'tabla' => 'VersionCadena'
		]);
		$versiones = VersionCadena::listarVersiones($idCadenaSuministro);
		return _Mensaje::enviar('', $versiones);
	}



}
