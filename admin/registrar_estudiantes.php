<?php
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");
include_once("../config/conexion.php");

// Consulta para obtener los países
try {
    $stmt = $pdo->prepare("SELECT id, nombre FROM paises ORDER BY nombre ASC");
    $stmt->execute();
    $paises = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>Swal.fire('Error', 'Error al cargar los países: " . $e->getMessage() . "', 'error');</script>";
    $paises = [];
}
?>

<main class="content" id="mainContentGuin">
    <div class="container mt-4">
        <div class="card shadow rounded-4">
            <div class="card-header bg-success text-white d-flex align-items-center">
                <i class="bi bi-person-lines-fill fs-4 me-2"></i>
                <h5 class="mb-0">Formulario de Registro de Estudiante</h5>
            </div>

            <div class="card-body">
                <form action="../php/guardar_estudiantes.php" method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre_completo" class="form-label fw-bold">Nombre Completo</label>
                            <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" required placeholder="Ej: María López">
                        </div>

                        <div class="col-md-6">
                            <label for="fecha_nacimiento" class="form-label fw-bold">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label for="pais" class="form-label fw-bold">País</label>
                            <select id="pais" name="pais" class="form-select" required>
                                <option value="" disabled selected>Selecciona tu país</option>
                                <?php foreach ($paises as $pais): ?>
                                    <option value="<?= htmlspecialchars($pais['id']) ?>">
                                        <?= htmlspecialchars($pais['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="foto" class="form-label fw-bold">Foto del Estudiante</label>
                            <input type="file" id="foto" name="foto" class="form-control" accept="image/*" required onchange="previewImage(event)">
                        </div>

                        <div class="col-md-12 text-center">
                            <label class="form-label fw-bold">Vista previa:</label><br>
                            <img id="foto_preview" src="#" alt="Vista previa" class="img-thumbnail rounded-4 shadow" style="max-width: 200px; display: none;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success me-2">
                            <i class="bi bi-check-circle-fill me-1"></i> Registrar
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

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- JS Vista previa imagen -->
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('foto_preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include_once("../componentes/footer.php"); ?>
