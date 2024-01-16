@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ url('/listado-areas') }}" class="btn btn-outline-danger" type="button">Regresar</a>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="form-container">
                <h2>Editar Área </h2>
              <form  method="POST"  action="{{ route('actualizar-area', $clave) }}" enctype="multipart/form-data">
                @csrf


                
                <div class="mb-3">
                    <label for="formGroupExampleInput2" class="form-label">Área</label>
                    <input type="text" class="form-control" id="area" name="area" placeholder="Nombre de la Area" value="{{ $registro['nombre_area'] }}"required>
                </div>


      
                    <button type="submit" class="btn btn-success btn-block" id="btnActualizarArea">Actualizar
                        Área</button>

            </form>
            </div>



        </div>
    </div>
</div>


<script>
    document.getElementById('btnActualizarArea').addEventListener('click', function() {
        alert('Área actualizada con éxito');
        window.location.href = "{{ url('/buscar-area') }}"; 
    });
</script>
@endsection