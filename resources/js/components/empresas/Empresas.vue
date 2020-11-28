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
						<button @click="cargarVer(row.index, row.item)" class="btn btn-success">Ver</button>
						<button @click="eliminarEmpresa(row.index, row.item)" class="btn btn-danger">Eliminar</button>
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
		<b-modal id="modal-empresas" size="md" scrollable centered hide-backdrop :title="texto+' Empresa'" hide-footer>
			<empresas-editor :listaempresas="items" @cerrarModal="cerrarModal" registrando="true"></empresas-editor>
		</b-modal>
		<b-modal id="modal-empresas-unidad-negocio" size="lg" scrollable centered hide-backdrop :title="texto+' Empresa'" hide-footer>
			<b-row>
				<b-col sm="12" md="5">
					<div class="border">
						<div class="text-center">
							Datos de la Empresa
						</div>
						<br>
						<empresas-editor :listaempresas="items" @cerrarModal="cerrarModal" registrando="false" :datosempresa="datosempresa"></empresas-editor>
						<br>
					</div>
				</b-col>
				<br>
				<b-col sm="12" md="7">
					<div class="border">
						<div class="text-center">
							Unidad(es) de Negocio
						</div>
						<br>
						<unidad-negocio :datosempresa="datosempresa"></unidad-negocio>
						<br>
					</div>
				</b-col>
			</b-row>
			<div class="text-sm-right text-md-center">
				<br>
				<b-button variant="secondary" @click="cerrarModal(true)">Cerrar</b-button>
			</div>
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
				{ key: 'ruc', label: 'RUC', sortable: true},
				{ key: 'nombre', label: 'Nombre', sortable: true},
				{ key: 'telefono', label: 'Teléfono', sortable: true},
				{ key: 'direccion', label: 'Dirección', sortable: true},
				{ key: 'opciones', label: 'Opciones'}
			],
			totalRows: 1,
			currentPage: 1,
			perPage: 5,
			pageOptions: [5, 10, 15],
			filter: null,
			filterOn: [],
			texto: '',
			datosempresa: null,
			index: -1,
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
			console.log(datos);
			if (datos == true)
				this.$bvModal.hide('modal-empresas-unidad-negocio');
			else if (this.index == -1){
				this.$bvModal.hide('modal-empresas');
				if(datos.datos !== null){
					this.items.push(datos.datos);
					this.cargarVer(this.items.length-1, datos.datos);
				}
			}
			else
				this.items[this.index] = datos.datos;
			this._refresacar_tablita(this);
		},
		cargarRegistrar: function(){
			this.index = -1;
			this.datosempresa = null;
			this.$bvModal.show('modal-empresas');
			this.texto = 'Registrar';
		},
		cargarVer: function(index, item){
			this.index = index + (this.currentPage - 1) * this.perPage;
			console.log(this.index);
			this.datosempresa = item;
			this.$bvModal.show('modal-empresas-unidad-negocio');
			this.texto = 'Ver';
		},
		eliminarEmpresa: function(index, item){
			//this.index = index;
			//this.datosempresa = item;
			this.mostrarLoader = true;
			var that = this;
			this._axios('/empresas/eliminar', {idEmpresa: item.idEmpresa}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					delete that.items[index];
					that.items.splice(index,1);
					//that.items = that.items.splice(index, 1);
					that._refresacar_tablita(that);
					that._mensaje_exito('Los datos se han eliminado correctamente');
				}
				else
					that._mensaje_error(res.mensaje);
			}, ()=>{that.mostrarLoader = false});
		},
		cargarEmpresas(){
			this.mostrarLoader = true;
			var that = this;
			//this.totalRows = this.items.length;
			this._axios('/empresas/listar', null, function(res){
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
		this.cargarEmpresas();
		this.totalRows = this.items.length;
		//this.$bvModal.show('modal-empresas');
		console.log('Component mounted.')
	}
}
</script>
