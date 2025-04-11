<?php
session_start();

// Validar sesión activa de estudiante
if (!isset($_SESSION['estudiante'])) {
    header("Location: index.php");
    exit();
}

$estudianteSesion = $_SESSION['estudiante'];

require_once '../config/conexion.php'; // Asegúrate que esta ruta sea correcta

try {
    // Obtener datos del estudiante desde la base (opcional si ya están en sesión)
    $stmtEstudiante = $pdo->prepare("SELECT id, nombre_completo FROM estudiantes WHERE codigo_acceso = ?");
    $stmtEstudiante->execute([$estudianteSesion['codigo_acceso']]);
    $estudiante = $stmtEstudiante->fetch(PDO::FETCH_ASSOC);

    if (!$estudiante) {
        echo "<div class='alert alert-danger'>Estudiante no encontrado.</div>";
        exit;
    }

    // Obtener años académicos
    $stmtAnios = $pdo->query("SELECT id, nombre FROM anios_academicos ORDER BY nombre ASC");
    $anios = $stmtAnios->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error de conexión: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>


<!-- ... incluye previamente los <link> y <script> de Bootstrap, Bootstrap Icons, jQuery y Select2 como antes ... -->

<div class="card shadow rounded-4 mt-3 mb-5">
    <div class="card-header bg-primary text-white rounded-top-4">
        <h5 class="mb-0"><i class="bi bi-journal-plus me-2"></i>Registro de Nota Académica</h5>
    </div>
    <div class="card-body bg-light">
        <form action="../php/guardar_notas.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="row g-3">

                <!-- ID oculto -->
                <input type="hidden" name="estudiante_id" value="<?= htmlspecialchars($estudiante['id']) ?>">

                <!-- Estudiante -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Estudiante</label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($estudiante['nombre_completo']) ?>" disabled>
                    </div>
                </div>

                <!-- Año Académico -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Año Académico</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text bg-primary text-white"><i class="bi bi-calendar3"></i></span>
                        <select name="anio_academico_id" id="anio_academico_id" class="form-select" required>
                            <option value="" disabled selected>Seleccione el año académico</option>
                            <?php foreach ($anios as $anio): ?>
                                <option value="<?= htmlspecialchars($anio['id']) ?>"><?= htmlspecialchars($anio['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Debe seleccionar un año académico.</div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Observaciones</label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white"><i class="bi bi-chat-left-text-fill"></i></span>
                        <textarea name="observaciones" id="observaciones" class="form-control border rounded-3" rows="3" placeholder="Observaciones sobre la nota..."></textarea>
                    </div>
                </div>

                <!-- Archivo -->
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Archivo adjunto</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text bg-primary text-white"><i class="bi bi-paperclip"></i></span>
                        <input type="file" name="archivo_url" id="archivo" class="form-control" accept="application/pdf" required>
                        <div class="invalid-feedback">Debe adjuntar un archivo válido (PDF).</div>
                    </div>
                    <small class="text-muted">Archivos permitidos: PDF Máx. 5MB.</small>

                    <!-- Previsualización -->
                    <div id="preview" class="mt-3" style="display: none;">
                        <label class="form-label fw-semibold">Vista previa de imagen:</label><br>
                        <img id="preview-image" src="#" alt="Previsualización" class="img-thumbnail" style="max-height: 200px;">
                    </div>
                </div>

                <!-- Botón -->
                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn btn-success px-4 rounded-pill shadow">
                        <i class="bi bi-save me-2"></i>Guardar Nota
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
 
