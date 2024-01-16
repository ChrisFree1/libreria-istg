@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ url('/pantalla-principal') }}" type="button" class="btn btn-outline-primary ">Ir al Inicio</a>

    <a href="{{ url('/registar-categoria') }}" class="btn btn-outline-danger" type="button">Registrar Categoría</a>

    <h3 class="text-center">Listado de Categoría</h3>
    <br>
    <div class="container-fluid">
        <form action="{{ route('buscar-categoria') }}" method="GET" class="d-flex">
            <input name="categoria" class="form-control me -2 ligth-table-filter" data-table="table-id" autocomplete="off" type="text" placeholder="Buscar...">

            <button class="btn btn-primary" type="submit">Buscar</button>
        </form>
    </div>
    <br>

                <div class="container">
                    <table class="table table-striped col-25 mx-auto">
                        <thead class="thead-dark" style="position: sticky; top: 0; background-color: #343a40; z-index: 1;">
                            <tr>
                                <th scope="col">Nombre Categoría</th>
                                <th scope="col">Opciones</th>
                            </tr>
                        </thead>
                <tbody>
                    @if(empty($results))

                        @foreach($registros as $clave => $registro)
                            @if(isset($registro['estado']) && $registro['estado'] == 'Activo')
                                <tr>
                                    <td>{{ $registro['nombre_categoria'] }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">

                                          <a href="#" class="btn btn-outline-danger btn-sm" type="button" onclick="eliminarRegistro('{{ $clave }}')">Eliminar</a>
                                          <a href="{{ url('/editar-categoria/' . $clave) }}" type="button" class="btn btn-outline-warning btn-sm">Editar</a>
                                        <div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                    @else

                        @foreach($results as $clave => $resultado)
                            <tr>
                                <td>{{ $resultado['nombre_categoria'] }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">

                                          <a href="#" class="btn btn-outline-danger btn-sm" type="button" onclick="eliminarRegistro('{{ $clave }}')">Eliminar</a>
                                          <a href="{{ url('/editar-categoria/' . $clave) }}" type="button" class="btn btn-outline-warning btn-sm">Editar</a>
                                    <div>
                                </td>
                            </tr>
                        @endforeach
    

                    @endif

                </tbody>
            </table>

            <nav aria-label="Paginador">
                <ul class="pagination">
                    @if ($registros->currentPage() > 1)
                        <li class="page-item">
                            <a class="page-link"
                                href="{{ route('listado-categorias', ['page' => $registros->currentPage() - 1]) }}">
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
                                href="{{ route('listado-categorias', ['page' => $registros->currentPage() + 1]) }}">
                                Siguiente
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>

        </div>
    </div>

    <script>
        function eliminarRegistro(clave) {
            if (confirm('¿Estás seguro de que deseas eliminar esta Categoría?')) {
                window.location.href = "{{ url('/eliminar-categoria') }}/" + clave;
                // Mostrar mensaje
                alert('Categoría eliminada exitosamente');
            }
        }
    </script>
</div>
@endsection