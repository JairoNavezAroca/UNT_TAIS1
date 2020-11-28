<template>
	<div class="container">
		<b-col sm="12" md="12">
			<b-form-group label="Nombre del indicador" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="obj.nombre" :state="!$v.obj.nombre.$invalid"></b-form-input>
				<div class="text-danger" v-if="!$v.obj.nombre.required">Por favor ingrese el nombre del indicador</div>
				<div class="text-danger" v-if="!$v.obj.nombre.nombreobjestrategico">Por favor, asegurese de escribir bien el nombre del indicador</div>
				<div class="text-danger" v-if="!$v.obj.nombre.minLength">Debe ingresar 4 caracteres como mínimo</div>
				<div class="text-danger" v-if="!$v.obj.nombre.maxLength">Debe ingresar 50 caracteres como máximo</div>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12">
			<b-form-group label="Aspecto" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<cool-select @select="seleccionaAspecto" v-model="obj.aspecto" :items="listaAspectos" item-text="text" item-value="text" size="sm"></cool-select>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12" :hidden="listaRelacion.length==0">
			<b-form-group label="Relacionado a(viene de)" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<cool-select @select="" v-model="obj.idAnterior" :items="listaRelacion" item-text="nombre" item-value="idDetalleMapaEstrategico" size="sm"></cool-select>
			</b-form-group>
		</b-col>
		<br>
		<div class="text-center">
			<b-button @click="enviarCerrarModal(false)" variant="danger">Cancelar</b-button>
			<b-button @click="mantenedor" variant="success">Guardar</b-button>
		</div>
	</div>
</template>

<script>
import { CoolSelect } from 'vue-cool-select'
import { required, minLength, maxLength } from 'vuelidate/lib/validators'
import { nombreobjestrategico } from '../../_expresiones_regulares.js'
export default {
	components: {
		CoolSelect
	},
	props: {
		datosmapa: Object,
		objmaestro: Object,
		objmapaest: Object,
		elementoeditar: Object,
		listado: Array,
		/*
		tipo: String,
		entradasalida: Object,
		datostodos: Object,/////////////////////
		*/
	},
	data() {
		return {
			ruta: '',
			//tipo_: '',
			obj: {
				nombre: '',
				aspecto: '',
				idAnterior: '',
			},
			orden: '',
			listaAspectos: [],
			listaRelacion: [],
		}
	},
	validations: {
		obj: {
			nombre: {
				required,
				nombreobjestrategico,
				minLength: minLength(4),
				maxLength: maxLength(50),
			},
		}
	},
	methods: {
		seleccionaAspecto: function(){
			console.log(this.listado);
			var pos;
			this.listado.forEach((item, i) => {
				console.log(item.aspecto + '___' + this.obj.aspecto)
				if (item.aspecto == this.obj.aspecto)
					pos = i;
			});
			console.log(pos);
			pos++;
			this.listaRelacion = [];
			if (pos >= 4)
				return;
			this.listado[pos].objetivos.forEach((item, i) => {
				this.listaRelacion.push(item);
			});
		},
		enviarCerrarModal: function(bul, datos){
			this.$emit('cerrarModal', {bul:bul, datos:datos});
		},
		mantenedor: function(){
			if (this.$v.obj.$invalid){
				this._mensaje_error('Por favor, revise el formulario y corrija el campo incorrecto');
				return;
			}
			/* VALIDACION
			if (this.obj.idEntradaSalida == null){
				var bul = this._verificar_duplicados(this.datostodos.entradas, 'nombre', this.obj.nombre);
				bul = bul || this._verificar_duplicados(this.datostodos.salidas, 'nombre', this.obj.nombre);
				if (bul){
					this._mensaje_error("Ya hay una entrada o salida con el nombre indicado, por favor verifique el dato que desea ingresar");
					return;
				}
			}
			*/
			var that = this;
			this._axios(this.ruta, {
				obj: this.obj, objmaestro:this.objmaestro,
				datosmapa: this.datosmapa, objmapaest: this.objmapaest
			}, function(res){
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
		this.listaAspectos = this._aspectos_mapa_estrategico(this.objmapaest.orden_aspectos);
		this.ruta = '/mapaprocesosver/mapaestrategico/setupdel';
		if (this.elementoeditar != null){
			this.obj = {...this.elementoeditar};
			if (this.obj.estado == '0')
				this.mantenedor();
		}
		/*
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
		*/
	}
}
</script>
