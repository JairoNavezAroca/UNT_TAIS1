<template>
	<div>
		<div class="border text-center pl-4 pr-4 pt-2 pb-2 mt-4 mb-4">
			<div class="text-center">
				<span>Relacionar</span>
			</div>
			<table class="table table-sm table-hover">
				<thead>
					<tr>
						<th>Proceso</th>
						<th>Se dirige a:</th>
						<th :hidden="datosmapa.priorizado==true">Opciones</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(item, index) in datos.relaciones">
						<td>
							{{item.proceso_desde}}
						</td>
						<td>
							{{item.proceso_hasta}}
						</td>
						<td :hidden="datosmapa.priorizado==true">
							<div>
								<b-button @click="cargarEliminar(item, index)" variant="outline-danger" size="sm">Eliminar</b-button>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<b-modal id="modal-relaciones" size="md" scrollable centered hide-backdrop :title="texto+' Relación'" hide-footer>
				<mapaprocesos-mapaprocesos-previsualizar-relacionar :listadatos="datos.relaciones" :relacion="relacion" :datos="datos" :datosmapa="datosmapa" @cerrarModal="cerrarModal"></mapaprocesos-mapaprocesos-previsualizar-relacionar>
			</b-modal>
			<div class="text-center">
				<b-button v-if="datosmapa.priorizado==false" @click="botonAgregar" variant="success">Agregar</b-button>
				<span v-else class="text-danger">Usted no puede agregar o eliminar relaciones entre procesos porque ya se ha realizado la priorización de procesos</span>
			</div>
		</div>
		<br>
		<div class="text-center">
			<span>Gráfico del Mapa de Procesos</span>
			<br>
			<b-button @click="cargarGrafico" size="sm" :disabled="datos.relaciones.length==0">Cargar Gráfico</b-button>
			<b-button @click="botonpdf" size="sm" :disabled="datos.relaciones.length==0">Descargar en PDF</b-button>
			<br><br>
			<div id="diagrama"></div>
			<div id="imagen" ref="areaexportar" style="display:block; width:100%;" hidden></div>
		</div>
	</div>
</template>

<script>
import jsPDF from 'jspdf'
const html2canvas = require('html2canvas');
export default {
	props:{
		datosmapa: Object,
		datos: Object//aca están todos los items, entrada, salida y procesos varios
	},
	data() {
		return {
			relacion: [],
			index: -1,
			texto: '',
		}
	},
	methods:{
		botonpdf: function(){
			var jpg = myDiagram.makeImage({background: "white"});
			document.getElementById("imagen").innerHTML='<img src="'+jpg.src+'">';
			var doc = new jsPDF();
			var canvasElement = document.createElement('canvas');
			html2canvas(this.$refs.areaexportar, { canvas: canvasElement })
			.then(function (canvas) {
				//doc.addImage(jpg,'JPEG', 20, 20);
				doc.addImage(jpg,'JPEG', 5, 5);
				doc.save("grafico.pdf");
			});
		},
		cargarGrafico: function(){
			var nodeDataArray = [
				{ key: -1, text: "Procesos Estratégicos", color: "black", isGroup: true },
				{ key: -10, text: "", color: "white", isGroup: true, },
				{ key: -100, text: "", color: "white", isGroup: true },
				{ key: -2, text: "Procesos Primarios", color: "black", isGroup: true },
				{ key: -20, text: "", color: "white", isGroup: true },
				{ key: -200, text: "", color: "white", isGroup: true },
				{ key: -3, text: "Procesos de Apoyo", color: "black", isGroup: true },
				{ key: -30, text: "", color: "white", isGroup: true },
				{ key: -300, text: "", color: "white", isGroup: true },
			];
			this.datos.estrategico.forEach((item, i) => {
				nodeDataArray.push({
					key: item.idProceso,
					text: item.nombre,
					color: 'white',
					group: -1
				});
			});
			this.datos.apoyo.forEach((item, i) => {
				nodeDataArray.push({
					key: item.idProceso,
					text: item.nombre,
					color: 'white',
					group: -3
				});
			});
			this.datos.primarios.forEach((item, i) => {
				if (item.idProcesoAnterior == null){
					var bul = false;// true = el proceso tiene subprocesos
					this.datos.primarios.forEach((item2) => {
						bul = bul || item.idProceso == item2.idProcesoAnterior
					});
					if (bul)
						nodeDataArray.push({
							key: item.idProceso,
							text: item.nombre,
							color: 'black',
							group: -2,
							isGroup: true
						});
					else
						nodeDataArray.push({
							key: item.idProceso,
							text: item.nombre,
							color: 'white',
							group: -2
						});
					}
				else
					nodeDataArray.push({
						key: item.idProceso,
						text: item.nombre,
						color: 'white',
						group: item.idProcesoAnterior
					});
			});
			var linkDataArray = [
				{ from: 1, to: 2, color: "blue" },
				{ from: 2, to: 2 },
				{ from: 3, to: 4, color: "green" },
				{ from: 3, to: 1, color: "purple" },
				{ from: 6, to: 5, color: "purple" }
			];
			console.log(this.datos.relaciones);
			this.datos.relaciones.forEach((item, i) => {
				linkDataArray.push({
					from: item.idProceso_desde,
					to: item.idProceso_hasta,
					color: 'black'
				});
			});
			var _eventocargar = new CustomEvent('creargrafico', {
				detail: {
					type: 'MAPAPROCESO',
					nodeDataArray: nodeDataArray,
					linkDataArray: linkDataArray
				}
			});
			var aa = document.dispatchEvent(_eventocargar);
		},
		cargarEliminar: function(item, index){
			item.estado = '0';
			this.relacion = item;
			this.index = index;
			this.texto = 'Eliminar';
			this.$bvModal.show('modal-relaciones');
		},
		botonAgregar: function(){
			this.relacion = null;
			this.index = -1;
			this.texto = 'Eliminar';
			this.$bvModal.show('modal-relaciones');
		},
		cerrarModal: function(data){
			try {
				this.datos[this.index].estado = "1";
			} catch (e) {}
			console.log(data);
			console.log(this.datos.relaciones);
			this.datos.relaciones = data.datos;
			/*
			if (data.bul)
				if (this.index != -1)
					if (data.datos.estado == '1')
						this.datos.relaciones[this.index] = data.datos;
					else
						this.datos.relaciones.splice(this.index, 1);
				else
					this.datos.relaciones.push(data.datos);
			*/
			this.$bvModal.hide('modal-relaciones');
				console.log(data);
				console.log(this.datos.relaciones);
		},
		cargarRelaciones: function(){
			var that = this;
			this._axios('/mapaprocesosver/relaciones/listar', {datosmapa: this.datosmapa}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.datos.relaciones = res.datos;
					that.cargarGrafico();
				}
			});
		},
	},
	mounted() {
		this.cargarRelaciones();
	}
}
</script>
