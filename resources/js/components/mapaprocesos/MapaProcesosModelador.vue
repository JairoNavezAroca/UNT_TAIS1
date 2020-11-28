<template>
	<div>
		<div class="border text-center pl-5 pr-5 pt-2 pb-2">
			<span>Click acá para ver las opciones</span><br>
			<!--
			class = ml-3 mr-3 mt-2 mb-2
			cambiar el size sm por size md
			-->
			<b-button class="btn" size="sm" :pressed.sync="coleccionBotones[0]" @click="selecciona(0)" variant="outline-secondary">Mapa de Procesos</b-button>
			<b-button class="btn" size="sm" :pressed.sync="coleccionBotones[1]" @click="selecciona(1)" variant="outline-secondary">Matriz de Priorización</b-button>
			<b-button class="btn" size="sm" :pressed.sync="coleccionBotones[2]" @click="selecciona(2)" variant="outline-secondary">Caracterización de Procesos</b-button>
			<br>
			<b-button class="btn" size="sm" :pressed.sync="coleccionBotones[3]" @click="selecciona(3)" variant="outline-secondary">Diagrama de Flujo de Procesos</b-button>
			<b-button class="btn" size="sm" :pressed.sync="coleccionBotones[4]" @click="selecciona(4)" variant="outline-secondary">Diagrama de Seguimiento de Actividades de Procesos</b-button>
			<br>
			<b-button class="btn" size="sm" :pressed.sync="coleccionBotones[5]" @click="selecciona(5)" variant="outline-secondary">Indicadores de Desempeño</b-button>
			<b-button class="btn" size="sm" :pressed.sync="coleccionBotones[6]" @click="selecciona(6)" variant="outline-secondary">Mapa Estratégico</b-button>
			<b-button class="btn" size="sm" :pressed.sync="coleccionBotones[7]" @click="selecciona(7)" variant="outline-secondary">Tableros de Control</b-button>
			<!--<b-button class="btn" size="sm" :pressed.sync="coleccionBotones[5]" @click="selecciona(5)" variant="outline-secondary">Guardar en Histórico</b-button>-->
			<b-button class="btn" size="sm" variant="outline-secondary" @click="gohistorico">Guardar en Histórico</b-button>
		</div>
		<b-modal id="modal-historico" size="md" scrollable centered hide-backdrop title="Guardar en Histórico" hide-footer><b-form-group label="Descripción" label-cols="12" label-align="center" label-size="md" class="mb-1">
			<b-form-textarea placeholder="Este campo es opcional" v-model="detalle" rows="6"></b-form-textarea>
			<div class="text-center">
				<b-button @click="cerrarmodal" variant="danger">Cancelar</b-button>
				<b-button @click="guardarhistorico" variant="success">Guardar</b-button>
			</div>
		</b-form-group>
			<!--
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
			-->
		</b-modal>
		<br><br>
		<div class="border text-center pl-5 pr-5 pt-2 pb-2">
			<!--
			<span>Seleccionado</span><br>
			-->
			<div v-if="coleccionBotones[0]">
				<!--<b-button class="ml-3 mr-3 mt-2 mb-2" pressed.sync="coleccionBotones[0]" @click="selecciona(0)" size="md" variant="outline-secondary">Mapa de Procesos</b-button>-->
				<span>Mapa de Procesos</span>
				<mapaprocesos-mapaprocesos :relacionpuntajes="priorizacion_2.matriz" :datosmapa="datosmapa" :datos="mapa_1"></mapaprocesos-mapaprocesos>
			</div>
			<div v-if="coleccionBotones[1]">
				<!--<b-button class="ml-3 mr-3 mt-2 mb-2" pressed.sync="coleccionBotones[1]" @click="selecciona(1)" size="md" variant="outline-secondary">Matriz de Priorización</b-button>-->
				<span>Matriz de Priorización</span>
				<mapaprocesos-matrizpriorizacion :datosmapa="datosmapa" :datos="priorizacion_2" :objprocesos="mapa_1"></mapaprocesos-matrizpriorizacion>
			</div>
			<div v-if="coleccionBotones[2]">
				<!--<b-button class="ml-3 mr-3 mt-2 mb-2" pressed.sync="coleccionBotones[2]" @click="selecciona(2)" size="md" variant="outline-secondary">Caracterización de Procesos</b-button>-->
				<span>Caracterización de Procesos</span>
				<mapaprocesos-caracterizacion :datosmapa="datosmapa" :procpriorizados="priorizacion_2.priorizados"></mapaprocesos-caracterizacion>
			</div>
			<div v-if="coleccionBotones[3]">
				<!--<b-button class="ml-3 mr-3 mt-2 mb-2" pressed.sync="coleccionBotones[3]" @click="selecciona(3)" size="md" variant="outline-secondary">Diagrama de Flujo de Procesos</b-button>-->
				<span>Diagrama de Flujo de Procesos</span>
				<mapaprocesos-diagramaflujo :datosmapa="datosmapa" :procpriorizados="priorizacion_2.priorizados"></mapaprocesos-diagramaflujo>
			</div>
			<div v-if="coleccionBotones[4]">
				<!--<b-button class="ml-3 mr-3 mt-2 mb-2" pressed.sync="coleccionBotones[4]" @click="selecciona(4)" size="md" variant="outline-secondary">Diagrama de Seguimiento de Actividades de Procesos</b-button>-->
				<span>Diagrama de Seguimiento de Actividades de Procesos</span>
				<mapaprocesos-diagramaseguimiento :datosmapa="datosmapa" :procpriorizados="priorizacion_2.priorizados"></mapaprocesos-diagramaseguimiento>
			</div>
			<div v-if="coleccionBotones[5]">
				<span>Indicadores de Desempeño</span>
				<mapaprocesos-indicadoresdesempenio :datosmapa="datosmapa" :procpriorizados="priorizacion_2.priorizados"></mapaprocesos-indicadoresdesempenio>
			</div>
			<div v-if="coleccionBotones[6]">
				<span>Mapa Estratégico</span>
				<mapaprocesos-mapaestrategico :datosmapa="datosmapa" :procpriorizados="priorizacion_2.priorizados"></mapaprocesos-mapaestrategico>
			</div>
			<div v-if="coleccionBotones[7]">
				<span>Tablero de Control</span>
				<mapaprocesos-tablerocontrol :datosmapa="datosmapa" :procpriorizados="priorizacion_2.priorizados"></mapaprocesos-tablerocontrol>
			</div>

			<!--
			<div v-if="coleccionBotones[5]">
				<b-button class="ml-3 mr-3 mt-2 mb-2" pressed.sync="coleccionBotones[5]" @click="selecciona(5)" size="md" variant="outline-secondary">Guardar en Histórico</b-button>
			</div>
			-->
		</div>
	</div>
