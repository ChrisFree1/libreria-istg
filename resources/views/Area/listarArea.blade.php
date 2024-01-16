@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ url('/pantalla-principal') }}" type="button" class="btn btn-outline-primary ">Ir al Inicio</a>

        <a href="{{ url('/registrar-area') }}" class="btn btn-outline-danger" type="button">Registrar Área</a>

        <h3 class="text-center">Listado de Área</h3>
        <br>
        <div class="container-fluid">
            <form action="{{ route('buscar-area') }}" method="GET" class="d-flex">
                <input name="area" class="form-control me -2 ligth-table-filter" data-table="table-id" autocomplete="off"
                    type="text" placeholder="Buscar...">

                <button class="btn btn-primary" type="submit">Buscar</button>
            </form>
        </div>
        <br>
        <div class="container-fluid">
        <table class="table table-striped col-12 mx-auto">
            <thead class="thead-dark" style="position: sticky; top: 0; background-color: #343a40; z-index: 1;">
                        <tr>
                            <th scope="col">Nombre Área</th>
                            <th scope="col">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results ?? [] ?: $registros as $clave => $registro)
                            @if (isset($registro['estado']) && $registro['estado'] == 'Activo')
                                <tr>
                                    <td>{{ $registro['nombre_area'] }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">

                                            <a href="#" class="btn btn-outline-danger btn-sm" id="eliminar" type="button"
                                                onclick="eliminarRegistro('{{ $clave }}')">Eliminar</a>
                                            <a href="{{ url('/editar-area/' . $clave) }}" type="button"
                                                class="btn btn-outline-warning btn-sm">Editar</a>
                                        </div>
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
                            href="{{ route('listar-area', ['page' => $registros->currentPage() - 1]) }}">
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
                            href="{{ route('listar-area', ['page' => $registros->currentPage() + 1]) }}">
                            Siguiente
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
</div>
</div>
</div>

        <script>
            function eliminarRegistro(clave) {
                if (confirm('¿Estás seguro de que deseas eliminar esta Área por Carrera?')) {
                    window.location.href = "{{ url('/eliminar-area') }}/" + clave;
                    // Mostrar mensaje
                    alert('Área por Carrera eliminada exitosamente');
                }
            }
        </script>

@endsection