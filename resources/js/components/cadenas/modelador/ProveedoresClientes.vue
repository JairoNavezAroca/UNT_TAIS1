<template>
	<div class="container">
		<div v-for="(item, index) in datostabla">
			<div class="container border">
				<br>
				<h5>{{tipo}} Nivel {{(index+1)}}</h5>
				<br>
				<div class="table-responsive">
					<table class="table table-sm table-hover">
						<thead>
							<tr>
								<th>Empresa</th>
								<th>{{txt}}</th>
								<th>Opciones</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(item2, index2) in item">
								<td>{{item2.empresaAnterior}}</td>
								<td>{{item2.empresa}}</td>
								<td>
									<button @click="eliminar(index, item2, index2)" class="btn btn-danger btn-sm">Eliminar</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<div class="text-center">
					<button @click="cargarAgregarItem(index)" class="btn btn-secondary">Agregar {{txt}}</button>
				</div>
				<br>
			</div>
			<br>
		</div>
		<div class="text-center">
			<button @click="agregarNivel" class="btn btn-primary">Agregar Nivel</button>
		</div>
		<b-modal :id="'modal-agregar-'+tipo" size="md" scrollable centered hide-backdrop :title="'Agregar ' + txt + ' Nivel ' + nivel" hide-footer>
			<cadenas-proveedores-clientes-editor :datostabla="datostabla" @cerrarModal="cerrarModal" :nivel="nivel" :datoscadena="datoscadena" :tipo="tipo" :txt="txt" :listaNivelA="listaNivelA" :listaNivelB="listaNivelB"></cadenas-proveedores-clientes-editor>
		</b-modal>
	</div>
</template>

<script>
export default {
	props: {
		tipo: String,
		txt: String,
		listaEmpresas: Array,
		empresa: Object,
		datoscadena: Object,
		datostabla: Array,
	},
	data() {
		return {
			nivel: -1,
			listaNivelA: [],
			listaNivelB: [],
		}
	},
	methods:{
		eliminar: function(index, item2, index2){
			console.log('.........................');
			console.log(this.datostabla[index]);
			var idEmpresa_eliminar = item2.idEmpresa;
			var bul = false;
			var datosnivel;
			for (var pos = index + 1; pos < this.datostabla.length; pos++) {
				datosnivel = this.datostabla[pos];
				//console.log(datosnivel);
				datosnivel.forEach((item, i) => {
					if (item.idEmpresaAnterior == idEmpresa_eliminar)
						bul = true;
				});
			}
			console.log(item2);
			if (bul){
				this._mensaje_error("El " + this.txt + " que quiere eliminar tiene que se eliminado desde el último nivel");
				return;
			}
			var that = this;
			this._axios('/cadenasver/eliminar', {datoscadena: this.datoscadena, item:item2}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.solicitarRecargar();
					that._mensaje_exito('Se ha eliminado correctamente');
				}
				else
					that._mensaje_error(res.mensaje);
			});
		},
		solicitarRecargar(){
			this.$emit('solicitarrecargar');
		},
		cerrarModal: function(data){
			if (data.datos != null)
				this.solicitarRecargar();
			this.$bvModal.hide('modal-agregar-'+this.tipo);
		},
		cargarAgregarItem: function(index){
			//Array.unique(), para que los arrays no repitan datos
			//Array.prototype.unique=function(a){
				//return function(){return this.filter(a)}}(function(a,b,c){return c.indexOf(a,b+1)<0
			//});
			//reinicio datos a enviar al modal
			this.listaNivelA = [];
			this.listaNivelB = [];
			/************************************************/
			var listaA = [];
			var listaB = [];
			//DATOS LISTA A
			listaA.push(this.empresa.idEmpresa);
			this.datostabla.forEach((item, i) => {
				//i, es el nivel
				if (i < index)
					item.forEach((item2, i2) => {
						listaA.push(item2.idEmpresa);
					});
			});
			//listaA.unique();
			listaA = this._eliminarDuplicadosArray(listaA);
			//DATOS LISTA B => B=Empresas-A
			console.log(this.listaEmpresas);
			this.listaEmpresas.forEach((item1, i) => {
				var bul = true;
				listaA.forEach((item2, i2) => {
					if (item1.idEmpresa == item2)
						bul = false;
				});
				if (bul == true)
					listaB.push(item1.idEmpresa);
			});
			//listaB.unique();
			listaB = this._eliminarDuplicadosArray(listaB);
			//dar a las listas de datos A y B, el nombre de la empresa

			this.listaEmpresas.forEach((item1, i) => {
				listaA.forEach((item2, i2) => {
					if (item1.idEmpresa == item2){
						listaA[i2] = {};
						listaA[i2].idEmpresa = item1.idEmpresa;
						listaA[i2].nombre = item1.nombre;
						return;
					}
				});
				listaB.forEach((item2, i2) => {
					if (item1.idEmpresa == item2){
						listaB[i2] = {};
						listaB[i2].idEmpresa = item1.idEmpresa;
						listaB[i2].nombre = item1.nombre;
						return;
					}
				});
			});
			this.listaNivelA = listaA;
			this.listaNivelB = listaB;
			console.log(listaA);
			console.log(listaB);
			console.log(this.listaNivelA);
			console.log(this.listaNivelB);
			/************************************************/
			this.nivel = index + 1;
			this.$bvModal.show('modal-agregar-'+this.tipo);
		},
		agregarNivel: function(){
			this.datostabla.push([]);
			this.enviarDatos();
		},
		enviarDatos(){
			var tipo = this.tipo;
			var datos = this.datostabla;
			this.$emit('updatos', {datos: datos, tipo: tipo});
		},
	},
	mounted() {
		console.log('Component mounted.')
	}
}
</script>
