<?php
session_start(); // <-- Asegura que las sesiones funcionen
require_once '../config/conexion.php';
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de usuario no válido.");
}

$id = (int)$_GET['id'];

// Obtener datos del usuario
try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        die("Usuario no encontrado.");
    }
} catch (PDOException $e) {
    die("Error al obtener datos del usuario: " . $e->getMessage());
}
?>

<main class="content" id="mainContent">
    <div class="container mt-4">

         <!-- INICIO DE LA ALERTA DE ERRORRES -->
         <?php


if (isset($_SESSION['errores']) && is_array($_SESSION['errores'])):
    ?>
    <div id="alerta-errores"
        class="alert alert-danger alert-dismissible shadow-sm fade show d-flex align-items-start gap-2 p-3 mt-3 border border-danger-subtle rounded-3"
        role="alert" style="animation: fadeIn 0.5s ease-in-out;">
        <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0 mt-1"></i>
        <div>
            <strong>Se detectaron errores:</strong>
            <ul class="mb-0 mt-1">
                <?php foreach ($_SESSION['errores'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <button type="button" class="btn-close ms-auto mt-1" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>

    <script>
        // Ocultar automáticamente luego de 6 segundos
        setTimeout(() => {
            const alerta = document.getElementById('alerta-errores');
            if (alerta) {
                alerta.classList.remove('show');
                alerta.classList.add('fade');
                setTimeout(() => alerta.remove(), 500); // Lo remueve del DOM
            }
        }, 9000);
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
    <?php
    unset($_SESSION['errores']); // Limpiar errores de la sesión
endif;
?>


<!-- FIN DE LA ALERTA -->

        <div class="card shadow rounded-4">
            <div class="card-header bg-warning text-dark d-flex align-items-center">
                <i class="bi bi-pencil-square fs-4 me-2"></i>
                <h5 class="mb-0">Editar Usuario</h5>
            </div>
            <div class="card-body">
                <form action="../php/actualizar_usuario.php" method="POST" novalidate>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre completo</label>
                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Correo electrónico</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nueva Contraseña (opcional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Solo si deseas cambiarla">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Confirmar nueva contraseña</label>
                            <input type="password" name="contrasena_confirmada" class="form-control" placeholder="Repetir contraseña">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success me-2">
                            <i class="bi bi-save-fill me-1"></i> Actualizar
                        </button>
                        <a href="usuario.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle-fill me-1"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php include_once("../componentes/footer.php"); ?>
