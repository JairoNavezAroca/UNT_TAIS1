<template>
	<div>
		<table class="table table-sm table-hover">
			<thead>
				<tr>
					<th>Criterios</th>
					<th>Peso</th>
					<th>Valor Mínimo</th>
					<th>Valor Máximo</th>
					<th :hidden="datosmapa.priorizado==true">Opciones</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(item, index) in datos">
					<td>{{item.nombre}}</td>
					<td>{{item.peso}}</td>
					<td>{{item.valmin}}</td>
					<td>{{item.valmax}}</td>
					<td :hidden="datosmapa.priorizado==true">
						<b-button @click="cargarEditar(item, index)" variant="outline-warning" size="sm">Editar</b-button>
						<b-button @click="cargarEliminar(item, index)" variant="outline-danger" size="sm">Eliminar</b-button>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="text-center" :hidden="datosmapa.priorizado==true">
			<span v-if="index==-1">Registrar</span>
			<span v-else>Editar</span>
		</div>
		<b-row :hidden="datosmapa.priorizado==true">
			<b-col sm="1" md="2"></b-col>
			<b-col sm="10" md="8">
				<b-form-group label="Criterio" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
					<b-form-input :state="!$v.obj.nombre.$invalid" v-model="obj.nombre"></b-form-input>
					<div class="text-danger" v-if="!$v.obj.nombre.required">Por favor ingrese el criterio</div>
					<div class="text-danger" v-if="!$v.obj.nombre.nombrecriterio">Por favor, asegurese de escribir bien el criterio</div>
					<div class="text-danger" v-if="!$v.obj.nombre.minLength">Debe ingresar 4 caracteres como mínimo</div>
					<div class="text-danger" v-if="!$v.obj.nombre.maxLength">Debe ingresar 50 caracteres como máximo</div>
				</b-form-group>
				<b-form-group label="Peso" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
					<b-form-input :state="!$v.obj.peso.$invalid" v-model="obj.peso" type="number"></b-form-input>
					<div class="text-danger" v-if="!$v.obj.peso.required">Por favor ingrese el peso</div>
					<div class="text-danger" v-if="!$v.obj.peso.integer">Por favor, asegurese de escribir un número entero</div>
					<div class="text-danger" v-if="!$v.obj.peso.minValue">No puede ingresar números menores a -10</div>
					<div class="text-danger" v-if="!$v.obj.peso.maxValue">No puede ingresar números mayores a 10</div>
				</b-form-group>
				<b-form-group label="Justificación" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
					<b-form-textarea :state="!$v.obj.justificacion.$invalid" v-model="obj.justificacion" rows="4"></b-form-textarea>
					<div class="text-danger" v-if="!$v.obj.justificacion.required">Por favor ingrese la justificación</div>
					<div class="text-danger" v-if="!$v.obj.justificacion.txtjustificacion">Por favor, asegurese de escribir bien la justificación</div>
					<div class="text-danger" v-if="!$v.obj.justificacion.minLength">Debe ingresar 4 caracteres como mínimo</div>
					<div class="text-danger" v-if="!$v.obj.justificacion.maxLength">Debe ingresar 250 caracteres como máximo</div>
				</b-form-group>
				<b-form-group label="Valor Mínimo" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
					<b-form-input :state="!$v.obj.valmin.$invalid" v-model="obj.valmin" type="number"></b-form-input>
					<div class="text-danger" v-if="!$v.obj.valmin.required">Por favor ingrese el valor mínimo de calificación</div>
					<div class="text-danger" v-if="!$v.obj.valmin.integer">Por favor, asegurese de escribir un número entero</div>
					<div class="text-danger" v-if="!$v.obj.valmin.minValue">No puede ingresar números menores a -5</div>
					<div class="text-danger" v-if="!$v.obj.valmin.maxValue">No puede ingresar números mayores a 5</div>
				</b-form-group>
				<b-form-group label="Valor Máximo" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
					<b-form-input :state="!$v.obj.valmax.$invalid" v-model="obj.valmax" type="number"></b-form-input>
					<div class="text-danger" v-if="!$v.obj.valmax.required">Por favor ingrese el valor máximo de calificación</div>
					<div class="text-danger" v-if="!$v.obj.valmax.integer">Por favor, asegurese de escribir un número entero</div>
					<div class="text-danger" v-if="!$v.obj.valmax.minValue">No puede ingresar números menores a -5</div>
					<div class="text-danger" v-if="!$v.obj.valmax.maxValue">No puede ingresar números mayores a 5</div>
				</b-form-group>
				<div class="text-danger" v-if="obj.valmin>=obj.valmax">Considere que el malor mínimo no puede ser mayor o igual al valor máximo de calificación</div>
			</b-col>
			<b-col sm="1" md="2"></b-col>
		</b-row>
		<div class="text-center" :hidden="datosmapa.priorizado==true">
			<b-button v-if="index!=-1" @click="cargarAgregar" variant="primary">Cancelar edicion</b-button>
			<b-button @click="mantenedor" variant="success">Guardar</b-button>
		</div>
		<span v-if="datosmapa.priorizado==true" class="text-danger">Usted no puede agregar o eliminar criterios porque ya se ha realizado la priorización de procesos</span>
	</div>