</template>

<script>
export default {
	data() {
		return {
			detalle: '',
			coleccionBotones: [false, false, false, false, false, false, false, true, false],
			datosmapa: { //datos generales del mapa de procesos
				priorizado: false
			},
			mapa_1: { //datos del 1 - mapa de procesos
				entradas: null,
				salidas: null,
				primarios: null,
				estrategico: null,
				apoyo: null,
				relaciones: [],
			},
			priorizacion_2: { //datos del 2 - matriz de priorizacion
				criterios: null,
				matriz: null,
				priorizados: null,
			},
		}
	},
	methods:{
		guardarhistorico: function(){
			var that = this;
			this._axios('/mapaprocesosver/historico', { idMapaProcesos: this.datosmapa.idMapaProcesos, descripcion: this.detalle }, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that._mensaje_exito("Se ha generado el Histórico");
					location.href = "/mapaprocesos";
				}
				else{
					that._mensaje_error("Error");
				}
			});
		},
		cerrarmodal: function(){
			this.$bvModal.hide('modal-historico');
		},
		gohistorico: function(){
			console.log(this.datosmapa.priorizado);
			if (this.datosmapa.priorizado == false){
				this._mensaje_error("El mapa de procesos no ha sido priorizado");
				return;
			}
			if (this.datosmapa.estado2 == "H"){
				this._mensaje_error("El mapa de procesos ya pertenece a un histórico");
				return;
			}
			this.$bvModal.show('modal-historico');
		},
		selecciona: function(index){
			//console.log(this.coleccionBotones);
			this.coleccionBotones = this.coleccionBotones.map((item)=>{
				return false;
			});
			this.coleccionBotones[index] = true;
			//console.log(this.coleccionBotones);
		},
		cargarDatos(){
			var that = this;
			this._axios('/mapaprocesosver/mapaprocesos', { idMapaProcesos: this.datosmapa.idMapaProcesos}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.mapa_1.entradas = res.datos.entradas;
					that.mapa_1.salidas = res.datos.salidas;
					that.mapa_1.primarios = res.datos.primarios;
					that.mapa_1.estrategico = res.datos.estrategico;
					that.mapa_1.apoyo = res.datos.apoyo;
					that.mapa_1.relaciones = res.datos.relaciones;
					//
					that.priorizacion_2.criterios = res.datos.criterios;
					that.priorizacion_2.matriz = res.datos.matriz;
					that.priorizacion_2.priorizados = res.datos.priorizados;
				}
			});
		},
	},
	mounted() {
		this.datosmapa = {...elemento};
		console.log(this.datosmapa);
		this.cargarDatos();
		document.addEventListener('goPriorizacion', (datos) => {
			this.selecciona(1);
		});
	}
}
</script>
