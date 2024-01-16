@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ url('/listado-carreras') }}" class="btn btn-outline-danger" type="button">Regresar</a>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="form-container">
                <h2>Editar Carrera </h2>
              <form  method="POST"  action="{{ route('actualizar-carrera', $clave) }}" enctype="multipart/form-data">
                @csrf


                
                <div class="mb-3">
                    <label for="formGroupExampleInput2" class="form-label">Area</label>
                    <input type="text" class="form-control" id="carrera" name="carrera" placeholder="Nombre de la Carrera" value="{{ $registro['nombre_carrera'] }}"required>
                </div>


      
                    <button type="submit" class="btn btn-success btn-block" id="ActualizarCarrera">Actualizar
                        Carrera</button>

            </form>
            </div>



        </div>
    </div>
</div>
    <script>
        document.getElementById('ActualizarCarrera').addEventListener('click', function() {
            alert('Carrera editada con éxito');
            window.location.href = "{{ url('/listar-carrera') }}"; 
        });
    </script>
@endsection