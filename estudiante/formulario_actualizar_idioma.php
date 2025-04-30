<?php
// Conexión
require_once '../config/conexion.php';

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID no válido');
}

$id = (int) $_GET['id'];

// Obtener idioma actual
$sql = "SELECT * FROM registro_idiomas WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$registro = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$registro) {
    die('Registro no encontrado');
}

// Obtener listado de idiomas
$sqlIdiomas = "SELECT id, nombre FROM idiomas";
$stmtIdiomas = $pdo->prepare($sqlIdiomas);
$stmtIdiomas->execute();
$idiomas = $stmtIdiomas->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- HTML + Bootstrap -->
<div class="card shadow rounded-4 mt-3 mb-5">
    <div class="card-header bg-warning text-dark rounded-top-4">
        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Actualizar Idioma</h5>
    </div>
    <div class="card-body bg-light">
        <form action="../php/editar_idioma.php" method="POST" class="needs-validation" novalidate>
            <div class="row g-3">

                <!-- ID oculto -->
                <input type="hidden" name="id" value="<?= htmlspecialchars($registro['id']) ?>">

                <!-- Idioma -->
                <div class="col-md-6">
                    <label for="idioma_id" class="form-label fw-semibold">Idioma</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text bg-warning text-dark"><i class="bi bi-flag-fill"></i></span>
                        <select name="idioma_id" id="idioma_id" class="form-select" required>
                            <option value="" disabled>Seleccione un idioma</option>
                            <?php foreach ($idiomas as $idioma): ?>
                                <option value="<?= htmlspecialchars($idioma['id']) ?>" <?= $idioma['id'] == $registro['idioma_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($idioma['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Debe seleccionar un idioma.</div>
                    </div>
                </div>

                <!-- Duración -->
                <div class="col-md-6">
                    <label for="meses_duracion" class="form-label fw-semibold">Duración (en meses)</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text bg-warning text-dark"><i class="bi bi-clock-fill"></i></span>
                        <input type="number" name="meses_duracion" id="meses_duracion" class="form-control" value="<?= htmlspecialchars($registro['meses_duracion']) ?>" min="1" required>
                        <div class="invalid-feedback">Debe ingresar la duración.</div>
                    </div>
                </div>

                <!-- Botón -->
                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn btn-warning px-4 rounded-pill shadow">
                        <i class="bi bi-save2 me-2"></i>Actualizar Idioma
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
