<template>
	<div class="container">
		<div class="text-center">
			<loader :mostrar="mostrarLoader"></loader>
			<button @click="consultarVersiones" class="btn btn-primary">Recargar</button>
			<br>
		</div>
		<div class="table-responsive">
			<table class="table table-sm table-hover">
				<thead>
					<tr>
						<th>Nº</th>
						<th>Descripcion</th>
						<th>Version válida desde</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(item, index) in listaVersiones">
						<td>{{(listaVersiones.length - index)}}</td>
						<td>{{item.descripcion}}</td>
						<td>{{item.creado}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		empresa: Object,
		datoscadena: Object,
	},
	data() {
		return {
			listaVersiones: [],
			mostrarLoader: false
		}
	},
	methods:{
		consultarVersiones: function(){
			if (this.datoscadena == null)
				return;
			this.mostrarLoader = true
			var that = this;
			this._axios('/cadenasver/versiones', {datoscadena: this.datoscadena}, function(res){
				res = res.data;
				if (res.mensaje == ''){
					that.listaVersiones = res.datos;
				}
				else
					that._mensaje_error(res.mensaje);
			}, ()=>{that.mostrarLoader = false});
		},
	},
	mounted() {
		this.consultarVersiones();
		console.log('Component mounted.')
	}
}
</script>
