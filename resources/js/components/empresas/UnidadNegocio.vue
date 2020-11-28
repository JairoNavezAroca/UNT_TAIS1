<template>
	<div class="container">
		<div class="text-center">
			<loader :mostrar="mostrarLoader"></loader>
		</div>
		<div class="table-responsive">
			<table class="table table-sm table-hover">
				<thead>
					<tr>
						<th>Nombre y Descripcion</th>
						<th>Opciones</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(item, index) in unidadesNegocio">
						<td>
							<div v-if="indexEditar==index">
								<input v-model="unidadEditar.nombre" class="form-control" type="text">
								<textarea v-model="unidadEditar.descripcion" rows="3" class="form-control"></textarea>
							</div>
							<div v-else>
								Nombre: {{item.nombre}} <br> Descipción: {{item.descripcion}}
							</div>
						</td>
						<td>
							<div v-if="indexEditar==index">
								<button @click="enviarEditar(unidadEditar, index)" class="btn btn-success btn-sm">Guardar</button>
								<button @click="cancelarEditar()" class="btn btn-secondary btn-sm">Cancelar</button>
							</div>
							<div v-else>
								<button @click="cargarEditar(item, index)" class="btn btn-warning btn-sm">Editar</button>
								<button @click="cargarEliminar(item, index)" class="btn btn-danger btn-sm">Eliminar</button>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<b-col sm="12" md="12">
			<b-form-group label="Registrar Unidad de Neogcio" label-cols-sm="12" label-align-sm="Center" label-align="center" label-size="md" class="mb-1">
			</b-form-group>
			<b-form-group label="Nombre" label-cols-sm="12" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
				<b-form-input v-model="unidadnegocio.nombre" placeholder="Nombre"></b-form-input>
				<!--
					<div class="text-danger" v-if="!$v.producto.nombre.required">Campo requerido</div>
				-->
			</b-form-group>
				<b-form-group label="Descipcion" label-cols-sm="12" label-align-sm="left" label-align="center" label-size="md" class="mb-1">
					<b-form-textarea v-model="unidadnegocio.descripcion" placeholder="Descipcion"></b-form-textarea>
				</b-form-group>
		</b-col>
		<div class="text-center">
			<br>
			<loader :mostrar="mostrarLoader2"></loader>
			<b-button variant="success" @click="guardar">Guardar</b-button>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		datosempresa: Object,
	},
	data() {
		return {
			unidadesNegocio: [],
			indexEditar: -1,
			unidadnegocio: {
				nombre: '',
				descripcion: '',
			},
			unidadEditar: null,
			mostrarLoader: false,
			mostrarLoader2: false,
		}
	},
	methods: {
		cargarEliminar: function(item, index){
			var item_ = {...item};
			item_.eliminar = 'true';
			this.setupdel(item_, index);
		},
		cancelarEditar: function(){
			this.indexEditar = -1;
		},
		cargarEditar: function(item, index){
			this.indexEditar = index;
			this.unidadEditar = item;
			console.log(item);
			this.unidadnegocio.nombre = '';
			this.unidadnegocio.descripcion = '';
		},
		enviarEditar: function(item, index){
			this.setupdel(item, 'Modificar');
		},
		guardar: function(){
			this.setupdel(this.unidadnegocio, 'Registrar');
		},
		setupdel: function(unidadnegocio, txt){
			if (this.unidadnegocio.idUnidadNegocio == null){
				var bul = this._verificar_duplicados(this.unidadesNegocio, 'nombre', this.unidadnegocio.nombre);
				if (bul){
					this._mensaje_error("Ya hay una unidad de negocio registrada con ese nombre");
					return;
				}
			}
			this.mostrarLoader2 = true;
			var that = this;
			this._axios('/unidadnegocio/setupdel', {unidadnegocio: unidadnegocio, idEmpresa: this.datosempresa.idEmpresa}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.unidadnegocio.nombre = '';
					that.unidadnegocio.descripcion = '';
					if (txt == 'Registrar'){
						that.unidadesNegocio.push({...res.datos});
						that._mensaje_exito('Los datos se han registrado correctamente');
					}
					else if (txt == 'Modificar'){
						that.unidadesNegocio[that.indexEditar] = {...that.unidadEditar};
						that.cancelarEditar();
						that._mensaje_exito('Los datos se han modicado correctamente');
					}
					else {
						delete that.unidadesNegocio[txt];
						that.unidadesNegocio.splice(txt,1);
						that._mensaje_exito('Los datos se han eliminado correctamente');
					}
				}
				else
					that._mensaje_error(res.mensaje);
			}, ()=>{that.mostrarLoader2 = false});
		},
		cargarUnidadesNegocio: function(unidadnegocio, txt){
			this.mostrarLoader = true;
			var that = this;
			this._axios('/unidadnegocio/listar', {idEmpresa: this.datosempresa.idEmpresa}, function(res){
				res = res.data;
				if (res.mensaje == '')
					that.unidadesNegocio = res.datos;
			}, ()=>{that.mostrarLoader = false});
		},
	},
	mounted() {
		this.cargarUnidadesNegocio();
		console.log('Component mounted.')
	}
}
</script>
