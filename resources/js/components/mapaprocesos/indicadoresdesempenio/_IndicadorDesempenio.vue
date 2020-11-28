<template>
	<div>
		<br>
		<b-form-group label="Seleccione su proceso priorizado" label-cols-sm="6" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
			<cool-select @select="seleccionoProceso" v-model="obj.procesoSeleccionado" :items="listaProcesos" item-text="nombre" item-value="idProceso"></cool-select>
		</b-form-group>
		<b-form-group label="Sub-Proceso" label-cols-sm="6" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
			<cool-select @select="seleccionoSubProceso(true)" v-model="obj.subProcesoSeleccionado" :items="listaSubprocesos" item-text="nombre" item-value="idProceso"></cool-select>
		</b-form-group>
		<div class="text-center">
			<br>
			<span>Indicadores de Desempeño Registrados</span>
			<br>
		</div>
		<table class="table table-sm table-hover">
			<thead>
				<tr>
					<th>Nombre</th>
					<th>Puesto que mide</th>
					<th>Opciones</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(item, index) in listado">
					<td>{{item.nombre}}</td>
					<td>{{item.puesto}}</td>
					<td>
						<b-button @click="cargarVer(item, index)" variant="outline-warning" size="sm">Ver</b-button>
						<b-button @click="cargarEliminar(item, index)" variant="outline-danger" size="sm">Eliminar</b-button>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="text-center">
			<b-button @click="botonAgregar" variant="success">Agregar</b-button>
		</div>
		<b-modal id="modal-indicadoresdesempenio" size="lg" scrollable centered hide-backdrop title="titulo" hide-footer>
			<mapaprocesos-indicadoresdesempenio-editor @cerrarModal="cerrarModal" :datosmapa="datosmapa" :objmaestro="obj" :objindicador="objindicador" :elementoeditar="elementoeditar"></mapaprocesos-indicadoresdesempenio-editor>
		</b-modal>
	</div>
</template>

<script>
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
			},
			listaProcesos: [],
			listaSubprocesos: [],
			objindicador: null,
			listado: [{},{}],
			elementoeditar: null,
			indexeditar: null,
		}
	},
	methods:{
		cargarVer: function(item, index){
			this.elementoeditar = item;
			this.indexeditar = index;
			this.$bvModal.show('modal-indicadoresdesempenio');
		},
		cargarEliminar: function(item, index){
			this.elementoeditar = item;
			this.indexeditar = index;
			this.elementoeditar.estado = '0';
			this.$bvModal.show('modal-indicadoresdesempenio');
		},
		seleccionoSubProceso: function(estado){
			this.listado = [];
			//that.objindicador.tipo = '-', 'P', 'S'
			if (this.objindicador.tipo != '-' && estado == true){
				if (this.objindicador.tipo == 'S' && this.obj.procesoSeleccionado == this.obj.subProcesoSeleccionado){
					this._mensaje_error('El registro de indicadores para este proceso se da por subprocesos, por favor, seleccione un subproceso');
					//this.obj.procesoSeleccionado = '';
					this.obj.subProcesoSeleccionado = '';
					return;
				}
				else if (this.objindicador.tipo == 'P' && this.obj.procesoSeleccionado != this.obj.subProcesoSeleccionado){
					this._mensaje_error('El registro de indicadores para este proceso se da por proceso, por favor, seleccione solamente el proceso general');
					//this.obj.procesoSeleccionado = '';
					this.obj.subProcesoSeleccionado = '';
					return;
				}
			}
			var that = this;
			this._axios('/mapaprocesosver/indicadores/detalle', {
				objindicador: this.objindicador,
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
			this._axios('/mapaprocesosver/indicadores/maestro', {
				datosmapa: this.datosmapa,
				obj: this.obj,
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.objindicador = res.datos.indicadores;
					that.listaSubprocesos = [{
						'idProceso': idProceso,
						'nombre': '[No seleccionar]'
					}];
					that.listaSubprocesos = that.listaSubprocesos.concat(res.datos.subprocesos);
					//that.obj.subProcesoSeleccionado = idProceso;
					that.seleccionoSubProceso(false);
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
		botonAgregar: function(){
			this.elementoeditar = null;
			this.indexeditar = null;
			this.$bvModal.show('modal-indicadoresdesempenio');
			/*
			var that = this;
			this._axios('/mapaprocesosver/indicadores/setupdel', {
				objindicador: this.objindicador,
				obj: this.obj
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that._mensaje_exito('Los datos se han guardado correctamente');
					that.objindicador.tipo = res.datos.tipo;
					that.listado.unshift(res.datos.detalle);
					that.$refs.subearchivos.reset()
					that.obj.detalle = "";
				}
				else
					that._mensaje_error('Error, complete la informacion del proceso');
					//that._mensaje_error(res.mensaje);
			});
			*/
		},
		cerrarModal: function(data){
			if (data.bul){
				this.objindicador.tipo = data.datos.tipo;
				if (this.indexeditar != null)
					if (data.datos.detalle.estado == '1')
						this.listado[this.indexeditar] = data.datos.detalle;
					else
						this.listado.splice(this.indexeditar, 1);
				else
					this.listado.push(data.datos.detalle);
			}
			this.$forceUpdate();
			this.$bvModal.hide('modal-indicadoresdesempenio');
		},
	},
	mounted() {
		/*
		if (this.datosmapa.priorizado != true){
			this._mensaje_error('Usted primero debe realizar la priorizacion de procesos')
			.then(()=>{
				var _eventocargar = new CustomEvent('goPriorizacion');
				document.dispatchEvent(_eventocargar);
			});
			return;
		}
		*/
		this.listaProcesos = this.procpriorizados;
		console.log(this.listaProcesos);
	}
}
</script>
