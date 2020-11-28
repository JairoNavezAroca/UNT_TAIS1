<?php
namespace App;

class _Mensaje
{
	public $mensaje;
	public $datos;
	public function __construct($mensaje, $datos = null){
		$this->mensaje = $mensaje;
		$this->datos = $datos;
	}
	public static function enviar($mensaje, $datos = null){
		$mensaje = new _Mensaje($mensaje, $datos);
		return json_encode($mensaje);
	}
}