</template>

<script>
import { required, minLength, maxLength, integer, minValue, maxValue } from 'vuelidate/lib/validators'
import { nombrecriterio, txtjustificacion } from '../../_expresiones_regulares.js'
export default {
	props:{
		datosmapa: Object,
		datos: Array,
		listapuntajes: Array,/////////
	},
	data() {
		return {
			index: -1,
			obj: {},
			plantilla: {
				nombre: '',
				peso: 0,
				justificacion: '',
				valmin: 0,
				valmax: 0,
				estado: '1'
			},
		}
	},
	validations: {
		obj: {
			nombre: {
				required,
				nombrecriterio,
				minLength: minLength(4),
				maxLength: maxLength(50),
			},
			peso: {
				required,
				integer,
				minValue: minValue(-10),
				maxValue: maxValue(10)
			},
			justificacion: {
				required,
				txtjustificacion,
				minLength: minLength(4),
				maxLength: maxLength(250),
			},
			valmin: {
				required,
				integer,
				minValue: minValue(-5),
				maxValue: maxValue(5),
			},
			valmax: {
				required,
				integer,
				minValue: minValue(-5),
				maxValue: maxValue(5),
			},
		}
	},
	methods:{
		cargarEliminar: function(item, index){
			this.obj = {...item};
			this.obj.estado = '0';
			this.index = index;
			this.mantenedor();
		},
		cargarEditar: function(item, index){
			this.obj = {...item};
			this.index = index;
		},
		cargarAgregar: function(){
			this.obj = {...this.plantilla};
			this.index = -1;
		},
		actualizar: function(datos){
			if (this.index != -1)
				if (datos.estado == '1')
					this.datos[this.index] = datos;
				else
					this.datos.splice(this.index, 1);
			else
				this.datos.push(datos);
			this.$forceUpdate();
			this.cargarAgregar();
		},
		mantenedor: function(){
			if (this.$v.obj.$invalid || this.obj.valmin >= this.obj.valmax){
				this._mensaje_error('Por favor, revise el formulario y corrija el(los) campo(s) incorrecto(s)');
				return;
			}
			if (this.obj.estado == '0'){
				//eliminar
				//para ver si tiene puntajes
				var bul = false;
				this.listapuntajes.forEach((item, i) => {
					if (item.idCriteriosPriorizacion == this.obj.idCriteriosPriorizacion)
						bul = true;
				});
				if (bul){
					var that = this;
					this._mensaje_error("Ya se han registrado puntajes con este criterio, no es posible eliminar");
					return;
				}
			}
			if (this.obj.estado == '1' && this.obj.idCriteriosPriorizacion == null){
				//agregar
				var bul2 = this._verificar_duplicados(this.datos, 'nombre', this.obj.nombre);
				if (bul2){
					this._mensaje_error("Ya hay un criterio registrado con ese nombre, por favor verifique");
					this.cargarAgregar();
					return;
				}
			}
			var that = this;
			this._axios('/mapaprocesosver/criterios/setupdel', {obj: this.obj, datosmapa: this.datosmapa}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that._mensaje_exito('Los datos se han guardado correctamente');
					that.actualizar(res.datos);
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
	},
	mounted() {
		this.cargarAgregar();
	}
}
</script>
