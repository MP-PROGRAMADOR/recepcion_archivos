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
                <form action="../php/guardar_estudiantes.php" method="POST" enctype="multipart/form-data"
                    class="needs-validation" novalidate>
                    <div class="row g-3">

                        <!-- Nombre Completo -->
                        <div class="col-md-6 mb-2">
                            <label for="nombre_completo" class="form-label fw-bold">
                                Nombre Completo
                            </label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                <input type="text" id="nombre_completo" name="nombre_completo" class="form-control"
                                    required placeholder="Ej: María López">
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, ingresa tu nombre completo.</div>
                            </div>
                        </div>

                        <!-- Fecha de Nacimiento -->
                        <div class="col-md-6 mb-2">
                            <label for="fecha_nacimiento" class="form-label fw-bold">
                                Fecha de Nacimiento
                            </label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-calendar-date-fill"></i></span>
                                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control"
                                    required>
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, selecciona tu fecha de nacimiento.</div>
                            </div>
                        </div>

                        <!-- Fecha de Inicio de Carrera -->
                        <div class="col-md-6 mb-2">
                            <label for="fecha_inicio_carrera" class="form-label fw-bold">
                                Fecha de Inicio de Carrera
                            </label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-calendar-plus-fill"></i></span>
                                <input type="date" id="fecha_inicio_carrera" name="fecha_inicio_carrera"
                                    class="form-control" required>
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, selecciona la fecha de inicio de carrera.</div>
                            </div>
                        </div>

                        <!-- Fecha de Fin de Carrera -->
                        <div class="col-md-6 mb-2">
                            <label for="fecha_fin_carrera" class="form-label fw-bold">
                                Fecha de Fin de Carrera
                            </label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-calendar-check-fill"></i></span>
                                <input type="date" id="fecha_fin_carrera" name="fecha_fin_carrera" class="form-control"
                                    required>
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, selecciona la fecha de fin de carrera.</div>
                            </div>
                        </div>

                        <!-- País de estudios -->
                        <div class="col-md-6 mb-2">
                            <label for="pais" class="form-label fw-bold">
                                País de Estudios
                            </label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                <select id="pais" name="pais" class="form-select" required>
                                    <option value="" disabled selected>Selecciona tu país</option>
                                    <?php foreach ($paises as $pais): ?>
                                        <option value="<?= htmlspecialchars($pais['id']) ?>">
                                            <?= htmlspecialchars($pais['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, selecciona un país.</div>
                            </div>
                        </div>

                        <!-- Ciudad de estudios -->
                        <div class="col-md-6 mb-2">
                            <label for="ciudad" class="form-label fw-bold">
                                Ciudad de Estudios
                            </label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-geo-fill"></i></span>
                                <select id="ciudad" name="ciudad" class="form-select" required>
                                    <option value="" disabled selected>Selecciona tu ciudad</option>
                                    <?php foreach ($ciudades as $ciudad): ?>
                                        <option value="<?= htmlspecialchars($ciudad['id']) ?>">
                                            <?= htmlspecialchars($ciudad['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, selecciona una ciudad.</div>
                            </div>
                        </div>

                        <!-- Universidad -->
                        <div class="col-md-6 mb-2">
                            <label for="universidad" class="form-label fw-bold">
                                Universidad
                            </label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <select id="universidad" name="universidad" class="form-select" required>
                                    <option value="" disabled selected>Selecciona tu universidad</option>
                                    <?php foreach ($universidades as $universidad): ?>
                                        <option value="<?= htmlspecialchars($universidad['id']) ?>">
                                            <?= htmlspecialchars($universidad['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, selecciona una universidad.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="d-flex justify-content-between mb-4 p-4">
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

<script>
// Validación Bootstrap 5
(() => {
  'use strict'

  const forms = document.querySelectorAll('.needs-validation')

  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>


<?php include_once("../componentes/footer.php"); ?>