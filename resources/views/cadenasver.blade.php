@extends('layout')

@section('titulo')
Cadena de Suministro - {{$cadenasuministro['nombreempresa']}}
@endsection

@section('subtitulo')
Unidad de Negocio: {{$cadenasuministro['nombreunidadnegocio']}}
@endsection

@section('html')
<cadenas-modelador></cadenas-modelador>
<!--
<div id="sample">
-->
	<!--style="display:block; width:100%; height:70vh;"-->
	<!--style="border: solid 1px black; width:300px; height:300px"></div-->
<!--
  <div id="myDiagramDiv" style="display:block; width:100%; height:70vh;"></div>
  <canvas id="canvas" width="800" height="400"></canvas>
  <div id="png-container"></div>
  <button onclick="makeSVG()">Make SVG</button>
  <button onclick="makeJPG()">Make JPG</button>
  <div id="SVGResult"></div>
</div>
-->
@endsection

<script>
	let elemento = @json($cadenasuministro);
</script>

@section('onload')
onload="init()"
@endsection
@section('script')
<script src="{{asset('gojs/release/go-debug.js')}}"></script>
<!--<script src="{{asset('gojs/assets/js/goSamples.js')}}"></script>-->
<script id="code">
	var $ = go.GraphObject.make;
	function init(){
		document.getElementById("svgButton").addEventListener("click", makeSvg);
	}


	function makePort(name, leftside) {
		var port = $(go.Shape, "Rectangle", {
			fill: "gray",
			stroke: null,
			desiredSize: new go.Size(8, 8),
			portId: name, // declare this object to be a "port"
			toMaxLinks: 1, // don't allow more than one link into a port
			cursor: "pointer" // show a different cursor to indicate potential link point
		});
		var lab = $(go.TextBlock, name, // the name of the port
			{
				font: "7pt sans-serif"
			});
		var panel = $(go.Panel, "Horizontal", {
			margin: new go.Margin(2, 0)
		});
		// set up the port/panel based on which side of the node it will be on
		if (leftside) {
			port.toSpot = go.Spot.Left;
			port.toLinkable = true;
			lab.margin = new go.Margin(1, 0, 0, 1);
			panel.alignment = go.Spot.TopLeft;
			panel.add(port);
			panel.add(lab);
		}
		else {
			port.fromSpot = go.Spot.Right;
			port.fromLinkable = true;
			lab.margin = new go.Margin(1, 1, 0, 0);
			panel.alignment = go.Spot.TopRight;
			panel.add(lab);
			panel.add(port);
		}
		return panel;
	}
	function makeTemplate(typename, icon, background, inports, outports) {
		var node = $(go.Node, "Spot",
			$(go.Panel, "Auto", {
					width: 100,
					height: 120
				},
				$(go.Shape, "Rectangle", {
					fill: background,
					stroke: null,
					strokeWidth: 0,
					spot1: go.Spot.TopLeft,
					spot2: go.Spot.BottomRight
				}),
				$(go.Panel, "Table",
					$(go.TextBlock, typename, {
						row: 0,
						margin: 2,
						maxSize: new go.Size(80, NaN),
						stroke: "white",
						font: "bold 11pt sans-serif"
					}),
					$(go.Picture, icon, {
						row: 1,
						width: 55,
						height: 55
					}),
					$(go.TextBlock, {
							row: 2,
							margin: 3,
							editable: true,
							maxSize: new go.Size(80, 40),
							stroke: "white",
							font: "bold 9pt sans-serif"
						},
						new go.Binding("text", "name").makeTwoWay())
				)
			),
			$(go.Panel, "Vertical", {
					alignment: go.Spot.Left,
					alignmentFocus: new go.Spot(0, 0.5, 8, 0)
				},
				inports),
			$(go.Panel, "Vertical", {
					alignment: go.Spot.Right,
					alignmentFocus: new go.Spot(1, 0.5, -8, 0)
				},
				outports)
		);
		myDiagram.nodeTemplateMap.set(typename, node);
	}


	document.addEventListener('crearcuadros', (datos) => {
		//console.log(datos);
		crearcuadros(
			datos.detail.listaEmpresas,
			datos.detail.datosProveedores,
			datos.detail.datosClientes
		);
		load(datos.detail.modelo);
	});

	function crearcuadros(listaEmpresas, datosProveedores, datosClientes){
		//document.getElementById("diagrama").innerHTML="";
		document.getElementById("diagrama").innerHTML='<div id="myDiagramDiv" style="border: solid 1px black; width: 100%; height: 1200"></div>';

		if (window.goSamples) goSamples(); // init for these samples -- you don't need to call this
		$ = go.GraphObject.make;

		myDiagram =
			$(go.Diagram, "myDiagramDiv", {
				initialContentAlignment: go.Spot.Left,
				initialAutoScale: go.Diagram.UniformToFill,
				layout: $(go.LayeredDigraphLayout, {
					direction: 0
				}),
				"undoManager.isEnabled": true
			});

		// when the document is modified, add a "*" to the title and enable the "Save" button
		myDiagram.addDiagramListener("Modified", function(e) {
			var button = document.getElementById("SaveButton");
			if (button) button.disabled = !myDiagram.isModified;
			var idx = document.title.indexOf("*");
			if (myDiagram.isModified) {
				if (idx < 0) document.title += "*";
			} else {
				if (idx >= 0) document.title = document.title.substr(0, idx);
			}
		});

		//datosProveedores
		//datosClientes

		listaEmpresas.forEach((item, i) => {
			//console.log(item);

			datosProveedores.forEach((_itm, _i) => {
				_itm.forEach((_itm2, _i2) => {
					console.log(_itm2);
					if (_itm2.idEmpresa == item.idEmpresa || _itm2.idEmpresaAnterior == item.idEmpresa){
						console.log(item.idEmpresa);
						makeTemplate(
							//"Table",
							'Proveedor: '+item.nombre,
							//"/img/200725110349_9152.png",
							//"{{asset('img/200725110349_9152.png')}}",
							"/img/"+item.foto,
							"forestgreen",
							[makePort("I", false)],
							[makePort("D", false)]
						);
					}
				});
			});
			datosClientes.forEach((_itm, _i) => {
				_itm.forEach((_itm2, _i2) => {
					console.log(_itm2);
					if (_itm2.idEmpresa == item.idEmpresa || _itm2.idEmpresaAnterior == item.idEmpresa){
						console.log(item.idEmpresa);
						makeTemplate(
							'Cliente: '+item.nombre,
							"/img/"+item.foto,
							"forestgreen",
							[makePort("I", false)],
							[makePort("D", false)]
						);
					}
				});
			});
			/*
			makeTemplate(
				//"Table",
				'Proveedor: '+item.nombre,
				//"/img/200725110349_9152.png",
				//"{{asset('img/200725110349_9152.png')}}",
				"/img/"+item.foto,
				"forestgreen",
				[makePort("I", false)],
				[makePort("D", false)]
			);
			makeTemplate(
				'Cliente: '+item.nombre,
				"/img/"+item.foto,
				"forestgreen",
				[makePort("I", false)],
				[makePort("D", false)]
			);
			*/
		});

		myDiagram.linkTemplate =
			$(go.Link, {
				routing: go.Link.Orthogonal,
				corner: 5,
				relinkableFrom: true,
				relinkableTo: true
			},
			$(go.Shape, {
				stroke: "gray",
				strokeWidth: 2
			}),
			$(go.Shape, {
				stroke: "gray",
				fill: "gray",
				toArrow: "Standard"
			})
		);
	}


	document.addEventListener('botondescargar', (datos) => {
		botondescargar();
	});


	//https://www.todojs.com/obtener-todas-las-propiedades-objeto/
	function getPropertyNames(obj) {
	    var proto = Object.getPrototypeOf(obj);
	    return (
	        (typeof proto === 'object' && proto !== null ? getPropertyNames(proto) : [])
	        .concat(Object.getOwnPropertyNames(obj))
	        .filter(function(item, pos, result) { return result.indexOf(item) === pos; })
	        .sort()
	    );
	}
	function botondescargar() {
		var jpg = myDiagram.makeImage({background: "white"});
		console.log(getPropertyNames(jpg));
		console.log(jpg.src);
		var a = document.createElement('a');
		a.href = jpg.src;
		a.download = "output.png";
		document.body.appendChild(a);
		console.log(a);
		a.click();
		document.body.removeChild(a);
		return;
	}


	function myCallback(blob) {
      var url = window.URL.createObjectURL(blob);
      var filename = "mySVGFile.svg";

      var a = document.createElement("a");
      a.style = "display: none";
      a.href = url;
      a.download = filename;

      // IE 11
      if (window.navigator.msSaveBlob !== undefined) {
        window.navigator.msSaveBlob(blob, filename);
        return;
      }

      document.body.appendChild(a);
      requestAnimationFrame(function() {
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
      });
    }

    function makeSvg() {
      var svg = myDiagram.makeSvg({ scale: 1, background: "white" });
      var svgstr = new XMLSerializer().serializeToString(svg);
      var blob = new Blob([svgstr], { type: "image/svg+xml" });
      myCallback(blob);
    }




	// Show the diagram's model in JSON format that the user may edit
	function save(){
		document.getElementById("mySavedModel").value = myDiagram.model.toJson();
		myDiagram.isModified = false;
	}

	function load(modelo){
		//myDiagram.model = go.Model.fromJson(document.getElementById("mySavedModel").value);
		myDiagram.model = go.Model.fromJson(modelo);
	}
