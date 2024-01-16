@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ url('/listado-area-carreras') }}" class="btn btn-outline-danger" type="button">Regresar</a>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="form-container">
                <h2>Crear Área por Carrera </h2>

                <form  method="POST" action="{{ route('registroAreaCarrera') }}" enctype="multipart/form-data">
                @csrf


                
                <div class="mb-3">
                    <label for="formGroupExampleInput2" class="form-label">Carrera</label>
                    <select class="form-control" id="carrera" name="carrera">
                        @foreach($carreras as $carrera)
                            @if(is_array($carrera) && isset($carrera['nombre_carrera']))
                                <option value="{{ htmlspecialchars($carrera['nombre_carrera']) }}">{{ htmlspecialchars($carrera['nombre_carrera']) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>


                <div class="mb-3">
                    <label for="formGroupExampleInput2" class="form-label">Área</label>
                    <select class="form-control" id="area" name="area">
                        @foreach($areas as $area)
                            @if(is_array($area) && isset($area['nombre_area']))
                                <option value="{{ htmlspecialchars($area['nombre_area']) }}">{{ htmlspecialchars($area['nombre_area']) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <label for="prefijo">Prefijo de la carrera 3 caracteres:</label>
                <input type="text" name="prefijo" maxlength="3" required>

                    <button type="submit" class="btn btn-success btn-block" id="CrearAreaPorCarrera">Crear
                        Área por Carrera</button>
             </form>

            </div>



        </div>
    </div>
</div>

        <script>
            document.getElementById('CrearAreaPorCarrera').addEventListener('click', function() {
                alert('Área por Carrera creada con éxito');
                window.location.href = "{{ url('/listado-area-carreras') }}";
            });
        </script>

@endsection