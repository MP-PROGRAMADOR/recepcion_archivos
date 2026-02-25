<?php
// Iniciar sesión de forma segura
if (session_status() === PHP_SESSION_DISABLED) {
    die("⚠️ Las sesiones están deshabilitadas en la configuración del servidor.");
} elseif (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iniciar sesión - Sistema Académico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="config/img/logo_pais.svg" type="image/png">


    <style>
    :root {
        --color-principal: #00558c;
        /* Azul Institucional */
        --color-acento: #00b894;
        /* Verde (para el foco) */
        --color-secundario: #f8f9fa;
        --borde-suave: 12px;
        --sombra-input: 0 1px 4px rgba(0, 0, 0, 0.08);
        --glass-blur: 8px;
        /* Intensidad del desenfoque para glassmorphism */
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--color-secundario);
        margin: 0;
        overflow-x: hidden;
    }

    .fade-in {
        animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ---------------------------------------------------- */
    /* ESTILOS DE FORMULARIO MEJORADOS (Panel Derecho) */
    /* ---------------------------------------------------- */
    .form-control,
    .input-group-text {
        border-radius: var(--borde-suave);
        box-shadow: none;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
    }

    .input-group-text {
        border-right: none !important;
        background-color: white !important;
        color: var(--color-principal);
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.1rem var(--color-acento), 0 0 0 0.4rem rgba(0, 184, 148, 0.2);
        border-color: var(--color-acento);
    }

    .btn-principal {
        background-color: var(--color-principal);
        border-color: var(--color-principal);
        color: white;
        padding: 12px 20px;
        font-weight: 600;
        letter-spacing: 0.5px;
        border-radius: var(--borde-suave);
        transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 10px rgba(0, 85, 140, 0.2);
    }

    .btn-principal:hover {
        background-color: #003f66;
        border-color: #003f66;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 85, 140, 0.3);
    }

    .panel-derecho {
        box-shadow: -10px 0 30px rgba(0, 0, 0, 0.05);
    }

    .link-volver {
        color: var(--color-principal);
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .link-volver:hover {
        color: var(--color-acento);
        text-decoration: underline;
    }

    .login-header {
        color: var(--color-principal);
        font-weight: 700;
    }

    /* ---------------------------------------------------- */
    /* MEJORAS DEL PANEL IZQUIERDO */
    /* ---------------------------------------------------- */
    .panel-izquierdo {
        position: relative;
        min-height: 100vh;
        color: white;
        overflow: hidden;
        /* Para que el pseudo-elemento no se desborde */
        border-top-right-radius: 40px;
        border-bottom-right-radius: 40px;
        box-shadow: 10px 0 30px rgba(0, 0, 0, 0.2);
        display: flex;
        /* Usamos flexbox para centrar el contenido */
        align-items: center;
        /* Centrado vertical */
        justify-content: center;
        /* Centrado horizontal */
        padding: 3rem;
        /* Padding general */
        background-color: #003050;
        /* Color de fondo base si no carga el pseudo-elemento */
    }

    /* Pseudo-elemento para el fondo abstracto y animado */
    .panel-izquierdo::before {
        content: "";
        position: absolute;
        top: -20%;
        /* Inicia un poco fuera para la animación */
        left: -20%;
        /* Inicia un poco fuera para la animación */
        width: 140%;
        height: 140%;
        background:
            radial-gradient(circle at 20% 80%, rgba(0, 84, 140, 0.8) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(0, 184, 148, 0.6) 0%, transparent 50%),
            linear-gradient(to bottom right, #003f66, #001a33);
        /* Degradado base más oscuro */
        background-size: 200% 200%;
        /* Tamaño para que el gradiente se mueva */
        animation: backgroundPan 20s ease infinite alternate;
        /* Animación lenta */
        z-index: -1;
        filter: blur(50px);
        /* Desenfoque para un efecto abstracto */
        opacity: 0.9;
    }

    @keyframes backgroundPan {
        0% {
            background-position: 0% 0%;
        }

        100% {
            background-position: 100% 100%;
        }
    }

    .left-panel-content {
        position: relative;
        /* Para que esté por encima del pseudo-elemento */
        z-index: 2;
        /* Aseguramos que el contenido esté al frente */
        text-align: center;
        /* Centramos el texto dentro del panel */
        max-width: 400px;
        /* Ancho máximo para el contenido */
        padding: 2.5rem;
        border-radius: var(--borde-suave);
        /* Glassmorphism */
        background: rgba(255, 255, 255, 0.1);
        /* Fondo blanco semitransparente */
        backdrop-filter: blur(var(--glass-blur));
        /* Desenfoque de fondo */
        -webkit-backdrop-filter: blur(var(--glass-blur));
        /* Compatibilidad Safari */
        border: 1px solid rgba(255, 255, 255, 0.2);
        /* Borde sutil */
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        /* Sombra para el efecto de profundidad */
        animation: slideUp 0.8s ease-out forwards;
        /* Animación de entrada */
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .logo-container {
        margin-bottom: 1.5rem;
        display: inline-block;
        /* Para que el padding y border-radius funcionen bien */
        padding: 15px 25px;
        /* Más padding para el logo */
        background-color: rgba(255, 255, 255, 0.08);
        /* Fondo aún más sutil */
        border-radius: var(--borde-suave);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .logo-container:hover {
        transform: scale(1.03);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }

    .logo-img {
        max-width: 120px;
        /* Logo un poco más pequeño para una estética más "card" */
        height: auto;
        filter: drop-shadow(0 0 8px rgba(0, 0, 0, 0.6));
        /* Sombra más pronunciada para el logo */
    }

    .panel-izquierdo h1 {
        font-size: 2.5rem;
        /* Título más grande */
        font-weight: 700;
        line-height: 1.2;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
        /* Sombra para el texto */
        margin-bottom: 1rem;
    }

    .panel-izquierdo .lead {
        font-size: 1.1rem;
        margin-bottom: 2rem;
        text-shadow: 0 1px 5px rgba(0, 0, 0, 0.3);
    }

    .panel-izquierdo .small {
        margin-top: 2rem;
        color: rgba(255, 255, 255, 0.7);
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        padding-top: 1rem;
    }


    /* Ajuste responsivo para el borde del panel */
    @media (max-width: 767.98px) {
        .panel-izquierdo {
            min-height: 50vh;
            /* Un poco más alto en móvil */
            border-radius: 0;
        }

        .panel-izquierdo::before {
            border-radius: 0;
        }

        .left-panel-content {
            padding: 1.5rem;
            /* Menos padding en móvil */
            margin: 1rem;
            /* Margen para no pegar a los bordes */
        }

        .panel-izquierdo h1 {
            font-size: 2rem;
        }

        .panel-izquierdo .lead {
            font-size: 1rem;
        }
    }
    </style>

</head>

<body class="fade-in">

    <div class="container-fluid g-0">
        <div class="row g-0 min-vh-100">

            <div class="col-md-5 d-flex panel-izquierdo">

                <div class="left-panel-content">
                    <div class="logo-container">
                        <img src="config/img/logo_pais.svg" class="logo-img" alt="Logo Institucional">
                    </div>

                    <h1 class="h3 fw-bold mt-3">Bienvenido/a</h1>
                    <p class="lead">
                        Plataforma oficial para la gestión y recepción de archivos académicos. **Accede con tus
                        credenciales institucionales.**
                    </p>
                    <p class="small">
                        Sistema de Gestión Académica © 2025.
                    </p>
                </div>
            </div>

            <div class="col-md-7 d-flex align-items-center justify-content-center bg-white px-4 py-5 panel-derecho">
                <div class="w-100" style="max-width: 420px;">

                    <h2 class="mb-5 text-center login-header">
                        <i class="bi bi-shield-lock-fill me-2"></i>Acceso al Sistema
                    </h2>

                    <form action="php/login.php" method="POST" class="fade-in" autocomplete="off">

                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="ejemplo@institucion.edu" required autofocus>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label for="contrasena" class="form-label fw-bold">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="contrasena" name="contrasena"
                                    placeholder="Ingresa tu contraseña institucional" required>
                            </div>
                        </div>


                        <!-- INICIO DE LA ALERTA -->
                        <?php if (isset($_SESSION['exito']) && !empty($_SESSION['exito'])): ?>
                        <div id="alerta-exito"
                            class="alert alert-success alert-dismissible shadow-sm fade show d-flex align-items-start gap-2 p-3 mt-3 border border-success-subtle rounded-3"
                            role="alert" style="animation: fadeIn 0.5s ease-in-out;">
                            <i class="bi bi-check-circle-fill fs-4 flex-shrink-0 mt-1"></i>
                            <div>
                                <strong>¡Aviso!</strong>
                                <p class="mb-0 mt-1"><?= htmlspecialchars($_SESSION['exito']) ?></p>
                            </div>
                            <button type="button" class="btn-close ms-auto mt-1" data-bs-dismiss="alert"
                                aria-label="Cerrar"></button>
                        </div>
                        <script>
                        setTimeout(() => {
                            const alerta = document.getElementById('alerta-exito');
                            if (alerta) {
                                alerta.classList.remove('show');
                                alerta.classList.add('fade');
                                setTimeout(() => alerta.remove(), 500);
                            }
                        }, 5000);
                        </script>
                        <style>
                        @keyframes fadeIn {
                            from {
                                opacity: 0;
                                transform: translateY(-10px);
                            }

                            to {
                                opacity: 1;
                                transform: translateY(0);
                            }
                        }
                        </style>
                        <?php unset($_SESSION['exito']); ?>
                        <?php endif; ?>
                        <!-- FIN DE LA ALERTA -->


                        <button type="submit" class="btn btn-principal w-100">
                            <i class="bi bi-box-arrow-in-right me-1"></i> **Entrar**
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="estudiante/index.php" class="link-volver">
                            <i class="bi bi-arrow-left me-1"></i> **Ver Panel de Estudiante**
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>