<template>
	<div class="container">
		<b-col sm="12" md="12">
			<b-form-group label="Nombre" label-cols-sm="5" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="empresa.nombre" placeholder="Nombre"></b-form-input>
				<!--
					<div class="text-danger" v-if="!$v.producto.nombre.required">Campo requerido</div>
				-->
			</b-form-group>
			<b-form-group label="Ruc" label-cols-sm="5" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="empresa.ruc" placeholder="Ruc" type="number"></b-form-input>
			</b-form-group>
			<b-form-group label="Teléfono" label-cols-sm="5" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="empresa.telefono" placeholder="Teléfono"></b-form-input>
			</b-form-group>
			<b-form-group label="Direccion" label-cols-sm="5" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="empresa.direccion" placeholder="Direccion"></b-form-input>
			</b-form-group>
			<b-form-group label="Foto" label-cols-sm="12" label-align-sm="center" label-align="center" label-size="md" class="mb-1">
				<div class="text-center">
					<b-img :src="empresa._foto" class="img-fluid"></b-img>
					<b-button v-if="registrando=='false' && !versubirfoto" variant="warning" @click="versubirfoto=!versubirfoto">Editar Foto</b-button>
					<b-button v-if="registrando=='false' && versubirfoto" variant="primary" @click="versubirfoto=!versubirfoto">Cancelar</b-button>
					<br>
					<sube-archivos @archivosubido="archivosubido" :hidden="!versubirfoto"></sube-archivos>
				</div>
			</b-form-group>
		</b-col>
		<div class="text-center">
			<br>
			<loader :mostrar="mostrarLoader"></loader>
			<b-button v-if="registrando=='true'" variant="danger" @click="enviarCerrarModal(null)">Cancelar</b-button>
			<b-button variant="success" @click="guardar">Guardar</b-button>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		registrando: String,
		datosempresa: Object,
		listaempresas: Array,
	},
	data() {
		return {
			empresa: {
				nombre: '',
				ruc: '',
				telefono: '',
				direccion: '',
				foto: '',
				foto_: '',
				_foto: '',
			},
			operacion: 'Registrar',
			versubirfoto: true,
			mostrarLoader: false,
		}
	},
	methods: {
		archivosubido: function({valor, fileRecords}){
			console.log(valor);
			console.log(fileRecords);
			this.empresa.foto_ = fileRecords;
		},
		enviarCerrarModal: function(datos){
			console.log(datos);
			this.$emit('cerrarModal', {datos:datos});
		},
		guardar: function(){
			if (this.empresa.ruc.length != 11){
				this._mensaje_error("El campo RUC debe tener 11 Numeros");
				return;
			}
			if (this.empresa.idEmpresa == null){
				var bul = this._verificar_duplicados(this.listaempresas, 'nombre', this.empresa.nombre);
				bul = bul || this._verificar_duplicados(this.listaempresas, 'ruc', this.empresa.ruc);
				if (bul){
					this._mensaje_error("Ya hay una empresa registrada con ese RUC y/o Nombre");
					return;
				}
			}
			this.mostrarLoader = true;
			var that = this;
			this._axios('/empresas/setupd', {empresa: this.empresa}, function(res){
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
	},
	mounted() {
		//console.log(this.datosempresa);
		if (this.datosempresa != null){
			this.empresa = {...this.datosempresa};
			this.operacion = 'Editar';
			this.versubirfoto = false;
		}
		//console.log(typeof this.empresa.foto == "undefined");
		if (this.empresa.foto != '')
			this.empresa._foto = '/img/'+ this.empresa.foto;
		console.log('Component mounted.')
	}
}
</script>
