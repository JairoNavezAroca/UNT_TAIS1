<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
//use DB;
use Illuminate\Support\Facades\Hash;

class CreateUsersTable extends Migration
{
	/**
	* Run the migrations.
	*
	* @return void
	*/
	public function up()
	{
		//Empresa y Usuario
		Schema::create('Usuario', function (Blueprint $table) {
			$table->increments('idUsuario');
			$table->string('dni', 8)->unique();
			$table->string('nombres', 50);
			$table->string('apellidos', 50);
			$table->string('usuario', 50);
			$table->string('contrasena', 128);
			$table->string('rol', 1);
			$table->string('estado', 1);
			$table->string('remember_token', 100)->nullable();
			$table->timestamps();
		});
		Schema::create('Empresa', function (Blueprint $table) {
			$table->increments('idEmpresa');
			$table->string('nombre', 50);
			$table->string('ruc', 11);
			$table->string('telefono', 20);
			$table->string('direccion', 20);
			$table->string('foto', 20);
			$table->string('estado', 1);
			$table->integer('idUsuario')->unsigned();
			$table->timestamps();
			$table->foreign('idUsuario')->references('idUsuario')->on('Usuario');
		});
		Schema::create('UnidadNegocio', function (Blueprint $table) {
			$table->increments('idUnidadNegocio');
			$table->integer('idEmpresa')->unsigned();
			$table->string('nombre', 50);
			$table->string('descripcion', 100);
			$table->string('estado', 1);
			$table->timestamps();
			$table->foreign('idEmpresa')->references('idEmpresa')->on('Empresa');
		});
		//Cadena de Suministro
		Schema::create('CadenaSuministro', function (Blueprint $table) {
			$table->increments('idCadenaSuministro');
			$table->integer('idUnidadNegocio')->unsigned();
			$table->integer('idEmpresa')->unsigned();
			$table->string('detalle', 250)->nullable();
			$table->string('estado', 1);
			$table->integer('idUsuario')->unsigned();
			$table->timestamps();
			$table->foreign('idUnidadNegocio')->references('idUnidadNegocio')->on('UnidadNegocio');
			$table->foreign('idEmpresa')->references('idEmpresa')->on('Empresa');
			$table->foreign('idUsuario')->references('idUsuario')->on('Usuario');
		});
		Schema::create('VersionCadena', function (Blueprint $table) {
			$table->increments('idVersionCadena');
			$table->integer('idCadenaSuministro')->unsigned();
			$table->string('detalle', 100)->nullable();
			$table->string('estado', 1);
			$table->timestamps();
			$table->foreign('idCadenaSuministro')->references('idCadenaSuministro')->on('CadenaSuministro');
		});
		Schema::create('ProveedorCliente', function (Blueprint $table) {
			$table->increments('idProveedorCliente');
			$table->integer('idEmpresa')->unsigned();
			$table->integer('idVersionCadena')->unsigned();
			$table->string('tipo', 1);
			$table->string('nivel', 1);
			$table->string('estado', 1);
			$table->timestamps();
			$table->foreign('idEmpresa')->references('idEmpresa')->on('Empresa');
			$table->foreign('idVersionCadena')->references('idVersionCadena')->on('VersionCadena');
		});
		//Mapa de Procesos
		Schema::create('MapaProcesos', function(Blueprint $table){
			$table->increments('idMapaProcesos');
			$table->integer('idUnidadNegocio')->unsigned();
			$table->integer('idEmpresa')->unsigned();
			$table->string('descripcion', 250)->nullable();
			$table->char('tipo', 1);
			$table->boolean('priorizado');
			$table->integer('cantidadPriorizar')->unsigned();
			$table->char('estado', 1);
			$table->integer('idUsuario')->unsigned();
			$table->timestamps();
			$table->foreign('idUnidadNegocio')->references('idUnidadNegocio')->on('UnidadNegocio');
			$table->foreign('idEmpresa')->references('idEmpresa')->on('Empresa');
			$table->foreign('idUsuario')->references('idUsuario')->on('Usuario');
		});
		Schema::create('EntradaSalida', function(Blueprint $table){
			$table->increments('idEntradaSalida');
			$table->integer('idMapaProcesos')->unsigned();
			$table->string('nombre', 50);
			$table->char('estado', 1);
			$table->timestamps();
			$table->foreign('idMapaProcesos')->references('idMapaProcesos')->on('MapaProcesos');
		});
		Schema::create('Procesos', function(Blueprint $table){
			$table->increments('idProceso');
			$table->integer('idMapaProcesos')->unsigned();
			$table->string('nombre', 50);
			$table->char('tipo', 1);
			$table->char('estado', 1);
			$table->boolean('priorizado')->nullable();
			$table->timestamps();
			$table->integer('idProcesoAnterior')->unsigned()->nullable();
			$table->foreign('idMapaProcesos')->references('idMapaProcesos')->on('MapaProcesos');
		});
		Schema::create('RelacionProceso', function(Blueprint $table){
			$table->increments('idRelacionProceso');
			$table->integer('idProceso_desde')->unsigned();
			$table->integer('idProceso_hasta')->unsigned();
			$table->integer('idMapaProcesos')->unsigned();
			$table->char('estado', 1);
			$table->timestamps();
			$table->foreign('idProceso_desde')->references('idProceso')->on('Procesos');
			$table->foreign('idProceso_hasta')->references('idProceso')->on('Procesos');
			$table->foreign('idMapaProcesos')->references('idMapaProcesos')->on('MapaProcesos');
		});
		Schema::create('CriteriosPriorizacion', function(Blueprint $table){
			$table->increments('idCriteriosPriorizacion');
			$table->integer('idMapaProcesos')->unsigned();
			$table->string('nombre', 50);
			$table->decimal('peso', 8, 2);
			$table->string('justificacion', 250);
			$table->decimal('valmin', 8, 2);
			$table->decimal('valmax', 8, 2);
			$table->char('estado', 1);
			$table->timestamps();
			$table->foreign('idMapaProcesos')->references('idMapaProcesos')->on('MapaProcesos');
		});
		Schema::create('EvaluacionCriterios', function(Blueprint $table){
			$table->integer('idMapaProcesos')->unsigned();
			$table->integer('idProceso')->unsigned();
			$table->integer('idCriteriosPriorizacion')->unsigned();
			$table->decimal('puntaje', 8, 2);
			$table->timestamps();
			$table->foreign('idMapaProcesos')->references('idMapaProcesos')->on('MapaProcesos');
			$table->foreign('idProceso')->references('idProceso')->on('Procesos');
			$table->foreign('idCriteriosPriorizacion')->references('idCriteriosPriorizacion')->on('CriteriosPriorizacion');
		});
		Schema::create('Caracterizacion', function(Blueprint $table){
			$table->increments('idCaracterizacion');
			$table->char('tipo', 1);
			$table->char('estado', 1);
			$table->integer('idMapaProcesos')->unsigned();
			$table->integer('idProceso')->unsigned();
			$table->timestamps();
			$table->foreign('idMapaProcesos')->references('idMapaProcesos')->on('MapaProcesos');
			$table->foreign('idProceso')->references('idProceso')->on('Procesos');
		});
		Schema::create('DetalleCaracterizacion', function(Blueprint $table){
			$table->increments('idDetalleCaracterizacion');
			$table->integer('idCaracterizacion')->unsigned();
			$table->string('nombreArchivo', 100);
			$table->string('descripcion', 250);
			$table->integer('idProceso')->unsigned();
			$table->timestamps();
			$table->foreign('idCaracterizacion')->references('idCaracterizacion')->on('Caracterizacion');
			$table->foreign('idProceso')->references('idProceso')->on('Procesos');
		});
		Schema::create('DiagramaFlujo', function(Blueprint $table){
			$table->increments('idDiagramaFlujo');
			$table->char('tipo', 1);
			$table->char('situacion', 1);
			$table->char('estado', 1);
			$table->integer('idMapaProcesos')->unsigned();
			$table->integer('idProceso')->unsigned();
			$table->timestamps();
			$table->foreign('idMapaProcesos')->references('idMapaProcesos')->on('MapaProcesos');
			$table->foreign('idProceso')->references('idProceso')->on('Procesos');
		});
		Schema::create('DetalleDiagramaFlujo', function(Blueprint $table){
			$table->increments('idDetalleDiagramaFlujo');
			$table->integer('idDiagramaFlujo')->unsigned();
			$table->string('nombreArchivo', 100);
			$table->string('descripcion', 250);
			$table->integer('idProceso')->unsigned();
			$table->timestamps();
			$table->foreign('idDiagramaFlujo')->references('idDiagramaFlujo')->on('DiagramaFlujo');
			$table->foreign('idProceso')->references('idProceso')->on('Procesos');
		});
		Schema::create('SeguimientoActividades', function(Blueprint $table){
			$table->increments('idSeguimientoActividades');
			$table->char('tipo', 1);
			$table->char('situacion', 1);
			$table->char('estado', 1);
			$table->integer('idMapaProcesos')->unsigned();
			$table->integer('idProceso')->unsigned();
			$table->timestamps();
			$table->foreign('idMapaProcesos')->references('idMapaProcesos')->on('MapaProcesos');
			$table->foreign('idProceso')->references('idProceso')->on('Procesos');
		});
		Schema::create('DetalleSeguimientoActividades', function(Blueprint $table){
			$table->increments('idDetalleSeguimientoActividades');
			$table->integer('idSeguimientoActividades')->unsigned();
			$table->string('actividad', 50);
			$table->string('rol', 50);
			$table->string('flujo', 50);
			$table->decimal('tiempo', 8, 2);
			$table->integer('orden')->nullable();
			$table->char('estado', 1);
			$table->integer('idProceso')->unsigned();
			$table->timestamps();
			$table->foreign('idSeguimientoActividades')->references('idSeguimientoActividades')->on('SeguimientoActividades');
			$table->foreign('idProceso')->references('idProceso')->on('Procesos');
		});
		//Mapa de Procesos v2
		Schema::create('IndicadorDesempenio', function(Blueprint $table){
			$table->increments('idIndicadorDesempenio');
			$table->integer('idMapaProcesos')->unsigned();
			$table->integer('idProceso')->unsigned();
			$table->char('tipo', 1);
			$table->char('estado', 1);
			$table->timestamps();
			$table->foreign('idMapaProcesos')->references('idMapaProcesos')->on('MapaProcesos');
			$table->foreign('idProceso')->references('idProceso')->on('Procesos');
		});
		Schema::create('DetalleIndicadorDesempenio', function(Blueprint $table){
			$table->increments('idDetalleIndicadorDesempenio');
			$table->integer('idIndicadorDesempenio')->unsigned();
			$table->integer('idProceso')->unsigned();
			$table->string('nombre', 50);
			$table->string('puesto', 50);
			$table->string('medir', 150);
			$table->string('mecanismo', 150);
			$table->string('tolerancia', 150);
			$table->string('quehacer', 150);
			$table->text('formula');
			$table->char('estado', 1);
			$table->timestamps();
			$table->foreign('idIndicadorDesempenio')->references('idIndicadorDesempenio')->on('IndicadorDesempenio');
			$table->foreign('idProceso')->references('idProceso')->on('Procesos');
		});
		Schema::create('MapaEstrategico', function(Blueprint $table){
			$table->increments('idMapaEstrategico');
			$table->integer('idMapaProcesos')->unsigned();
			$table->integer('idProceso')->unsigned();
			$table->char('tipo', 1);
			$table->char('orden_aspectos', 1)->nullable();
			$table->char('estado', 1);
			$table->timestamps();
			$table->foreign('idMapaProcesos')->references('idMapaProcesos')->on('MapaProcesos');
			$table->foreign('idProceso')->references('idProceso')->on('Procesos');
		});
		Schema::create('DetalleMapaEstrategico', function(Blueprint $table){
			$table->increments('idDetalleMapaEstrategico');
			$table->integer('idMapaEstrategico')->unsigned();
			$table->integer('idProceso')->unsigned();
			$table->string('nombre', 50);
			$table->string('aspecto', 25);
			$table->char('estado', 1);
			$table->integer('idAnterior')->unsigned()->nullable();
			$table->timestamps();
			$table->foreign('idMapaEstrategico')->references('idMapaEstrategico')->on('MapaEstrategico');
			$table->foreign('idProceso')->references('idProceso')->on('Procesos');
		});
		Schema::create('TableroControl', function(Blueprint $table){
			$table->increments('idTableroControl');
			$table->integer('idMapaProcesos')->unsigned();
			$table->integer('idProceso')->unsigned();
			$table->char('tipo', 1);
			$table->char('estado', 1);
			$table->timestamps();
			$table->foreign('idMapaProcesos')->references('idMapaProcesos')->on('MapaProcesos');
			$table->foreign('idProceso')->references('idProceso')->on('Procesos');
		});
		Schema::create('DetalleTableroControl', function(Blueprint $table){
			$table->increments('idDetalleTableroControl');
			$table->integer('idTableroControl')->unsigned();
			$table->integer('idProceso')->unsigned();
			$table->integer('idDetalleIndicadorDesempenio')->unsigned();
			$table->integer('idDetalleMapaEstrategico')->unsigned();
			$table->string('objetivo', 150);
			$table->decimal('lineabase', 8, 2);
			$table->decimal('meta', 8, 2);
			$table->string('responsable', 50);
			$table->string('iniciativas', 200);
			$table->char('luz_roja_signo', 1);
			$table->decimal('luz_roja_valor', 8, 2);
			$table->char('luz_verde_signo', 1);
			$table->decimal('luz_verde_valor', 8, 2);
			$table->string('frecuenciamedicion', 20);
			$table->char('estado', 1);
			$table->timestamps();
			$table->foreign('idTableroControl')->references('idTableroControl')->on('TableroControl');
			$table->foreign('idProceso')->references('idProceso')->on('Procesos');
			$table->foreign('idDetalleIndicadorDesempenio')->references('idDetalleIndicadorDesempenio')->on('DetalleIndicadorDesempenio');
			$table->foreign('idDetalleMapaEstrategico')->references('idDetalleMapaEstrategico')->on('DetalleMapaEstrategico');
		});
		Schema::create('DataFuente', function(Blueprint $table){
			$table->increments('idDataFuente');
			$table->integer('idDetalleTableroControl')->unsigned();
			$table->date('fecha');
			$table->string('descripcion', 50);
			$table->text('variables_resultado');
			$table->boolean('considerar');
			$table->char('estado', 1);
			$table->timestamps();
			$table->foreign('idDetalleTableroControl')->references('idDetalleTableroControl')->on('DetalleTableroControl');
		});


		//Log
		Schema::create('Log', function (Blueprint $table) {
			$table->increments('idLog');
			$table->integer('idUsuario')->unsigned()->nullable();
			$table->string('descripcion', 100)->nullable();
			$table->string('tabla', 20)->nullable();
			$table->string('uri', 100)->nullable();
			$table->string('ip', 20)->nullable();
			$table->string('puerto', 20)->nullable();
			$table->integer('idTabla')->unsigned()->nullable();
			$table->timestamps();
		});

		DB::insert('insert into usuario (
				dni, nombres, apellidos, usuario,
				contrasena, rol, estado
			) values (?, ?, ?, ?, ?, ?, ?)',
		[
			'72409948', 'Jairo', 'Navez Aroca', 'jnavez',
			Hash::make('123'), 'A', 'H'
		]);

