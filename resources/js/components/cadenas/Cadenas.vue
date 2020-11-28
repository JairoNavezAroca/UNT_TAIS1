<template>
	<div>
		<div class="text-center">
			<loader :mostrar="mostrarLoader"></loader>
		</div>
		<div class="table-responsive">
			<b-container fluid>
				<b-row>
					<b-col sm="6" md="6" class="my-1">
						<b-form-group label="Buscar" label-cols-sm="3" label-align-sm="right" label-size="md" label-for="filterInput" class="mb-0">
							<b-input-group size="md">
								<b-form-input v-model="filter" type="search" id="filterInput"></b-form-input>
								<b-input-group-append>
									<b-button :disabled="!filter" @click="filter=''" class="mt-0">Limpiar</b-button>
								</b-input-group-append>
							</b-input-group>
						</b-form-group>
					</b-col>
					<b-col sm="6" md="6" class="my-1">
						<b-form-group label="" label-align="left" label-size="md" label-for="perPageSelect1" class="mb-0 text-right">
							<button @click="cargarRegistrar" class="btn btn-primary">Agregar</button>
						</b-form-group>
					</b-col>
				</b-row>
				<b-table ref="tablita" show-empty stacked="md" :items="items" :fields="fields" :current-page="currentPage" :per-page="perPage" :filter="filter" :filterIncludedFields="filterOn" @filtered="onFiltered">
					<template v-slot:cell(opciones)="row">
						<!--
						<a href="#"><i @click="" class="fa fa-eye" style="color:green"></i></a>
						<a href="#"><i @click="" class="fa fa-angle-double-down" style="color:purple"></i></a>
						<a href="#"><i @click="" class="fa fa-angle-double-up" style="color:blue"></i></a>
						-->
						<button @click="cargarVer(row.item)" class="btn btn-success">Ver</button>
					</template>
				</b-table>
				<b-row>
					<b-col sm="6" md="6" class="my-1">
						<b-form-group label="Registros por página" label-cols-sm="8" label-cols-md="6" label-cols-lg="4" label-align-sm="right" label-size="md" label-for="perPageSelect" class="mb-0">
							<b-form-select v-model="perPage" id="perPageSelect" size="md" :options="pageOptions"></b-form-select>
						</b-form-group>
					</b-col>
					<b-col sm="6" md="6" class="my-1">
						<b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage" align="fill" size="sm" class="my-0"></b-pagination>
					</b-col>
				</b-row>
			</b-container>
		</div>
		<b-modal id="modal-cadenas" size="md" scrollable centered hide-backdrop title="Registrar Cadena" hide-footer>
			<cadenas-editor @cerrarModal="cerrarModal"></cadenas-editor>
		</b-modal>
	</div>
</template>

<script>
export default {
	data() {
		return {
			items: [],
			coleccionItems: [],
			fields: [
				{ key: 'nombreempresa', label: 'Empresa', sortable: true},
				{ key: 'nombreunidadnegocio', label: 'Unidad de Negocio', sortable: true},
				{
					key: 'detalle',
					label: 'Detalle',
					sortable: true,
					formatter: (value, key, item) => {
						return item.detalle
					}
				},
				{ key: 'ultimafecha', label: 'Ultima Fecha de Modificación', sortable: true},
				{ key: 'opciones', label: 'Opciones'}
			],
			totalRows: 1,
			currentPage: 1,
			perPage: 5,
			pageOptions: [5, 10, 15],
			filter: null,
			filterOn: [],
			mostrarLoader: false,
		}
	},
	methods: {
		onFiltered(filteredItems) {
			// Trigger pagination to update the number of buttons/pages due to filtering
			this.totalRows = filteredItems.length;
			this.currentPage = 1;
		},
		cerrarModal: function(datos){
			this.$bvModal.hide('modal-cadenas');
			if(datos.datos !== null){
				this.items.push(datos.datos);
				this._refresacar_tablita(this);
				this.cargarVer(datos.datos);
			}
		},
		cargarRegistrar: function(){
			this.$bvModal.show('modal-cadenas');
		},
		cargarVer: function(item){
			console.log(item);
			location.href = '/cadenasver/cargar/'+item.idCadenaSuministro;
		},
		cargarCadenas(){
			this.mostrarLoader = true;
			var that = this;
			//this.totalRows = this.items.length;
			this._axios('/cadenas/listar', null, function(res){
				//console.log(res);
				res = res.data;
				if (res.mensaje == ''){
					that.items = res.datos;
					that.totalRows = that.items.length;
				}
			}, ()=>{that.mostrarLoader = false});
		},
	},
	mounted() {
		//this.totalRows = this.items.length;
		this.cargarCadenas();
		//this.$bvModal.show('modal-cadenas');
		console.log('Component mounted.')
		if(mensaje != '')
			this._mensaje_error(mensaje);
	}
}
</script>
