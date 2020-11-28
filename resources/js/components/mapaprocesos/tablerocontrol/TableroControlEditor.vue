<template>
	<div class="container">
		<b-col sm="12" md="12">
			<b-form-group label="Objetivo" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-textarea v-model="obj.objetivo" :state="!$v.obj.objetivo.$invalid" rows="2"></b-form-textarea>
				<div class="text-danger" v-if="!$v.obj.objetivo.required">Este campo es requerido</div>
				<div class="text-danger" v-if="!$v.obj.objetivo.nombretablero">Por favor, asegurese de escribir bien el nombre del indicador</div>
				<div class="text-danger" v-if="!$v.obj.objetivo.minLength">Debe ingresar 4 caracteres como mínimo</div>
				<div class="text-danger" v-if="!$v.obj.objetivo.maxLength">Debe ingresar 50 caracteres como máximo</div>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12">
			<b-form-group label="Indicador" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<cool-select @select="" v-model="obj.idDetalleIndicadorDesempenio" :items="indicador_desempenio" item-text="nombre" item-value="idDetalleIndicadorDesempenio"></cool-select>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12">
			<b-form-group label="Linea base" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="obj.lineabase" :state="!$v.obj.lineabase.$invalid" type="number"></b-form-input>
				<div class="text-danger" v-if="!$v.obj.lineabase.required">Este campo es requerido</div>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12">
			<b-form-group label="Meta" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="obj.meta" :state="!$v.obj.meta.$invalid" type="number"></b-form-input>
				<div class="text-danger" v-if="!$v.obj.meta.required">Este campo es requerido</div>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12">
			<b-form-group label="Responsable" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="obj.responsable" :state="!$v.obj.responsable.$invalid"></b-form-input>
				<div class="text-danger" v-if="!$v.obj.responsable.required">Este campo es requerido</div>
				<div class="text-danger" v-if="!$v.obj.responsable.nombretablero">Por favor, asegurese de escribir bien el puesto responsable</div>
				<div class="text-danger" v-if="!$v.obj.responsable.minLength">Debe ingresar 4 caracteres como mínimo</div>
				<div class="text-danger" v-if="!$v.obj.responsable.maxLength">Debe ingresar 50 caracteres como máximo</div>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12">
			<b-form-group label="Iniciativas" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-textarea v-model="obj.iniciativas" :state="!$v.obj.iniciativas.$invalid" rows="2"></b-form-textarea>
				<div class="text-danger" v-if="!$v.obj.iniciativas.required">Este campo es requerido</div>
				<div class="text-danger" v-if="!$v.obj.iniciativas.descripciontablero">Por favor, asegurese de escribir bien las iniciativas</div>
				<div class="text-danger" v-if="!$v.obj.iniciativas.minLength">Debe ingresar 4 caracteres como mínimo</div>
				<div class="text-danger" v-if="!$v.obj.iniciativas.maxLength">Debe ingresar 50 caracteres como máximo</div>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12">
			<b-form-group label="Semáforo luz roja" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-row>
					<b-col sm="6" md="6">
						<b-form-select @change="estadoSemaforo('I')" v-model="obj.luz_roja_signo" :options="luz_opciones"></b-form-select>
					</b-col>
					<b-col sm="6" md="6">
						<b-form-input type="number" v-model="obj.luz_roja_valor"></b-form-input>
					</b-col>
				</b-row>
			</b-form-group>
		<b-col sm="12" md="12">
		</b-col>
			<b-form-group label="Semáforo luz amarilla" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-row>
					<b-col sm="6" md="6">
						<b-form-select v-model="obj.luz_amarilla_signo" :options="['Entre']"></b-form-select>
					</b-col>
					<b-col sm="6" md="6">
						<b-form-input type="text" :value="obj.luz_roja_valor+' - '+obj.luz_verde_valor"></b-form-input>
					</b-col>
				</b-row>
			</b-form-group>
		<b-col sm="12" md="12">
		</b-col>
			<b-form-group label="Semáforo luz verde" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-row>
					<b-col sm="6" md="6">
						<b-form-select @change="estadoSemaforo('D')" v-model="obj.luz_verde_signo" :options="luz_opciones"></b-form-select>
					</b-col>
					<b-col sm="6" md="6">
						<b-form-input type="number" v-model="obj.luz_verde_valor"></b-form-input>
					</b-col>
				</b-row>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12">
			<b-form-group label="Relacionado al objetivo estratégico" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<cool-select @select="" v-model="obj.idDetalleMapaEstrategico" :items="mapa_estrategico" item-text="nombre" item-value="idDetalleMapaEstrategico"></cool-select>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12">
			<b-form-group label="Frecuencia de medición" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<cool-select @select="" v-model="obj.frecuenciamedicion" :items="listaFrecuencias" item-text="text" item-value="text"></cool-select>
			</b-form-group>
		</b-col>

		<!--
		<b-col sm="12" md="12">
			<b-form-group label="Puesto que evalúa" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="obj.puesto" :state="!$v.obj.puesto.$invalid"></b-form-input>
				<div class="text-danger" v-if="!$v.obj.puesto.required">Por favor ingrese el puesto que evalua</div>
				<div class="text-danger" v-if="!$v.obj.puesto.nombretablero">Por favor, asegurese de escribir bien el puesto que evalua</div>
				<div class="text-danger" v-if="!$v.obj.puesto.minLength">Debe ingresar 4 caracteres como mínimo</div>
				<div class="text-danger" v-if="!$v.obj.puesto.maxLength">Debe ingresar 50 caracteres como máximo</div>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12">
			<b-form-group label="Mecanismos de medición" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-textarea :state="!$v.obj.mecanismosmed.$invalid" v-model="obj.mecanismosmed" rows="2"></b-form-textarea>
				<div class="text-danger" v-if="!$v.obj.mecanismosmed.required">Por favor llene este campo</div>
				<div class="text-danger" v-if="!$v.obj.mecanismosmed.descripciontablero">Por favor asegurese de escribir bien en este campo</div>
				<div class="text-danger" v-if="!$v.obj.mecanismosmed.minLength">Debe ingresar 4 caracteres como mínimo</div>
				<div class="text-danger" v-if="!$v.obj.mecanismosmed.maxLength">Debe ingresar 50 caracteres como máximo</div>
			</b-form-group>
		</b-col>
		-->
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
import { nombretablero, descripciontablero } from '../../_expresiones_regulares.js'
export default {
	components: {
		CoolSelect
	},
	props: {
		datosmapa: Object,
		objmaestro: Object,
		objtablero: Object,
		elementoeditar: Object,
		indicador_desempenio: Array,
		mapa_estrategico: Array,
		/*
		tipo: String,
		datosmapa: Object,
		entradasalida: Object,
		datostodos: Object,/////////////////////
		*/
	},
	data() {
		return {
			ruta: '',
			//tipo_: '',
			luz_opciones: [
				{
					value: '<',
					text: 'Menor a'
				},
				{
					value: '>',
					text: 'Mayor a'
				}
			],
			listaFrecuencias: [{text:'Anual'}, {text:'Mensual'}, {text:'Diario'}],
			obj: {
				objetivo: '',
				idDetalleIndicadorDesempenio: '',
				lineabase: '',
				meta: '',
				responsable: '',
				iniciativas: '',
				idDetalleMapaEstrategico: '',
				frecuenciamedicion: '',
				luz_roja_signo: '<',
				luz_roja_valor: '',
				luz_amarilla_signo: 'Entre',
				//luz_amarilla: '',
				luz_verde_signo: '>',
				luz_verde_valor: '',
			},
		}
	},
	validations: {
		obj: {
			objetivo: {
				required,
				nombretablero,
				minLength: minLength(4),
				maxLength: maxLength(50),
			},
			meta: {
				required,
			},
			lineabase: {
				required,
			},
			responsable: {
				required,
				nombretablero,
				minLength: minLength(4),
				maxLength: maxLength(50),
			},
			iniciativas: {
				required,
				descripciontablero,
				minLength: minLength(4),
				maxLength: maxLength(50),
			},
		}
	},
	methods: {
		estadoSemaforo: function(flag){
			if (flag == 'I'){
				if (this.obj.luz_roja_signo == '<')
					this.obj.luz_verde_signo = '>'
				if (this.obj.luz_roja_signo == '>')
					this.obj.luz_verde_signo = '<'
			}
			else if (flag == 'D'){
				if (this.obj.luz_verde_signo == '<')
					this.obj.luz_roja_signo = '>'
				if (this.obj.luz_verde_signo == '>')
					this.obj.luz_roja_signo = '<'
			}
		},
		enviarCerrarModal: function(bul, datos){
			this.$emit('cerrarModal', {bul:bul, datos:datos});
		},
		mantenedor: function(){
			if (this.$v.obj.$invalid){
				this._mensaje_error('Por favor, revise el formulario y corrija el campo incorrecto');
				return;
			}
			/*VALIDACION
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
				datosmapa: this.datosmapa, objtablero: this.objtablero
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
		this.ruta = '/mapaprocesosver/tablerocontrol/setupdel';
		if (this.elementoeditar != null){
			this.obj = {...this.elementoeditar};
			this.obj.luz_amarilla_signo = 'Entre';
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
