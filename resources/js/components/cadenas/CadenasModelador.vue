<template>
	<div>
		<div class="text-center">
			<loader :mostrar="mostrarLoader"></loader>
		</div>
		<b-tabs content-class="mt-3" fill>
			<b-tab title="Proveedores" active>
				<cadenas-proveedores-clientes @solicitarrecargar="recargartodo" @updatos="updatos" :datostabla="datosProveedores" tipo="Proveedores" txt="Proveedor" :empresa="empresa" :listaEmpresas="listaEmpresas" :datoscadena="datoscadena"></cadenas-proveedores-clientes>
			</b-tab>
			<b-tab title="Clientes">
				<cadenas-proveedores-clientes @solicitarrecargar="recargartodo" @updatos="updatos" :datostabla="datosClientes" tipo="Clientes" txt="Cliente" :empresa="empresa" :listaEmpresas="listaEmpresas" :datoscadena="datoscadena"></cadenas-proveedores-clientes>
			</b-tab>
			<b-tab title="Previsualización de la Cadena de Suministro">
				<cadenas-previsualizar :datosProveedores="datosProveedores" :datosClientes="datosClientes" :empresa="empresa" :listaEmpresas="listaEmpresas"></cadenas-previsualizar>
			</b-tab>
			<b-tab title="Historial de Cambios">
				<cadenas-versiones :empresa="empresa" :datoscadena="datoscadena"></cadenas-versiones>
			</b-tab>
			<!--
			<template v-slot:tabs-end>
				<li role="presentation" class="nav-item align-self-center">
					<b-form-select v-model="versionSeleccionada" :options="listaVersiones"></b-form-select>
				</li>
			</template>
			-->
		</b-tabs>
	</div>
</template>

<script>
export default {
	data() {
		//todas las empresas = empresa:{} + listaEmpresas:null|[]
		return {
			empresa: {}, //datos de la empresa a la que se está haciendo la cadena de suministro
			datoscadena: null, //datos generales de la cadena de suministro
			versionSeleccionada: null,
			listaVersiones: [
				{ value: 'null', text: 'Seleccione versión' },
				{ value: '1', text: 'Version ayer' },
				{ value: '2', text: 'Version de la semana pasada' },
			],
			listaEmpresas: null, //datos de todas las empresas, se le quita la empresa a la que se está haciendo el análisis
			datosEmpresa: null, //datos de la empresa a la que se está haciendo el análisis
			datosProveedores: null,
			datosClientes: null,
			mostrarLoader: false,
		}
	},
	methods:{
		updatos: function({datos, tipo}){
			if (tipo == "Proveedores")
				this.datosProveedores = datos;
			else if (tipo == "Clientes")
				this.datosClientes = datos;
		},
		cargarEmpresas(){
			this.mostrarLoader = true;
			var that = this;
			this._axios('/cadenasver/listaempresas', null, function(res){
				res = res.data;
				that.cargarProveedores();
				that.cargarClientes();
				if (res.mensaje == ''){
					that.listaEmpresas = res.datos;
					that.listaEmpresas.forEach((item, index) => {
						console.log(item.idEmpresa + '___' +that.datoscadena.idEmpresa);
						if (item.idEmpresa == that.datoscadena.idEmpresa){
							//console.log(item);
							that.empresa = {...item};
							//delete that.listaEmpresas[index];
							//that.listaEmpresas.splice(index,1);
						}
					});
				}
				//console.log(that.listaEmpresas);
			}, ()=>{that.mostrarLoader = false});
		},
		procesarEmpresas(datos, empresa, listaEmpresas){
			var nivel_datos; // datos por nivel temporal, array
			var nivel = 1;
			var datos_final = []
			datos.forEach((itm, indx) => {
				listaEmpresas.forEach((item, index) => {
					//console.log(datos);
					if (itm.idEmpresa == item.idEmpresa)
						datos[indx].empresa = item.nombre;
					if (itm.idEmpresaAnterior == item.idEmpresa)
						datos[indx].empresaAnterior = item.nombre;
					/*
					if (itm.idEmpresa == empresa.idEmpresa)
						datos[indx].empresa = empresa.nombre;
					if (itm.idEmpresaAnterior == empresa.idEmpresa)
						datos[indx].empresaAnterior = empresa.nombre;
					*/
					if (datos[indx].empresa==undefined)
						datos[indx].empresa = "[Empresa Eliminada]";
					if (datos[indx].empresaAnterior==undefined)
						datos[indx].empresaAnterior = "[Empresa Eliminada]";
				});
				nivel_datos = [];
				datos.forEach((item, index) => {
					if (item.nivel == nivel)
						nivel_datos.push(item)
				});
				if (nivel_datos.length != 0)
					datos_final.push(nivel_datos);
				nivel++;
			});
			return datos_final;
		},
		cargarProveedores(){
			this.mostrarLoader = true;
			var that = this;
			this._axios('/cadenasver/proveedores/listar', {idCadenaSuministro: this.datoscadena.idCadenaSuministro}, function({data}){
				if (data.mensaje == ''){
					var datos = data.datos;
					that.datosProveedores = that.procesarEmpresas(datos, that.empresa, that.listaEmpresas);
					console.log(that.datosProveedores);
				}
			}, ()=>{that.mostrarLoader = false});
		},
		cargarClientes(){
			this.mostrarLoader = true;
			var that = this;
			this._axios('/cadenasver/clientes/listar', {idCadenaSuministro: this.datoscadena.idCadenaSuministro}, function({data}){
				if (data.mensaje == ''){
					var datos = data.datos;
					that.datosClientes = that.procesarEmpresas(datos, that.empresa, that.listaEmpresas);
					console.log(that.datosClientes);
				}
			}, ()=>{that.mostrarLoader = false});
		},
		recargartodo: function(){
			this.cargarEmpresas();
		},
	},
	mounted() {
		this.datoscadena = {...elemento};
		this.cargarEmpresas();
		console.log('Component mounted.');
		console.log(elemento);
	}
}
</script>