		DB::insert('insert into usuario (
				dni, nombres, apellidos, usuario,
				contrasena, rol, estado
			) values (?, ?, ?, ?, ?, ?, ?)',
		[
			'72409949', 'Raul', 'Navez Aroca', 'rnavez',
			Hash::make('123'), 'T', 'H'
		]);
	}

	/**
	* Reverse the migrations.
	*
	* @return void
	*/
	public function down()
	{
		//Log
		Schema::dropIfExists('Log');
		//Mapa de Procesos v2
		Schema::dropIfExists('DataFuente');
		Schema::dropIfExists('DetalleTableroControl');
		Schema::dropIfExists('TableroControl');
		Schema::dropIfExists('DetalleMapaEstrategico');
		Schema::dropIfExists('MapaEstrategico');
		Schema::dropIfExists('DetalleIndicadorDesempenio');
		Schema::dropIfExists('IndicadorDesempenio');
		//Mapa de Procesos
		Schema::dropIfExists('DetalleSeguimientoActividades');
		Schema::dropIfExists('SeguimientoActividades');
		Schema::dropIfExists('DetalleDiagramaFlujo');
		Schema::dropIfExists('DiagramaFlujo');
		Schema::dropIfExists('DetalleCaracterizacion');
		Schema::dropIfExists('Caracterizacion');
		Schema::dropIfExists('EvaluacionCriterios');
		Schema::dropIfExists('CriteriosPriorizacion');
		Schema::dropIfExists('MatrizPriorizacion');
		Schema::dropIfExists('RelacionProceso');
		Schema::dropIfExists('Procesos');
		Schema::dropIfExists('EntradaSalida');
		Schema::dropIfExists('MapaProcesos');
		//Cadena de Suministro
		Schema::dropIfExists('Varios');
		Schema::dropIfExists('ProveedorCliente');
		Schema::dropIfExists('VersionCadena');
		Schema::dropIfExists('CadenaSuministro');
		//Empresa y Usuario
		Schema::dropIfExists('UnidadNegocio');
		Schema::dropIfExists('Empresa');
		Schema::dropIfExists('Usuario');
	}
}
