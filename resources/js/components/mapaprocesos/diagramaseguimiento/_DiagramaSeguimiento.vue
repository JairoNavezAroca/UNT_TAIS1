<template>
	<div>
		<br>
		<b-form-group label="Tipo" label-cols-sm="6" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
			<cool-select @select="seleccionoTipo" v-model="obj.tipoSeleccionado" :items="listaTipos" item-text="text" item-value="value"></cool-select>
		</b-form-group>
		<b-form-group label="Seleccione su proceso priorizado" label-cols-sm="6" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
			<cool-select @select="seleccionoProceso" v-model="obj.procesoSeleccionado" :items="listaProcesos" item-text="nombre" item-value="idProceso"></cool-select>
		</b-form-group>
		<b-form-group label="Sub-Proceso" label-cols-sm="6" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
			<cool-select @select="seleccionoSubProceso(true)" v-model="obj.subProcesoSeleccionado" :items="listaSubprocesos" item-text="nombre" item-value="idProceso"></cool-select>
		</b-form-group>
		<br>
		<table class="table table-sm table-hover">
			<thead>
				<tr>
					<th>Nº</th>
					<th>Actividad</th>
					<th>Rol</th>
					<th>Flujo</th>
					<th>Ícono</th>
					<th>Tiempo(Min)</th>
					<th>%</th>
					<th>Opciones</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(item, index) in listadoActividades">
					<td>{{(index+1)}}</td>
					<td>
						<b-form-input v-model="item.actividad" size="sm"></b-form-input>
					</td>
					<td>
						<b-form-input @input="actualizarDatos" v-model="item.rol" size="sm"></b-form-input>
					</td>
					<td>
						<cool-select @select="seleccionoFlujo(item)" v-model="item.flujo" :items="listaFlujos" size="sm" item-text="text" item-value="value"></cool-select>
					</td>
					<td>
						<b-img :src="'data:image/gif;base64,'+item.icono"></b-img>
					</td>
					<td>
						<b-form-input @change="actualizarDatos" v-model="item.tiempo" size="sm" type="number" min="0"></b-form-input>
					</td>
					<td>
						<b-form-group :label="item.porcentaje" label-size="sm"></b-form-group>
					</td>
					<td>
						<b-button @click="eliminarActividad(index)" variant="outline-danger" size="sm">Eliminar</b-button>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="text-center">
			<div :hidden="datosmapa.estado2=='H'">
				<b-button @click="agregarActividad" variant="primary">Agregar Actividad</b-button>
				&nbsp&nbsp&nbsp&nbsp
				<b-button @click="botonGuardar" variant="success">Guardar Cambios</b-button>
				<br>
				<br>
			</div>
			<div :hidden="datosmapa.estado2!='H'">
				<span class="text-danger">No es posbile agregar porque el mapa actual se encuentra en el histórico</span>
			</div>
			<b-button @click="botonpdf">Descargar en PDF</b-button>
		</div>
		<br>
		<b-row>
			<b-col md="6" sm="12">
				<b-form-group label="Resumen según denominación"></b-form-group>
				<table class="table table-sm table-hover">
					<thead>
						<tr>
							<th>Actividad</th>
							<th>Tiempo</th>
							<th>%</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(item, index) in listadoResumenDenominacion">
							<td>{{item.text}}</td>
							<td>{{item.tiempo}}</td>
							<td>{{item.porcentaje}}</td>
						</tr>
					</tbody>
				</table>
			</b-col>
			<b-col md="6" sm="12">
				<b-form-group label="Resumen según rol"></b-form-group>
				<table class="table table-sm table-hover">
					<thead>
						<tr>
							<th>Rol</th>
							<th>Tiempo</th>
							<th>%</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(item, index) in listadoResumenRol">
							<td>{{item.text}}</td>
							<td>{{item.tiempo}}</td>
							<td>{{item.porcentaje}}</td>
						</tr>
					</tbody>
				</table>
			</b-col>
		</b-row>
	</div>
</template>

