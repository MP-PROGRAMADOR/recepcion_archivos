<?php
session_start();

// Validar sesión activa de estudiante
if (!isset($_SESSION['estudiante'])) {
    header("Location: index.php");
    exit();
}

$estudianteSesion = $_SESSION['estudiante'];

require_once '../config/conexion.php';

try {
    $stmtEstudiante = $pdo->prepare("SELECT id, nombre_completo, email, telefono FROM estudiantes WHERE codigo_acceso = ?");
    $stmtEstudiante->execute([$estudianteSesion['codigo_acceso']]);
    $estudiante = $stmtEstudiante->fetch(PDO::FETCH_ASSOC);

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
        <h5 class="mb-0"><i class="bi bi-telephone-forward-fill me-2"></i>Actualizar Información de Contacto</h5>
    </div>
    <div class="card-body bg-light">
        <form action="../php/guardar_contacto.php" method="POST" class="needs-validation" novalidate>
            <div class="row g-3">

                <!-- ID oculto -->
                <input type="hidden" name="estudiante_id" value="<?= htmlspecialchars($estudiante['id']) ?>">

                <!-- Estudiante -->
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Estudiante</label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($estudiante['nombre_completo']) ?>" disabled>
                    </div>
                </div>

                <!-- Correo Electrónico -->
                <div class="col-md-6">
                    <label for="correo" class="form-label fw-semibold">Correo Electrónico</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text bg-primary text-white"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" name="correo" id="correo" class="form-control" value="<?= htmlspecialchars($estudiante['email']) ?>" required>
                        <div class="invalid-feedback">Debe ingresar un correo válido.</div>
                    </div>
                </div>

                <!-- Teléfono -->
                <div class="col-md-6">
                    <label for="telefono" class="form-label fw-semibold">Teléfono</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text bg-primary text-white"><i class="bi bi-telephone-fill"></i></span>
                        <input type="text" name="telefono" id="telefono" class="form-control" value="<?= htmlspecialchars($estudiante['telefono']) ?>" pattern="^\d{7,15}$" required>
                        <div class="invalid-feedback">Ingrese un número válido (mínimo 8 dígitos).</div>
                    </div>
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
