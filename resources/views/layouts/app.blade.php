<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Inventario - MASSHA\'S CATERING')</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="{{ asset('images/logo.png') }}" rel="icon">
    
    <style>
        :root {
            --naranja: #f59622;
            --naranja-hover: #e68512;
            --naranja-light: #fff4e6;
            --negro: #191919;
            --negro-light: #2a2a2a;
            --blanco: #ffffff;
            --gris-claro: #f5f5f5;
            --gris-medio: #cccccc;
            --gris-oscuro: #666666;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--gris-claro);
            overflow-x: hidden;
        }
        
        /* NAVBAR */
        .navbar {
            background: linear-gradient(135deg, var(--negro) 0%, var(--negro-light) 100%) !important;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.3);
            padding: 12px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-brand {
            color: var(--blanco) !important;
            font-weight: 700;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: translateX(5px);
        }
        
        .navbar-brand i {
            color: var(--naranja);
            font-size: 1.5rem;
        }
        
        .navbar .nav-link {
            color: var(--blanco) !important;
            transition: color 0.3s ease;
        }
        
        .navbar .dropdown-toggle {
            background: rgba(245, 150, 34, 0.1);
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .navbar .dropdown-toggle:hover {
            background: rgba(245, 150, 34, 0.2);
        }
        
        .dropdown-menu {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .dropdown-item {
            padding: 12px 20px;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover {
            background: var(--naranja-light);
            color: var(--naranja);
        }
        
        /* SIDEBAR */
        .sidebar {
            background: linear-gradient(180deg, var(--negro) 0%, var(--negro-light) 100%);
            min-height: calc(100vh - 56px);
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 56px;
        }
        
        .sidebar .nav {
            padding: 20px 0;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 14px 24px;
            margin: 6px 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--naranja);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(245, 150, 34, 0.15);
            color: var(--blanco);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link:hover::before,
        .sidebar .nav-link.active::before {
            transform: scaleY(1);
        }
        
        .sidebar .nav-link i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }
        
        .sidebar .nav-link.active {
            background: var(--naranja);
            color: var(--blanco);
            box-shadow: 0 4px 15px rgba(245, 150, 34, 0.4);
        }
        
        .sidebar .nav-link.active::before {
            background: var(--blanco);
        }
        
        /* MAIN CONTENT */
        .main-content {
            background: var(--blanco);
            border-radius: 20px;
            padding: 30px;
            margin: 25px 20px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            min-height: calc(100vh - 140px);
        }
        
        /* CARDS */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--naranja) 0%, var(--naranja-hover) 100%);
            color: var(--blanco);
            border-radius: 15px 15px 0 0 !important;
            font-weight: 600;
            padding: 18px 24px;
            border: none;
        }
        
        .card-body {
            padding: 24px;
        }
        
        /* BUTTONS */
        .btn-primary {
            background: linear-gradient(135deg, var(--naranja) 0%, var(--naranja-hover) 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 150, 34, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 150, 34, 0.4);
            background: linear-gradient(135deg, var(--naranja-hover) 0%, var(--naranja) 100%);
        }
        
        .btn-secondary {
            background: var(--gris-oscuro);
            border: none;
            border-radius: 10px;
            padding: 10px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: var(--negro-light);
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #27ae60;
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            background: #229954;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: #e74c3c;
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .btn-info {
            background: #3498db;
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-info:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-warning {
            background: #f39c12;
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            color: var(--blanco);
        }
        
        .btn-warning:hover {
            background: #e67e22;
            transform: translateY(-2px);
            color: var(--blanco);
        }
        
        .btn-sm {
            padding: 6px 14px;
            font-size: 0.875rem;
        }
        
        /* TABLES */
        .table {
            font-size: 0.95rem;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .table thead {
            background: var(--negro);
            color: var(--blanco);
        }
        
        .table thead th {
            border: none;
            padding: 16px;
            font-weight: 600;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background: var(--naranja-light);
            transform: scale(1.01);
        }
        
        .table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        /* BADGES */
        .badge {
            font-size: 0.85rem;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .badge.bg-warning {
            background: #f39c12 !important;
            color: var(--blanco);
        }
        
        .badge.bg-success {
            background: #27ae60 !important;
        }
        
        .badge.bg-danger {
            background: #e74c3c !important;
        }
        
        /* ALERTS */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 16px 20px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        /* PAGINATION */
        .pagination {
            margin-top: 25px;
        }
        
        .pagination .page-link {
            color: var(--naranja);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin: 0 4px;
            padding: 10px 16px;
            transition: all 0.3s ease;
        }
        
        .pagination .page-link:hover {
            background: var(--naranja);
            color: var(--blanco);
            border-color: var(--naranja);
            transform: translateY(-2px);
        }
        
        .pagination .page-item.active .page-link {
            background: var(--naranja);
            border-color: var(--naranja);
        }
        
        .pagination svg {
            width: 16px !important;
            height: 16px !important;
        }
        
        /* FORMS */
        .form-control,
        .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 10px 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus,
        .form-select:focus {
            border-color: var(--naranja);
            box-shadow: 0 0 0 0.2rem rgba(245, 150, 34, 0.15);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--negro-light);
            margin-bottom: 8px;
        }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                top: 0;
                min-height: auto;
            }
            
            .main-content {
                margin: 15px 10px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: 12px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 40px; filter: brightness(0) invert(1);">
                <span>MASSHA'S CATERING</span>
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> {{ Auth::check() ? Auth::user()->name : 'Usuario' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <nav class="nav flex-column py-3">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
                        <i class="fas fa-boxes"></i>
                        <span>Inventario</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('movements.incoming') ? 'active' : '' }}" href="{{ route('movements.incoming') }}">
                        <i class="fas fa-arrow-down"></i>
                        <span>Ingresos</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('movements.outgoing') ? 'active' : '' }}" href="{{ route('movements.outgoing') }}">
                        <i class="fas fa-arrow-up"></i>
                        <span>Salidas</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('solicitudes.*') ? 'active' : '' }}" href="{{ route('solicitudes.index') }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Solicitudes</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                        <i class="fas fa-truck"></i>
                        <span>Proveedores</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reportes</span>
                    </a>
                    
                    @if(Auth::check() && Auth::user()->isAdmin())
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <i class="fas fa-users"></i>
                        <span>Usuarios</span>
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

<!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.top = '80px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.style.minWidth = '300px';
            alertDiv.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            document.body.appendChild(alertDiv);
            setTimeout(() => alertDiv.remove(), 4000);
        }

        // Alerta de bienvenida al iniciar sesión
        @if(session('login_success'))
            Swal.fire({
                title: '¡Bienvenido!',
                text: '{{ session("login_success") }}',
                icon: 'success',
                confirmButtonText: 'Continuar',
                confirmButtonColor: '#f59622',
                timer: 3000,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        @endif

        @if(session('success'))
            showAlert('success', '<i class="fas fa-check-circle me-2"></i>{{ session("success") }}');
        @endif
        @if(session('error'))
            showAlert('danger', '<i class="fas fa-times-circle me-2"></i>{{ session("error") }}');
        @endif
        @if(session('warning'))
            showAlert('warning', '<i class="fas fa-exclamation-triangle me-2"></i>{{ session("warning") }}');
        @endif
    </script>
    
    @yield('scripts')
</body>
</html>