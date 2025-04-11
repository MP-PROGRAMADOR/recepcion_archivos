<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Portal Estudiantil - Bienvenida</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f8;
        }

        .container-login {
            width: 100%;
            max-width: 420px;
        }

        .card-img-top {
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            overflow: hidden;
            background-color: #fff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        }

        .card-img-top img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .login-card {
            border-radius: 20px;
            background-color: #fff;
            padding: 2rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="container-login">

        <!-- Card de imagen superior -->
        <div class="card-img-top">
            <img src="img.jpg" alt="Imagen encabezado">
            <div class="login-card text-center">

                <!-- Card de formulario -->
                <h2 class="mb-4"><i class="bi bi-person-check-fill me-2 text-primary"></i>Acceso Estudiantil</h2>
                <form action="../php/verificar_codigo.php" method="POST">
                    <div class="mb-3 text-start">
                        <label for="codigo" class="form-label">Ingresa tu código de registro</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input type="text" name="codigo" id="codigo" class="form-control"
                                placeholder="Ej: MB-E-25-7" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        Verificar acceso <i class="bi bi-arrow-right-circle ms-2"></i>
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>