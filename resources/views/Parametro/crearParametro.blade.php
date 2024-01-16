@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ url('/listado-parametros') }}" class="btn btn-outline-danger" type="button">Regresar</a>

        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="form-container">
                    <h2>Crear Parámetro </h2>
                    <form id="miFormulario" action="{{ route('registroParametro') }}" method="post">
                        @csrf

                        <label for="codigo">Codigo de 4 caracteres:</label>
                        <input type="text" id="codigo" name="codigo" maxlength="4" autocomplete="off"
                            placeholder="Código" required>

                        <div class="mb-3">
                            <label for="formGroupExampleInput2" class="form-label">Parámetro</label>
                            <input type="text" class="form-control" id="parametro" name="parametro" autocomplete="off"
                                placeholder="Nombre de Parametro" required>
                        </div>

                        <div class="mb-3">
                            <label for="formGroupExampleInput2" class="form-label">Descripción</label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion"
                                autocomplete="off" placeholder="Descripcion" required>
                        </div>

                        <div class="mb-3">
                            <label for="formGroupExampleInput2" class="form-label">Valor</label>
                            <input type="text" class="form-control" id="valor" name="valor" placeholder="Valor"
                                autocomplete="off" required>
                        </div>

                        <button type="submit" id="enviarButton" class="btn btn-success btn-block">Crear Parámetro</button>
                        <p id="codigoError" style="color: red;"></p>

                    </form>
                </div>

            </div>
        </div>

    </div>

    <script>
    
                    document.getElementById('enviarButton').addEventListener('click', function() {
                        alert('Parámetro creado con éxito');
                        window.location.href = "{{ url('/listado-parametros') }}";
                    });
                


        $(document).ready(function() {
            var codigoInput = $('#codigo');
            var parametroInput = $('#parametro');
            var descripcionInput = $('#descripcion');
            var valorInput = $('#valor');
            var enviarButton = $('#enviarButton');
            var codigoError = $('#codigoError');
            var formulario = $('#miFormulario');

            enviarButton.on('click', function(e) {
                e.preventDefault();

                var codigo = codigoInput.val();
                var parametro = parametroInput.val();
                var descripcion = descripcionInput.val();
                var valor = valorInput.val();

                // Verificar que todos los campos estén llenos
                if (codigo.trim() !== '' && parametro.trim() !== '' && descripcion.trim() !== '' && valor
                    .trim() !== '') {
                    $.post("{{ route('verificar-codigo-unico') }}", {
                        codigo: codigo,
                        _token: '{{ csrf_token() }}'
                    }, function(response) {
                        if (response.existe) {
                            codigoError.text('El código que ingresó ya se encuentra registrado.');
                        } else {
                            codigoError.text('');
                            formulario.submit();
                        }
                    }).fail(function(xhr, status, error) {
                        console.error('Error al verificar el código:', error);
                        formulario.submit();
                    });
                } else {
                    codigoError.text('Llene todos los campos antes de enviar el formulario.');
                }
            });
        });
    </script>
@endsection