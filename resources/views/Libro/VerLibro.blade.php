@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ url('/listado-libro') }}" class="btn btn-outline-danger" type="button">Regresar</a>
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="form-container">
                <h2>Editar Libro</h2>

                <form method="POST" action="{{ route('actualizar-registro-libro', $clave) }}" enctype="multipart/form-data" onsubmit="mostrarMensajeEdicionExitosa()">
                    @csrf
                    <div class="mb-3">
                    <label for="titulo">Titulo:</label>
                    <input type="text" class="form-control" name="nombre" autocomplete="off" value="{{ $registro['nombre'] }}" disabled>
                    </div>

                    <div class="mb-3">
                    <label for="titulo">Autor:</label>
                    <input type="text" class="form-control" name="autor" autocomplete="off" value="{{ $registro['autor'] }}" disabled>
                    </div>

                    <div class="mb-3">
                    <label for="nombre_editorial">Nombre Editorial:</label>
                    <input type="text" class="form-control" name="nombre_editorial" autocomplete="off" value="{{ $registro['nombre_editorial'] }}" disabled>
                    </div>

                    <div class="mb-3">
                    <label for="resenia">Reseña:</label>
                    <input name="resenia" class="form-control" value="{{ $registro['resenia'] }}"  autocomplete="off"disabled>
                    </div>

                    <div class="mb-3">
                    <label for="anio_publicacion">Año de Publicación:</label>
                    <input type="number" class="form-control" name="anio_publicacion" autocomplete="off" value="{{ $registro['anio_publicacion'] }}" disabled>
                    </div>

                    <div class="mb-3">
                    <label for="link_drive">Link de Drive:</label>
                    <input type="url" class="form-control" name="link_drive"autocomplete="off"  value="{{ isset($registro['link_drive']) ? $registro['link_drive'] : '' }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="formGroupExampleInput2" class="form-label">Carrera</label>
                        <select class="form-control" id="carrera" name="carrera" disabled>
                            @foreach($carreras as $carrera)
                            @if(isset($carrera['nombre_carrera' ]) && $carrera['estado'] === 'Activo')
                            <option value="{{ htmlspecialchars($carrera['nombre_carrera']) }}" {{ $registro['carrera'] === $carrera['nombre_carrera'] ? 'selected' : '' }}>
                                {{ htmlspecialchars($carrera['nombre_carrera']) }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="formGroupExampleInput2" class="form-label">Área</label>
                        <select class="form-control" id="area" name="area" disabled>
                            @foreach($areas as $area)
                            @if(isset($area['nombre_area' ]) && $area['estado'] === 'Activo')
                            <option value="{{ htmlspecialchars($area['nombre_area']) }}" {{ $registro['area'] === $area['nombre_area'] ? 'selected' : '' }}>
                                {{ htmlspecialchars($area['nombre_area']) }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="formGroupExampleInput2" class="form-label">Categoria</label>
                        <select class="form-control" id="categoria" name="categoria" disabled>
                            @foreach($categorias as $categoria)
                            @if(isset($categoria['nombre_categoria' ]) && $categoria['estado'] === 'Activo')
                            <option value="{{ htmlspecialchars($categoria['nombre_categoria']) }}" {{ $registro['categoria'] === $categoria['nombre_categoria'] ? 'selected' : '' }}>
                                {{ htmlspecialchars($categoria['nombre_categoria']) }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                    </div>
<div>

    
    <label for="imagen">Imagen:</label>
    <div>
        @if(isset($registro['imagen']) && $registro['imagen'] !== '')
         
            <a href="#" data-bs-toggle="modal" data-bs-target="#imagenModal">
                <img src="{{ $registro['imagen'] }}" alt="Imagen del libro" style="max-width: 300px; max-height: 300px;">
            </a>
        @else
            <p>No hay imagen disponible ¿Desea subir un nuevo archivo?</p>
        @endif
    </div>
    <input type="file" class="form-control" name="imagen" disabled>
</div>






<label><input type="checkbox" class="form-label" style="width: 15px; height: 15px;" id="fisico" name="fisico"  value="true" class="checkbox-grande" {{ isset($registro['fisico']) && $registro['fisico'] ? 'checked' : '' }}  disabled>¿Es un libro Físico?</label><br>

<label><input type="checkbox" class="form-label" style="width: 15px; height: 15px;" id="documento"  name="documento" value="true" class="checkbox-grande" {{ isset($registro['documento']) && $registro['documento'] ? 'checked' : '' }}  disabled>¿Es un documento para todas las carreras?</label><br>

    
                    <button id="createLibroButton" type="submit" class="btn btn-success btn-block" disabled>Editar Libro</button> 

                </form>
                <br>
               <center>
                    <button id="activar"   class="btn btn-danger btn-block"  onclick="activarCampos()">Activar Edición</button>
                    
               </center>
            </div>
        </div>
    </div>
<div class="modal fade" id="imagenModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm"> <!-- Cambio modal-lg por modal-sm -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Imagen del libro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center">
                @if(isset($registro['imagen']) && $registro['imagen'] !== '')
                    <img src="{{ $registro['imagen'] }}" alt="Imagen del libro" style="max-width: 100%; max-height: 100%;">
                @else
                    <p>No hay imagen disponible</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function activarCampos() {

        // Bloquear el botón "Activar Edición"
        document.getElementById('activar').setAttribute('disabled', 'disabled');

        // Obtenemos todos los campos de formulario excepto los checkboxes
        const campos = document.querySelectorAll('.form-control:not([type="checkbox"])');

        // Iteramos sobre cada campo y los habilitamos
        campos.forEach((campo) => {
            campo.removeAttribute('disabled');
        });

        // Habilitar los checkboxes
        const checkboxes = document.querySelectorAll('[type="checkbox"]');
        checkboxes.forEach((checkbox) => {
            checkbox.removeAttribute('disabled');
        });

        // Habilitar el botón de "Editar Libro"
        document.getElementById('createLibroButton').removeAttribute('disabled');
    }

        function mostrarMensajeEdicionExitosa() {
        // Redireccion a la pantalla principal
        window.location.href = "{{ url('/listado-libro') }}";

        // Mostrar mensaje de éxito 
        alert("El libro fue editado con éxito");
    }

    
</script>


</div>
@endsection