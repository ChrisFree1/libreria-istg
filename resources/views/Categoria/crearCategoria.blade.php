@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ url('/listado-categorias') }}" class="btn btn-outline-danger" type="button">Regresar</a>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="form-container">
                <h2>Crear Categoria </h2>
              <form action="{{ route('registroCategoria') }}" method="POST" enctype="multipart/form-data">
                @csrf


                
                <div class="mb-3">
                    <label for="formGroupExampleInput2" class="form-label">Categoria</label>
                    <input type="text" class="form-control" id="categoria" name="categoria" placeholder="Nombre de la Categoria" required>
                </div>


      
                    <button type="submit" class="btn btn-success btn-block" id="CrearCategoria">Crear
                        Categoria</button>

            </form>
            </div>



        </div>
    </div>
</div>

        <script>
            document.getElementById('CrearCategoria').addEventListener('click', function() {
                alert('Categoría creada con éxito');
                window.location.href = "{{ url('/listado-categorias') }}";
            });
        </script>
@endsection