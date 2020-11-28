<template>
	<div class="container">
		<b-col sm="12" md="12">
			<b-form-group label="Tipo" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<cool-select @select="" v-model="obj.idProceso_desde" :items="listaProcesosA" item-text="nombre" item-value="idProceso" placeholder="Seleccione Proceso"></cool-select>
			</b-form-group>
			<b-form-group label="Asociado a" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<cool-select @select="" v-model="obj.idProceso_hasta" :items="listaProcesosB" item-text="nombre" item-value="idProceso" placeholder="Seleccione Proceso"></cool-select>
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
export default {
	components: {
		CoolSelect
	},
	props: {
		datosmapa: Object,
		datos: Object,
		relacion: Object,
		listadatos: Array,
	},
	data() {
		return {
			listaProcesosA: [],
			listaProcesosB: [],
			obj: {
				idProceso_desde: '',
				idProceso_hasta: '',
			},
		}
	},
	methods: {
		enviarCerrarModal: function(bul, datos){
			this.$emit('cerrarModal', {bul:bul, datos:datos});
		},
		mantenedor: function(){
			if (this.obj.estado == '1'){
				var bul = this._verificar_duplicados(this.listadatos, 'idProceso_desde', this.obj.idProceso_desde);
				bul = bul && this._verificar_duplicados(this.listadatos, 'idProceso_hasta', this.obj.idProceso_hasta);
				if (bul){
					this._mensaje_error("No pueden haber relaciones repetidas");
					return;
				}
				var bul = this._verificar_duplicados(this.listadatos, 'idProceso_desde', this.obj.idProceso_hasta);
				bul = bul && this._verificar_duplicados(this.listadatos, 'idProceso_hasta', this.obj.idProceso_desde);
				if (bul){
					this._mensaje_error("No pueden haber relaciones inversas");
					return;
				}
			}
			var that = this;
			this._axios('/mapaprocesosver/relaciones/setupdel', {obj: this.obj, datosmapa: this.datosmapa}, function(res){
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
		if (this.relacion != null){
		this.obj = {...this.relacion};
		if (this.obj.estado == '0')
			this.mantenedor();
		}
		var primarios = [...this.datos.primarios];
		var eliminar = [];
		var temporal = 0;
		//verifico los que voy a eliminar y lo agrego a un array
		primarios.forEach((item, i) => {
			primarios.forEach((item2, i2) => {
				if (item.idProceso == item2.idProcesoAnterior)
					eliminar.push(item.idProceso);
			});
		});
		//elimino los que tengan el idProceso del array
		eliminar.forEach((item, i) => {
			temporal = -1;
			primarios.forEach((item2, i2) => {
				if (item == item2.idProceso)
					temporal = i2;
			});
			if (temporal != -1)
				primarios.splice(temporal, 1);
		});
		console.log(eliminar);
		console.log(primarios);
		//console.log(primarios_fin);
		this.listaProcesosA = primarios;
		this.listaProcesosB = primarios;
	}
}
</script>
