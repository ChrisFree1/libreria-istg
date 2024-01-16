@extends('layouts.app')

@section('content')
    <div class="container">

        <a href="{{ url('/pantalla-principal') }}" type="button" class="btn btn-outline-primary ">Ir al Inicio</a>
        <a href="{{ url('/crear-libro-reserva') }}" class="btn btn-outline-danger" type="button">Crear Reserva</a>
        <a href="{{ url('/pdf-reservas') }}" class="btn btn-outline-success" type="button">PDF</a>
        <a href="{{ url('/generar-listado-reserva-excel') }}" class="btn btn-outline-success" type="button">Excel</a>
        <a href="{{ route('liberar.reservas') }}" class="btn btn-danger">Liberar Reservas Expiradas</a>

        <br>
        <br>

        <h3 class="text-center">Listado de Reservas de Libros </h3>
        <br>
        <br>

        <div class="container-fluid">
            <form action="{{ route('buscar-reserva') }}" method="GET" class="d-flex">
                <input name="query" class="form-control me-2 light-table-filter" data-table="table-id" autocomplete="off"
                    type="text" placeholder="Buscar...">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </form>
        </div>

        <br>
        <br>

        <table class="table table-striped col-12 mx-auto">
            <thead class="thead-dark" style="position: sticky; top: 0; background-color: #343a40; z-index: 1;">
                <tr>
                    <th scope="col">Carrera</th>
                    <th scope="col">Cédula</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Fecha Reserva</th>
                    <th scope="col">Libro</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Opciones</th>
                </tr>
            </thead>
            <tbody>
                @if (empty($results))
                    @foreach ($registros as $clave => $registro)
                        @if (isset($registro['estado']) &&
                                ($registro['estado'] == 'Activo' || $registro['estado'] == 'Entregado' || $registro['estado'] == 'Devuelto'))
                            <tr>
                                <td>{{ $registro['carrera'] }}</td>
                                <td>{{ $registro['cedula'] }}</td>
                                <td>{{ $registro['estado'] }}</td>
                                <td>{{ $registro['fechaReserva'] }}</td>
                                <td>{{ $registro['libro'] }}</td>
                                <td>{{ $registro['nombre'] }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="#" class="btn btn-outline-danger btn-sm" type="button"
                                            onclick="eliminarRegistroReserva('{{ $clave }}')">Eliminar</a>
                                        <a href="{{ url('/editar-registro-reserva/' . $clave) }}" type="button"
                                            class="btn btn-outline-warning btn-sm">Editar</a>
                                        @if ($registro['estado'] == 'Activo')
                                            <a href="#" class="btn btn-outline-success btn-sm" type="button"
                                                onclick="entregarReserva('{{ $clave }}')">Entregar</a>
                                        @elseif ($registro['estado'] == 'Entregado')
                                            <a href="#" class="btn btn-outline-secondary btn-sm" type="button"
                                                onclick="reservaDevuelta('{{ $clave }}')">Devuelto</a>
                                        @endif
                                        <a href="{{ url('/ver-registro-reserva/' . $clave) }}" type="button"
                                            class="btn btn-outline-primary btn-sm">Ver</a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    @foreach ($results as $clave => $resultado)
                        @if (isset($resultado['estado']) &&
                                ($resultado['estado'] == 'Activo' || $resultado['estado'] == 'Entregado' || $resultado['estado'] == 'Devuelto'))
                            <tr>
                                <td>{{ $resultado['carrera'] }}</td>
                                <td>{{ $resultado['cedula'] }}</td>
                                <td>{{ $resultado['estado'] }}</td>
                                <td>{{ $resultado['fechaReserva'] }}</td>
                                <td>{{ $resultado['libro'] }}</td>
                                <td>{{ $resultado['nombre'] }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="#" class="btn btn-outline-danger btn-sm" type="button"
                                            onclick="eliminarRegistroReserva('{{ $clave }}')">Eliminar</a>
                                        <a href="{{ url('/editar-registro-reserva/' . $clave) }}" type="button"
                                            class="btn btn-outline-warning btn-sm">Editar</a>
                                        @if ($resultado['estado'] == 'Activo')
                                            <a href="#" class="btn btn-outline-success btn-sm" type="button"
                                                onclick="entregarReserva('{{ $clave }}')">Entregar</a>
                                        @elseif ($resultado['estado'] == 'Entregado')
                                            <a href="#" class="btn btn-outline-secondary btn-sm" type="button"
                                                onclick="reservaDevuelta('{{ $clave }}')">Devuelto</a>
                                        @endif
                                        <a href="{{ url('/ver-registro-reserva/' . $clave) }}" type="button"
                                            class="btn btn-outline-primary btn-sm">Ver</a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <nav aria-label="Paginador">
        <ul class="pagination">
            @if ($registros->currentPage() > 1)
                <li class="page-item">
                    <a class="page-link"
                        href="{{ route('listarRegistrosLbReserva', ['page' => $registros->currentPage() - 1]) }}">
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
                        href="{{ route('listarRegistrosLbReserva', ['page' => $registros->currentPage() + 1]) }}">
                        Siguiente
                    </a>
                </li>
            @endif
        </ul>
    </nav>

    <script>
        function eliminarRegistroReserva(clave) {
            if (confirm('¿Estás seguro de que deseas eliminar esta reserva?')) {
                window.location.href = "{{ url('/eliminar-registro-reserva') }}/" + clave;
                // Mostrar mensaje
                alert('Reserva eliminada exitosamente');
            }
        }

        function entregarReserva(clave) {
            if (confirm('¿Estás seguro de que deseas entregar esta reserva?')) {
                window.location.href = "{{ url('/entregar-registro-reserva') }}/" + clave;
            }
        }

        function reservaDevuelta(clave) {
            if (confirm('¿Estás seguro de que deseas dar por devuelto este libro?')) {
                window.location.href = "{{ url('/devolver-registro-reserva') }}/" + clave;
            }
        }
    </script>

    </div>
@endsection