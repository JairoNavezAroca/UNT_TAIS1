<template>
	<div>
		<b-tabs content-class="mt-3" fill>
			<b-tab title="Entradas" active>
				<mapaprocesos-mapaprocesos-entradasalida :datostodos="datos" :datos="datos.entradas" :datosmapa="datosmapa" txt="Entradas" tipo="entrada"></mapaprocesos-mapaprocesos-entradasalida>
			</b-tab>
			<b-tab title="Procesos Estratégicos">
				<mapaprocesos-mapaprocesos-procesos :datostodos="datos" :datos="datos.estrategico" :datosmapa="datosmapa" txt="Estratégicos" tipo="estrategico"></mapaprocesos-mapaprocesos-procesos>
			</b-tab>
			<b-tab title="Procesos Primarios">
				<mapaprocesos-mapaprocesos-procesos :relacionpuntajes="relacionpuntajes" :datostodos="datos" :datos="datos.primarios" :datosmapa="datosmapa" txt="Primarios" tipo="primario"></mapaprocesos-mapaprocesos-procesos>
			</b-tab>
			<b-tab title="Procesos de Apoyo">
				<mapaprocesos-mapaprocesos-procesos :datostodos="datos" :datos="datos.apoyo" :datosmapa="datosmapa" txt="de Apoyo" tipo="de apoyo"></mapaprocesos-mapaprocesos-procesos>
			</b-tab>
			<b-tab title="Salidas">
				<mapaprocesos-mapaprocesos-entradasalida :datostodos="datos" :datos="datos.salidas" :datosmapa="datosmapa" txt="Salidas" tipo="salida"></mapaprocesos-mapaprocesos-entradasalida>
			</b-tab>
			<b-tab title="Previsualización" lazy>
				<mapaprocesos-mapaprocesos-previsualizar :datos="datos" :datosmapa="datosmapa"></mapaprocesos-mapaprocesos-previsualizar>
			</b-tab>
		</b-tabs>
	</div>
</template>

<script>
export default {
	props: {
		datosmapa: Object,
		datos: Object,
		relacionpuntajes: Array,
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
