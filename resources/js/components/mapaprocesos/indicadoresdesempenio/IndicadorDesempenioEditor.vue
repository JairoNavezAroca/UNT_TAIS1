<template>
	<div class="container">
		<b-col sm="12" md="12">
			<b-form-group label="Nombre del indicador" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="obj.nombre" :state="!$v.obj.nombre.$invalid"></b-form-input>
				<div class="text-danger" v-if="!$v.obj.nombre.required">Por favor ingrese el nombre del indicador</div>
				<div class="text-danger" v-if="!$v.obj.nombre.nombreindicador">Por favor, asegurese de escribir bien el nombre del indicador</div>
				<div class="text-danger" v-if="!$v.obj.nombre.minLength">Debe ingresar 4 caracteres como mínimo</div>
				<div class="text-danger" v-if="!$v.obj.nombre.maxLength">Debe ingresar 50 caracteres como máximo</div>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12">
			<b-form-group label="Puesto que evalúa" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="obj.puesto" :state="!$v.obj.puesto.$invalid"></b-form-input>
				<div class="text-danger" v-if="!$v.obj.puesto.required">Por favor ingrese el puesto que evalua</div>
				<div class="text-danger" v-if="!$v.obj.puesto.nombreindicador">Por favor, asegurese de escribir bien el puesto que evalua</div>
				<div class="text-danger" v-if="!$v.obj.puesto.minLength">Debe ingresar 4 caracteres como mínimo</div>
				<div class="text-danger" v-if="!$v.obj.puesto.maxLength">Debe ingresar 50 caracteres como máximo</div>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12">
			<b-form-group label="¿Qué desea medir?" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-textarea :state="!$v.obj.medir.$invalid" v-model="obj.medir" rows="2"></b-form-textarea>
				<div class="text-danger" v-if="!$v.obj.medir.required">Por favor ingrese la respuesta</div>
				<div class="text-danger" v-if="!$v.obj.medir.descripcionindicador">Por favor, asegurese de escribir bien la respuesta</div>
				<div class="text-danger" v-if="!$v.obj.medir.minLength">Debe ingresar 4 caracteres como mínimo</div>
				<div class="text-danger" v-if="!$v.obj.medir.maxLength">Debe ingresar 50 caracteres como máximo</div>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12">
			<b-form-group label="Mecanismos de medición" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-textarea :state="!$v.obj.mecanismo.$invalid" v-model="obj.mecanismo" rows="2"></b-form-textarea>
				<div class="text-danger" v-if="!$v.obj.mecanismo.required">Por favor llene este campo</div>
				<div class="text-danger" v-if="!$v.obj.mecanismo.descripcionindicador">Por favor asegurese de escribir bien en este campo</div>
				<div class="text-danger" v-if="!$v.obj.mecanismo.minLength">Debe ingresar 4 caracteres como mínimo</div>
				<div class="text-danger" v-if="!$v.obj.mecanismo.maxLength">Debe ingresar 50 caracteres como máximo</div>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12">
			<b-form-group label="Tolerancia de desviación" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-textarea :state="!$v.obj.tolerancia.$invalid" v-model="obj.tolerancia" rows="2"></b-form-textarea>
				<div class="text-danger" v-if="!$v.obj.tolerancia.required">Por favor llene este campo</div>
				<div class="text-danger" v-if="!$v.obj.tolerancia.descripcionindicador">Por favor asegurese de escribir bien en este campo</div>
				<div class="text-danger" v-if="!$v.obj.tolerancia.minLength">Debe ingresar 4 caracteres como mínimo</div>
				<div class="text-danger" v-if="!$v.obj.tolerancia.maxLength">Debe ingresar 50 caracteres como máximo</div>
			</b-form-group>
		</b-col>
		<b-col sm="12" md="12">
			<b-form-group label="¿Qué se harán con los resultados?" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-textarea :state="!$v.obj.quehacer.$invalid" v-model="obj.quehacer" rows="2"></b-form-textarea>
				<div class="text-danger" v-if="!$v.obj.quehacer.required">Por favor llene este campo</div>
				<div class="text-danger" v-if="!$v.obj.quehacer.descripcionindicador">Por favor asegurese de escribir bien en este campo</div>
				<div class="text-danger" v-if="!$v.obj.quehacer.minLength">Debe ingresar 4 caracteres como mínimo</div>
				<div class="text-danger" v-if="!$v.obj.quehacer.maxLength">Debe ingresar 50 caracteres como máximo</div>
			</b-form-group>
		</b-col>
		<fieldset class="border pl-4 pr-4 pt-2 pb-2 m-2">
			<b-col sm="12" md="12">
				<h5>
					<b-form-group label="Fórmula" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
					</b-form-group>
				</h5>
			</b-col>
			<b-col sm="12" md="12">
				<b-form-group label="Nombre de la variable" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
					<b-row>
						<b-col sm="8" md="8">
							<b-form-input v-model="variable_nombre" maxLength="1"></b-form-input>
						</b-col>
						<b-col sm="4" md="4">
							<b-button variant="primary" @click="agregarVariable">Agregar variable</b-button>
						</b-col>
					</b-row>
				</b-form-group>
			</b-col>
			<b-col sm="12" md="12">
				<b-form-group label="Representa a" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
					<b-form-input v-model="variable_representa"></b-form-input>
				</b-form-group>
			</b-col>
			<b-col sm="12" md="12">
				<b-form-group label="Lista de variables" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
					<b-list-group>
						<b-list-group-item v-for="(item, index) in variables_lista" :key="index">
							{{item.variable_nombre}} -> {{item.variable_representa}}
							<b-button variant="danger" size="sm" @click="eliminarVariable(index)">Eliminar</b-button>
						</b-list-group-item>
					</b-list-group>
				</b-form-group>
			</b-col>
			<b-col sm="12" md="12">
				<b-form-group label="Formula" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
					<b-form-input v-model="formula"></b-form-input>
				</b-form-group>
			</b-col>
		</fieldset>
		<br>
		<div class="text-center">
			<b-button @click="enviarCerrarModal(false)" variant="danger">Cancelar</b-button>
			<b-button @click="mantenedor" variant="success">Guardar</b-button>
		</div>
	</div>
