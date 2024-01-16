@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ url('/listado-areas') }}" class="btn btn-outline-danger" type="button">Regresar</a>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="form-container">
                <h2>Crear Área </h2>
              <form action="{{ route('registroArea') }}" method="POST" enctype="multipart/form-data">
                @csrf


                
                <div class="mb-3">
                    <label for="formGroupExampleInput2" class="form-label">Área</label>
                    <input type="text" class="form-control" id="area" name="area" placeholder="Nombre de la Área" required>
                </div>


      
                    <button type="submit" id="Crear" class="btn btn-success btn-block">Crear
                        Área</button>

            </form>
            </div>



        </div>
    </div>

        <script>
            document.getElementById('Crear').addEventListener('click', function() {
                alert('Área creada con éxito');
                window.location.href = "{{ url('/listado-areas') }}";
            });
        </script>

</div>
@endsection