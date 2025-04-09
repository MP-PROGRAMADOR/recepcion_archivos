<?php
session_start();
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");
include_once("../config/conexion.php");

// Obtener ID
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['errores'] = ['ID de estudiante inválido.'];
    header("Location: estudiantes.php");
    exit;
}

// Obtener estudiante
try {
    $stmt = $pdo->prepare("SELECT * FROM estudiantes WHERE id = ?");
    $stmt->execute([$id]);
    $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$estudiante) {
        $_SESSION['errores'] = ['Estudiante no encontrado.'];
        header("Location: estudiantes.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['errores'] = ['Error al consultar el estudiante: ' . $e->getMessage()];
    header("Location: estudiantes.php");
    exit;
}

// Obtener países
try {
    $stmt = $pdo->prepare("SELECT id, nombre FROM paises ORDER BY nombre ASC");
    $stmt->execute();
    $paises = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['errores'] = ['Error al cargar los países: ' . $e->getMessage()];
    $paises = [];
}
?>

<main class="content" id="mainContentGuin">
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

        <!-- Card -->
        <div class="card shadow rounded-4">
            <div class="card-header bg-warning text-dark d-flex align-items-center">
                <i class="bi bi-pencil-square fs-4 me-2"></i>
                <h5 class="mb-0">Editar Estudiante</h5>
            </div>

            <div class="card-body">
                <form action="../php/actualizar_estudiante.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($estudiante['id']) ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre_completo" class="form-label fw-bold">Nombre Completo</label>
                            <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" required
                                value="<?= htmlspecialchars($estudiante['nombre_completo']) ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="fecha_nacimiento" class="form-label fw-bold">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required
                                value="<?= htmlspecialchars($estudiante['fecha_nacimiento']) ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="pais" class="form-label fw-bold">País</label>
                            <select id="pais" name="pais" class="form-select" required>
                                <option value="" disabled>Selecciona tu país</option>
                                <?php foreach ($paises as $pais): ?>
                                    <option value="<?= $pais['id'] ?>" <?= $pais['id'] == $estudiante['pais_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($pais['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="foto" class="form-label fw-bold">Foto del Estudiante</label>
                            <input type="file" id="foto" name="foto" class="form-control" accept="image/*" onchange="previewImage(event)">
                            <?php if (!empty($estudiante['ruta_foto'])): ?>
                                <small class="d-block mt-1">Foto actual:</small>
                                
                                <img src="../php/<?= $estudiante['ruta_foto'] ?>" alt="Foto actual" class="img-thumbnail mt-1" width="100">
                            <?php endif; ?>
                        </div>

                        <div class="col-md-12 text-center">
                            <label class="form-label fw-bold">Vista previa:</label><br>
                            <img id="foto_preview" src="#" alt="Vista previa" class="img-thumbnail rounded-4 shadow"
                                style="max-width: 200px; display: none;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-warning me-2">
                            <i class="bi bi-pencil-fill me-1"></i> Actualizar
                        </button>
                        <a href="estudiantes.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle-fill me-1"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- Bootstrap Icons + SweetAlert -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Preview JS -->
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('foto_preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<!-- Animación para alerta -->
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<?php include_once("../componentes/footer.php"); ?>
