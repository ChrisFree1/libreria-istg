@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">

      <a href="{{ url('/listado-areas') }}" class="btn btn-outline-primary" type="button">Mantenimiento Áreas</a>
      <a href="{{ url('/listado-carreras') }}" class="btn btn-outline-primary" type="button">Mantenimiento Carreras</a>
      <a href="{{ url('/listado-area-carreras') }}" class="btn btn-outline-primary" type="button">Mantenimiento Áreas por Carreras</a>
      <a href="{{ url('/listado-categorias') }}" class="btn btn-outline-primary" type="button">Mantenimiento Categoria</a>
      <a href="{{ url('/listado-parametros') }}" class="btn btn-outline-primary" type="button">Mantenimiento Parámetros</a>



    </div>
  </div>
</div>
@endsection