<template>
	<div class="container table-responsive">
		<table class="table table-sm table-hover text-center">
			<thead>
				<tr>
					<th>Nº</th>
					<th>Fecha</th>
					<th>Descripción</th>
					<th v-for="(item, index) in variables_general">
						{{item.variable_nombre}}
					</th>
					<th>Considerar el fórmula</th>
					<th>Opciones</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(item, index) in listado">
					<td>{{(index+1)}}</td>
					<td>
						<input v-model="item.fecha" type="date" class="form-control" size="sm">
					</td>
					<td>
						<b-form-input v-model="item.descripcion" size="sm"></b-form-input>
					</td>
					<td v-for="(item2, index2) in item.variables_resultado">
						<b-form-input v-model="item2.variable_valor" type="number" size="sm"></b-form-input>
					</td>
					<td>
						<b-form-checkbox v-model="item.considerar" value="true" unchecked-value="false">
							Si
						</b-form-checkbox>
					</td>
					<td>
						<b-button @click="eliminarItem(item, index)" variant="outline-danger" size="sm">Eliminar</b-button>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<div class="text-center">
			<b-button @click="enviarCerrarModal(false)" variant="danger">Cancelar</b-button>
			<b-button @click="mantenedor" variant="success">Guardar</b-button>
			<b-button @click="agregarItem" variant="primary">Agregar Item</b-button>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		elemento: Object,
	},
	data() {
		return {
			ruta: '/mapaprocesosver/tablerocontrol/datafuente',
			listado: [],
			variables_general: [],
		}
	},
	methods: {
		enviarCerrarModal: function(bul, datos){
			this.$emit('cerrarModal', {bul:bul, datos:datos});
		},
		mantenedor: function(){
			var that = this;
			this._axios(this.ruta, {
				obj: this.listado, objtablero: this.elemento,
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that._mensaje_exito('Los datos se han guardado correctamente');
					that.listado = res.datos;
					that.darFormatoLista();
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
		cargarRegistros: function(){
			var that = this;
			this._axios(this.ruta, {
				objtablero: this.elemento,
			}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.listado = res.datos;
					that.darFormatoLista();
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
		darFormatoLista: function(){
			this.listado.forEach((item, i) => {
				var variables_resultado = JSON.parse(item.variables_resultado);
				this.listado[i].variables_resultado = variables_resultado;
				this.listado[i].considerar = (this.listado[i].considerar==1);
			});
		},
		agregarItem: function(){
			var variables_ = [];
			this.variables_general.forEach((item, i) => {
				variables_.push({...item});
			});
			this.listado.push({
				variables_resultado: variables_,
				fecha: '',
				descripcion: '',
				considerar: true,
			});
		},
		eliminarItem: function(item, index){
			this.listado.splice(index, 1);
		},
	},
	mounted() {
		console.log(this.elemento.indicador_formula);
		var variables = this.elemento.indicador_formula.variables;
		variables.forEach((item, i) => {
			delete variables[i].variable_representa;
			variables[i].variable_valor = '';
		});
		this.variables_general = variables;
		this.cargarRegistros();
		//this.agregarItem();
		console.log();
	}
}
</script>
