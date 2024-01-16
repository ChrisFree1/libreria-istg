@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ url('/listado-area-carreras') }}" class="btn btn-outline-danger" type="button">Regresar</a>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="form-container">
                <h2>Editar Área por Carrera</h2>

                <form method="POST" action="{{ route('actualizar-area-carrera', $clave) }}" enctype="multipart/form-data">
                    @csrf
<div class="mb-3">
    <label for="formGroupExampleInput2" class="form-label">Carrera</label>
    <select class="form-control" id="carrera" name="carrera">
        @foreach($carreras as $index => $carrera)
            @if(isset($carrera['nombre_carrera']) && $carrera['estado'] === 'Activo')
                <option value="{{ $index }}" {{ $registro['nombre_carrera'] === $carrera['nombre_carrera'] ? 'selected' : '' }}>
                    {{ htmlspecialchars($carrera['nombre_carrera']) }}
                </option>
            @endif
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="formGroupExampleInput2" class="form-label">Área</label>
    <select class="form-control" id="area" name="area">
        @foreach($areas as $index => $area)
            @if(isset($area['nombre_area']) && $area['estado'] === 'Activo')
                <option value="{{ $index }}" {{ $registro['nombre_area'] === $area['nombre_area'] ? 'selected' : '' }}>
                    {{ htmlspecialchars($area['nombre_area']) }}
                </option>
            @endif
        @endforeach
    </select>
</div>
                    <label for="prefijo">Prefijo de la carrera (3 caracteres):</label>
                    <input type="text" name="prefijo" maxlength="3" value="{{ $registro['prefijo'] }}" required>

                    <button type="submit" class="btn btn-success btn-block" id= "btnActualizarAreaporCarrera">Editar Area por Carrera</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <script>
        document.getElementById('btnActualizarAreaporCarrera').addEventListener('click', function() {
            alert('Área por Carrera editada con éxito');
            window.location.href = "{{ url('/listado-area-carreras') }}"; 
        });
    </script>
@endsection