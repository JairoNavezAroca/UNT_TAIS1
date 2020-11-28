<template>
	<div>
		<b-tabs content-class="mt-3" fill>
			<b-tab title="Criterios" active>
				<mapaprocesos-matrizpriorizacion-criterios :listapuntajes="datos.matriz" :datos="datos.criterios" :datosmapa="datosmapa"></mapaprocesos-matrizpriorizacion-criterios>
			</b-tab>
			<b-tab title="Matriz de Priorización" lazy>
				<mapaprocesos-matrizpriorizacion-matrizpriorizacion :objprocesos="objprocesos" :datos="datos" :datosmapa="datosmapa"></mapaprocesos-matrizpriorizacion-matrizpriorizacion>
			</b-tab>
		</b-tabs>
	</div>
</template>

<script>
export default {
	props:{
		datosmapa: Object,
		datos: Object,
		objprocesos: Object,
	},
	data() {
		return {
		}
	},
	methods:{
	},
	mounted() {
	}
}
</script>
