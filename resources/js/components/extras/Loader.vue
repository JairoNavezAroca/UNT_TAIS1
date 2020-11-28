<template>
    <div :hidden="!mostrar">
		<!--
  		<vue-loaders name="pacman" color="black" scale="0.9"></vue-loaders>
		-->
	  	<vue-loaders name="ball-spin-fade-loader" color="black" scale="0.9"></vue-loaders>
		<br>{{texto_}}
    </div>
</template>

<script>
export default {
	props: {
		mostrar: Boolean,
		texto: String
	},
	data() {
		return {
			texto_: this.texto
		}
	},
	methods: {
	},
	mounted() {
	},
	created: function(){
		//console.log(this.texto);
		if (this.texto == null)
			this.texto_ = 'Cargando...';
	},
}
</script>
