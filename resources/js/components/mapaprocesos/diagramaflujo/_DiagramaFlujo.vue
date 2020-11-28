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
		<b-row>
			<b-col md="5" sm="12">
				<b-form-group label="Ingrese documento" label-cols="12" label-align="center" label-size="md" class="mb-1">
					<sube-archivos ref="subearchivos" @archivosubido="evento_subearchivos" tipos="imgdoc"></sube-archivos>
				</b-form-group>
			</b-col>
			<b-col md="7" sm="12">
				<b-form-group label="Descripción" label-cols="12" label-align="center" label-size="md" class="mb-1">
					<b-form-textarea :state="!$v.obj.detalle.$invalid" v-model="obj.detalle" rows="6"></b-form-textarea>
					<div class="text-danger" v-if="!$v.obj.detalle.required">Por favor ingrese el detalle</div>
					<div class="text-danger" v-if="!$v.obj.detalle.txtdescipcion">Por favor, asegurese de escribir bien el detalle, no se aceptan caracteres especiales</div>
					<div class="text-danger" v-if="!$v.obj.detalle.minLength">Debe ingresar 4 caracteres como mínimo</div>
					<div class="text-danger" v-if="!$v.obj.detalle.maxLength">Debe ingresar 250 caracteres como máximo</div>
				</b-form-group>
			</b-col>
		</b-row>
		<div class="text-center">
			<br>
			<div :hidden="datosmapa.estado2=='H'">
				<b-button @click="botonAgregar" variant="success">Guardar</b-button>
				<br>
				<br>
			</div>
			<div :hidden="datosmapa.estado2!='H'">
				<span class="text-danger">No es posbile agregar porque el mapa actual se encuentra en el histórico</span>
			</div>
		</div>
		<table class="table table-sm table-hover">
			<thead>
				<tr>
					<th width="40%">Descripción</th>
					<th>Fecha de subida</th>
					<th>Opciones</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(item, index) in listado">
					<td width="40%">{{item.descripcion}}</td>
					<td>{{item.fechasubida}}</td>
					<td>
						<b-button variant="outline-primary" size="sm">Descargar documento</b-button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
import { required, minLength, maxLength } from 'vuelidate/lib/validators'
import { txtdescipcion } from '../../_expresiones_regulares.js'
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
				tipoSeleccionado: '',
				procesoSeleccionado: '',
				subProcesoSeleccionado: '',
				archivo: '',
				detalle: '',
			},
			listaProcesos: [],
			listaSubprocesos: [],
			listaTipos: [
				{
					text: 'Situación Actual',
					value: 'A',
				},
				{
					text: 'Rediseño',
					value: 'R',
				},
			],
			listado: [],
			objdiagramaflujo: null,
		}
	},
	validations: {
		obj: {
			detalle: {
				required,
				txtdescipcion,
				minLength: minLength(4),
				maxLength: maxLength(250),
			},
		}
	},
	methods:{
		botonAgregar: function(){
			if (this.$v.obj.detalle.$invalid){
				this._mensaje_error('Por favor, revise el formulario y corrija el campo incorrecto');
				return;
			}
			if (this.obj.archivo == ""){
				this._mensaje_error('Por favor, suba un archivo o documento');
				return;
			}
			var that = this;
			this._axios('/mapaprocesosver/diagramaflujo/setupdel', {
				objdiagramaflujo: this.objdiagramaflujo,
				obj: this.obj
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that._mensaje_exito('Los datos se han guardado correctamente');
					that.objdiagramaflujo.tipo = res.datos.tipo;
					that.listado.unshift(res.datos.detalle);
					that.$refs.subearchivos.reset()
					that.obj.detalle = "";
				}
				else
					that._mensaje_error('Error, complete la informacion del proceso');
					//that._mensaje_error(res.mensaje);
			});
		},
		seleccionoSubProceso: function(estado){
			this.listado = [];
			//that.objdiagramaflujo.tipo = '-', 'P', 'S'
			if (this.objdiagramaflujo.tipo != '-' && estado == true){
				if (this.objdiagramaflujo.tipo == 'S' && this.obj.procesoSeleccionado == this.obj.subProcesoSeleccionado){
					this._mensaje_error('La caracterizacion para este proceso se da por subprocesos, por favor, seleccione un subproceso');
					//this.obj.procesoSeleccionado = '';
					this.obj.subProcesoSeleccionado = '';
					return;
				}
				else if (this.objdiagramaflujo.tipo == 'P' && this.obj.procesoSeleccionado != this.obj.subProcesoSeleccionado){
					this._mensaje_error('La caracterizacion para este proceso se da por proceso, por favor, seleccione solamente el proceso');
					//this.obj.procesoSeleccionado = '';
					this.obj.subProcesoSeleccionado = '';
					return;
				}
			}
			var that = this;
			this._axios('/mapaprocesosver/diagramaflujo/detalle', {
				objdiagramaflujo: this.objdiagramaflujo,
				obj: this.obj
			}, function(res){
				res = res.data;
				if (res.mensaje == '')
					that.listado = res.datos;
				else
					that._mensaje_error(res.mensaje);
			});
		},
		seleccionoProceso: function(){
			var idProceso = this.obj.procesoSeleccionado;
			var that = this;
			this._axios('/mapaprocesosver/diagramaflujo/maestro', {
				datosmapa: this.datosmapa,
				obj: this.obj,
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.objdiagramaflujo = res.datos.diagramaflujo;
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
		evento_subearchivos: function(data){
			var nombre = '';
			data.fileRecords.forEach((item, i) => {
				nombre = item.upload.data;
			});
			this.obj.archivo = nombre;
			console.log(this.obj);
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
		this.listaProcesos = this.procpriorizados;
		this.obj.tipoSeleccionado = 'A';
	}
}
</script>
