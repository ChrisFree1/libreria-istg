@extends('layouts.app')
@section('content')
 
 <div class="row justify-content-center">
         <div class="col-md-8">
             <div class="card">
                <div class="card-header">Estudiantes Registrados</div>
 
              
               <!-- Agrega un elemento de lienzo para el gráfico -->
                <canvas id="estudiantesPorCarreraChart" width="400" height="200"></canvas>
                <a href="{{ route('reporte-carreras-pdf') }}" class="btn btn-primary" target="_blank">
    Generar Reporte PDF
</a>

             </div>
         </div>
     </div>
 </div>
<!-- Agrega tu script para manejar los datos y crear el gráfico -->
<script>
   $(document).ready(function () {
        // Llama a tu función AJAX para obtener los datos de cantidad de estudiantes por carrera
        $.ajax({
            type: "POST",
            url: "{{ route('cantidad-estudiantes-carrera') }}",
        data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                // Llama a la función para crear el gráfico con los datos obtenidos
                crearGrafico(response.cantidadEstudiantesPorCarrera);
            },
            error: function (xhr, status, error) {
                console.error('Error al obtener datos:', error);
            }
        });
    });

    // Función para crear el gráfico
    function crearGrafico(datos) {
        // Obtiene el contexto del lienzo
        var ctx = document.getElementById('estudiantesPorCarreraChart').getContext('2d');

        // Crea un arreglo de colores para cada barra en el gráfico
        var colores = Object.keys(datos).map(function (carrera) {
            return getRandomColor();
        });

        // Configuración del gráfico
        var chartConfig = {
            type: 'bar',
            data: {
                labels: Object.keys(datos),
                datasets: [{
                    label: 'Cantidad de Estudiantes',
                    data: Object.values(datos),
                    backgroundColor: colores,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        // Crea el gráfico
        var myChart = new Chart(ctx, chartConfig);
    }

    // Función para obtener un color aleatorio
    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
</script>
@endsection
