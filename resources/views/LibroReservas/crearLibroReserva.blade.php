@extends('layouts.app')

@section('content')
<div class="container">
  <a href="{{ url('/listado-reservas') }}" class="btn btn-outline-danger" type="button">Regresar</a>
  <div class="row justify-content-center">
    <div class="col-md-8">

      <div class="form-container">
        <h2>Crea Reserva</h2>



        <form action="{{ route('registroLibroReserva') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="mb-3">
            <label for="libro" class="form-label">Libro</label>
            <input type="text" id="libro" name="libro" placeholder="Nombre del Libro" autocomplete="off" required>
            <div class="suggestions-container">
              <ul id="suggestions"></ul>
            </div>
            <div id="libro-error" class="alert alert-danger " role="alert" style="display: none;">
              El libro que ingreso no se encuentra disponible.
            </div>


          </div>


          <div class="mb-3">
            <label for="autor" class="form-label">Autor</label>
            <input type="text" class="form-control" id="autor" name="autor" autocomplete="off" placeholder="Nombre del Autor" required>
          </div>

          <label for="fechaReserva">Fecha y Hora de Reserva:</label>
          <div class="row">
            <div class="col">
              <input type="date" class="form-control" id="fechaReserva" name="fechaReserva" required>
            </div>
            <div class="col">
              <input type="time" class="form-control" id="horaReserva" name="horaReserva" required>
            </div>
          </div>


          <div class="mb-3">
            <label for="carrera" class="form-label">Carrera</label>
            <select class="form-control" id="carrera" name="carrera" autocomplete="off" required>
              @foreach($carreras as $carrera)
              @if(isset($carrera['nombre_carrera' ]) && $carrera['estado'] === 'Activo')
              <option value="{{ htmlspecialchars($carrera['nombre_carrera']) }}">{{ htmlspecialchars($carrera['nombre_carrera']) }}</option>
              @endif
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de Estudiante/Docente</label>
            <input type="text" class="form-control" id="nombre" name="nombre" autocomplete="off" placeholder="Ingresa El Nombre" required>
          </div>

          <div class="mb-3">
            <label for="cedula" class="form-label">Cédula de Estudiante/Docente</label>
            <input type="text" minlength="10" class="form-control" maxlength="10" autocomplete="off" id="cedula" name="cedula" placeholder="Número de Cédula" required>
          </div>

          <div class="mb-3">
            <label for="correo" class="form-label">Correo</label>
            <input type="email" class="form-control" id="correo" name="correo" autocomplete="off" placeholder="Correo" required>
          </div>



          <div class="mb-3">
            <label for="comentario" class="form-label">Agregar un comentario</label>
            <textarea class="form-control" id="comentario" name="comentario"></textarea>
          </div>

          <center>
            <button id="createLibroButton" type="submit" class="btn btn-success btn-sm">Guardar</button> <a href="{{ url('/listado-reservas') }}" class="btn btn-outline-danger btn-sm" type="button">Cancelar</a>
          </center>
        </form>


      </div>
    </div>
  </div>
</div>

<script>
         
            document.getElementById('createLibroButton').addEventListener('click', function() {
                alert('Reserva creada con éxito');
                window.location.href = "{{ url('/listado-reservas') }}";
            });
      
  // Obtén la lista de libros activos desde PHP y conviértela en una variable de JavaScript
  var librosActivos = <?php echo json_encode($librosActivos); ?>;

  // Función para manejar la entrada de texto y mostrar sugerencias
  $(document).ready(() {
    var maxSuggestions = 3;
    var libroInput = $('#libro');
    var createLibroButton = $('#createLibroButton');
    var libroError = $('#libro-error');

    libroInput.on('input', function() {
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
        libroError.hide();
        createLibroButton.removeAttr('disabled');
      } else {
        suggestionsList.parent().hide();
        libroError.show();
        createLibroButton.attr('disabled', 'disabled');
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
@endsection