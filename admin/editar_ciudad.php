<?php
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ciudades.php?error=ID inválido");
    exit;
}

$id = (int) $_GET['id'];

// Obtener cuenta actual
$stmt = $pdo->prepare("SELECT * FROM ciudades WHERE id = ?");
$stmt->execute([$id]);
$cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cuenta) {
    header("Location: cuentas.php?error=Cuenta no encontrada");
    exit;
}


?>

<main class="content" id="mainContent">
    <div class="container mt-5">

        <div class="card shadow rounded-4">
            <div class="card-header bg-warning text-white d-flex align-items-center">
                <i class="bi bi-geo-alt-fill fs-4 me-2"></i>
                <h5 class="mb-0">Editar la Ciudad</h5>

            </div>
            <div class="card-body">
                <form action="../php/actualizar_ciudades.php" method="POST" novalidate>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($cuenta['id']) ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre_ciudad" class="form-label fw-bold">Nombre de la Ciudad</label>
                            <input type="text" id="nombre_ciudad" name="nombre" class="form-control"
                                placeholder="Ej: Buenos Aires" required
                                value="<?= htmlspecialchars($cuenta['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="pais_id" class="form-label fw-bold">País</label>
                            <select name="pais_id" class="form-select" required>
                                <option value="">Seleccionar País</option>
                                <?php
                try {
                    include_once('../config/conexion.php');
                    $query = "SELECT id, nombre FROM paises";
                    $stmt = $pdo->query($query);

                    while ($pais = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $selected = ($pais['id'] == $cuenta['pais_id']) ? "selected" : "";
                        echo "<option value='" . htmlspecialchars($pais['id'], ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($pais['nombre'], ENT_QUOTES, 'UTF-8') . "</option>";
                    }
                } catch (PDOException $e) {
                    echo "<option value=''>Error al cargar países</option>";
                }
                ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-warning me-2">
                            <i class="bi bi-save-fill me-1"></i> Actualizar
                        </button>
                        <a href="ciudades.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle-fill me-1"></i> Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>

    </div>
</main>



<?php include_once("../componentes/footer.php"); ?>