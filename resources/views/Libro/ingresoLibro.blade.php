@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ url('/listado-libro') }}" class="btn btn-outline-danger" type="button">Regresar</a>
    <div class="row justify-content-center">
        <div class="col-md-8">
            
 <div class="form-container">
        <h2>Crea Libro</h2>
        <form action="{{ route('registrolibro') }}" method="POST" enctype="multipart/form-data" id="crearLibroForm">
            @csrf
            <label for="titulo">Titulo:</label>
            <input type="text" name="nombre" autocomplete="off" required>

            <label for="autor">Autor:</label>
            <input type="text" name="autor" autocomplete="off"required>

            <label for="nombre_editorial">Nombre Editorial:</label>
            <input type="text" name="nombre_editorial" autocomplete="off" required>

            <label for="resenia">Reseña:</label>
            <textarea name="resenia" autocomplete="off" required></textarea>

            <label for="anio_publicacion">Año de Publicación:</label>
            <input type="number" name="anio_publicacion" autocomplete="off" required>

            <label for="link_drive">Link de Drive:</label>
            <input type="url" name="link_drive" autocomplete="off" >

                            <div class="mb-3">
                    <label for="formGroupExampleInput2" class="form-label">Carrera</label>
                    <select class="form-control" id="carrera" name="carrera">
                        @foreach($carreras as $carrera)
                        @if(isset($carrera['nombre_carrera' ]) && $carrera['estado'] === 'Activo')
                                <option value="{{ htmlspecialchars($carrera['nombre_carrera']) }}">{{ htmlspecialchars($carrera['nombre_carrera']) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="formGroupExampleInput2" class="form-label">Área</label>
                    <select class="form-control" id="area" name="area">
                        @foreach($areas as $area)
                             @if(isset($area['nombre_area' ]) && $area['estado'] === 'Activo')
                                <option value="{{ htmlspecialchars($area['nombre_area']) }}">{{ htmlspecialchars($area['nombre_area']) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="formGroupExampleInput2" class="form-label">Categoría</label>
                    <select class="form-control" id="categoria" name="categoria">
                        @foreach($categorias as $categoria)
                             @if(isset($categoria['nombre_categoria' ]) && $categoria['estado'] === 'Activo')
                                <option value="{{ htmlspecialchars($categoria['nombre_categoria']) }}">{{ htmlspecialchars($categoria['nombre_categoria']) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

            <label for="imagen">Imagen:</label>
            <input type="file" class="form-control" name="imagen">

                    <label><input type="checkbox" style="width: 15px; height: 15px;" id="fisico" name="fisico" value="true" class="checkbox-grande" {{ old('fisico') ? 'checked' : '' }}>¿Es un libro Físico?</label><br>

                    <label><input type="checkbox" style="width: 15px; height: 15px;" id="documento" name="documento" value="true" class="checkbox-grande" {{ old('documento') ? 'checked' : '' }}>¿Es un documento para todas las carreras?</label><br>

                    <input type="submit" id="CrearLibro">
        </form>
    </div>

        </div>
    </div>
</div>

            <script>
                document.getElementById('crearLibroForm').addEventListener('submit', function (event) {
                    // Verificar si algún campo obligatorio está vacío
                    var camposVacios = false;
                    var requiredInputs = document.querySelectorAll('[required]');
                    requiredInputs.forEach(function (input) {
                        if (input.value.trim() === '') {
                            camposVacios = true;
                        }
                    });

                    if (camposVacios) {
                        event.preventDefault(); // Evita que el formulario se envíe
                        mostrarMensajeError('Por favor, complete todos los campos obligatorios.');
                    } else {
                        mostrarMensajeExito('Libro creado con éxito');
                        // Redirigir después de mostrar el mensaje de éxito
                        setTimeout(function () {
                            window.location.href = "{{ url('/listado-libro') }}";
                        }, 1000); 
                    }
                });

                function mostrarMensajeError(mensaje) {
                    var mensajeError = document.getElementById('mensajeError');
                    if (!mensajeError) {
                        mensajeError = document.createElement('div');
                        mensajeError.id = 'mensajeError';
                        mensajeError.style.color = 'red';
                        document.getElementById('crearLibroForm').prepend(mensajeError);
                    }
                    mensajeError.textContent = mensaje;
                }

                function mostrarMensajeExito(mensaje) {
                    alert(mensaje);
                }
            </script>
@endsection