</template>

<script>
import { required, minLength, maxLength } from 'vuelidate/lib/validators'
import { nombreindicador, descripcionindicador } from '../../_expresiones_regulares.js'
export default {
	props: {
		datosmapa: Object,
		objmaestro: Object,
		objindicador: Object,
		elementoeditar: Object,
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
				puesto: '',
				medir: '',
				mecanismo: '',
				tolerancia: '',
				quehacer: '',
				formula: '',
			},
			formula: '',
			variable_nombre: '',
			variable_representa: '',
			variables_lista: [],
		}
	},
	validations: {
		obj: {
			nombre: {
				required,
				nombreindicador,
				minLength: minLength(4),
				maxLength: maxLength(50),
			},
			puesto: {
				required,
				nombreindicador,
				minLength: minLength(4),
				maxLength: maxLength(50),
			},
			medir: {
				required,
				descripcionindicador,
				minLength: minLength(4),
				maxLength: maxLength(50),
			},
			mecanismo: {
				required,
				descripcionindicador,
				minLength: minLength(4),
				maxLength: maxLength(50),
			},
			tolerancia: {
				required,
				descripcionindicador,
				minLength: minLength(4),
				maxLength: maxLength(50),
			},
			quehacer: {
				required,
				descripcionindicador,
				minLength: minLength(4),
				maxLength: maxLength(50),
			}
		}
	},
	methods: {
		eliminarVariable: function(index){
			this.variables_lista.splice(index, 1);
		},
		agregarVariable: function(){
			this.variables_lista.push({
				variable_nombre: this.variable_nombre,
				variable_representa: this.variable_representa,
			});
			this.variable_nombre = '';
			this.variable_representa = '';
		},
		enviarCerrarModal: function(bul, datos){
			this.$emit('cerrarModal', {bul:bul, datos:datos});
		},
		mantenedor: function(){
			this.obj.formula = {
				formula: this.formula,
				variables: this.variables_lista,
			};
			console.log(this.obj.formula);
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
				datosmapa: this.datosmapa, objindicador: this.objindicador
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
		this.ruta = '/mapaprocesosver/indicadores/setupdel';
		if (this.elementoeditar != null){
			this.obj = {...this.elementoeditar};
			try {
				var formulita_json = JSON.parse(this.obj.formula);
				this.formula = formulita_json.formula;
				this.variables_lista = formulita_json.variables;
			}
			catch (e) {}
			///////para eliminar
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
		//console.log(this.ruta);
		*/
	}
}
</script>
