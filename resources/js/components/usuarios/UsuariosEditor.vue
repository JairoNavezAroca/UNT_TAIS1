<template>
	<div class="container">
		<b-col sm="12" md="12">
			<b-form-group label="Nombres" label-cols-sm="5" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="usuario.nombres" placeholder="Nombres"></b-form-input>
				<!--
					<div class="text-danger" v-if="!$v.producto.nombre.required">Campo requerido</div>
				-->
			</b-form-group>
			<b-form-group label="Apellidos" label-cols-sm="5" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="usuario.apellidos" placeholder="Apellidos"></b-form-input>
			</b-form-group>
			<b-form-group label="Usuario" label-cols-sm="5" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="usuario.usuario" placeholder="Usuario"></b-form-input>
			</b-form-group>
			<b-form-group label="Dni" label-cols-sm="5" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="usuario.dni" placeholder="Dni"></b-form-input>
			</b-form-group>
			<b-form-group label="Rol" label-cols-sm="5" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				 <b-form-select v-model="usuario.rol" :options="listaRoles"></b-form-select>
			</b-form-group>
		</b-col>
		<div class="text-center">
			<br>
			<loader :mostrar="mostrarLoader"></loader>
			<b-button variant="danger" @click="enviarCerrarModal">Cancelar</b-button>
			<b-button variant="success" @click="guardar">Guardar</b-button>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		datosusuario: Object,
		listaUsuarios: Array,
	},
	data() {
		return {
			listaRoles: [
				{ value: 'A', text: 'Administrador' },
				{ value: 'T', text: 'Trabajador' },
			],
			usuario: {
				nombres: '',
				apellidos: '',
				usuario: '',
				dni: '',
				rol: 'T',
			},
			operacion: 'Registrar',
			mostrarLoader: false,
		}
	},
	methods: {
		enviarCerrarModal: function(datos){
			console.log(datos);
			this.$emit('cerrarModal', {datos:datos});
		},
		guardar: function(){
			if (this.usuario.dni.length != 8){
				this._mensaje_error("El campo DNI debe tener 8 Numeros");
				return;
			}
			if (this.usuario.idUsuario == null){
				var bul = this._verificar_duplicados(this.listaUsuarios, 'usuario', this.usuario.usuario);
				bul = bul || this._verificar_duplicados(this.listaUsuarios, 'dni', this.usuario.dni);
				if (bul){
					this._mensaje_error("Ya hay una persona registrada con ese DNI y/o Usuario");
					return;
				}
			}
			this.mostrarLoader = true;
			var that = this;
			var usuario = this.usuario.usuario;
			this._axios('/usuarios/setupd', {usuario: this.usuario}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					if (that.operacion == 'Registrar')
						that._mensaje_exito('Los datos se han registrado correctamente, no olvide que para esta persona, su usuario es: ' + usuario + ' y su contraseña es su número de dni');
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
		console.log(this.datosusuario);
		if (this.datosusuario != null){
			this.usuario = {...this.datosusuario};
			this.operacion = 'Editar';
		}
		console.log('Component mounted.')
	}
}
</script>
