@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ url('/pantalla-principal') }}" type="button" class="btn btn-outline-primary">Ir al Inicio</a>
        <a href="{{ url('/registrar-parametro') }}" class="btn btn-outline-danger" type="button">Registrar Parámetro</a>

        <h3 class="text-center">Listado de Parámetro</h3>
        <br>

        <div class="container-fluid">
            <form action="{{ route('buscar-parametro') }}" method="GET" class="d-flex">
                <input name="parametro" class="form-control me-2 ligth-table-filter" data-table="table-id"
                    autocomplete="off" type="text" placeholder="Buscar...">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </form>
        </div>
        <br>
                <table class="table table-striped col-12 mx-auto">
                    <thead class="thead-dark" style="position: sticky; top: 0; background-color: #343a40; z-index: 1;">
                        <tr>
                            <th scope="col" >Nombre Parámetro</th>
                            <th scope="col" >Codigo</th>
                            <th scope="col" >Descripción</th>
                            <th scope="col" >Valor</th>
                            <th scope="col" >Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrosPaginados as $clave => $registro)
                            <tr>
                                <td >{{ $registro['nombre_parametro'] ?? 'Valor por defecto' }}</td>
                                <td >{{ $registro['codigo'] }}</td>
                                <td >{{ $registro['descripcion'] ?? 'Valor por defecto' }}</td>
                                <td >{{ $registro['valor'] ?? 'Valor por defecto' }}</td>
                                <td  class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="#" class="btn btn-outline-danger btn-sm" type="button"
                                            onclick="eliminarRegistro('{{ $clave }}')">Eliminar</a>
                                        <a href="{{ url('/editar-parametro/' . $clave) }}" type="button"
                                            class="btn btn-outline-warning btn-sm">Editar</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No se encontraron registrosPaginados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>


                <nav aria-label="Paginador">
                    <ul class="pagination">
                        @if ($registrosPaginados->currentPage() > 1)
                            <li class="page-item">
                                <a class="page-link"
                                    href="{{ route('listado-parametros', ['page' => $registrosPaginados->currentPage() - 1]) }}">
                                    Anterior
                                </a>
                            </li>
                        @endif

                        @foreach ($registrosPaginados->getUrlRange(1, $registrosPaginados->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $registrosPaginados->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        @if ($registrosPaginados->hasMorePages())
                            <li class="page-item">
                                <a class="page-link"
                                    href="{{ route('listado-parametros', ['page' => $registrosPaginados->currentPage() + 1]) }}">
                                    Siguiente
                                </a>
                            </li>
                        @endif
                    </ul>
                </nav>


            </div>

        <script>
            function eliminarRegistro(clave) {
                if (confirm('¿Estás seguro de que deseas eliminar esta Parametro?')) {
                    window.location.href = "{{ url('/eliminar-parametro') }}/" + clave;
                    // Mostrar mensaje
                alert('Parámetro eliminado exitosamente');
                }
            }
        </script>
    </div>
@endsection