<?php
session_start();

if (!isset($_SESSION['estudiante'])) {
    header("Location: ../index.php");
    exit();
}

$estudianteSesion = $_SESSION['estudiante'];

require_once '../config/conexion.php';

try {
    $stmt = $pdo->prepare("SELECT id, nombre_completo FROM estudiantes WHERE codigo_acceso = ?");
    $stmt->execute([$estudianteSesion['codigo_acceso']]);
    $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$estudiante) {
        echo "<div class='alert alert-danger'>Estudiante no encontrado.</div>";
        exit;
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error de conexión: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>

<div class="card shadow rounded-4 mt-3 mb-5">
    <div class="card-header bg-primary text-white rounded-top-4">
        <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Actualizar Información de Contacto y Perfil</h5>
    </div>
    <div class="card-body bg-light">
        <form action="../php/actualizar_perfil.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="row g-3">

                <!-- ID oculto -->
                <input type="hidden" name="estudiante_id" value="<?= htmlspecialchars($estudiante['id']) ?>">

                <!-- Nombre (solo lectura) -->
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Nombre del Estudiante</label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($estudiante['nombre_completo']) ?>" disabled>
                    </div>
                </div>
          
                                 <!-- Imagen de perfil -->
                <div class="col-md-12">
                    <label for="foto_perfil" class="form-label fw-semibold">Foto de Perfil (opcional)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white"><i class="bi bi-image-fill"></i></span>
                        <input type="file" name="foto_perfil" id="foto_perfil" class="form-control" accept="image/*">
                    </div>
                    <small class="form-text text-muted">Solo se aceptan imágenes (.jpg, .jpeg, .png, .webp). Tamaño: 2MB.</small>
                </div>

                <!-- Botón -->
                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn btn-success px-4 rounded-pill shadow">
                        <i class="bi bi-save me-2"></i>Actualizar Contacto
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- Validación Bootstrap -->
<script>
    (() => {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
