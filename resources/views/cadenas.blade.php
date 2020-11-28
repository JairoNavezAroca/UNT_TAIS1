@extends('layout')

@section('titulo')
Gestionar Cadena de Suministros
@endsection

@section('subtitulo')
@endsection

@section('html')
<cadenas></cadenas>
@endsection

<script type="text/javascript">
	var mensaje = '';
	@if (session('mensaje'))
		mensaje = '{{ session('mensaje') }}';
	@endif
</script>
