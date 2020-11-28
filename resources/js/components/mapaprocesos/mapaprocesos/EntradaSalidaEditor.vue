<template>
	<div class="container">
		<b-col sm="12" md="12">
			<b-form-group :label="tipo_" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="obj.nombre" :state="!$v.obj.nombre.$invalid"></b-form-input>
				<div class="text-danger" v-if="!$v.obj.nombre.required">Por favor ingrese el nombre del proceso</div>
				<div class="text-danger" v-if="!$v.obj.nombre.nombreentradasalida">Por favor, asegurese de escribir bien el nombre del proceso</div>
				<div class="text-danger" v-if="!$v.obj.nombre.minLength">Debe ingresar 4 caracteres como mínimo</div>
				<div class="text-danger" v-if="!$v.obj.nombre.maxLength">Debe ingresar 50 caracteres como máximo</div>
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
import { nombreentradasalida } from '../../_expresiones_regulares.js'
export default {
	components: {
		CoolSelect
	},
	props: {
		tipo: String,
		datosmapa: Object,
		entradasalida: Object,
		datostodos: Object,/////////////////////
	},
	data() {
		return {
			tipo_: '',
			obj: {
				nombre: '',
			},
			ruta: '',
		}
	},
	validations: {
		obj: {
			nombre: {
				required,
				nombreentradasalida,
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
				this._mensaje_error('Por favor, revise el formulario y corrija el campo incorrecto');
				return;
			}
			if (this.obj.idEntradaSalida == null){
				var bul = this._verificar_duplicados(this.datostodos.entradas, 'nombre', this.obj.nombre);
				bul = bul || this._verificar_duplicados(this.datostodos.salidas, 'nombre', this.obj.nombre);
				if (bul){
					this._mensaje_error("Ya hay una entrada o salida con el nombre indicado, por favor verifique el dato que desea ingresar");
					return;
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
		this.tipo_ = this.tipo .toLowerCase();
		this.tipo_ = this.tipo_ .charAt(0).toUpperCase() + this.tipo_ .slice(1);
		//console.log(this.datosmapa);
		if (this.tipo_ == 'Entrada')
			this.ruta = '/mapaprocesosver/entradas/';
		else if (this.tipo_ == 'Salida')
			this.ruta = '/mapaprocesosver/salidas/';
		else
			location.reload();
		if (this.entradasalida != null){
			this.obj = {...this.entradasalida};
			if (this.obj.estado == '0')
				this.mantenedor();
		}
		//console.log(this.ruta);
	}
}
</script>
