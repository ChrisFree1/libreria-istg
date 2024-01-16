@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ url('/listado-parametros') }}" class="btn btn-outline-danger" type="button">Regresar</a>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="form-container">
                <h2>Editar Parámetro </h2>
              <form  method="POST"  action="{{ route('actualizar-parametro', $clave) }}" enctype="multipart/form-data">
                @csrf


                <label for="codigo">Codigo de 4 caracteres:</label>
                <input type="text" name="codigo" maxlength="4" autocomplete="off" value="{{ $registro['codigo'] }}" required>
                
                <div class="mb-3">
                    <label for="formGroupExampleInput2" class="form-label">Parámetro</label>
                    <input type="text" class="form-control" id="parametro" name="parametro" placeholder="Nombre de Parametro" autocomplete="off" value="{{ $registro['nombre_parametro'] }}"required>
                </div>

                <div class="mb-3">
                    <label for="formGroupExampleInput2" class="form-label">Descripción</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="Descripcion" autocomplete="off" value="{{ $registro['descripcion'] }}"required>
                </div>

                <div class="mb-3">
                    <label for="formGroupExampleInput2" class="form-label">Valor</label>
                    <input type="text" class="form-control" id="valor" name="valor" placeholder="Valor" autocomplete="off" value="{{ $registro['valor'] }}"required>
                </div>


      
                    <button type="submit" id="enviarButton" class="btn btn-success btn-block">Actualizar Parámetro</button>
                    <p id="codigoError" style="color: red;"></p>

            </form>
            </div>



        </div>
    </div>
</div>
<script>

        document.getElementById('enviarButton').addEventListener('click', function() {
            alert('Parámetro editado con éxito');
            window.location.href = "{{ url('/listado-parametros') }}"; 
        });
   
    $(document).ready(function () {
        var codigoInput = $('#codigo');
        var enviarButton = $('#enviarButton');
        var codigoError = $('#codigoError');

        codigoInput.on('change', function () {
            var codigo = $(this).val();

            $.post("{{ route('verificar-codigo-unico') }}", { codigo: codigo, _token: '{{ csrf_token() }}' }, function (response) {
                if (response.existe) {
                    enviarButton.prop('disabled', true);
                    codigoError.show().text('El código que ingresó ya se encuentra registrado.');
                } else {
                    enviarButton.prop('disabled', false);
                    codigoError.hide().text('');
                }
            }).fail(function (xhr, status, error) {
                console.error('Error al verificar el código:', error);
            });
        });
    });
</script>
@endsection