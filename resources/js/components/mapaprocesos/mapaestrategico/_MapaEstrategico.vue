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
		<b-form-group label="Orden" label-cols-sm="2" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
			<b-row>
				<b-col sm="9" md="9">
					<cool-select @select="" v-model="orden_aspectos" :items="listaOrden" item-text="text" item-value="value" size="sm"></cool-select>
				</b-col>
				<b-col sm="3" md="3">
					<b-button variant="primary" size="sm" :disabled="objmapaest.orden_aspectos!=null" @click="guardar_orden()">Guardar Orden</b-button>
				</b-col>
			</b-row>
		</b-form-group>
		<table class="table table-sm table-hover" :hidden="objmapaest.orden_aspectos==null || obj.subProcesoSeleccionado==''">
			<thead>
				<tr>
					<th>Apecto</th>
					<th>Objetivo estratégico (Viene de)</th>
					<th>Opciones</th>
				</tr>
			</thead>
			<tbody v-for="(item, index) in listado">
				<tr v-for="(item2, index2) in item.objetivos">
					<td v-if="index2==0" :rowspan="item.objetivos.length">{{item.aspecto}}</td>
					<td>
						{{item2.nombre}} <span v-if="item2.anterior!=null">({{item2.anterior}})</span>
					</td>
					<td>
						<b-button :hidden="item2==''" @click="cargarVer(item2, index2)" variant="outline-warning" size="sm">Ver</b-button>
						<b-button :hidden="item2==''" @click="cargarEliminar(item2, index2)" variant="outline-danger" size="sm">Eliminar</b-button>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="text-center" :hidden="objmapaest.orden_aspectos==null">
			<b-button @click="botonAgregar" variant="success">Agregar</b-button>
			<br><br>
			<b-button @click="botonVerGrafico" variant="primary">Ver Gráfico</b-button>
		</div>
		<b-modal id="modal-mapaestrategico" size="md" scrollable centered hide-backdrop title="titulo" hide-footer>
			<mapaprocesos-mapaestrategico-editor @cerrarModal="cerrarModal" :datosmapa="datosmapa" :objmaestro="obj" :objmapaest="objmapaest" :elementoeditar="elementoeditar" :listado="listado"></mapaprocesos-mapaestrategico-editor>
		</b-modal>
		<b-modal id="modal-mapaestrategico-grafico" size="md" scrollable centered hide-backdrop title="titulo" hide-footer>
			<mapaprocesos-mapaestrategico-grafico @cerrarModal="cerrarModal2" :datos="listado"></mapaprocesos-mapaestrategico-grafico>
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
			objmapaest: {
				orden_aspectos: null
			},
			orden_aspectos: 'null',
			listado_original: [],
			listado: [],
			/*
			listado: [
				{
					aspecto: 'Financiero',
					objetivos: ['1', '2', '3'],
				},
				{
					aspecto: 'Clientes',
					objetivos: ['1', '2'],
				},
				{
					aspecto: 'Procesos internos',
					objetivos: ['1', '2'],
				},
				{
					aspecto: 'Aprendizaje y crecimiento',
					objetivos: ['1', '2', '3', '4'],
				}
			],
			*/
			listaOrden: [
				{
					value: 'A',
					text: 'Financiero, Clientes, Procesos internos, Aprendizaje y crecimiento',
				},
				{
					value: 'B',
					text: 'Clientes, Financiero, Procesos internos, Aprendizaje y crecimiento',
				}
			],
			elementoeditar: null,
			indexeditar: null,
		}
	},
	methods:{
		cargarVer: function(item, index){
			console.log(item);
			this.elementoeditar = item;
			this.indexeditar = index;
			this.$bvModal.show('modal-mapaestrategico');
		},
		cargarEliminar: function(item, index){
			this.elementoeditar = item;
			this.indexeditar = index;
			this.elementoeditar.estado = '0';
			this.$bvModal.show('modal-mapaestrategico');
		},
		formar_listatabla: function(){
			//console.log(this.listado_original);
			var listado = this.listado_original;
			var aspectos = this._aspectos_mapa_estrategico(this.objmapaest.orden_aspectos);
			//armar la tabla
			aspectos.forEach((item, i) => {
				aspectos[i].aspecto = aspectos[i].text;
				delete aspectos[i].text;
				aspectos[i].objetivos = [];
				listado.forEach((item2, i2) => {
					if (item.aspecto == item2.aspecto){
						listado.forEach((item3, i3) => {
							if (item2.idAnterior == item3.idDetalleMapaEstrategico)
								item2.anterior = item3.nombre;
						});
						aspectos[i].objetivos.push(item2);
					}
				});
			});
			//poner espacios para cuando hay aspectos sin objetivos
			aspectos.forEach((item, i) => {
				if (item.objetivos.length == 0)
					item.objetivos.push('');
			});
			this.listado = aspectos;
			//console.log(aspectos);
		},
		guardar_orden: function(){
			console.log(this.objmapaest);
			var that = this;
			this._axios('/mapaprocesosver/mapaestrategico/orden', {
				//datosmapa: this.datosmapa,
				//obj: this.obj,
				objmapaest: this.objmapaest,
				orden_aspectos: this.orden_aspectos,
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.objmapaest = res.datos;
					that._mensaje_exito("Se ha guardado el orden de los aspectos, ahora puede registrar los objetivos estratégicos");
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
		seleccionoSubProceso: function(estado){
			this.listado = [];
			//that.objmapaest.tipo = '-', 'P', 'S'
			if (this.objmapaest.tipo != '-' && estado == true){
				if (this.objmapaest.tipo == 'S' && this.obj.procesoSeleccionado == this.obj.subProcesoSeleccionado){
					this._mensaje_error('El mapa estrategico para este proceso se da por subprocesos, por favor, seleccione un subproceso');
					//this.obj.procesoSeleccionado = '';
					this.obj.subProcesoSeleccionado = '';
					return;
				}
				else if (this.objmapaest.tipo == 'P' && this.obj.procesoSeleccionado != this.obj.subProcesoSeleccionado){
					this._mensaje_error('El mapa estrategico para este proceso se da por proceso, por favor, seleccione solamente el proceso general');
					//this.obj.procesoSeleccionado = '';
					this.obj.subProcesoSeleccionado = '';
					return;
				}
			}
			var that = this;
			this._axios('/mapaprocesosver/mapaestrategico/detalle', {
				objmapaest: this.objmapaest,
				obj: this.obj
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.listado_original = res.datos;
					that.formar_listatabla();
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
		seleccionoProceso: function(){
			var idProceso = this.obj.procesoSeleccionado;
			var that = this;
			this._axios('/mapaprocesosver/mapaestrategico/maestro', {
				datosmapa: this.datosmapa,
				obj: this.obj,
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.objmapaest = res.datos.mapaestrategico;
					that.listaSubprocesos = [{
						'idProceso': idProceso,
						'nombre': '[No seleccionar]'
					}];
					that.listaSubprocesos = that.listaSubprocesos.concat(res.datos.subprocesos);
					//that.obj.subProcesoSeleccionado = idProceso;
					that.seleccionoSubProceso(false);
					///
					that.orden_aspectos = (that.objmapaest.orden_aspectos ?? 'A');
					///
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
		botonAgregar: function(){
			this.elementoeditar = null;
			this.indexeditar = null;
			this.$bvModal.show('modal-mapaestrategico');
		},
		cerrarModal: function(data){
			if (data.bul){
				this.objmapaest.tipo = data.datos.tipo;
				if (this.indexeditar != null)
					if (data.datos.detalle.estado == '1')
						this.listado_original[this.indexeditar] = data.datos.detalle;
					else
						this.listado_original.splice(this.indexeditar, 1);
				else
					this.listado_original.push(data.datos.detalle);
			}
			console.log(data);
			console.log(this.listado_original);
			this.formar_listatabla();
			this.$forceUpdate();
			this.$bvModal.hide('modal-mapaestrategico');
		},
		botonVerGrafico: function(){
			this.$bvModal.show('modal-mapaestrategico-grafico');
		},
		cerrarModal2: function(){
			this.$bvModal.hide('modal-mapaestrategico-grafico');
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