<script>
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable' //https://evilnapsis.com/2017/10/24/javascript-agregar-una-tabla-en-un-pdf-con-jspdf-y-el-plugin-autotable/ //https://github.com/simonbengtsson/jsPDF-AutoTable
import { CoolSelect } from 'vue-cool-select'
export default {
	components: {
		CoolSelect
	},
	props:{
		datosmapa: Object,
		procpriorizados: Array,
	},
	data() {
		return {
			obj: {
				procesoSeleccionado: '',
				subProcesoSeleccionado: '',
				tipoSeleccionado: '',
			},
			objdiagramaseguimientoactividades: null,
			listaProcesos: [],
			listaSubprocesos: [],
			listaTipos: [
				{
					text: 'Situación Actual',
					value: 'A',
				},
				{
					text: 'Propuesta',
					value: 'P',
				},
			],
			//https://www.base64-image.de/
			listaFlujos: [
				{
					text: 'Operacion',
					value: '1',
					img: 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAfCAIAAAAJNFjbAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAHISURBVEhL1ZYhkIMwEEVPnkRiKysrsScrK7FIZCUWiUTWViKRtUgkshZZWUnfNdtrCIFSMjdz90Rnfgj5yWZ36Uf3y/wBg+v1WhRFGIZfGlEUHY9HHsmkcaYMLpfLfr/3PG+32x0Oh5MGEkvv02MC0+QFG6MG1alar9dZlk1sk0dMWK1WHFGGBtgN8jwnDm3bip6EE3BEnET3sRhw/CiM5sRXh1uxepgGxJe9v7u6YrvdlmUp4kHPgHUJ6MzIDOFFrs24854BKZGmqYhFECUWEXHnacD2ychlwflhuMjTgPARfREOUB/UoAjdII5jslOEAyzCUiJ0A8N5MeRhEAQidANGq6oS4UDTNOSSCN2AC8BchAN1XdsNKPeJljIfkoWKE6EbJEniWASKPBu5ZNUkRDhghPppAL7nL+4TCl73fV/EnZ6BkcILoE8YPbVnAJvNhjQQ8Sbn85leaTQb04BJeEx/Ba2wLpXU1I3oB6YBqFJ8y4PJ1o8BWAyAkuYcM+uOkLKhU2mfbDcAYkXCsa8JGx5RntQt7UGGBowaKMriu4eTebRCylAVI798gRnk0cvif2GgILv5J8C6ijRJkTMrZpaBC//doOtupInETU+zwNgAAAAASUVORK5CYII=',
				},
				{
					text: 'Transporte',
					value: '2',
					img: 'iVBORw0KGgoAAAANSUhEUgAAAFIAAAAdCAIAAAC2QKx1AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAJHSURBVFhH5ZghmClRFMfnvWSaSBSJGpFGFEUabTTqNNpqRBqNNqKJmmlEGo3m/dY9n5lvP/bN2v3e5877BXvumbXfnPu/59xz9tflcjH+P37LT304n89ifQP9wq5UKsVi0fM8WT+FlmrncrlqtWrb9tPK6xf26XQqFArL5RIjm826risPvgQlTS+I2XEcZa/Xa5Sv1WqHw0F5QqKl2mIZRjqdRnYiz+fz4/FYvCHwL7D9fs/mKds0zJMhf/3V7Far1e120VwtFby8ZVnH43E4HCYSCfE+xg+bIrFareLxuGma7OjLfvKqo9Hobmzz+bxZazaaDattiesR16P+DhkyGAxkoS1sDbJz7Dn84rqHn9t8QSydicVipECv1yN40uHRDeeHrc5PNFBqJ5PJTCazmC/EGyBqagdBcIK3ezZli5on3ivRVPsGlY9LvlwuU/mDN1yU1b6RSqVI8u12K+vIq81NTmHjtM9ms3a7Ld5oqz2dTuneKGxkOP2ceK9EU20KGPlMV7NYLJBavAEiqDYDKSI3Go3JZPKoUfWb03q9zhzDKEf8KP/Kn4REE61eOwjNNY+IudPp3P2FG37YfIftwfjOnPAP7LfhG6f3wyhCoSZUjnS/36dXEe8nXFtUnQjO2wpn5lCx6EllHQL9wkbMW9i73Y4Jio3YbDbKExK/pOkCua0MRmsCVrtAQ6KcIdEvbKqa53nULdd1uZBRWx58CVFdH0ql0vv/kpzPxum/4ldyXaAV4XJirpb1U+gX9o+gX27/AIbxB79TVALL+IHhAAAAAElFTkSuQmCC',
				},
				{
					text: 'Inspeccion',
					value: '3',
					img: 'iVBORw0KGgoAAAANSUhEUgAAACEAAAAfCAIAAADm9jPlAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAABcSURBVEhL7ZaxDcBACAMhE7AK+w9HiEBf+yVI5evc+GQqNCJkm3Qk7t55juys8t6hOj/odD6VV6EDhw4cOnDowKEDhw4cOnDouOB7FiPMrPMc2Vnlf/zU+7cSeQF6fSidB6RKAgAAAABJRU5ErkJggg==',
				},
				{
					text: 'Demora',
					value: '4',
					img: 'iVBORw0KGgoAAAANSUhEUgAAACsAAAAdCAIAAAC8HAInAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAHUSURBVEhL7ZcvlIJAEMa5S9q0aTRigyZRo02aRCIRm1WbNG0atWm0QYTmNW1ajTZo3nfOHOrz/jzvwW65X9DZ4fF2+Hb3Y3g5n8+KVF75Xx6sQRRFzWYzSRLKPkWpVNJ1neJGo2HohqqptVqNMr/CFcxmsyAI5vM5ZZ/idDptNhuKoyAK38LdbqfEim3bHaujqipd+hZUAKbTKW6gOBP2+32/38f0kDb0Q85+xbWCbrdLcbb4vo+lQR3b7ZZT91x3YrFY5ChTPjQIQ+gBjVerFWdvuFYQxzFHOYA61us19tlwOOTUJ7lrkIIjs1wuD4fDZDLh1AVBGqSMx2OIgXPHY5EaEIVCYTAY9Ho9HovXAGiaVq1W010pWgPCcRx4IMUSNABw8dRG5WhQqVSOxyPFcjTA9CiCYjkawBXSl6ccDWCOlmlRLEEDLAFMyXZsGkrQwHVdmBKsiYaiNfA8D3aEToDHgjWAGcMGRqMRjy8I0gCb3zAMPP1jI5i7BpjbsqxWqwX9sQM4e0NeGqDthvObpom52+022kb0anztnsw0wBnDW3+xWGCxMWu5XMYXAJ4ec9/uu0e4W8fNuI1SfwMuW6/X0QhhvfH+xW963n7m/6tNUd4B3EysGSRUsKQAAAAASUVORK5CYII=',
				},
				{
					text: 'Almacenaje',
					value: '5',
					img: 'iVBORw0KGgoAAAANSUhEUgAAADcAAAAeCAIAAAAHGhHvAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAG9SURBVFhH7Zihk4JAFIe5a/wZRqPRaiQSrUajkUo0arMatRExErFpsxqN2LiP2Xd3enKwwCLnzH0BeDDOfOzu+y3jW5qm1p/nXc5/m9ewzGZ8v99vNhsK27ITK1EPOr/u9XqTyUTdzMbyeDwul8vr9cpj27a5w7Hb691ut91uUeJmBmMJQRAMh8PL5aLKblmtVq7rJkkiNf0t5zSN43gwGJxOJ6k7wvM8JlqKT74tAcV+v4+u1E9nPB77ni/FDXeWwKSPRqMwCKV+Fsyv4zrr9Vrqe35aAqKO8+sP2kANDTkj9QM5lsCbsX4Xi4XUbcIyQzEKI6nzyLdUTKdT1rIU7XA4HMiW0k4osgTf9+m421AwSBRFKOqkSoklPKaXESoldLklGM98WpMG1X9zLUtg6WjOTimsInJRCj10LUFzpRdDR85mMym0qWAJ5/M5y/ywTuYzvwxhvXSrZgkq8wsSOJeGO0VlS6ia+U1mQFHHUqGZ+TRc89Vc3xKwLO5W5Ix8DTayhILkM5iyTS0h18bsjmXAEuIopj++ZnY+n5vd/c1YgtqcONbL7WKMWQJjSa+08VX6/w+MOV7B0rI+AIgD314tWfqGAAAAAElFTkSuQmCC',
				},
				{
					text: 'Operacion e Inspección',
					value: '6',
					img: 'iVBORw0KGgoAAAANSUhEUgAAACsAAAAnCAIAAAAZ/SbsAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAKqSURBVFhHxZihuzFBFIfXl2ir7caNtvkaUdwoqqIoqqLoXxBpNCKNRiPSaDT3vXfO9czu3bWLudcbmN96ds5vZs/MmZW7Xq/WW/kn3+/j/Q4sngKUy2XRGbC/ibSzQzgVF8QBV1Ujifl8HgSBnbcbjcZgMJh+wfXlckljOBy2Wi3HcUqlEr+qW+6gh0t3sN1u6/V6pVIZj8dyKZn1eo1Fhqj8JfGAAwbHsPgUnQ0mBsfdTlf0D7I66PV6jP54PIp+kE6nw+3n81m0RiYH/V6/2WyKeBZyAhMiNNIdTMdT8k7Ea3S7XSZDxDcpDvb7PVkdO3vPwWAimZTioNloZllR2dmut6wOfUj3HJDG+nZhina7TV6LuO+AjaXf76u2QdgnWNUi7jtwbIc8UG2zeJ6HD9XWHYQq02w28//7pKFoo7C2SS8RGiEHu93ul8IDPR8OBxEaUQe+74swje/59C9CI+TgdDo9WmezY7s2GSZCI+Qgn89fLhcRpkkaXsiB53qxE2UEJsB1XREaIQdFt0glFGEaenaLMQ5C+8FnRbAddcU4FMnbZn+LCyEHUKvV7h9vnoOiQBLcSoPuIPQUAKej0UiEOeiTsZHponWUkVsDm2zgnA2VNAJ9cmhbL2VLhls4iDoAarmp44mCqkhtFPFFigNQR3IRr0E1YgIih810B8wbj413BNHPQmBOGz+faboDUDe/si4IzDA48ojWyOQAMEEX+tkmO1hnALHhIasDBUlEX+Nh+guTgqhBPcB65NnrPOYAPjsNAlYpbpKGxZwzWwTO8o6lh5N/MHK59L8yNpvNZDJhjaxWK9K7UCjID5ZFzlJ12M0wigm5mowe7gEHOovFgvUiwrKq1Wr8fpeAAQcvEgpHC37vaBSLnbdVXPjToccSrY1/jWV9AOUG70lRTv7vAAAAAElFTkSuQmCC',
				},
			],
			actividad: {
				actividad: '',
				rol: '',
				flujo: '',
				icono: 'iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAIAAAAC64paAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAkSURBVDhPY/z//z8DuYAJSpMFRjWTCEY1kwhGNZMIhqRmBgYAnKADJSVVsOwAAAAASUVORK5CYII=',
				tiempo: '',
				porcentaje: '',
			},
			listadoActividades: [],
			listadoResumenDenominacion: [],
			listadoResumenRol: [],
			guardocambios: true,
		}
	},
	methods:{
		botonpdf: function(){

				if (this.guardocambios != true){
					this._mensaje_error('Por favor, primero haga click en el botón "Guardar Cambios"');
					return;
				}
				const  pdf = new jsPDF();
				pdf.setFontSize(18);
				pdf.text("Diagrama de Seguimiento de Actividades", 50, 25);
				pdf.setFontSize(13);
				//columnas
				var columns = [
					'Proceso',
					'Actividad',
					'Rol',
					'Flujo',
					//'Ícono',
					'Tiempo(Min)',
					'%',
				];
				//rows
				var body = [];
				this.listadoActividades.forEach((item, i) => {
					var temp = [];
					temp.push(i+1);
					temp.push(item.actividad);
					temp.push(item.rol);
					this.listaFlujos.forEach((item2, i2) => {
						if (item.flujo == item2.value)
							temp.push(item2.text);
					});
					//temp.push(item.icono);
					temp.push(item.tiempo);
					temp.push(item.porcentaje);
					body.push(temp);
				});
				pdf.autoTable({
					startY: 35,
					styles: { halign: 'center' },
					//columnStyles: { 0: { halign: 'center' } },
					columns: columns,
					body: body,
				});
				pdf.save('priorizacion.pdf');

		},
		reestructurarIconos: function(){
			this.listadoActividades.forEach((item, i) => {
				this.listaFlujos.forEach((item2, i2) => {
					if (item.flujo == item2.value)
						item.icono = item2.img;
				});
				this.listadoActividades[i] = item;
			});
			this.actualizarDatos();
		},
		botonGuardar: function(){
			var arregloEnviar = [];
			this.listadoActividades.forEach((item, i) => {
				var temp = {...item};
				temp.icono = null;
				arregloEnviar.push(temp);
			});
			var that = this;
			this._axios('/mapaprocesosver/diagramaseguimientoactividades/setupdel', {
				objdiagramaseguimientoactividades: this.objdiagramaseguimientoactividades,
				obj: this.obj,
				arreglo: arregloEnviar
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that._mensaje_exito('Los datos se han guardado correctamente');
					that.objdiagramaseguimientoactividades.tipo = res.datos.tipo;
					that.listadoActividades = res.datos.arreglo;
					that.reestructurarIconos();
					that.guardocambios = true;
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
		seleccionoSubProceso: function(estado){
			this.listadoActividades = [];
			//that.objdiagramaseguimientoactividades.tipo = '-', 'P', 'S'
			if (this.objdiagramaseguimientoactividades.tipo != '-' && estado == true){
				if (this.objdiagramaseguimientoactividades.tipo == 'S' && this.obj.procesoSeleccionado == this.obj.subProcesoSeleccionado){
					this._mensaje_error('La caracterizacion para este proceso se da por subprocesos, por favor, seleccione un subproceso');
					//this.obj.procesoSeleccionado = '';
					this.obj.subProcesoSeleccionado = '';
					return;
				}
				else if (this.objdiagramaseguimientoactividades.tipo == 'P' && this.obj.procesoSeleccionado != this.obj.subProcesoSeleccionado){
					this._mensaje_error('La caracterizacion para este proceso se da por proceso, por favor, seleccione solamente el proceso');
					//this.obj.procesoSeleccionado = '';
					this.obj.subProcesoSeleccionado = '';
					return;
				}
			}
			var that = this;
			this._axios('/mapaprocesosver/diagramaseguimientoactividades/detalle', {
				objdiagramaseguimientoactividades: this.objdiagramaseguimientoactividades,
				obj: this.obj
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.listadoActividades = res.datos;
					that.reestructurarIconos();
					that.guardocambios = true;
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
		seleccionoProceso: function(){
			var idProceso = this.obj.procesoSeleccionado;
			var that = this;
			this._axios('/mapaprocesosver/diagramaseguimientoactividades/maestro', {
				datosmapa: this.datosmapa,
				obj: this.obj,
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.objdiagramaseguimientoactividades = res.datos.diagramaseguimientoactividades;
					that.listaSubprocesos = [{
						'idProceso': idProceso,
						'nombre': '[No seleccionar]'
					}];
					that.listaSubprocesos = that.listaSubprocesos.concat(res.datos.subprocesos);
					that.seleccionoSubProceso(false);
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
		seleccionoTipo: function(){
			this.obj.procesoSeleccionado = '';
			this.obj.subProcesoSeleccionado = '';
		},
		agregarActividad: function(){
			this.listadoActividades.push({...this.actividad});
			this.actualizarDatos();
			this.guardocambios = false;
		},
		eliminarActividad: function(index){
			console.log(index);
			this.listadoActividades.splice(index, 1);
			this.actualizarDatos();
			this.guardocambios = false;
		},
		seleccionoFlujo: function(item){
			var flujoseleccionado = {};
			this.listaFlujos.forEach((item2, i2) => {
				if (item2.value == item.flujo)
				flujoseleccionado = item2;
			});
			item.icono = flujoseleccionado.img;
			this.actualizarDatos();
			this.guardocambios = false;
		},
		actualizarDatos: function(){
			/*Tabla*/
			var sumatiempos = 0;
			//corrijo tiempos iguales o menores a cero
			//hayo la suma de los tiempos
			this.listadoActividades.forEach((item, i) => {
				if (item.tiempo <= 0)
					this.listadoActividades[i].tiempo = 1;
				sumatiempos += parseFloat(this.listadoActividades[i].tiempo);
			});
			//calculo prorcentajes
			this.listadoActividades.forEach((item, i) => {
				this.listadoActividades[i].porcentaje = (100.0 * item.tiempo / sumatiempos).toFixed(1);
			});
			/*Tabla*/
			/*Tabla Resumen Denominacion*/
			var sumatiempos2 = 0;
			//calculo tiempos por cada uno y los sumo
			this.listadoResumenDenominacion.forEach((item, i) => {
				this.listadoResumenDenominacion[i].tiempo = 0;
				this.listadoActividades.forEach((item2, i2) => {
					if (item.value == item2.flujo)
						this.listadoResumenDenominacion[i].tiempo += parseFloat(item2.tiempo);
				});
				sumatiempos2 += parseFloat(this.listadoResumenDenominacion[i].tiempo);
			});
			//calculo el porcentaje
			this.listadoResumenDenominacion.forEach((item, i) => {
				this.listadoResumenDenominacion[i].porcentaje = 0;
				if (sumatiempos2 != 0)
					this.listadoResumenDenominacion[i].porcentaje = (100.0 * item.tiempo / sumatiempos2).toFixed(1);
				//console.log(this.listadoResumenDenominacion[i].porcentaje);
			});
			/*Tabla Resumen Denominacion*/
			/*Tabla Resumen Rol*/
			//obtengo los roles
			var arreglo_roles = [];
			this.listadoActividades.forEach((item, i) => {
				var rol = item.rol;
				rol = rol.toLowerCase();
				rol = rol.charAt(0).toUpperCase() + rol .slice(1);
				arreglo_roles.push(rol);
			});
			//elimino duplicado de los roles
			arreglo_roles = this._eliminarDuplicadosArray(arreglo_roles);
			//paso esos roles al array que enlaza en el form
			this.listadoResumenRol = [];
			arreglo_roles.forEach((item, i) => {
				this.listadoResumenRol.push({
					text: item,
					//tiempo: 0,
					//porcentaje: 0,
				});
			});
			//obtengo los tiempos
			var sumatiempos3 = 0;
			this.listadoResumenRol.forEach((item, i) => {
				this.listadoResumenRol[i].tiempo = 0;
				this.listadoActividades.forEach((item2, i2) => {
					if (item.text.toLowerCase() == item2.rol.toLowerCase())
						this.listadoResumenRol[i].tiempo += parseFloat(item2.tiempo);
				});
				sumatiempos3 += parseFloat(this.listadoResumenRol[i].tiempo);
			});
			//obtengo el porcentaje
			this.listadoResumenRol.forEach((item, i) => {
				this.listadoResumenRol[i].porcentaje = (100.0 * item.tiempo / sumatiempos3).toFixed(1);
			});
			/*Tabla Resumen Rol*/
			this.guardocambios = false;
		},
	},
	mounted() {
		if (this.datosmapa.priorizado != true){
			this._mensaje_error('Usted primero debe realizar la priorizacion de procesos')
			.then(()=>{
				var _eventocargar = new CustomEvent('goPriorizacion');
				document.dispatchEvent(_eventocargar);
			});
			return;
		}
		this.obj.tipoSeleccionado = 'A';
		this.listaProcesos = this.procpriorizados;
		//para el estadistico
		this.listadoResumenDenominacion = [...this.listaFlujos];
		//funciones varias
		//this.agregarActividad();
		this.actualizarDatos();
		this.guardocambios = true;
	}
}
</script>
