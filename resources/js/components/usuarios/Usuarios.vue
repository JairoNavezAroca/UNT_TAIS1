<template>
	<div>
		<div class="text-center">
			<loader :mostrar="mostrarLoader"></loader>
		</div>
		<hr>
		<button class="btn btn-danger" @click="restar(5)">disminuir</button>
		<br>
		<input type="text" class="form-control" v-model="numero">
		<br>
		<button class="btn btn-success" @click="sumar(10)">aumentar</button>
		<br>
		{{numero}}
		<hr>
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
						<button @click="deshabilita(row.index, row.item)" v-if="row.item.estado_=='Habilitado'" class="btn btn-danger">Deshabilitar</button>
						<button @click="habilita(row.index, row.item)" v-else class="btn btn-warning">Habilitar</button>
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
		<b-modal id="modal-usuarios" size="md" scrollable centered hide-backdrop :title="texto+' Usuario'" hide-footer>
			<usuarios-editor :listaUsuarios="items" @cerrarModal="cerrarModal" :datosusuario="datosusuario"></usuarios-editor>
		</b-modal>
	</div>
</template>

<script>
export default {
	data() {
		return {
			numero: 0,
			items: [],
			coleccionItems: [],
			fields: [
				{
					key: 'nombres',
					label: 'Apellidos y Nombres',
					sortable: true,
					formatter: (value, key, item) => {
						return item.apellidos + ", " + item.nombres
					}
				},
				{ key: 'rol_', label: 'Rol', sortable: true},
				{ key: 'estado_', label: 'Estado', sortable: true},
				{ key: 'opciones', label: 'Opciones'}
			],
			totalRows: 1,
			currentPage: 1,
			perPage: 5,
			pageOptions: [5, 10, 15],
			filter: null,
			filterOn: [],
			texto: '',
			datosusuario: null,
			index: -1,
			mostrarLoader: false,
		}
	},
	methods: {
		restar: function(item){
			this.numero -= item;
		},
		sumar: function(item){
			this.numero += item;
		},
		onFiltered(filteredItems) {
			// Trigger pagination to update the number of buttons/pages due to filtering
			this.totalRows = filteredItems.length;
			this.currentPage = 1;
		},
		cerrarModal: function(datos){
			if (this.index == -1)
				this.items.push(datos.datos);
			else
				this.items[this.index] = datos.datos;
			this._refresacar_tablita(this);
			console.log(datos);
			this.$bvModal.hide('modal-usuarios');
		},
		cargarRegistrar: function(){
			this.index = -1;
			this.datosusuario = null;
			this.$bvModal.show('modal-usuarios');
			this.texto = 'Registrar';
		},
		cargarVer: function(index, item){
			this.index = index;
			this.index = index + (this.currentPage - 1) * this.perPage;
			this.datosusuario = item;
			this.$bvModal.show('modal-usuarios');
			this.texto = 'Ver';
		},
		cargarUsuarios(){
			this.mostrarLoader = true;
			var that = this;
			//this.totalRows = this.items.length;
			this._axios('/usuarios/listar', null, function(res){
				//console.log(res);
				res = res.data;
				if (res.mensaje == ''){
					that.items = res.datos;
					that.totalRows = that.items.length;
				}
			}, ()=>{that.mostrarLoader = false});
		},
		deshabilita: function(index, item){
			this.cambiaEstado('/usuarios/deshabilita', index, item, 'deshabilitado');
		},
		habilita: function(index, item){
			this.cambiaEstado('/usuarios/habilita', index, item, 'habilitado');
		},
		cambiaEstado: function(ruta, index, item, txt){
			//console.log(item);
			this.mostrarLoader = true;
			var that = this;
			this._axios(ruta, {idUsuario: item.idUsuario}, function(res){
				res = res.data;
				//console.log(res);
				if (res.mensaje == ''){
					that.items[index] = res.datos;
					that._mensaje_exito('El usuario ha sido ' + txt)
					.then(()=>{
						that._refresacar_tablita(that);
					});
					//that.cargarUsuarios();
				}
				else
					that._mensaje_error(res.mensaje);
			}, ()=>{that.mostrarLoader = false});
		},
	},
	mounted() {
		this.cargarUsuarios();
		//this.$bvModal.show('modal-usuarios');
		console.log('Component mounted.')
	}
}
</script>
