@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ url('/pantalla-principal') }}" type="button" class="btn btn-outline-primary">Ir al Inicio</a>
        <a href="{{ url('/registro-libro') }}" class="btn btn-outline-danger" type="button">Registrar Libro</a>
        <a href="{{ url('/registar-libro-excel') }}" class="btn btn-outline-warning" type="button">Migrar Libro Fisico</a>
        <a href="{{ url('/pdf') }}" class="btn btn-outline-success" type="button">PDF</a>
        <a href="{{ url('/generar-listado-excel') }}" class="btn btn-outline-success" type="button">Excel</a>
        <button type="button" class="btn btn-outline-danger" onclick="beforeSubmit()">Eliminar Libros Seleccionados</button>



        <br>
        <br>
        <h3 class="text-center">Listado de Libros</h3>

        <div class="container-fluid">
            <form action="{{ route('buscar-libro') }}" method="GET" class="d-flex">
                <input name="query" class="form-control me -2 ligth-table-filter" data-table="table-id" autocomplete="off"
                    type="text" placeholder="Buscar...">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </form>
        </div>
        <br>
        
            <form action="{{ route('eliminar-libros-seleccionados') }}" method="POST"
                onsubmit="return confirm('¿Estás seguro de que deseas eliminar los libros seleccionados?')">
                @csrf

                <table class="table table-striped col-12 mx-auto">
                    <thead class="thead-dark" style="position: sticky; top: 0; background-color: #343a40; z-index: 1;">
                        <tr>
                            <th scope="col">Título</th>
                            <th scope="col">Año Publicación</th>
                            <th scope="col">Área</th>
                            <th scope="col">Carrera</th>
                            <th scope="col">Nombre Editorial</th>
                            <th scope="col">Opciones</th>
                            <th scope="col">
                                <input type="checkbox" id="id" onclick="toggleCheckboxes()">
                                Eliminar Libros
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results ?? [] ?: $registros as $clave => $registro)
                            @if (isset($registro['estado']) && in_array($registro['estado'], ['Activo', 'Reservado', 'Entregado']))
                                <tr>
                                <td>{{ isset($registro['nombre']) ? $registro['nombre'] : '' }}</td>
                                    <td>{{ isset($registro['anio_publicacion']) ? $registro['anio_publicacion'] : '' }}</td>
                                    <td>{{ isset($registro['area']) ? $registro['area'] : '' }}</td>
                                    <td>{{ isset($registro['carrera']) ? $registro['carrera'] : '' }}</td>
                                    <td>{{ isset($registro['nombre_editorial']) ? $registro['nombre_editorial'] : '' }}</td>
                                    <td class="text-center">
                                    <div class="btn-group" role="group">
                                            <a href="{{ url('/ver-registro-libro/' . $clave) }}" type="button"
                                                class="btn btn-outline-primary btn-sm">Ver</a>
                                            <a href="{{ url('/editar-registro-libro/' . $clave) }}" type="button"
                                                class="btn btn-outline-warning btn-sm">Editar</a>
                                            <a href="#" class="btn btn-outline-danger btn-sm" type="button"
                                                onclick="eliminarRegistro('{{ $clave }}')">Eliminar</a>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" name="libros_seleccionados[]" value="{{ $clave }}">
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay libros disponibles.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <nav aria-label="Paginador">
                    <ul class="pagination">
                        @if ($registros->currentPage() > 1)
                            <li class="page-item">
                                <a class="page-link"
                                    href="{{ route('listado-libros', ['page' => $registros->currentPage() - 1]) }}">
                                    Anterior
                                </a>
                            </li>
                        @endif

                        @foreach ($registros->getUrlRange(1, $registros->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $registros->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        @if ($registros->hasMorePages())
                            <li class="page-item">
                                <a class="page-link"
                                    href="{{ route('listado-libros', ['page' => $registros->currentPage() + 1]) }}">
                                    Siguiente
                                </a>
                            </li>
                        @endif
                    </ul>
                </nav>
            </form>
        </div>



    <script>
        function toggleCheckboxes() {
            var checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = event.target.checked;
            });
        }

        function eliminarRegistro(clave) {
            if (confirm('¿Estás seguro de que deseas eliminar este libro?')) {
                window.location.href = "{{ url('/eliminar-registro-libro') }}/" + clave;
                // Mostrar mensaje
                alert('Libro eliminado exitosamente');
            }
        }

        function beforeSubmit() {
                // Verificar si al menos un checkbox está seleccionado
                var checkboxes = document.querySelectorAll('tbody input[type="checkbox"]:checked');
                if (checkboxes.length === 0) {
                    // Mostrar mensaje si no hay ningún libro seleccionado
                    alert('Por favor, selecciona al menos un libro.');
                }else {
                // Obtener los valores de los checkboxes seleccionados
                var librosSeleccionados = [];
                checkboxes.forEach(function(checkbox) {
                    librosSeleccionados.push(checkbox.value);
                });

                // Enviar una solicitud al servidor para eliminar los libros seleccionados
                eliminarLibrosSeleccionados(librosSeleccionados);
            }
        }
        function eliminarLibrosSeleccionados(librosSeleccionados) {
            // Crear un formulario dinámicamente
            var form = document.createElement('form');
            form.action = "{{ route('eliminar-libros-seleccionados') }}";
            form.method = 'POST';

            // Agregar el token CSRF al formulario
            var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            // Agregar un campo al formulario con los IDs de los libros seleccionados
            librosSeleccionados.forEach(function(libroId) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'libros_seleccionados[]';
                input.value = libroId;
                form.appendChild(input);
            });

            // Adjuntar el formulario al cuerpo del documento y enviarlo
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endsection