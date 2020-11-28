@extends('layout')

@section('titulo')
Gestionar Mapa de Procesos
@endsection

@section('subtitulo')
@endsection

@section('html')
<mapaprocesos></mapaprocesos>
@endsection

<script type="text/javascript">
	var mensaje = '';
	@if (session('mensaje'))
		mensaje = '{{ session('mensaje') }}';
	@endif
</script>
