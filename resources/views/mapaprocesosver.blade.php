@extends('layout')

@section('titulo')
Mapa de Procesos - {{$mapaprocesos['nombreempresa']}}
@endsection

@section('subtitulo')
Unidad de Negocio: {{$mapaprocesos['nombreunidadnegocio']}}
@endsection

@section('html')
<!--<cadenas-modelador></cadenas-modelador>-->
<mapaprocesos-modelador></mapaprocesos-modelador>
@endsection

<script>
	let elemento = @json($mapaprocesos);
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
		//document.getElementById("svgButton").addEventListener("click", makeSvg);
	}
	document.addEventListener('creargrafico', (datos) => {
		if (datos.detail.type == 'MAPAPROCESO')
			creargrafico(datos);
	});

	function creargrafico(datos){
		document.getElementById("diagrama").innerHTML='<div id="myDiagramDiv" style="border: solid 1px black; width: 100%; height: 500"></div>';

		if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this
		var $ = go.GraphObject.make;  // for conciseness in defining templates
		myDiagram =
		$(go.Diagram, "myDiagramDiv",  // create a Diagram for the DIV HTML element
		{
			// allow double-click in background to create a new node
			"clickCreatingTool.archetypeNodeData": { text: "Node", color: "white" },

			// allow Ctrl-G to call groupSelection()
			"commandHandler.archetypeGroupData": { text: "Group", isGroup: true, color: "blue" },

			// enable undo & redo
			"undoManager.isEnabled": true
		});

		var partContextMenu =
		$("ContextMenu",
			makeButton("Properties",
			function(e, obj) {  // OBJ is this Button
				var contextmenu = obj.part;  // the Button is in the context menu Adornment
				var part = contextmenu.adornedPart;  // the adornedPart is the Part that the context menu adorns
				// now can do something with PART, or with its data, or with the Adornment (the context menu)
				if (part instanceof go.Link) alert(linkInfo(part.data));
				else if (part instanceof go.Group) alert(groupInfo(contextmenu));
				else alert(nodeInfo(part.data));
			}),
			makeButton("Cut",
			function(e, obj) { e.diagram.commandHandler.cutSelection(); },
			function(o) { return o.diagram.commandHandler.canCutSelection(); }),
			makeButton("Copy",
			function(e, obj) { e.diagram.commandHandler.copySelection(); },
			function(o) { return o.diagram.commandHandler.canCopySelection(); }),
			makeButton("Paste",
			function(e, obj) { e.diagram.commandHandler.pasteSelection(e.diagram.toolManager.contextMenuTool.mouseDownPoint); },
			function(o) { return o.diagram.commandHandler.canPasteSelection(o.diagram.toolManager.contextMenuTool.mouseDownPoint); }),
			makeButton("Delete",
			function(e, obj) { e.diagram.commandHandler.deleteSelection(); },
			function(o) { return o.diagram.commandHandler.canDeleteSelection(); }),
			makeButton("Undo",
			function(e, obj) { e.diagram.commandHandler.undo(); },
			function(o) { return o.diagram.commandHandler.canUndo(); }),
			makeButton("Redo",
			function(e, obj) { e.diagram.commandHandler.redo(); },
			function(o) { return o.diagram.commandHandler.canRedo(); }),
			makeButton("Group",
			function(e, obj) { e.diagram.commandHandler.groupSelection(); },
			function(o) { return o.diagram.commandHandler.canGroupSelection(); }),
			makeButton("Ungroup",
			function(e, obj) { e.diagram.commandHandler.ungroupSelection(); },
			function(o) { return o.diagram.commandHandler.canUngroupSelection(); })
		);

		myDiagram.nodeTemplate =
		$(go.Node, "Auto",
		{ locationSpot: go.Spot.Center },
		$(go.Shape, "RoundedRectangle",
		{
			fill: "white", // the default fill, if there is no data bound value
			portId: "", cursor: "pointer",  // the Shape is the port, not the whole Node
			// allow all kinds of links from and to this port
			fromLinkable: true, fromLinkableSelfNode: true, fromLinkableDuplicates: true,
			toLinkable: true, toLinkableSelfNode: true, toLinkableDuplicates: true
		},
		new go.Binding("fill", "color")),
		$(go.TextBlock,
			{
				font: "bold 14px sans-serif",
				stroke: '#333',
				margin: 6,  // make some extra space for the shape around the text
				isMultiline: false,  // don't allow newlines in text
				editable: true  // allow in-place editing by user
			},
			new go.Binding("text", "text").makeTwoWay()),  // the label shows the node data's text
			{ // this tooltip Adornment is shared by all nodes
				toolTip:
				$("ToolTip",
				$(go.TextBlock, { margin: 4 },  // the tooltip shows the result of calling nodeInfo(data)
					new go.Binding("text", "", nodeInfo))
				),
				// this context menu Adornment is shared by all nodes
				contextMenu: partContextMenu
			}
		);


		myDiagram.linkTemplate =
		$(go.Link, { toShortLength: 3, relinkableFrom: true, relinkableTo: true },  // allow the user to relink existing links
			$(go.Shape, { strokeWidth: 2 }, new go.Binding("stroke", "color")),
			$(go.Shape, { toArrow: "Standard", stroke: null }, new go.Binding("fill", "color")),
				{ // this tooltip Adornment is shared by all links
					toolTip:
					$("ToolTip",
					$(go.TextBlock, { margin: 4 },  // the tooltip shows the result of calling linkInfo(data)
						new go.Binding("text", "", linkInfo))
					),
					// the same context menu Adornment is shared by all links
					contextMenu: partContextMenu
				}
		);

		myDiagram.groupTemplate =
		$(go.Group, "Vertical",
		{
			selectionObjectName: "PANEL",  // selection handle goes around shape, not label
			ungroupable: true  // enable Ctrl-Shift-G to ungroup a selected Group
		},
		$(go.TextBlock,
			{
				//alignment: go.Spot.Right,
				font: "bold 19px sans-serif",
				isMultiline: false,  // don't allow newlines in text
				editable: true  // allow in-place editing by user
			},
			new go.Binding("text", "text").makeTwoWay(),
			new go.Binding("stroke", "color")),
			$(go.Panel, "Auto",
				{ name: "PANEL" },
				$(go.Shape, "Rectangle",  // the rectangular shape around the members
				{
				fill: "rgba(128,128,128,0.2)", stroke: "gray", strokeWidth: 3,
				portId: "", cursor: "pointer",  // the Shape is the port, not the whole Node
				// allow all kinds of links from and to this port
				fromLinkable: true, fromLinkableSelfNode: true, fromLinkableDuplicates: true,
				toLinkable: true, toLinkableSelfNode: true, toLinkableDuplicates: true
			}),
				$(go.Placeholder, { margin: 10, background: "transparent" })  // represents where the members are
			),
			{ // this tooltip Adornment is shared by all groups
				toolTip:
				$("ToolTip",
				$(go.TextBlock, { margin: 4 },
					// bind to tooltip, not to Group.data, to allow access to Group properties
					new go.Binding("text", "", groupInfo).ofObject())
				),
				// the same context menu Adornment is shared by all groups
				contextMenu: partContextMenu
			}
		);

		myDiagram.toolTip =
		$("ToolTip",
			$(go.TextBlock, { margin: 4 },
			new go.Binding("text", "", diagramInfo))
		);

		myDiagram.contextMenu =
		$("ContextMenu",
			makeButton("Paste",
			function(e, obj) { e.diagram.commandHandler.pasteSelection(e.diagram.toolManager.contextMenuTool.mouseDownPoint); },
			function(o) { return o.diagram.commandHandler.canPasteSelection(o.diagram.toolManager.contextMenuTool.mouseDownPoint); }),
			makeButton("Undo",
			function(e, obj) { e.diagram.commandHandler.undo(); },
			function(o) { return o.diagram.commandHandler.canUndo(); }),
			makeButton("Redo",
			function(e, obj) { e.diagram.commandHandler.redo(); },
			function(o) { return o.diagram.commandHandler.canRedo(); })
		);


		console.log(datos.detail);
		/*
		var nodeDataArray = [
			{ key: 1, text: "Alpha", color: "lightblue" },
			{ key: 2, text: "Beta", color: "orange" },
			{ key: 3, text: "Gamma", color: "lightgreen", group: 5 },
			{ key: 4, text: "Delta", color: "pink", group: 5 },
			{ key: 34, text: "Delta", color: "pink", isGroup: true, group: 5 },
			{ key: 44, text: "Delta", color: "pink", group: 34 },
			{ key: 5, text: "Epsilon", color: "green", isGroup: true },
			{ key: 6, text: "Epsilon6", color: "green", isGroup: true }
		];
		var linkDataArray = [
			{ from: 1, to: 2, color: "blue" },
			{ from: 2, to: 2 },
			{ from: 3, to: 4, color: "green" },
			{ from: 3, to: 1, color: "purple" },
			{ from: 6, to: 5, color: "purple" }
		];
		*/
		myDiagram.model = new go.GraphLinksModel(
			datos.detail.nodeDataArray,
			datos.detail.linkDataArray
		);
	}

	function makeButton(text, action, visiblePredicate) {
		return $("ContextMenuButton",
		$(go.TextBlock, text),
		{ click: action },
		// don't bother with binding GraphObject.visible if there's no predicate
		visiblePredicate ? new go.Binding("visible", "", function(o, e) { return o.diagram ? visiblePredicate(o, e) : false; }).ofObject() : {});
	}
	function nodeInfo(d) {  // Tooltip info for a node data object
		var str = "Node " + d.key + ": " + d.text + "\n";
		if (d.group)
		str += "member of " + d.group;
		else
		str += "top-level node";
		return str;
	}
	function linkInfo(d) {  // Tooltip info for a link data object
		return "Link:\nfrom " + d.from + " to " + d.to;
	}
	function groupInfo(adornment) {  // takes the tooltip or context menu, not a group node data object
		var g = adornment.adornedPart;  // get the Group that the tooltip adorns
		var mems = g.memberParts.count;
		var links = 0;
		g.memberParts.each(function(part) {
			if (part instanceof go.Link) links++;
		});
		return "Group " + g.data.key + ": " + g.data.text + "\n" + mems + " members including " + links + " links";
	}
	function diagramInfo(model) {  // Tooltip info for the diagram's model
		return "Model:\n" + model.nodeDataArray.length + " nodes, " + model.linkDataArray.length + " links";
	}



</script>
@endsection
