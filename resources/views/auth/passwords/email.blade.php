<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - MASSHA'S CATERING</title>
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
        
        .password-container {
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
        
        .password-card {
            background: var(--blanco);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }
        
        .password-header {
            background: linear-gradient(135deg, var(--naranja) 0%, var(--naranja-hover) 100%);
            padding: 50px 30px;
            text-align: center;
            color: var(--blanco);
            position: relative;
            overflow: hidden;
        }
        
        .password-header::before {
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
        
        .password-header i {
            font-size: 3rem;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
        }
        
        .password-header h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.6rem;
            position: relative;
            z-index: 2;
        }
        
        .password-header p {
            margin: 10px 0 0 0;
            opacity: 0.95;
            font-size: 0.9rem;
            position: relative;
            z-index: 2;
        }
        
        .password-body {
            padding: 45px 40px;
        }
        
        .form-floating {
            position: relative;
            margin-bottom: 25px;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 14px 18px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .form-control:focus {
            border-color: var(--naranja);
            box-shadow: 0 0 0 0.25rem rgba(245, 150, 34, 0.15);
            background-color: #fffbf7;
        }
        
        .btn-primary {
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
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 150, 34, 0.4);
            background: linear-gradient(135deg, var(--naranja-hover) 0%, var(--naranja) 100%);
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
            .password-container {
                padding: 0 15px;
            }
            
            .password-body {
                padding: 35px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="password-container">
            <div class="password-card">
                <div class="password-header">
                    <i class="fas fa-lock"></i>
                    <h3>Recuperar Contraseña</h3>
                    <p>Ingresa tu correo electrónico para recibir un enlace de recuperación</p>
                </div>
                
                <div class="password-body">
                    <form method="POST" action="{{ route('password.email') }}">
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

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Enlace de Recuperación
                        </button>
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('login') }}" class="back-link">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        @if($errors->has('email'))
            Swal.fire({
                title: '¡Error!',
                text: '{{ $errors->first('email') }}',
                icon: 'error',
                confirmButtonText: 'Intentar de nuevo',
                confirmButtonColor: '#e74c3c',
                timer: 5000,
                timerProgressBar: true
            });
        @endif

        @if(session('status'))
            Swal.fire({
                title: '¡Correo Enviado!',
                text: '{{ session('status') }}',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#27ae60',
                timer: 5000,
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
    <title>Recuperar Contraseña - MASSHA'S CATERING</title>
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
        
        .password-container {
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
        
        .password-card {
            background: var(--blanco);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }
        
        .password-header {
            background: linear-gradient(135deg, var(--naranja) 0%, var(--naranja-hover) 100%);
            padding: 50px 30px;
            text-align: center;
            color: var(--blanco);
            position: relative;
            overflow: hidden;
        }
        
        .password-header::before {
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
        
        .password-header i {
            font-size: 3rem;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
        }
        
        .password-header h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.6rem;
            position: relative;
            z-index: 2;
        }
        
        .password-header p {
            margin: 10px 0 0 0;
            opacity: 0.95;
            font-size: 0.9rem;
            position: relative;
            z-index: 2;
        }
        
        .password-body {
            padding: 45px 40px;
        }
        
        .form-floating {
            position: relative;
            margin-bottom: 25px;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 14px 18px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .form-control:focus {
            border-color: var(--naranja);
            box-shadow: 0 0 0 0.25rem rgba(245, 150, 34, 0.15);
            background-color: #fffbf7;
        }
        
        .btn-primary {
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
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 150, 34, 0.4);
            background: linear-gradient(135deg, var(--naranja-hover) 0%, var(--naranja) 100%);
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
            .password-container {
                padding: 0 15px;
            }
            
            .password-body {
                padding: 35px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="password-container">
            <div class="password-card">
                <div class="password-header">
                    <i class="fas fa-lock"></i>
                    <h3>Recuperar Contraseña</h3>
                    <p>Ingresa tu correo electrónico para recibir un enlace de recuperación</p>
                </div>
                
                <div class="password-body">
                    <form method="POST" action="{{ route('password.email') }}">
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

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Enlace de Recuperación
                        </button>
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('login') }}" class="back-link">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        @if($errors->has('email'))
            Swal.fire({
                title: '¡Error!',
                text: '{{ $errors->first('email') }}',
                icon: 'error',
                confirmButtonText: 'Intentar de nuevo',
                confirmButtonColor: '#e74c3c',
                timer: 5000,
                timerProgressBar: true
            });
        @endif

        @if(session('status'))
            Swal.fire({
                title: '¡Correo Enviado!',
                text: '{{ session('status') }}',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#27ae60',
                timer: 5000,
                timerProgressBar: true
            });
        @endif
    </script>
</body>
</html>


{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}} 
