<template>
	<div class="container">
		<b-col sm="12" md="12">
			<b-form-group label="Empresa" label-cols-sm="5" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<cool-select @select="selEmpresas" v-model="mapa.idEmpresa" :items="listaEmpresas" item-text="nombre" item-value="idEmpresa" placeholder="Seleccione Empresa" no-data="Todavía no se han registrado empresas"></cool-select>
			</b-form-group>
			<b-form-group :hidden="!mostrarUnNeg" label="Unidad de Negocio" label-cols-sm="5" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<cool-select @select="selUnidadNegocio" v-model="mapa.idUnidadNegocio" :items="listaUnidadNegocio" item-text="nombre" item-value="idUnidadNegocio" placeholder="Seleccione Unidad de Negocio" no-data="Seleccione una Empresa"></cool-select>
			</b-form-group>
		</b-col>
		<div class="text-center">
			<br>
			<loader :mostrar="mostrarLoader"></loader>
			<b-button variant="danger" @click="enviarCerrarModal(null)">Cancelar</b-button>
			<b-button variant="success" @click="guardar">Guardar</b-button>
		</div>
	</div>
</template>

<script>
import { CoolSelect } from 'vue-cool-select'
export default {
	components: {
		CoolSelect
	},
	data() {
		return {
			listaEmpresas: [],
			listaUnidadNegocio: [],
			mapa: {
				idEmpresa: '',
				idUnidadNegocio: '',
			},
			mostrarUnNeg: false,
			mostrarDetall: false,
			operacion: 'Registrar',
			mostrarLoader: false,
		}
	},
	methods: {
		selUnidadNegocio: function(){
			this.mostrarDetall = true;
		},
		selEmpresas: function(){
			this.mostrarUnNeg = true;
			this.mostrarDetall = false;
			this.listaUnidadNegocio = [];
			this.cargarUnidadesNegocio(this.mapa.idEmpresa);
		},
		enviarCerrarModal: function(datos){
			this.$emit('cerrarModal', {datos:datos});
		},
		guardar: function(){
			this.mostrarLoader = true;
			var that = this;
			this._axios('/mapaprocesos/setupd', {mapa: this.mapa}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					if (that.operacion == 'Registrar')
						that._mensaje_exito('Los datos se han registrado correctamente');
					else
						that._mensaje_exito('Los datos se han modicado correctamente');
					that.enviarCerrarModal(res.datos);
				}
				else
					that._mensaje_error(res.mensaje);
			}, ()=>{that.mostrarLoader = false});
		},
		cargarEmpresas(){
			this.mostrarLoader = true;
			var that = this;
			//this.totalRows = this.items.length;
			this._axios('/mapaprocesos/listaempresas', null, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.listaEmpresas = res.datos;
				}
			}, ()=>{that.mostrarLoader = false});
		},
		cargarUnidadesNegocio(idEmpresa){
			this.mostrarLoader = true;
			var that = this;
			//this.totalRows = this.items.length;
			this._axios('/mapaprocesos/listaunidades', {idEmpresa:idEmpresa}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					console.log(res.datos);
					that.listaUnidadNegocio = res.datos;
				}
			}, ()=>{that.mostrarLoader = false});
		},
	},
	mounted() {
		this.cargarEmpresas();
		console.log('Component mounted.')
	}
}
</script>
