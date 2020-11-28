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
			<span>Tablero de control</span>
			<br>
		</div>
		<table class="table table-sm table-hover">
			<thead>
				<tr>
					<th>Indicador</th>
					<th>Objetivo Estratégico</th>
					<th>Opciones</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(item, index) in listado">
					<td>{{item.indicador_nombre}}</td>
					<td>{{item.objetivo}}</td>
					<td>
						<b-button @click="cargarVer(item, index)" variant="outline-warning" size="sm">Ver</b-button>
						<b-button variant="outline-info" size="sm">Ver Gráfico</b-button>
						<b-button @click="cargarDataFuente(item, index)" variant="outline-secondary" size="sm">Data Fuente</b-button>
						<b-button @click="cargarEliminar(item, index)" variant="outline-danger" size="sm">Eliminar</b-button>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="text-center">
			<b-button @click="botonAgregar" variant="success">Agregar</b-button>
		</div>
		<b-modal id="modal-tablerocontrol" size="md" scrollable centered hide-backdrop title="titulo" hide-footer>
			<mapaprocesos-tablerocontrol-editor @cerrarModal="cerrarModal1" :datosmapa="datosmapa" :objmaestro="obj" :objtablero="objtablero" :elementoeditar="elementoeditar" :indicador_desempenio="indicador_desempenio" :mapa_estrategico="mapa_estrategico"></mapaprocesos-tablerocontrol-editor>
		</b-modal>
		<b-modal id="modal-tablerocontrol-datafuente" size="lg" scrollable centered hide-backdrop title="titulo" hide-footer>
			<mapaprocesos-tablerocontrol-datafuente @cerrarModal="cerrarModal2" :elemento="elemento_para_datafuente"></mapaprocesos-tablerocontrol-datafuente>
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
			objtablero: null,
			listado: [],
			indicador_desempenio: '', //indicadores de desempeño para el proceso seleccionado
			mapa_estrategico: '', //objetivos estrategicos del mapa estrategico del proceso seleccinado
			elementoeditar: null, // para editar o eliminar
			indexeditar: null, // para editar o eliminar
			elemento_para_datafuente: {},
		}
	},
	methods:{
		cargarVer: function(item, index){
			console.log(item);
			this.elementoeditar = item;
			this.indexeditar = index;
			this.$bvModal.show('modal-tablerocontrol');
		},
		cargarEliminar: function(item, index){
			this.elementoeditar = item;
			this.indexeditar = index;
			this.elementoeditar.estado = '0';
			this.$bvModal.show('modal-tablerocontrol');
		},
		cargarDataFuente: function(item, index){
			this.elemento_para_datafuente = item;
			this.$bvModal.show('modal-tablerocontrol-datafuente');
		},
		seleccionoSubProceso: function(estado){
			this.listado = [];
			//that.objtablero.tipo = '-', 'P', 'S'
			if (this.objtablero.tipo != '-' && estado == true){
				if (this.objtablero.tipo == 'S' && this.obj.procesoSeleccionado == this.obj.subProcesoSeleccionado){
					this._mensaje_error('La caracterizacion para este proceso se da por subprocesos, por favor, seleccione un subproceso');
					//this.obj.procesoSeleccionado = '';
					this.obj.subProcesoSeleccionado = '';
					return;
				}
				else if (this.objtablero.tipo == 'P' && this.obj.procesoSeleccionado != this.obj.subProcesoSeleccionado){
					this._mensaje_error('La caracterizacion para este proceso se da por proceso, por favor, seleccione solamente el proceso');
					//this.obj.procesoSeleccionado = '';
					this.obj.subProcesoSeleccionado = '';
					return;
				}
			}
			var that = this;
			this._axios('/mapaprocesosver/tablerocontrol/detalle', {
				objtablero: this.objtablero,
				obj: this.obj
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.listado = res.datos.tablerocontrol;
					that.indicador_desempenio = res.datos.indicador_desempenio;
					that.mapa_estrategico = res.datos.mapa_estrategico;
					if (that.listado.length != 0)
						that.refrescarLista();
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
		seleccionoProceso: function(){
			var idProceso = this.obj.procesoSeleccionado;
			var that = this;
			this._axios('/mapaprocesosver/tablerocontrol/maestro', {
				datosmapa: this.datosmapa,
				obj: this.obj,
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.objtablero = res.datos.tablerocontrol;
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
			this.$bvModal.show('modal-tablerocontrol');
			/*
			var that = this;
			this._axios('/mapaprocesosver/tablerocontrol/setupdel', {
				objtablero: this.objtablero,
				obj: this.obj
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that._mensaje_exito('Los datos se han guardado correctamente');
					that.objtablero.tipo = res.datos.tipo;
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
		refrescarLista: function(){
			this.listado.forEach((item, i) => {
				this.indicador_desempenio.forEach((item2, i2) => {
					if (item.idDetalleIndicadorDesempenio == item2.idDetalleIndicadorDesempenio){
						this.listado[i].indicador_nombre = item2.nombre;
						var formula = JSON.parse(item2.formula);
						this.listado[i].indicador_formula = formula;
					}
				});
			});
			console.log(this.listado);
		},
		cerrarModal1: function(data){
			console.log(data);
			if (data.bul)
				this.objtablero.tipo = data.datos.tipo;
				if (this.indexeditar != null)
					if (data.datos.detalle.estado == '1')
						this.listado[this.indexeditar] = data.datos.detalle;
					else
						this.listado.splice(this.indexeditar, 1);
				else
					this.listado.push(data.datos.detalle);
			this.refrescarLista()
			this.$forceUpdate();
			this.$bvModal.hide('modal-tablerocontrol');
		},
		cerrarModal2: function(){
			this.$bvModal.hide('modal-tablerocontrol-datafuente');
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
