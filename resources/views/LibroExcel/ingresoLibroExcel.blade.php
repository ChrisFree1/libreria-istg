@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ url('/listado-libro') }}" class="btn btn-outline-danger" type="button">Regresar</a>
    <a href="{{ route('descargar.template') }}" class="btn btn-primary" target="_blank">Descargar Plantilla Excel</a>

    <div class="row justify-content-center">
        <div class="col-md-8">
            
 <div class="form-container">
    <form action="{{ route('import.excel') }}" method="post" enctype="multipart/form-data">
    @csrf
    <input type="file" name="excel_file">
    <button type="submit">Subir Excel</button>
</form>

    </div>

        </div>
    </div>
</div>
@endsection