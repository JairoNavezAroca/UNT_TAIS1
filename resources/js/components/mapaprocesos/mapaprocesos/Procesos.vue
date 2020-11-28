<template>
	<div>
		<div class="border text-center pl-4 pr-4 pt-2 pb-2 mt-4 mb-4" v-for="(item1, index1) in lista" lazy>
			<div class="text-center">
				<span>{{item1.denominacion}}</span>
			</div>
			<table class="table table-sm table-hover">
				<thead>
					<tr>
						<th v-if="item1.val">Procesos {{txt}}</th>
						<th>{{item1.denominacion}}</th>
						<th>Opciones</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(item2, index2) in datos">
						<td v-if="item2.idProcesoAnterior!=null && item1.val">
							<tr v-for="(item3, index3) in datos">
								<span v-if="item2.idProcesoAnterior==item3.idProceso" break>
									{{item3.nombre}}
								</span>
							</tr>
						</td>
						<td v-if="(item2.idProcesoAnterior!=null)==item1.val">
							{{item2.nombre}}
						</td>
						<td v-if="(item2.idProcesoAnterior!=null)==item1.val">
							<b-button @click="cargarEditar(item2, index2)" variant="outline-warning" size="sm">Editar</b-button>
							<b-button v-if="datosmapa.priorizado==false" @click="cargarEliminar(item2, index2)" variant="outline-danger" size="sm">Eliminar</b-button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<b-modal :id="'modal-procesos-'+tipo" size="md" scrollable centered hide-backdrop :title="texto+' proceso '+tipo" hide-footer>
			<mapaprocesos-mapaprocesos-procesos-editor :relacionpuntajes="relacionpuntajes" :datostodos="datostodos" :listaprocesos="datos" :infoproceso="infoproceso" :datosmapa="datosmapa" @cerrarModal="cerrarModal" :tipo="tipo"></mapaprocesos-mapaprocesos-procesos-editor>
		</b-modal>
		<div class="text-center">
			<b-button v-if="datosmapa.priorizado==false" @click="cargarAgregar" variant="success">Agregar</b-button>
			<span v-else class="text-danger">Usted no puede agregar o eliminar procesos {{tipo}} porque ya se ha realizado la priorización de procesos</span>
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
		relacionpuntajes: Array,////////
	},
	data() {
		return {
			lista: [
				{
					denominacion: "Procesos",
					val: false,
					tipo: 'P'
				},
				{
					denominacion: "Sub-Procesos",
					val: true,
					tipo: 'S'
				},
			],
			texto: '',
			infoproceso: null,
			index: -1,
		}
	},
	methods:{
		cargarEliminar: function(item, index){
			item.estado = '0';
			this.infoproceso = item;
			this.index = index;
			this.texto = 'Eliminar';
			this.$bvModal.show('modal-procesos-'+this.tipo);
		},
		cargarEditar: function(item, index){
			this.infoproceso = item;
			this.index = index;
			this.texto = 'Editar';
			this.$bvModal.show('modal-procesos-'+this.tipo);
		},
		cargarAgregar: function(){
			this.infoproceso = null;
			this.index = -1;
			this.texto = 'Registrar';
			this.$bvModal.show('modal-procesos-'+this.tipo);
		},
		cerrarModal: function(data){
			try {
				this.datos[this.index].estado = "1";
			} catch (e) {}
			console.log(data);
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
			this.$bvModal.hide('modal-procesos-'+this.tipo);
		},

	},
	mounted() {
		console.log(this.datos);
		if (this.tipo == 'estrategico')
			this.lista.length = 1;
		else if (this.tipo == 'de apoyo')
			this.lista.length = 1;
		else if (this.tipo == 'primario')
			this.lista.length = 2;
	}
}
</script>
