<?php
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: cuentas.php?error=ID inválido");
    exit;
}

$id = (int) $_GET['id'];

// Obtener cuenta actual
$stmt = $pdo->prepare("SELECT * FROM cuentas_bancarias WHERE id = ?");
$stmt->execute([$id]);
$cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cuenta) {
    header("Location: cuentas.php?error=Cuenta no encontrada");
    exit;
}


?>

<main class="content" id="mainContent">
    <div class="container mt-5">
        <h3><i class="bi bi-pencil-square me-2"></i>Editar Cuenta Bancaria</h3>

        <!-- INICIO DE LA ALERTA -->
        <?php include_once("../componentes/alerta.php"); ?> 
        <form action="../php/actualizar_cuenta.php" method="POST" class="card p-4 shadow rounded-4 mt-3">
            <div class="row g-3">

                <input type="hidden" name="estudiante_id" value="<?= htmlspecialchars($cuenta['estudiante_id']) ?>">
                <div class="col-md-6 mb-2">
                    <label for="tipo_cuenta" class="form-label fw-bold">Tipo de Cuenta</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text"><i class="bi bi-wallet2"></i></span>
                        <select id="tipo_cuenta" name="tipo_cuenta" class="form-select" required>
                            <option value="<?= htmlspecialchars($cuenta['tipo_cuenta']) ?>" selected>
                                <?= htmlspecialchars($cuenta['tipo_cuenta']) ?>
                            </option>
                            <option value="departamental">Departamental</option>
                            <option value="propia">Propia</option>
                        </select>
                        <div class="valid-feedback">¡Correcto!</div>
                        <div class="invalid-feedback">Por favor, indica el tipo de cuenta.</div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <label for="banco" class="form-label fw-bold">Banco</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text"><i class="bi bi-bank2"></i></span>
                        <select id="banco" name="banco" class="form-select" required>
                            <option value="<?= htmlspecialchars($cuenta['banco']) ?>" selected>
                                <?= htmlspecialchars($cuenta['banco']) ?>
                            </option>
                            <option value="ecobank">ECOBANK</option>
                            <option value="sgbge">SGBGE</option>
                            <option value="cceibank">CCEIBANK</option>
                            <option value="embajada">EMBAJADA</option>
                        </select>
                        <div class="valid-feedback">¡Correcto!</div>
                        <div class="invalid-feedback">Por favor, ingresa el nombre del banco.</div>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <label for="numero_cuenta" class="form-label fw-bold">Número de Cuenta</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text"><i class="bi bi-hash"></i></span>
                        <input type="text" id="numero_cuenta" class="form-control" name="numero_cuenta"
                            value="<?= htmlspecialchars($cuenta['numero_cuenta']) ?>">
                        <div class="valid-feedback">¡Correcto!</div>
                        <div class="invalid-feedback">Por favor, ingresa el número de cuenta.</div>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <label for="tarjeta_visa" class="form-label fw-bold">¿Tiene tarjeta VISA?</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-credit-card-2-back"></i></span>
                        <select id="tarjeta_visa" name="tarjeta_visa" class="form-select" required>
                            <option value="<?= htmlspecialchars($cuenta['tarjeta_visa']) ?>" selected>
                                <?= htmlspecialchars($cuenta['tarjeta_visa']) ?>
                            </option>
                            <option value="si">SÍ</option>
                            <option value="no">NO</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <label for="fecha_caducidad_tarjeta" class="form-label fw-bold">Fecha de Caducidad</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar-check-fill"></i></span>
                        <input type="date" id="fecha_caducidad_tarjeta" name="fecha_caducidad_tarjeta"
                            class="form-control" value="<?= htmlspecialchars($cuenta['fecha_caducidad_tarjeta']) ?> " disabled style="display: none;">
                    </div>
                </div>
            </div>
            <!-- Botones -->
            <div class="d-flex justify-content-between mb-4 p-4">
                <a href="cuentas_bancarias.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle-fill me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-save me-1"></i> Actualizar
                </button>
            </div>
        </form>
    </div>
</main>

<script>
    document.getElementById('tarjeta_visa').addEventListener('change', function () {
        const inputFecha = document.getElementById('fecha_caducidad_tarjeta');

        if (this.value === 'si') {
            inputFecha.disabled = false;
            inputFecha.style.display = 'block';
            inputFecha.setAttribute('name', 'fecha_caducidad_tarjeta'); // Asegura que se envíe
        } else {
            inputFecha.disabled = true;
            inputFecha.style.display = 'none';
            inputFecha.removeAttribute('name'); // Así no se envía al backend
            inputFecha.value = ''; // Limpia el valor si hubiera algo escrito
        }
    });
</script>

<?php include_once("../componentes/footer.php"); ?>