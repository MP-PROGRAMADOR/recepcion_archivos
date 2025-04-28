<?php

include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

// Consulta para obtener los países
try {
    $stmt = $pdo->prepare("SELECT id, nombre FROM paises ORDER BY nombre ASC");
    $stmt->execute();
    $paises = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>Swal.fire('Error', 'Error al cargar los países: " . $e->getMessage() . "', 'error');</script>";
    $paises = [];
}
// Consulta para obtener los países
try {
    $stmt = $pdo->prepare("SELECT id, nombre FROM paises ORDER BY nombre ASC");
    $stmt->execute();
    $paises = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>Swal.fire('Error', 'Error al cargar los países: " . $e->getMessage() . "', 'error');</script>";
    $paises = [];
}

// Consulta para obtener las ciudades
try {
    $stmt = $pdo->prepare("SELECT id, nombre FROM ciudades ORDER BY nombre ASC");
    $stmt->execute();
    $ciudades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>Swal.fire('Error', 'Error al cargar las ciudades: " . $e->getMessage() . "', 'error');</script>";
    $ciudades = [];
}

// Consulta para obtener las universidades
try {
    $stmt = $pdo->prepare("SELECT id, nombre FROM universidades ORDER BY nombre ASC");
    $stmt->execute();
    $universidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>Swal.fire('Error', 'Error al cargar las universidades: " . $e->getMessage() . "', 'error');</script>";
    $universidades = [];
}
?>



<main class="content" id="mainContentGuin">
    <div class="container mt-4">
        <!-- INICIO DE LA ALERTA DE ERRORRES -->
        <?php

include_once("../componentes/alerta.php");

        ?>


        <!-- FIN DE LA ALERTA -->
        <div class="card shadow rounded-4">
            <div class="card-header bg-success text-white d-flex align-items-center">
                <i class="bi bi-person-lines-fill fs-4 me-2"></i>
                <h5 class="mb-0">Formulario de Registro de Estudiante</h5>
            </div>

            <div class="card-body">
                <form action="../php/guardar_estudiantes.php" method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6 mb-2">
                            <label for="nombre_completo" class="form-label fw-bold">Nombre Completo</label>
                            <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" required
                                placeholder="Ej: María López">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="fecha_nacimiento" class="form-label fw-bold">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control"
                                required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="fecha_inicio_carrera" class="form-label fw-bold">Fecha de Inicio de
                                Carrera</label>
                            <input type="date" id="fecha_inicio_carrera" name="fecha_inicio_carrera"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="fecha_fin_carrera" class="form-label fw-bold">Fecha de fin de Carrera</label>
                            <input type="date" id="fecha_fin_carrera" name="fecha_fin_carrera" class="form-control"
                                required>
                        </div>



                        <!-- Selección de País -->
                        <div class="col-md-6 mb-2">
                            <label for="pais" class="form-label fw-bold">País de estudios</label>
                            <select id="pais" name="pais" class="form-select" required>
                                <option value="" disabled selected>Selecciona tu país</option>
                                <?php foreach ($paises as $pais): ?>
                                    <option value="<?= htmlspecialchars($pais['id']) ?>" class="country-item">
                                        <?= htmlspecialchars($pais['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>


                        </div>
                        <!-- Selección de ciudad -->
                        <div class="col-md-6 mb-2">
                            <label for="pais" class="form-label fw-bold">ciudad de estudios</label>
                            <select id="ciudad" name="ciudad" class="form-select" required>
                                <option value="" disabled selected>Selecciona tu ciudad</option>
                                <?php foreach ($ciudades as $ciudad): ?>
                                    <option value="<?= htmlspecialchars($ciudad['id']) ?>" class="country-item">
                                        <?= htmlspecialchars($ciudad['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Selección de universidad -->
                        <div class="col-md-6 mb-2">
                            <label for="universidad" class="form-label fw-bold">universidad</label>
                            <select id="universidad" name="universidad" class="form-select" required>
                                <option value="" disabled selected>Selecciona tu universidad</option>
                                <?php foreach ($universidades as $universidad): ?>
                                    <option value="<?= htmlspecialchars($universidad['id']) ?>" class="country-item">
                                        <?= htmlspecialchars($universidad['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>


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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- JS Vista previa imagen -->
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

<?php include_once("../componentes/footer.php"); ?>