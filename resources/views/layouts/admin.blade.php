<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Administrador - Perfumes')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <style>
        :root {
            --dark-gray: #282729;
            --medium-dark-gray: #4D4C4F;
            --medium-gray: #767578;
            --light-gray: #A2A1A3;
            --very-light-gray: #D0CFD1;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .navbar {
            background-color: var(--dark-gray) !important;
        }
        
        .btn-primary {
            background-color: var(--medium-dark-gray);
            border-color: var(--medium-dark-gray);
        }
        
        .btn-primary:hover {
            background-color: var(--dark-gray);
            border-color: var(--dark-gray);
        }
        
        .btn-info {
            background-color: var(--medium-gray);
            border-color: var(--medium-gray);
            color: white;
        }
        
        .btn-info:hover {
            background-color: var(--medium-dark-gray);
            border-color: var(--medium-dark-gray);
            color: white;
        }
        
        .btn-secondary {
            background-color: var(--light-gray);
            border-color: var(--light-gray);
        }
        
        .btn-secondary:hover {
            background-color: var(--medium-gray);
            border-color: var(--medium-gray);
        }
        
        .btn-danger {
            background-color: #dc3545;
        }
        
        .card-header {
            background-color: var(--medium-dark-gray) !important;
            color: white !important;
        }
        
        .table {
            --bs-table-hover-bg: var(--very-light-gray);
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--medium-dark-gray);
            border-color: var(--medium-dark-gray);
        }
        
        .badge-stock {
            background-color: #198754;
        }
        
        .badge-no-stock {
            background-color: #dc3545;
        }
        
        .gender-M {
            background-color: var(--medium-dark-gray);
        }
        
        .gender-F {
            background-color: var(--light-gray);
        }
        
        .gender-U {
            background-color: var(--medium-gray);
        }
        
        .sidebar {
            background-color: var(--dark-gray);
            min-height: calc(100vh - 56px);
        }
        
        .sidebar-link {
            color: white;
            padding: 10px 15px;
            display: block;
            text-decoration: none;
            border-left: 4px solid transparent;
        }
        
        .sidebar-link:hover, .sidebar-link.active {
            background-color: var(--medium-dark-gray);
            border-left-color: var(--very-light-gray);
        }
        
        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                min-height: auto;
            }
            
            .content-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('perfumes.index') }}">
                <i class="fas fa-spray-can me-2"></i>Admin Perfumería
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        @auth
                            @if(Auth::user()->rol === 'Administrador')
                                <span class="nav-link" style="cursor: default;">
                                    <i class="fas fa-user me-1"></i>{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}
                                </span>
                            @endif
                        @endauth
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">
                            <i class="fas fa-sign-out-alt me-1"></i>Cerrar sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                        <a class="sidebar-link {{ request()->routeIs('perfumes.*') ? 'active' : '' }}" href="{{ route('perfumes.index') }}">
                                <i class="fas fa-spray-can me-2"></i>Perfumes
                            </a>
                        </li>
                        <li class="nav-item">
                        <a class="sidebar-link {{ request()->routeIs('estadisticas.*') ? 'active' : '' }}" href="{{ route('estadisticas.index') }}">
                                <i class="fas fa-chart-bar me-2"></i>Estadísticas
                            </a>
                        </li>
                        <li class="nav-item">
                             <a class="sidebar-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                                <i class="fas fa-users me-2"></i>Usuarios
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Contenido principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>