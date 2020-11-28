import Swal from 'sweetalert2'
export default {
	methods:{
		_swal_pregunta: function(text, funcion){
			const swalWithBootstrapButtons = Swal.mixin({
				customClass: {
					confirmButton: 'btn btn-success',
					cancelButton: 'btn btn-danger'
				},
				buttonsStyling: false
			})
			swalWithBootstrapButtons.fire({
				title: 'Eliminar',
				text: text,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Si',
				cancelButtonText: 'No',
				reverseButtons: true
			})
			.then((result) => {
				if (result.isConfirmed) {
					funcion();
				}
				/*else if (result.dismiss === Swal.DismissReason.cancel) {}*/
			})
		},
		_mensaje_error: function(msj){
			return Swal.fire('Error', msj, 'error');
		},
		_mensaje_exito: function(msj){
			return Swal.fire('Éxito', msj, 'success');
		},
		_refresacar_tablita: function(that){;
			that.totalRows = that.items.length;
			that.$refs.tablita.refresh();
		},
		_axios: function(ruta, parametros, funcion, funcion2 = null){
			axios.post(ruta, parametros)
			.then(funcion)
			.catch((e)=>{
				console.log(e);
			})
			.finally(() => {
				if (funcion2 != null)
					funcion2();
			});
		},
		_eliminarDuplicadosArray: function(arr){
			//https://es.stackoverflow.com/questions/41202/eliminar-un-array-de-objetos-duplicados-en-javascript/41206
			let set = new Set(arr.map(JSON.stringify));
			let arrSinDuplicaciones = Array.from(set).map(JSON.parse);
			return arrSinDuplicaciones;
		},
		_comparar_texto: function(txt_1, txt_2){
			console.log(String(txt_1).trim().toUpperCase());
			console.log(String(txt_2).trim().toUpperCase());
			return String(txt_1).trim().toUpperCase() == String(txt_2).trim().toUpperCase()
		},
		_verificar_duplicados: function(arr, campo, valor){
			var bul = false;
			arr.forEach((item, i) => {
				if (campo == 'nombre'){
					if (this._comparar_texto(item.nombre, valor))
						bul = true;
				}
				if (campo == 'ruc'){
					if (this._comparar_texto(item.ruc, valor))
						bul = true;
				}
				if (campo == 'usuario'){
					if (this._comparar_texto(item.usuario, valor))
						bul = true;
				}
				if (campo == 'dni'){
					if (item.dni == valor)
						bul = true;
				}
				if (campo == 'idProceso_desde'){
					if (item.idProceso_desde == valor)
						bul = true;
				}
				if (campo == 'idProceso_hasta'){
					if (item.idProceso_hasta == valor)
						bul = true;
				}
			});
			console.log(bul);
			return bul;
		},
		_aspectos_mapa_estrategico: function(orden){
			if (orden == "A")
				return [
					{ text: 'Financiero' },
					{ text: 'Clientes' },
					{ text: 'Procesos internos' },
					{ text: 'Aprendizaje y crecimiento' }
				];
			if (orden == "B")
				return [
					{ text: 'Clientes' },
					{ text: 'Financiero' },
					{ text: 'Procesos internos' },
					{ text: 'Aprendizaje y crecimiento' }
				];
		},
	}
};
//https://freek.dev/823-using-global-mixins-in-vuejs
/*
En app.js
	import _funciones from './components/_funciones.js';
	Vue.mixin(_funciones);
En componente
	import _funciones from '../_funciones';
	export default {
		mixins: [_funciones],
		...

*/


//https://www.todojs.com/obtener-todas-las-propiedades-objeto/
/*
function getPropertyNames(obj) {
	var proto = Object.getPrototypeOf(obj);
	return (
		(typeof proto === 'object' && proto !== null ? getPropertyNames(proto) : [])
		.concat(Object.getOwnPropertyNames(obj))
		.filter(function(item, pos, result) { return result.indexOf(item) === pos; })
		.sort()
	);
}
*/
