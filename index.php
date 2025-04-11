<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iniciar sesión - Sistema Académico</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Fuente elegante -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-principal: #00558c;
            --color-secundario: #f8f9fa;
            --borde-suave: 12px;
            --sombra-input: 0 1px 4px rgba(0, 0, 0, 0.08);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--color-secundario);
            margin: 0;
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

        .form-control,
        .input-group-text {
            border-radius: var(--borde-suave);
            box-shadow: var(--sombra-input);
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 85, 140, 0.2);
            border-color: var(--color-principal);
        }

        .btn-dark {
            border-radius: var(--borde-suave);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-dark:hover {
            background-color: #003f66;
            transform: scale(1.02);
        }

        .link-volver {
            color: #6c757d;
            transition: color 0.2s ease;
        }

        .link-volver:hover {
            color: #343a40;
            text-decoration: none;
        }

        .panel-izquierdo {
            position: relative;
            background: url('img.jpg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            padding: 3rem;
            color: white;
            z-index: 1;
        }

        .panel-izquierdo::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* Opción A: Sombra con opacidad azul oscura */
            /*background: rgba(0, 50, 90, 0.75);*/
            /* Opción B: Degradado (puedes usar esta en vez de la de arriba) */
             background: linear-gradient(to bottom right, rgba(0, 84, 140, 0.34), rgba(0, 30, 60, 0.7)); 
            z-index: -1;
        }

        .panel-izquierdo h1,
        .panel-izquierdo p {
            z-index: 2;
            position: relative;
        }
    </style>

</head>

<body class="fade-in">

    <div class="container-fluid g-0">
        <div class="row g-0 min-vh-100">

            <!-- Panel izquierdo -->
            <!-- Panel izquierdo -->
            <div class="col-md-5 d-flex flex-column justify-content-center panel-izquierdo">
                <h1 class="h3 fw-bold">Bienvenido/a</h1>
                <p class="mt-3 fs-6">
                    Plataforma oficial para la gestión y recepción de archivos académicos. Accede con tus credenciales
                    institucionales.
                </p>
            </div>


            <!-- Panel derecho -->
            <div class="col-md-7 d-flex align-items-center justify-content-center bg-white px-4 py-5">
                <div class="w-100" style="max-width: 420px;">
                    <h2 class="mb-4 text-center text-dark">
                        <i class="bi bi-person-circle me-2"></i>Iniciar sesión
                    </h2>
                    <!-- Formulario -->
                    <form action="php/login.php" method="POST" class="fade-in" autocomplete="off">

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="ejemplo@institucion.edu" required>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="contrasena" name="contrasena"
                                    placeholder="Ingresa tu contraseña institucional" required>
                            </div>
                        </div>

                        <!-- Botón -->
                        <button type="submit" class="btn btn-dark w-100">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
                        </button>
                    </form>

                    <!-- Link volver -->
                    <div class="text-center mt-4">
                        <a href="estudiante/index.php" class="link-volver">
                            <i class="bi bi-arrow-left me-1"></i> Ver Panel de Estudiante
                        </a>
                    </div>
                    
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>