</script>

<!--
<script>
function init() {
	if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this

	var $ = go.GraphObject.make;  // for conciseness in defining templates

	myDiagram = $(go.Diagram, "myDiagramDiv",  // create a Diagram for the DIV HTML element
	{
		"undoManager.isEnabled": true  // enable undo & redo
	});

	// define a simple Node template
	myDiagram.nodeTemplate =
	$(go.Node, "Auto",  // the Shape will go around the TextBlock
	$(go.Shape, "RoundedRectangle", { strokeWidth: 0 },
	// Shape.fill is bound to Node.data.color
	new go.Binding("fill", "color")),
	$(go.Picture, { margin: 8, width: 55, height: 55 },
		new go.Binding("source"))
	);

	// create the model data that will be represented by Nodes and Links
	myDiagram.model = new go.GraphLinksModel(
		[
			{ key: "Alpha", source: "{{asset('matrix/assets/images/favicon.png')}}", color: "lightblue" },
			{ key: "Beta",  source: "{{asset('matrix/assets/images/favicon.png')}}", color: "orange" },
			{ key: "Gamma", source: "{{asset('matrix/assets/images/favicon.png')}}", color: "lightgreen" },
			{ key: "Delta", source: "{{asset('matrix/assets/images/favicon.png')}}", color: "white" },
			{ key: "Delta2", source: "{{asset('matrix/assets/images/favicon.png')}}", color: "red" },
			{ key: "Delta3", source: "{{asset('matrix/assets/images/favicon.png')}}", color: "lightgreen" },
			{ key: "Delta4", source: "{{asset('matrix/assets/images/favicon.png')}}", color: "green" }
		],
		[
			{ from: "Alpha", to: "Beta" },
			{ from: "Alpha", to: "Gamma" },
			{ from: "Beta", to: "Beta" },
			{ from: "Gamma", to: "Delta" },
			{ from: "Delta", to: "Alpha" },
			{ from: "Delta", to: "Delta2" },
			{ from: "Delta2", to: "Delta3" },
			{ from: "Delta3", to: "Delta4" }
		]);


	} // end init
	function toDataURL(url, callback) {
		var xhr = new XMLHttpRequest();
		xhr.onload = function() {
			var reader = new FileReader();
			reader.onloadend = function() {
				callback(reader.result);
			}
			reader.readAsDataURL(xhr.response);
		};
		xhr.open('GET', url);
		xhr.responseType = 'blob';
		xhr.send();
	}
	// Make SVG, but modify the SVG <image> Element's href to refer to a Base64 URI instead of the go.Picture source URL.
	function makeSVG() {
		var svg = myDiagram.makeSVG({
			elementFinished: function(graphobject, svgelement) {
				if (!(graphobject instanceof go.Picture)) return;
				toDataURL(svgelement.href.baseVal, function(dataUrl) {
					svgelement.setAttribute('href', dataUrl);
				});
			}
		});
		document.getElementById('SVGResult').innerHTML = "";
		document.getElementById('SVGResult').appendChild(svg);
		////
		document.getElementById('item1').innerHTML = '<canvas id="canvas" width="800" height="400"></canvas>';
		document.getElementById('item2').innerHTML = '<div id="png-container"></div>';
	}
	//http://bl.ocks.org/biovisualize/8187844
	function descrgar(imggg){
		console.log('...');
		try {
			var a = document.createElement('a');
			a.href = imggg;
			a.download = "output.png";
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);
		} catch (e) {
			console.log(e);
		} finally {
			console.log('.-.');
		}
		console.log('...');
	}
	function makeJPG(){
		var svg = document.getElementById('SVGResult').innerHTML;
		//////////
		var svgString = new XMLSerializer().serializeToString(document.querySelector('svg'));
		document.getElementById("canvas").innerHTML = "";
		var canvas = document.getElementById("canvas");
		var ctx = canvas.getContext("2d");
		var DOMURL = self.URL || self.webkitURL || self;
		var img = new Image();
		var svg = new Blob([svgString], {type: "image/svg+xml;charset=utf-8"});
		var url = DOMURL.createObjectURL(svg);
		var png;
		img.onload = function() {
			ctx.drawImage(img, 0, 0);
			/*var */png = canvas.toDataURL("image/png");
			document.querySelector('#png-container').innerHTML = "";
			document.querySelector('#png-container').innerHTML = '<img src="'+png+'"/>';
			console.log(png);
			//DOMURL.revokeObjectURL(png);
			descrgar(png);
			//////////

		};
		img.src = url;
		//console.log(img);
		//descrgar(png);
	}
</script>
-->
@endsection
