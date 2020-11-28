<template>
	<div>
		<b-form-group label="Procesos a Priorizar" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
			<b-row>
				<b-col md="12" sm="12">
					<b-form-input :disabled="datosmapa.priorizado==true" @change="cambioCantidad" v-model="cantidadpriorizar" type="number"></b-form-input>
				</b-col>
				<b-col md="3" sm="3" class="p-1" hidden>
					<b-button variant="success" size="sm" @click="guardarCambios">Actualizar</b-button>
				</b-col>
			</b-row>
		</b-form-group>
		<table class="table table-sm table-hover">
			<thead>
				<tr>
					<th>Proceso</th>
					<th v-for="(crit, icrit) in listaCriterios">{{crit.nombre}}</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(proc, iproc) in listaProcesos">
					<td>{{proc.nombre}}</td>
					<td v-for="(proccrit, iproccrit) in proc.criterios">
						<cool-select :disabled="datosmapa.priorizado==true" @select="actualizarTotales()" v-model="proccrit.seleccionado" :items="proccrit.puntajes" item-text="text" item-value="value"></cool-select>
					</td>
					<td>{{proc.total}}</td>
				</tr>
			</tbody>
		</table>
		<div class="text-center">
			<b-button v-if="datosmapa.priorizado==false" @click="guardarCambios" variant="success">Guardar Cambios</b-button>
			<br><br>
			<b-button @click="botonpdf">Descargar en PDF</b-button>
			<br><br>
			<b-button v-if="datosmapa.priorizado==false" @click="finalizarPriorizacion" variant="primary">Finalizar Priorización</b-button>
			<span v-else class="text-danger">La priorización de procesos ya se ha realizado</span>
			<br><br>
		</div>
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
		datos: Object,
		objprocesos: Object,
	},
	data() {
		return {
			cantidadpriorizar: 1,
			listaCriterios: [],
			listaProcesos: [],
			guardocambios: true,
			camposvacios: true,
		}
	},
	methods:{
		botonpdf: function(){
			//
			var priorizados = this.datos.priorizados;
			//
			if (this.camposvacios == true){
				this._mensaje_error('Hay campos vacíos, por favor llene los campos');
				return;
			}
			if (this.guardocambios != true){
				this._mensaje_error('Por favor, primero haga click en el botón "Guardar Cambios"');
				return;
			}
			const  pdf = new jsPDF();
			pdf.setFontSize(18);
			pdf.text("Matriz de Priorización", 80, 15);
			pdf.setFontSize(13);
			pdf.text("Nota: Los procesos priorizados son de color marrón", 15, 25);
			//columnas
			var columns = ["Proceso"]
			this.listaCriterios.forEach((item, i) => {
				columns.push(item.nombre);
			});
			columns.push("Total");
			//rows
			var body = [];
			this.listaProcesos.forEach((item, i) => {
				var temp = [];
				temp.push(item.nombre);
				item.criterios.forEach((item2, i2) => {
					temp.push(item2.seleccionado);
				});
				temp.push(item.total);
				temp.push({
					styles: { halign: 'center', fillColor: [0, 255, 0] }
				})
				body.push(temp);
			});
			pdf.autoTable({
				startY: 35,
  				styles: { halign: 'center' },
				//columnStyles: { 0: { halign: 'center' } },
				columns: columns,
				body: body,
				didParseCell: function (data) {
					var bul = false;
					priorizados.forEach((p) => {
						if (data.row.raw[0] == p.nombre)
							bul = true;
					});
					if (bul) {
						//data.cell.styles.fillColor = [41, 128, 186];
						data.cell.styles.fillColor = [186, 99, 41];
						data.cell.styles.textColor = [255, 255, 255];
					}
				},
			});
			pdf.save('priorizacion.pdf');
		},
		finalizarPriorizacion: function(){
			if (this.camposvacios == true){
				this._mensaje_error('Hay campos vacíos, por favor llene los campos');
				return;
			}
			if (this.guardocambios != true){
				this._mensaje_error('Por favor, primero haga click en el botón "Guardar Cambios"');
				return;
			}
			if (this.cantidadpriorizar > this.listaProcesos.length || this.cantidadpriorizar <= 0){
				this._mensaje_error('Asegurese que la cantidad de procesos a priorizar sea mayor a cero y menor a la cantidad de procesos que se tienen por priorizar');
				return;
			}
			var that = this;
			this._axios('/mapaprocesosver/finalizarpriorizar', {datosmapa: this.datosmapa}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that._mensaje_exito('La priorización se ha finalizado exitosamente, a partir de ahora ya podrá registrar la caracterizacion, diagrama de flujo y seguimiento de actividades de los procesos priorizados');
					that.datosmapa.priorizado = res.datos.mapaprocesos.priorizado;
					that.datos.priorizados = res.datos.listapriorizados;
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
		guardarCambios: function(){
			var that = this;
			this._axios('/mapaprocesosver/priorizacion/setupdel', {
				datosmapa: this.datosmapa,
				listaProcesos: this.listaProcesos,
				cantidadpriorizar: this.cantidadpriorizar
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that._mensaje_exito('Los datos se han guardado correctamente');
					that.datosmapa.cantidadPriorizar = res.datos.mapaprocesos.cantidadPriorizar;
					that.cantidadpriorizar = res.datos.mapaprocesos.cantidadPriorizar;
					that.datos.priorizados = res.datos.listapriorizados;
					that.guardocambios = true;
					that.datos.matriz = res.datos.matriz;
					//diseñar matriz
					this.diesenarmatriz();
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
		cambioCantidad: function(){
			this.guardocambios = false;
		},
		actualizarTotales: function(){
			this.guardocambios = false;
			//console.log(this.listaProcesos);
			this.camposvacios = false;
			this.listaProcesos.forEach((item, i) => {
				var suma = 0;
				item.criterios.forEach((item2, i2) => {
					suma += parseInt(item2.seleccionado || 0);
					console.log(item2.seleccionado=="");
					this.camposvacios = this.camposvacios || item2.seleccionado=="";
				});
				this.listaProcesos[i].total = parseInt(suma);
			});
			//console.log(this.listaProcesos);
			this.$forceUpdate();
		},
		diesenarmatriz: function(){
			//cargo la lista de procesos
			this.listaProcesos = [];
			if (this.objprocesos.apoyo != null)
				this.objprocesos.apoyo.forEach((item, i) => {
					this.listaProcesos.push(item);
				});
			if (this.objprocesos.estrategico != null)
				this.objprocesos.estrategico.forEach((item, i) => {
					this.listaProcesos.push(item);
				});
			if (this.objprocesos.primarios != null)
				this.objprocesos.primarios.forEach((item, i) => {
					if (item.idProcesoAnterior == null)
						this.listaProcesos.push(item);
				});
			//cargo la lista de criterios
			this.listaCriterios = [];
			this.listaCriterios = [...this.datos.criterios];
			//hago el cruce
			try {
				this.listaProcesos.forEach((item1, i1) => {
					this.listaProcesos[i1].total = 0;
					this.listaProcesos[i1].criterios = [];
					this.listaCriterios.forEach((item2, i2) => {
						var listado = [];
						for (var i = item2.valmin; i <= item2.valmax; i++) {
							listado.push({
								value: i * item2.peso,
								text: i,
							});
						}
						this.listaProcesos[i1].criterios.push({
							idCriteriosPriorizacion: item2.idCriteriosPriorizacion,
							peso: item2.peso,
							seleccionado: '',
							puntajes: listado,
						});
					});
				});
				//console.log(this.listaProcesos);
			}
			catch (e) {
				console.log(e);
			}
			finally {}
			//pongo los puntajes d ela bd al formulario
			let aux = true;
			this.listaProcesos.forEach((item1, i1) => {
				item1.criterios.forEach((item2, i2) => {
					this.datos.matriz.forEach((item3, i3) => {
						aux = true;
						aux &= (item3.idProceso == item1.idProceso);
						aux &= (item3.idCriteriosPriorizacion == item2.idCriteriosPriorizacion);
						if (aux){
							this.listaProcesos[i1].criterios[i2].seleccionado = item2.peso * item3.puntaje;
						}
					});
				});
			});
		},
	},
	mounted() {
		////////////
		//console.log(this.datosmapa);
		this.cantidadpriorizar = this.datosmapa.cantidadPriorizar;
		////////////
		//diseñar matriz
		this.diesenarmatriz();
		//actualizo totales
		this.actualizarTotales();
		this.guardocambios = true;
	}
}
</script>
