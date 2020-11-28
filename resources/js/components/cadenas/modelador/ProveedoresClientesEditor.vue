<template>
	<div class="container">
		<b-col sm="12" md="12">
			<b-form-group label="Empresa" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<cool-select @select="selAnterior" v-model="datos.idEmpresaAnterior" :items="listaNivelA" item-text="nombre" item-value="idEmpresa" placeholder="Seleccione Empresa"></cool-select>
				 <!--<b-form-select v-model="datos.empresa" :options="listaEmpresas"></b-form-select>-->
				 <!--
					 <div class="text-danger" v-if="!$v.producto.nombre.required">Campo requerido</div>
				 -->
			</b-form-group>
			<b-form-group :label="txt" label-cols-sm="3" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-row>
					<b-col md="9" sm="9">
						<cool-select @select="selEmpresa" v-model="datos.idEmpresa" :items="listaNivelB" item-text="nombre" item-value="idEmpresa" :placeholder="'Seleccione ' + txt"></cool-select>
						<!--<b-form-select v-model="datos.unidadnegocio" :options="listaProveedor"></b-form-select>-->
					</b-col>
					<b-col md="3" sm="3" class="p-1">
						<button @click="cargarAgregarEmpresa" class="btn btn-warning">Agregar</button>
					</b-col>
			 	</b-row>
			</b-form-group>
		</b-col>
		<div class="text-center">
			<b-button variant="danger" @click="enviarCerrarModal">Cancelar</b-button>
			<b-button variant="success" @click="guardar">Guardar</b-button>
		</div>
		<b-modal id="modal-agregar-cadena-empresa" size="md" scrollable centered hide-backdrop title="Registrar Empresa" hide-footer>
			<empresas-editor @cerrarModal="cerrarModal" botoncerrar="true"></empresas-editor>
		</b-modal>
	</div>
</template>

<script>
import { CoolSelect } from 'vue-cool-select'
export default {
	components: {
		CoolSelect
	},
	props: {
		tipo: String,
		txt: String,
		listaNivelA: Array,
		listaNivelB: Array,
		datoscadena: Object,
		nivel: Number,
		datostabla: Array,
	},
	data() {
		return {
			/*
			listaNivelA: [
				{ value: '1', text: 'Empresa 1' },
				{ value: '2', text: 'Empresa 2' },
			],
			*/
			/*
			listaNivelB: [
				{ value: '3', text: 'Empresa 3' },
				{ value: '4', text: 'Empresa 4' },
			],
			*/
			datos: {
				idEmpresaAnterior: '',
				idEmpresa: '',
				nivel: -1
			},
			ruta: ''
		}
	},
	methods: {
		selAnterior: function(item){
			console.log(this.listaNivelA);
			console.log(item);
		},
		selEmpresa: function(item){
			console.log(this.listaNivelB);
			console.log(item);
		},
		cerrarModal: function(data){
			this.$bvModal.hide('modal-agregar-cadena-empresa');
		},
		cargarAgregarEmpresa: function(){
			this.$bvModal.show('modal-agregar-cadena-empresa');
		},
		enviarCerrarModal: function(datos, bul){
			if (!bul)
				datos = null;
			console.log(datos == null);
			this.$emit('cerrarModal', { datos: datos });
		},
		guardar: function(){
			console.log('--------------------------------');
			console.log('--------------------------------');
			console.log(this.datos);
			//console.log(this.datos.nivel);
			//console.log(this.datostabla);
			var nivel = this.datos.nivel;
			//var idEmpresaAnterior = this.datos.idEmpresaAnterior;
			/*
			var bul = false;
			if (nivel > 2){
				bul = true;
				var nivelanterior = this.datostabla[(nivel - 1) - 1]; //(nivel-1) es el nivel actual en el array
				console.log(nivelanterior);
				nivelanterior.forEach((item, i) => {
					if (idEmpresaAnterior == item.idEmpresa)
						bul = false;
				});
			}
			*/
			var bul = false;
			/*********** VALIDACION: NO PUEDE HABER UN PROVEEDOR EN DOS NIVELES ***********/
			var datos_ = this.datos;
			this.datostabla.forEach((datosnivel, i) => {
				if ((nivel - 1) != i){
					datosnivel.forEach((item, j) => {
						if (datos_.idEmpresa == item.idEmpresa)
							bul = true;
					});
				}
			});
			if (bul == true){
				this._mensaje_error("El " + this.txt + " se encuentra registrado en otro nivel");
				return;
			}
			/*********** VALIDACION: NO PUEDEN HABER RELACIONES IGUALES ***********/
			var nivelpresente = this.datostabla[(nivel - 1)]; //(nivel-1) es el nivel actual en el array
			var datos = this.datos;
			nivelpresente.forEach((item, i) => {
				if (datos.idEmpresa == item.idEmpresa && datos.idEmpresaAnterior == item.idEmpresaAnterior)
					bul = true;
			});
			if (bul == true){
				this._mensaje_error("No pueden haber relaciones idénticas");
				return;
			}
			/***** VALIDACION: UN PROVEEDOR TIENE QUE TENER A UN PROVEEDOR EN UN NIVEL ANTERIOR *****/
			if (nivel >= 2){
				var idEmpresa = this.datos.idEmpresa;
				var temporal;
				var cont = nivel;
				while(cont >= 1){
					temporal = -1;
					var nivelanterior = this.datostabla[(cont - 1)]; //(cont-1) es el nivel actual en el array
					console.log(nivelanterior);
					console.log(idEmpresa);
					nivelanterior.forEach((item, i) => {
						if (idEmpresa == item.idEmpresa)
							temporal = item.idEmpresaAnterior;
					});
					if (temporal != -1)
						idEmpresa = temporal;
					console.log(cont + " --- " +idEmpresa + " --------- " + temporal);
					cont--;
				}
				if (temporal == this.datoscadena.idEmpresa)
					bul = true;
				console.log(bul + "--" + this.datoscadena.idEmpresa)
				if (bul == false){
					var idEmpresaAnterior = this.datos.idEmpresaAnterior;
					var nivelanterior_ = this.datostabla[(nivel - 1) - 1]; //(nivel-1) es el nivel actual en el array
					console.log('..........');
					console.log(nivelanterior_);
					nivelanterior_.forEach((item, i) => {
						if (idEmpresaAnterior == item.idEmpresa)
							bul = true;
					});
				}
			}
			else
				bul = true;
			if (bul == false){
				this._mensaje_error("El " + this.txt + " que quiere registrar debe registrarse en un nivel superior");
				return;
			}
			var that = this;
			this._axios(this.ruta+'agregar', {proveedorcliente: this.datos, datoscadena: this.datoscadena}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that._mensaje_exito('Los datos se han registrado correctamente');
					that.enviarCerrarModal(res.datos, true);
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
	},
	mounted() {
		if (this.tipo == "Proveedores")
			this.ruta = "/cadenasver/proveedores/";
		else if (this.tipo == "Clientes")
			this.ruta = "/cadenasver/clientes/";
		else
			location.reload();
		this.datos.nivel = this.nivel;
		console.log(this.ruta);
		console.log('Component mounted.')
	}
}
</script>
