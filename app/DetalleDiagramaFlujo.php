<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DetalleDiagramaFlujo extends Model
{
	protected $table = 'detallediagramaflujo';
	protected $primaryKey = 'idDetalleDiagramaFlujo';
	protected $fillable = [
		'idDetalleDiagramaFlujo',
		'idDiagramaFlujo',
		'nombreArchivo',
		'descripcion',
		'idProceso'
	];
	protected $casts = [
		'fechareg' => 'datetime:d/m/Y h:i a',
	];

	public static function listar($idDiagramaFlujo, $idProceso){
		return DetalleDiagramaFlujo::where([
			'idDiagramaFlujo' => $idDiagramaFlujo,
			'idProceso' => $idProceso
			])
			->select(
				'idDetalleDiagramaFlujo',
				'idDiagramaFlujo',
				'nombreArchivo',
				'descripcion',
				'idProceso',
				'created_at as fechareg'
			)
			->orderBy('idDetalleDiagramaFlujo', 'desc')
			->get();
	}

	public static function registrar($datos){
		$detallediagramaflujo = DetalleDiagramaFlujo::create([
			'idDiagramaFlujo' => $datos['idDiagramaFlujo'],
			'nombreArchivo' => $datos['nombreArchivo'],
			'descripcion' => $datos['descripcion'],
			'idProceso' => $datos['idProceso']
		]);
		$varrr = new \DateTime($detallediagramaflujo->created_at);
		$detallediagramaflujo->fechareg = $varrr->format('d/m/Y h:i a');
		return $detallediagramaflujo;
	}

}
