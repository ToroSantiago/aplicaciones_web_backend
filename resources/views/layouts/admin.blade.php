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

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: var(--medium-dark-gray);
            border-left-color: var(--very-light-gray);
        }
        
        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
        }
        

        /* ===================== */
        /* LOGO NAVBAR (FIX FINAL) */
        /* ===================== */

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-logo {
            height: 42px;
            width: auto;
            max-width: 180px;
            object-fit: contain;
            display: block;
            transform: scale(1.5);
            transition: transform 0.2s ease;
        }

        .navbar-brand:hover .navbar-logo {
            transform: scale(1.75);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                min-height: auto;
            }
            
            .content-wrapper {
                margin-left: 0;
            }

            .navbar-logo {
                height: 34px;
                max-width: 150px;
            }
        }

        @media (max-width: 480px) {
            .navbar-logo {
                height: 30px;
                max-width: 130px;
            }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">

        <!-- LOGO -->
        <a class="navbar-brand" href="{{ route('perfumes.index') }}">
            <img
                src="https://res.cloudinary.com/drnzeqcpu/image/upload/v1779636864/logo_t96wg3.svg"
                class="navbar-logo"
                alt="Logo"
            >
        </a>

        <!-- HAMBURGUESA -->
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Abrir menú de navegación">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- MENU -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    @auth
                        <span class="nav-link" style="cursor: default;">
                            <i class="fas fa-user me-1"></i>
                            {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}
                            <span class="badge bg-secondary ms-1">{{ Auth::user()->rol }}</span>
                        </span>
                    @endauth
                </li>

                @auth
                    <li class="nav-item d-md-none">
                        <a class="nav-link" href="{{ route('ventas.estadisticas') }}">
                            <i class="fas fa-chart-line me-2"></i>Estadísticas
                        </a>
                    </li>

                    <li class="nav-item d-md-none">
                        <a class="nav-link" href="{{ route('perfumes.index') }}">
                            <i class="fas fa-spray-can me-2"></i>Perfumes
                        </a>
                    </li>

                    <li class="nav-item d-md-none">
                        <a class="nav-link" href="{{ route('ventas.index') }}">
                            <i class="fas fa-shopping-cart me-2"></i>Ventas
                        </a>
                    </li>

                    @if(Auth::user()->isAdmin())
                        <li class="nav-item d-md-none">
                            <a class="nav-link" href="{{ route('usuarios.index') }}">
                                <i class="fas fa-users me-2"></i>Usuarios
                            </a>
                        </li>

                        <li class="nav-item d-md-none">
                            <a class="nav-link" href="{{ route('descuentos.index') }}">
                                <i class="fas fa-tags me-2"></i>Descuentos
                            </a>
                        </li>
                    @endif
                @endauth

                <li class="nav-item">
                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button
                                type="submit"
                                class="nav-link btn btn-link"
                                style="text-decoration:none;"
                                aria-label="Cerrar sesión">
                                <i class="fas fa-sign-out-alt me-1"></i>
                                Cerrar sesión
                            </button>
                        </form>
                    @else
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>Iniciar sesión
                        </a>
                    @endauth
                </li>

            </ul>
        </div>
    </div>
</nav>

<!-- CONTENIDO -->
<div class="container-fluid">
    <div class="row">

        <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <div class="position-sticky">
                <ul class="nav flex-column">

                    @auth
                        <li class="nav-item">
                            <a class="sidebar-link" href="{{ route('ventas.estadisticas') }}">
                                <i class="fas fa-chart-line me-2"></i>Estadísticas
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="sidebar-link" href="{{ route('perfumes.index') }}">
                                <i class="fas fa-spray-can me-2"></i>Perfumes
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="sidebar-link" href="{{ route('ventas.index') }}">
                                <i class="fas fa-shopping-cart me-2"></i>Ventas
                            </a>
                        </li>

                        @if(Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="sidebar-link" href="{{ route('usuarios.index') }}">
                                <i class="fas fa-users me-2" aria-hidden="true"></i>Usuarios
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="sidebar-link" href="{{ route('descuentos.index') }}">
                                    <i class="fas fa-tags me-2"></i>Descuentos
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            @yield('content')
        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>