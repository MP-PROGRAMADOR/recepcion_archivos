<?php
// Iniciar sesión de forma segura
if (session_status() === PHP_SESSION_DISABLED) {
    die("⚠️ Las sesiones están deshabilitadas en la configuración del servidor.");
} elseif (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/conexion.php';

// Obtener la configuración actual del sitio
$sql = "SELECT * FROM configuracion LIMIT 1";

try {
    $stmt = $pdo->query($sql);
    $config = $stmt->fetch(PDO::FETCH_ASSOC); // Asegura array asociativo
    
    // Validar que se obtuvo la configuración
    if ($config) {
        $_SESSION['config'] = $config;
        $_SESSION["favico"] = $config["logo"];
    } else {
        // Opcional: manejo si no hay config
        $_SESSION['config'] = 'está vacía';
    }

} catch (PDOException $e) {
    // Manejo de errores en producción debería ser más discreto
    die("Error al obtener configuración: " . $e->getMessage());
}

$foto = $config['img_admin']; // Ej: logo.png
$rutaRelativa = './php/upload/configuracion/' . basename($foto);
$degradado = $config['color_primario'];

// Ruta del favicon
$favico = $_SESSION["favico"]; // Ruta predeterminada
//$rutaFavico = './php/upload/configuracion/' . basename($favico);
$rutaFavico = './php/upload/configuracion/' . basename($favico);

// Verificar la extensión del favicon
$extension = strtolower(pathinfo($favico, PATHINFO_EXTENSION));

// Establecer el tipo MIME según la extensión del archivo
switch ($extension) {
    case 'ico':
        $mime_type = 'image/x-icon';
        break;
    case 'png':
        $mime_type = 'image/png';
        break;
    case 'webp': // Corregido a 'webp'
        $mime_type = 'image/webp'; 
        break;
    case 'svg':
        $mime_type = 'image/svg+xml';
        break;
    default:
        // Si la extensión no es válida, usamos el favicon por defecto
        $favico = 'favicon.ico';
        $mime_type = 'image/x-icon';
        break;
}

echo "la ruta: ".$rutaFavico;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iniciar sesión - Sistema Académico</title>

    <!-- Favicon dinámico -->
    <link rel="icon" type="<?php echo htmlspecialchars($mime_type); ?>" href="<?php echo htmlspecialchars($rutaFavico); ?>">

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
            background: url('<?php echo $rutaRelativa; ?>') no-repeat center center;
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
            background:
                <?php echo $degradado; ?>
            ;
            z-index: -1;
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
                        <a href="register.php" class="link-volver">
                            <i class="bi bi-arrow-left me-1"></i> Registrarse
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