
<?php
// Conexión a la base de datos 
require_once '../config/conexion.php';
$sql = "SELECT id, nombre FROM idiomas";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$idiomas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>




<div class="card shadow rounded-4 mt-3 mb-5">
    <div class="card-header bg-primary text-white rounded-top-4">
        <h5 class="mb-0"><i class="bi bi-translate me-2"></i>Registro de Idioma</h5>
    </div>
    <div class="card-body bg-light">
        <form action="../php/guardar_idioma.php" method="POST" class="needs-validation" novalidate>
            <div class="row g-3">

                <!-- Idioma (desde base de datos) -->
                <div class="col-md-6">
                    <label for="idioma_id" class="form-label fw-semibold">Idioma</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text bg-primary text-white"><i class="bi bi-flag-fill"></i></span>
                        <select name="idioma_id" id="idioma_id" class="form-select" required>
                            <option value="" selected disabled>Seleccione un idioma</option>
                            <?php foreach ($idiomas as $idioma): ?>
                                <option value="<?= htmlspecialchars($idioma['id']) ?>">
                                    <?= htmlspecialchars($idioma['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Debe seleccionar un idioma.</div>
                    </div>
                </div>

                <!-- Duración en Meses -->
                <div class="col-md-6">
                    <label for="meses_duracion" class="form-label fw-semibold">Duración (en meses)</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text bg-primary text-white"><i class="bi bi-clock-fill"></i></span>
                        <input type="number" name="meses_duracion" id="meses_duracion" class="form-control" placeholder="Ej. 12" min="1" required>
                        <div class="invalid-feedback">Debe ingresar la duración en meses.</div>
                    </div>
                </div>

                <!-- Botón -->
                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn btn-success px-4 rounded-pill shadow">
                        <i class="bi bi-save2 me-2"></i>Guardar Idioma
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
