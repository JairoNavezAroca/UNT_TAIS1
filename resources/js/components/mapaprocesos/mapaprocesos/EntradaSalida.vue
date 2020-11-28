<template>
	<div>
		<table class="table table-sm table-hover">
			<thead>
				<tr>
					<th>{{txt}}</th>
					<th>Opciones</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(item, index) in datos">
					<td>
						{{item.nombre}}
					</td>
					<td>
						<b-button @click="cargarEditar(item, index)" variant="outline-warning" size="sm">Editar</b-button>
						<b-button v-if="datosmapa.priorizado==false" @click="cargarEliminar(item, index)" variant="outline-danger" size="sm">Eliminar</b-button>
					</td>
				</tr>
			</tbody>
		</table>
		<b-modal :id="'modal-entradasalida-'+tipo" size="md" scrollable centered hide-backdrop :title="texto+' '+tipo" hide-footer>
			<mapaprocesos-mapaprocesos-entradasalida-editor :datostodos="datostodos" :entradasalida="entradasalida" :datosmapa="datosmapa" @cerrarModal="cerrarModal" :tipo="tipo"></mapaprocesos-mapaprocesos-entradasalida-editor>
		</b-modal>
		<div class="text-center">
			<b-button v-if="datosmapa.priorizado==false" @click="cargarAgregar" variant="success">Agregar</b-button>
			<span v-else class="text-danger">Usted no puede agregar o eliminar {{tipo}}s porque ya se ha realizado la priorización de procesos</span>
		</div>
	</div>
</template>

<script>
export default {
	props:{
		txt: String,
		tipo: String,
		datosmapa: Object,
		datos: Array,
		datostodos: Object,/////////////////////
	},
	data() {
		return {
			texto: '',
			entradasalida: null,
			index: -1,
		}
	},
	methods:{
		cargarEliminar: function(item, index){
			item.estado = '0';
			this.entradasalida = item;
			this.index = index;
			this.texto = 'Eliminar';
			this.$bvModal.show('modal-entradasalida-'+this.tipo);
		},
		cargarEditar: function(item, index){
			this.entradasalida = item;
			this.index = index;
			this.texto = 'Editar';
			this.$bvModal.show('modal-entradasalida-'+this.tipo);
		},
		cargarAgregar: function(){
			this.entradasalida = null;
			this.index = -1;
			this.texto = 'Registrar';
			this.$bvModal.show('modal-entradasalida-'+this.tipo);
		},
		cerrarModal: function(data){
			if (data.bul)
				if (this.index != -1)
					if (data.datos.estado == '1')
						this.datos[this.index] = data.datos;
					else
						this.datos.splice(this.index, 1);
				else
					this.datos.push(data.datos);
			this.$forceUpdate();
			//console.log(this.index);
			//console.log(data.datos);
			//console.log(this.datos);
			this.$bvModal.hide('modal-entradasalida-'+this.tipo);
		},
	},
	mounted() {
	}
}
</script>
