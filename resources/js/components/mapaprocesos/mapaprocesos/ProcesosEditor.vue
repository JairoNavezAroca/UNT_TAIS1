<template>
	<div class="container">
		<b-col sm="12" md="12">
			<b-form-group :label="'Proceso '+tipo" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="obj.nombre" placeholder="Nombre del proceso" :state="!$v.obj.nombre.$invalid"></b-form-input>
				<div class="text-danger" v-if="!$v.obj.nombre.required">Por favor ingrese el nombre del proceso</div>
				<div class="text-danger" v-if="!$v.obj.nombre.nombreproceso">Por favor, asegurese de escribir bien el nombre del proceso</div>
				<div class="text-danger" v-if="!$v.obj.nombre.minLength">Debe ingresar 4 caracteres como mínimo</div>
				<div class="text-danger" v-if="!$v.obj.nombre.maxLength">Debe ingresar 50 caracteres como máximo</div>
			</b-form-group>
			<b-form-group :hidden="tipo!='primario'" label="Tipo" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<cool-select @select="" :successful="Boolean('true')" :disabled="obj.idProceso!=null" v-model="obj.tipo" :items="listatiposprocesos" item-text="text" item-value="value" placeholder="Seleccione Tipo"></cool-select>
			</b-form-group>
			<b-form-group :hidden="obj.tipo=='P'||tipo!='primario'" label="Asociado a" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<cool-select @select="" :successful="obj.idProcesoAnterior!=''" v-model="obj.idProcesoAnterior" :items="listaprocesos_" item-text="nombre" item-value="idProceso" placeholder="Seleccione Proceso"></cool-select>
				<div class="text-danger" v-if="obj.idProcesoAnterior==''">Por favor, seleccione un proceso</div>
			</b-form-group>
		</b-col>
		<div class="text-center">
			<b-button @click="enviarCerrarModal(false)" variant="danger">Cancelar</b-button>
			<b-button @click="mantenedor" variant="success">Guardar</b-button>
		</div>
	</div>
</template>

<script>
import { CoolSelect } from 'vue-cool-select'
import { required, minLength, maxLength } from 'vuelidate/lib/validators'
import { nombreproceso } from '../../_expresiones_regulares.js'
export default {
	components: {
		CoolSelect
	},
	props: {
		tipo: String,
		datosmapa: Object,
		infoproceso: Object,
		listaprocesos: Array,
		datostodos: Object,/////////////////////
		relacionpuntajes: Array,
	},
	data() {
		return {
			listatiposprocesos: [
				{ value: 'P', text: 'Proceso' },
				{ value: 'S', text: 'Sub-Proceso' },
			],
			listaprocesos_: [],
			obj: {
				nombre: '',
				tipo: 'P',
				idProcesoAnterior: '',
			},
			ruta: '',
		}
	},
	validations: {
		obj: {
			nombre: {
				required,
				nombreproceso,
				minLength: minLength(4),
				maxLength: maxLength(50),
			}
		}
	},
	methods: {
		enviarCerrarModal: function(bul, datos){
			this.$emit('cerrarModal', {bul:bul, datos:datos});
		},
		mantenedor: function(){
			if (this.$v.obj.nombre.$invalid){
				this._mensaje_error('Por favor, revise el formulario y corrija los campos incorrectos');
				return;
			}
			if (this.obj.idProcesoAnterior=='' && this.obj.tipo!='P'){
				this._mensaje_error('Por favor, ingrese el proceso al que está asociado el proceso que desea registrar');
				return;
			}
			if (this.obj.idProceso == null){
				var bul = this._verificar_duplicados(this.datostodos.estrategico, 'nombre', this.obj.nombre);
				bul = bul || this._verificar_duplicados(this.datostodos.primarios, 'nombre', this.obj.nombre);
				bul = bul || this._verificar_duplicados(this.datostodos.apoyo, 'nombre', this.obj.nombre);
				if (bul){
					this._mensaje_error("Ya hay un proceso registrado con ese nombre, por favor verifique el dato que desea ingresar");
					return;
				}
			}
			//eliminar
			if (this.obj.estado == '0' && this.obj.idProcesoAnterior == null){
				var bul2 = false;
				console.log(this.datostodos.primarios);
				if (this.datostodos.primarios.length > 0){
					this.datostodos.primarios.forEach((item, i) => {
						if (item.idProcesoAnterior == this.obj.idProceso)
							bul2 = true;
					});
					if (bul2){
						this._mensaje_error("Por favor, asegurese de eliminar los subprocesos asociados a este proceso");
						var that = this;
						setTimeout(function(){ that.enviarCerrarModal(false, null); }, 1000);
						return;
					}
				}
			}
			if (this.obj.estado == '0'){
				console.log(this.datostodos);
				var bul3 = this._verificar_duplicados(this.datostodos.relaciones, 'idProceso_desde', this.obj.idProceso);
				bul3 = bul3 || this._verificar_duplicados(this.datostodos.relaciones, 'idProceso_hasta', this.obj.idProceso);
				if (bul3){
					var that = this;
					setTimeout(function(){ that.enviarCerrarModal(false, null); }, 1000);
					this._mensaje_error("Este proceso se encuentra relacionado con algún otro, por favor primero elimine la relacion");
					return;
				}
				console.log(this.relacionpuntajes);
				var bul4 = false;
				if (this.relacionpuntajes != null){
					this.relacionpuntajes.forEach((item, i) => {
						if (item.idProceso == this.obj.idProceso)
							bul4 = true;
					});
					if (bul4){
						var that = this;
						setTimeout(function(){ that.enviarCerrarModal(false, null); }, 1000);
						this._mensaje_error("El proceso que desea eliminar, tiene asignado un puntaje, no es posible eliminar");
						return;
					}
				}
			}
			var that = this;
			this._axios(this.ruta+'setupdel', {obj: this.obj, datosmapa: this.datosmapa}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that._mensaje_exito('Los datos se han guardado correctamente');
					that.enviarCerrarModal(true, res.datos);
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
	},
	mounted() {
		if (this.tipo == 'estrategico')
			this.ruta = '/mapaprocesosver/pestrategicos/';
		else if (this.tipo == 'de apoyo')
			this.ruta = '/mapaprocesosver/papoyo/';
		else if (this.tipo == 'primario')
			this.ruta = '/mapaprocesosver/pprimarios/';
		if (this.infoproceso != null){
			this.obj = {...this.infoproceso};
			console.log(this.obj)
			//si estado = 0, entonces eliminar
			if (this.obj.estado == '0')
				this.mantenedor();
			//verificar si es proceso o subproceso
			if (this.obj.idProcesoAnterior == null)
				this.obj.tipo = 'P';
			else
				this.obj.tipo = 'S';
		}
		//cargar lista de procesos
		this.listaprocesos.forEach((item, i) => {
			if (item.idProcesoAnterior == null)
				this.listaprocesos_.push(item);
		});
	}
}
</script>
