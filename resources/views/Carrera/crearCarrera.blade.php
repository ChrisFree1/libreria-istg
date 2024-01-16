@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ url('/listado-carreras') }}" class="btn btn-outline-danger" type="button">Regresar</a>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="form-container">
                <h2>Crear Carrera </h2>
              <form action="{{ route('registroCarrera') }}" method="POST" enctype="multipart/form-data">
                @csrf


                
                <div class="mb-3">
                    <label for="formGroupExampleInput2" class="form-label">Carrera</label>
                    <input type="text" class="form-control" id="carrera" name="carrera" placeholder="Nombre de la Carrera" required>
                </div>


      
                    <button type="submit" class="btn btn-success btn-block" id="CrearCarrera">Crear
                        Carrera</button>

            </form>
            </div>



        </div>
    </div>
</div>

        <script>
            document.getElementById('CrearCarrera').addEventListener('click', function() {
                alert('Carrera creada con éxito');
                window.location.href = "{{ url('/listar-carrera') }}";
            });
        </script>

@endsection