<template>
	<div class="container">
		<div id="sample">
			<!--
			<div id="myDiagramDiv" style="display:block; width:100%; height:70vh;"></div>
			<div id="item1" hidden>
				<canvas id="canvas" width="800" height="400"></canvas>
			</div>
			<div id="item2" hidden>
				<div id="png-container"></div>
			</div>
			<button onclick="makeSVG()">Make SVG</button>
			<button onclick="makeJPG()">Make JPG</button>
			<div id="SVGResult"></div>
			-->

			<button @click="cargargrafico()">Cargar Gráfico</button>
			<button @click="botondescargar()">Descargar en PNG</button>
			<button id="svgButton">Descargar en SVG(Beta)</button>
			<button @click="botonpdf()">Descargar en PDF</button>
			<div id="imagen" ref="areaexportar" style="display:block; width:100%;" hidden></div>
			<div id="diagrama"></div>
			<!--
			<button id="SaveButton" onclick="save()">Save</button>
			<button onclick="load()">Load</button>
			-->
			<textarea id="mySavedModel" style="width:100%;height:300px" hidden>
				{{modelo}}
			</textarea>

		</div>
	</div>
</template>

<script>
import jsPDF from 'jspdf'
const html2canvas = require('html2canvas');
export default {
	props: {
		tipo: String,
		txt: String,
		datosProveedores: Array,
		datosClientes: Array,
		empresa: Object,
		listaEmpresas: Array,
	},
	data() {
		return {
			listado: [
				[{},{}],
				[{},{}],
				[{},{}],
			],
			nivel: -1,
			modelo: {
				class: "go.GraphLinksModel",
				nodeCategoryProperty: "type",
				linkFromPortIdProperty: "frompid",
				linkToPortIdProperty: "topid",
				nodeDataArray: [
					{key:1, type:"Empresa 1", name:"Product"},
					{key:2, type:"Empresa 3", name:"Sales"},
					{key:3, type:"Table", name:"Period"},
					{key:11, type:"Join", name:"Product, Class"},
					{key:12, type:"Empresa 4", name:"Period"},
				],
				linkDataArray: [
					{from:1, frompid:"", to:12, topid:""},
					{from:1, frompid:"OUT", to:11, topid:"L"},
					{from:2, frompid:"OUT", to:11, topid:"R"},
					{from:3, frompid:"OUT", to:12, topid:"R"},
				]
			},
		}
	},
	methods:{
		botonpdf: function(){

			var jpg = myDiagram.makeImage({background: "white"});
			console.log(jpg);
			document.getElementById("imagen").innerHTML='<img src="'+jpg.src+'">';


			// Default export is a4 paper, portrait, using milimeters for units
			var doc = new jsPDF();

			//const contentHtml = this.$refs.areaexportar.innerHTML;
			//doc.fromHTML(contentHtml, 15, 15, {
			//	width: 170
			//});
			//doc.addImage(jpg, 'JPEG', 1, 2);
			//doc.save("sample.pdf");
			//return;

			var canvasElement = document.createElement('canvas');
			html2canvas(this.$refs.areaexportar, { canvas: canvasElement })
			.then(function (canvas) {
				//doc.addImage(jpg,'JPEG', 20, 20);
				doc.addImage(jpg,'JPEG', 5, 5);
				doc.save("sample.pdf");
			});


		},
		botondescargar: function(){
			var _eventodescargar = new CustomEvent('botondescargar', {detail: null});
			document.dispatchEvent(_eventodescargar);
		},
		cargargrafico: function(){
			//console.log(this.listaEmpresas);
			this.modelo.nodeDataArray = [];
			this.listaEmpresas.forEach((item, i) => {
				this.modelo.nodeDataArray.push({
					key: item.idEmpresa,
					type: 'Proveedor: ' + item.nombre,
					name: '.'//ssssssssssss
				});
				this.modelo.nodeDataArray.push({
					key: 1000 + item.idEmpresa,
					type: 'Cliente: ' + item.nombre,
					name: '.'//ssssssssssss
				});
			});

			//console.log(this.empresa);
			this.modelo.linkDataArray = [];

			//console.log(this.datosProveedores);
			this.datosProveedores.forEach((item, i) => {
				item.forEach((item2, i2) => {
					this.modelo.linkDataArray.push({
						from: (item2.idEmpresa == null)?(this.empresa.idEmpresa):(item2.idEmpresa),
						frompid:"D",
						to: (item2.idEmpresaAnterior == null)?(this.empresa.idEmpresa):(item2.idEmpresaAnterior),
						topid:"I"
					});
				});
			});

			//console.log(this.datosClientes);
			this.datosClientes.forEach((item3, i) => {
				item3.forEach((item4, i2) => {
					//console.log(item4.idEmpresaAnterior == null);
					//console.log(item4.idEmpresa == null);
					this.modelo.linkDataArray.push({
						from: (item4.idEmpresaAnterior == this.empresa.idEmpresa)?(this.empresa.idEmpresa):(1000 + item4.idEmpresaAnterior),
						frompid:"D",
						to: (item4.idEmpresa == this.empresa.idEmpresa)?(this.empresa.idEmpresa):(1000 + item4.idEmpresa),
						topid:"I"
					});
				});
			});

			var _eventocargar = new CustomEvent('crearcuadros', {
				detail: {
					listaEmpresas: this.listaEmpresas,
					datosProveedores: this.datosProveedores,
					datosClientes: this.datosClientes,
					modelo: this.modelo
				}
			});
			var aa = document.dispatchEvent(_eventocargar);
			console.log(aa);
		},
	},
	created(){
		//this.cargar();
	},
	mounted() {
		console.log('Component mounted.')
		//this.cargar();
	}
}
</script>
