@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ url('/listado-reservas') }}" class="btn btn-outline-danger" type="button">Regresar</a>
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="form-container">
                <h2>Ver Reserva</h2>



                <form action="{{ route('actualizar-registro-reserva', $clave) }}" method="POST" enctype="multipart/form-data" onsubmit="mostrarMensajeEdicionExitosa()">
                    @csrf
                    <div class="mb-3">
                        <label for="libro" class="form-label">Libro</label>
                        <input type="text" id="libro"  class="form-control" name="libro" value="{{ $registro['libro'] }}" placeholder="Nombre del Libro" autocomplete="off" disabled>
                        <div class="suggestions-container">
                          <ul id="suggestions"></ul>
                    </div>


                    <div class="mb-3">
                        <label for="formGroupExampleInput" class="form-label">Autor</label>
                        <input type="text" class="form-control" id="autor" name="autor" value="{{ $registro['autor'] }}" placeholder="Nombre del Autor" disabled>
                    </div>

  
                    <label for="fechaReserva">Fecha y Hora de Reserva:</label>
                    <div class="row">
                        <div class="col">
                            <input type="date" value="{{ $registro['fechaReserva'] }}" class="form-control" id="fechaReserva" name="fechaReserva" disabled>
                        </div>
                        <div class="col">
                            <input type="time" value="{{ $registro['horaReserva'] }}" class="form-control" id="horaReserva" name="horaReserva" disabled>
                        </div>
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
                        <label for="formGroupExampleInput" class="form-label">Nombre de Estudiante/Docente</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $registro['nombre'] }}"  placeholder="Ingresa El Nombre" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="formGroupExampleInput2" class="form-label">Cédula de Estudiante/Docente</label>
                        <input type="number" class="form-control" id="cedula" name="cedula" value="{{ $registro['cedula'] }}"  placeholder="Número de Cédula" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email"  class="form-control"   id="correo" name="correo"  value="{{ $registro['correo'] }}" placeholder="Correo" required disabled>
                    </div>
                    <div class="mb-3">
                        <label for="formGroupExampleInput" class="form-label">Agregar un comentario</label>
                        <input class="form-control" id="comentario" name="comentario"value="{{ isset($registro['comentario']) ? $registro['comentario'] : '' }}" disabled >
                    </div>

                    <button id="createLibroButton" type="submit" class="btn btn-success btn-block" disabled>Editar Reserva</button> 
                </form>
                <br>

            <center>
                <button id="activar" class="btn btn-danger btn-block" type="button" onclick="activarCampos()">Activar Edición</button>
            </center>



            </div>
        </div>
    </div>
        <script>
    let camposHabilitados = false;
    function activarCampos() {
        if (!camposHabilitados) {
            // Obtenemos todos los campos de formulario
            const campos = document.querySelectorAll('.form-control');

            // Iteramos sobre cada campo y los habilitamos
            campos.forEach((campo) => {
                campo.removeAttribute('disabled');
            });

            // Habilitar el botón de "Editar Reserva"
            document.getElementById('createLibroButton').removeAttribute('disabled');
            camposHabilitados = true;

            // Bloquear el botón "Activar Edición"
            document.getElementById('activar').setAttribute('disabled', 'disabled');
        }
    }
            function mostrarMensajeEdicionExitosa() {
                // Redireccion a la pantalla principal
                window.location.href = "{{ url('/listado-reservas') }}";

                // Mostrar mensaje de éxito 
                alert("La reserva fue editada con éxito");
            }

      // Obtén la lista de libros activos desde PHP y conviértela en una variable de JavaScript
  var librosActivos = <?php echo json_encode($librosActivos); ?>;

  // Función para manejar la entrada de texto y mostrar sugerencias
  $(document).ready(function() {

    var maxSuggestions = 3;
    $('#libro').on('input', function() {
      var inputText = $(this).val();
      var suggestions = [];

      // Filtrar los libros que coincidan con la entrada de texto
      for (var i = 0; i < librosActivos.length; i++) {
        if (librosActivos[i].toLowerCase().includes(inputText.toLowerCase())) {
          suggestions.push(librosActivos[i]);
        }
      }

     // Mostrar solo las primeras maxSuggestions sugerencias
    var suggestionsList = $('#suggestions');
    suggestionsList.empty();
    for (var j = 0; j < Math.min(maxSuggestions, suggestions.length); j++) {
      suggestionsList.append('<li>' + suggestions[j] + '</li>');
    }

    // Mostrar o ocultar la lista desplegable según si hay sugerencias
    if (suggestions.length > 0) {
      suggestionsList.parent().show();
    } else {
      suggestionsList.parent().hide();
    }       
    });

    // Manejar el clic en una sugerencia para rellenar el campo de texto
    $(document).on('click', '#suggestions li', function() {
      var selectedSuggestion = $(this).text();
      $('#libro').val(selectedSuggestion);
      $('#suggestions').empty().parent().hide();
    });

    // Ocultar la lista desplegable cuando se hace clic en cualquier lugar fuera de ella
    $(document).on('click', function(e) {
      var container = $('.suggestions-container');
      if (!container.is(e.target) && container.has(e.target).length === 0) {
        $('#suggestions').empty().parent().hide();
      }
    });
  });
</script>

</div>

@endsection