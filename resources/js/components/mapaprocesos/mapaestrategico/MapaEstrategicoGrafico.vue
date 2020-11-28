<template>
	<div class="container">
		<div id="diagrama"></div>
		<div id="imagen" ref="areaexportar" style="display:block; width:100%;" hidden></div>
		<br>
		<div class="text-center">
			<b-button @click="enviarCerrarModal(false)" variant="danger">Cancelar</b-button>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		listado: Array,
	},
	data() {
		return {
		}
	},
	methods: {
		enviarCerrarModal: function(bul, datos){
			this.$emit('cerrarModal', {bul:bul, datos:datos});
		},
	},
	mounted() {
	}
}
</script>
