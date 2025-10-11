<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MASSHA'S CATERING</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('images/logo.png') }}" rel="icon">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        :root {
            --naranja: #f59622;
            --naranja-hover: #e68512;
            --negro: #191919;
            --blanco: #ffffff;
        }
        
        body {
            background: linear-gradient(135deg, var(--negro) 0%, #2a2a2a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(245, 150, 34, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(245, 150, 34, 0.06) 0%, transparent 50%);
            animation: pulse 4s ease-in-out infinite;
            z-index: 0;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .login-container {
            max-width: 480px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-card {
            background: var(--blanco);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--naranja) 0%, var(--naranja-hover) 100%);
            padding: 50px 30px;
            text-align: center;
            color: var(--blanco);
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .login-header img {
            max-height: 90px;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
            filter: brightness(0) invert(1);
            transition: transform 0.3s ease;
        }
        
        .login-header img:hover {
            transform: scale(1.05);
        }
        
        .login-header h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .login-header p {
            margin: 10px 0 0 0;
            opacity: 0.95;
            font-size: 0.95rem;
            position: relative;
            z-index: 2;
        }
        
        .login-body {
            padding: 45px 40px;
        }
        
        .form-floating {
            position: relative;
            margin-bottom: 25px;
        }
        
        .form-floating label {
            color: #666;
            font-size: 0.95rem;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 14px 18px;
            padding-right: 50px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .form-control:focus {
            border-color: var(--naranja);
            box-shadow: 0 0 0 0.25rem rgba(245, 150, 34, 0.15);
            background-color: #fffbf7;
        }
        
        .form-control:focus + label {
            color: var(--naranja);
        }
        
        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            transition: color 0.3s ease;
            z-index: 10;
            font-size: 1.1rem;
        }
        
        .password-toggle:hover {
            color: var(--naranja);
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--naranja) 0%, var(--naranja-hover) 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            color: var(--blanco);
            width: 100%;
            transition: all 0.3s ease;
            font-size: 1.05rem;
            box-shadow: 0 4px 15px rgba(245, 150, 34, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 150, 34, 0.4);
            background: linear-gradient(135deg, var(--naranja-hover) 0%, var(--naranja) 100%);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .form-check-input:checked {
            background-color: var(--naranja);
            border-color: var(--naranja);
        }
        
        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(245, 150, 34, 0.25);
        }
        
        .forgot-password {
            color: var(--naranja);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .forgot-password:hover {
            color: var(--naranja-hover);
            text-decoration: underline;
        }
        
        .back-link {
            color: #666;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: var(--naranja);
        }
        
        @media (max-width: 576px) {
            .login-container {
                padding: 0 15px;
            }
            
            .login-body {
                padding: 35px 25px;
            }
            
            .login-header {
                padding: 40px 20px;
            }
            
            .login-header h3 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    <h3>MASSHA'S CATERING</h3>
                    <p>Sistema de Gestión de Inventario</p>
                </div>
                
                <div class="login-body">
                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        <div class="form-floating mb-4">
                            <input id="email" 
                                   type="email" 
                                   class="form-control" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   autofocus
                                   placeholder="nombre@ejemplo.com">
                            <label for="email">
                                <i class="fas fa-envelope me-2"></i>Correo Electrónico
                            </label>
                        </div>

                        <div class="form-floating mb-4 position-relative">
                            <input id="password" 
                                   type="password" 
                                   class="form-control" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="Contraseña">
                            <label for="password">
                                <i class="fas fa-lock me-2"></i>Contraseña
                            </label>
                            <span class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="remember" 
                                       id="remember" 
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Recordarme
                                </label>
                            </div>
                            
                            <a class="forgot-password" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>

                        <button type="submit" class="btn btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </button>
                        
                        <div class="text-center mt-4">
                            <a href="{{ url('/') }}" class="back-link">
                                <i class="fas fa-arrow-left me-2"></i>Volver al inicio
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // function comingSoon() {
        //     Swal.fire({
        //         title: 'Próximamente',
        //         text: 'La función de recuperación de contraseña estará disponible pronto',
        //         icon: 'info',
        //         confirmButtonText: 'Entendido',
        //         confirmButtonColor: '#f59622'
        //     });
        // }

        // Mostrar alertas de errores con SweetAlert2
        @if($errors->has('email'))
            Swal.fire({
                title: '¡Correo no encontrado!',
                text: '{{ $errors->first('email') }}',
                icon: 'error',
                confirmButtonText: 'Intentar de nuevo',
                confirmButtonColor: '#e74c3c',
                timer: 5000,
                timerProgressBar: true
            });
        @endif

        @if($errors->has('password'))
            Swal.fire({
                title: '¡Contraseña incorrecta!',
                text: '{{ $errors->first('password') }}',
                icon: 'error',
                confirmButtonText: 'Intentar de nuevo',
                confirmButtonColor: '#e74c3c',
                timer: 5000,
                timerProgressBar: true
            });
        @endif

        @if(session('status'))
            Swal.fire({
                title: '¡Éxito!',
                text: '{{ session('status') }}',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#27ae60',
                timer: 3000,
                timerProgressBar: true
            });
        @endif
    </script>
</body>
</html>

{{-- <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MASSHA'S CATERING</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('images/logo.png') }}" rel="icon">
    <style>
        :root {
            --naranja: #f59622;
            --naranja-hover: #e68512;
            --negro: #191919;
            --blanco: #ffffff;
        }
        
        body {
            background: linear-gradient(135deg, var(--negro) 0%, #2a2a2a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        /* Patrón de fondo */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(245, 150, 34, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(245, 150, 34, 0.06) 0%, transparent 50%);
            animation: pulse 4s ease-in-out infinite;
            z-index: 0;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .login-container {
            max-width: 480px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-card {
            background: var(--blanco);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--naranja) 0%, var(--naranja-hover) 100%);
            padding: 50px 30px;
            text-align: center;
            color: var(--blanco);
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .login-header img {
            max-height: 90px;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
            filter: brightness(0) invert(1);
            transition: transform 0.3s ease;
        }
        
        .login-header img:hover {
            transform: scale(1.05);
        }
        
        .login-header h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .login-header p {
            margin: 10px 0 0 0;
            opacity: 0.95;
            font-size: 0.95rem;
            position: relative;
            z-index: 2;
        }
        
        .login-body {
            padding: 45px 40px;
        }
        
        .form-floating {
            position: relative;
            margin-bottom: 25px;
        }
        
        .form-floating label {
            color: #666;
            font-size: 0.95rem;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 14px 18px;
            padding-right: 50px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .form-control:focus {
            border-color: var(--naranja);
            box-shadow: 0 0 0 0.25rem rgba(245, 150, 34, 0.15);
            background-color: #fffbf7;
        }
        
        .form-control:focus + label {
            color: var(--naranja);
        }
        
        /* Password Toggle Icon */
        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            transition: color 0.3s ease;
            z-index: 10;
            font-size: 1.1rem;
        }
        
        .password-toggle:hover {
            color: var(--naranja);
        }
        
        /* Botón de Login */
        .btn-login {
            background: linear-gradient(135deg, var(--naranja) 0%, var(--naranja-hover) 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            color: var(--blanco);
            width: 100%;
            transition: all 0.3s ease;
            font-size: 1.05rem;
            box-shadow: 0 4px 15px rgba(245, 150, 34, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 150, 34, 0.4);
            background: linear-gradient(135deg, var(--naranja-hover) 0%, var(--naranja) 100%);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        /* Checkbox */
        .form-check-input:checked {
            background-color: var(--naranja);
            border-color: var(--naranja);
        }
        
        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(245, 150, 34, 0.25);
        }
        
        /* Links */
        .forgot-password {
            color: var(--naranja);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .forgot-password:hover {
            color: var(--naranja-hover);
            text-decoration: underline;
        }
        
        .back-link {
            color: #666;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: var(--naranja);
        }
        
        /* Alert */
        .alert {
            border-radius: 12px;
            border: none;
        }
        
        .alert-danger {
            background-color: #fff5f5;
            color: #c53030;
        }
        
        .alert-success {
            background-color: #f0fdf4;
            color: #15803d;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .login-container {
                padding: 0 15px;
            }
            
            .login-body {
                padding: 35px 25px;
            }
            
            .login-header {
                padding: 40px 20px;
            }
            
            .login-header h3 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    <h3>MASSHA'S CATERING</h3>
                    <p>Sistema de Gestión de Inventario</p>
                </div>
                
                <div class="login-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Error:</strong> Credenciales incorrectas
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-floating mb-4">
                            <input id="email" 
                                   type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   autofocus
                                   placeholder="nombre@ejemplo.com">
                            <label for="email">
                                <i class="fas fa-envelope me-2"></i>Correo Electrónico
                            </label>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-floating mb-4 position-relative">
                            <input id="password" 
                                   type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="Contraseña">
                            <label for="password">
                                <i class="fas fa-lock me-2"></i>Contraseña
                            </label>
                            <span class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="remember" 
                                       id="remember" 
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Recordarme
                                </label>
                            </div>
                            
                            @if (Route::has('password.request'))
                                <a class="forgot-password" href="{{ route('password.request') }}">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </button>
                        
                        <div class="text-center mt-4">
                            <a href="{{ url('/') }}" class="back-link">
                                <i class="fas fa-arrow-left me-2"></i>Volver al inicio
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html> --}}