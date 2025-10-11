<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MASSHA'S CATERING - Sistema de Gestión</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('images/logo.png') }}" rel="icon">
    <style>
        :root {
            --naranja: #f59622;
            --naranja-hover: #e68512;
            --negro: #191919;
            --blanco: #ffffff;
            --gris-claro: #f5f5f5;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--negro);
            overflow-x: hidden;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--negro) 0%, #2a2a2a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        /* Patron de fondo */
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(245, 150, 34, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(245, 150, 34, 0.08) 0%, transparent 50%);
            animation: pulse 4s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        /* Líneas decorativas */
        .hero-section::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 40%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(245, 150, 34, 0.05));
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            color: var(--blanco);
        }
        
        /* Logo Container */
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeInDown 1s ease-out;
        }
        
        .logo-container img {
            max-height: 150px;
            filter: drop-shadow(0 10px 30px rgba(245, 150, 34, 0.3));
            transition: transform 0.3s ease;
        }
        
        .logo-container img:hover {
            transform: scale(1.05);
        }
        
        /* Títulos */
        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--blanco) 0%, var(--naranja) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeInUp 1s ease-out 0.2s both;
            letter-spacing: -1px;
        }
        
        .hero-subtitle {
            font-size: clamp(1rem, 3vw, 1.3rem);
            margin-bottom: 3rem;
            color: #cccccc;
            animation: fadeInUp 1s ease-out 0.4s both;
            font-weight: 300;
        }
        
        /* Botón Principal */
        .btn-access {
            background: linear-gradient(135deg, var(--naranja) 0%, var(--naranja-hover) 100%);
            color: var(--blanco);
            padding: 18px 50px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            box-shadow: 0 10px 40px rgba(245, 150, 34, 0.4);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            animation: fadeInUp 1s ease-out 0.6s both;
            position: relative;
            overflow: hidden;
        }
        
        .btn-access::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-access:hover::before {
            left: 100%;
        }
        
        .btn-access:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(245, 150, 34, 0.6);
            color: var(--blanco);
        }
        
        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
            animation: fadeInUp 1s ease-out 0.8s both;
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: all 0.4s ease;
            border: 2px solid rgba(245, 150, 34, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(245, 150, 34, 0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.4s;
        }
        
        .feature-card:hover::before {
            opacity: 1;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            border-color: var(--naranja);
            background: rgba(245, 150, 34, 0.05);
            box-shadow: 0 15px 40px rgba(245, 150, 34, 0.2);
        }
        
        .feature-icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            color: var(--naranja);
            transition: transform 0.4s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.15) rotate(5deg);
        }
        
        .feature-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
            color: var(--blanco);
        }
        
        .feature-card p {
            color: #aaaaaa;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        /* Animaciones */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-section::after {
                display: none;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .btn-access {
                padding: 15px 35px;
                font-size: 1rem;
            }
            
            .logo-container img {
                max-height: 100px;
            }
        }

        /* Decoración inferior */
        .bottom-decoration {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(180deg, transparent 0%, rgba(245, 150, 34, 0.1) 100%);
            pointer-events: none;
        }
    </style>
</head>
<body>
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 hero-content">
                    <div class="logo-container">
                        <img src="{{ asset('images/logo.png') }}" alt="MASSHA'S CATERING Logo">
                    </div>
                    
                    <div class="text-center">
                        <h1 class="hero-title">MASSHA'S CATERING</h1>
                        <p class="hero-subtitle">
                            Sistema Integrado de Gestión de Inventario y Almacén
                        </p>
                        
                        <a href="{{ route('login') }}" class="btn btn-access">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Acceder al Sistema</span>
                        </a>
                    </div>
                    
                    <div class="features-grid">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <h3>Control de Inventario</h3>
                            <p>Gestión en tiempo real de todos tus insumos y productos</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h3>Solicitudes Inteligentes</h3>
                            <p>Genera y aprueba solicitudes con validación de stock</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3>Reportes Avanzados</h3>
                            <p>Analítica de datos para decisiones estratégicas</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <h3>Gestión de Proveedores</h3>
                            <p>Administra relaciones y compras eficientemente</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bottom-decoration"></div>
    </section>
</body>
</html>