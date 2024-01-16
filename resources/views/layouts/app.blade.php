<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AppBooks') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Styles -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        
        .navbar {
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            background-color: #ffff;
            color: white;
        }

        .navbar-brand {
            color: white;
            text-decoration: none;
            margin-right: 20px;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            display: none;
            background-color: #ffff;
            min-width: 200px;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            color: black;
            text-decoration: none;
            display: block;
            padding: 10px;
            transition: background-color 0.3s;
        }

        .dropdown-item:hover {
            background-color: #555;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }


          .suggestions-container {
            position: relative;
          }

          .suggestions-container ul {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 0;
            margin: 0;
            list-style: none;
            max-height: 200px;
            overflow-y: auto;
            z-index: 999;
          }

          .suggestions-container li {
            padding: 8px 12px;
            cursor: pointer;
          }

          .suggestions-container li:hover {
            background-color: #f2f2f2;
          }



        .material-symbols-outlined {
          font-variation-settings:
          'FILL' 0,
          'wght' 400,
          'GRAD' 0,
          'opsz' 48
        }



        body {
            font-family: Arial, sans-serif;
        }

        .form-container {
            width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f2f2f2;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        .form-container h2 {
            text-align: center;
        }

        .form-container label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container input[type="url"],
        .form-container textarea {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
            border-radius: 3px;
            border: 1px solid #ccc;
        }

        .form-container input[type="file"] {
            margin-bottom: 10px;
        }

        .form-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style> 
</head>
<body  style="min-height:90vh;">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/home') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                @if( !Auth::guest() )
                <a class="navbar-brand" href="{{ url('/listado-libro') }}">
                    Libro
                </a>
                
                <a class="navbar-brand" href="{{ url('/listado-reservas') }}">
                    Reservas
                </a>
                <div class="dropdown">
                        <a class="navbar-brand dropdown-toggle" href="#" id="mantenimientosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Mantenimientos
                        </a>
                        <div class="dropdown-menu" aria-labelledby="mantenimientosDropdown">
                            <a class="dropdown-item" href="{{ url('/listado-carreras') }}">Mantenimiento Carreras</a>
                            <a class="dropdown-item" href="{{ url('/listado-areas') }}">Mantenimiento Areas</a>
                            <a class="dropdown-item" href="{{ url('/listado-area-carreras') }}">Mantenimiento Areas por Carreras</a>
                            <a class="dropdown-item" href="{{ url('/listado-categorias') }}">Mantenimiento Categorias</a>
                            <a class="dropdown-item" href="{{ url('/listado-parametros') }}">Mantenimiento Parametros</a>

                            <!-- Agrega más opciones de mantenimiento aquí -->
                        </div>
                    </div>
                    @endif
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
                                </li>
                            @endif
                        @else

                          <li class="nav-item">
                              <a class="nav-link text-dark" href="home/profile">Perfil</a>
                          </li>

                          <li class="nav-item">
                            <a class="nav-link text-dark" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                Cerrar Sesión
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                              </li>
                            </div>
                          </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>


        <main >
            <br>
            @yield('content')
        </main>
    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<!-- Agrega Chart.js con CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

</body>
</html>
