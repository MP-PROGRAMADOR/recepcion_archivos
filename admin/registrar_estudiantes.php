<?php
session_start(); // Necesario para acceder a $_SESSION
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
        <!-- INICIO DE LA ALERTA DE ERRORRES -->
        <?php


        if (isset($_SESSION['errores']) && is_array($_SESSION['errores'])):
            ?>
            <div id="alerta-errores"
                class="alert alert-danger alert-dismissible shadow-sm fade show d-flex align-items-start gap-2 p-3 mt-3 border border-danger-subtle rounded-3"
                role="alert" style="animation: fadeIn 0.5s ease-in-out;">
                <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0 mt-1"></i>
                <div>
                    <strong>Se detectaron errores:</strong>
                    <ul class="mb-0 mt-1">
                        <?php foreach ($_SESSION['errores'] as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="button" class="btn-close ms-auto mt-1" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>

            <script>
                // Ocultar automáticamente luego de 6 segundos
                setTimeout(() => {
                    const alerta = document.getElementById('alerta-errores');
                    if (alerta) {
                        alerta.classList.remove('show');
                        alerta.classList.add('fade');
                        setTimeout(() => alerta.remove(), 500); // Lo remueve del DOM
                    }
                }, 9000);
            </script>

            <style>
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(-10px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
            </style>
            <?php
            unset($_SESSION['errores']); // Limpiar errores de la sesión
        endif;
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
                        <div class="col-md-6">
                            <label for="nombre_completo" class="form-label fw-bold">Nombre Completo</label>
                            <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" required
                                placeholder="Ej: María López">
                        </div>

                        <div class="col-md-6">
                            <label for="fecha_nacimiento" class="form-label fw-bold">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control"
                                required>
                        </div>


                        <!-- Selección de País -->
                        <div class="col-md-6">
                            <div class="row">
                                <!-- Campo de búsqueda fuera del select -->
                                <div class="col-md-6">
                                    <label for="buscar_pais" class="form-label fw-bold">Buscar País</label>
                                    <input type="text" id="buscar_pais" class="form-control" placeholder="Buscar país"
                                        onkeyup="filterCountries()">
                                </div>

                                <!-- Selección de País -->
                                <div class="col-md-6">
                                    <label for="pais" class="form-label fw-bold">País</label>
                                    <select id="pais" name="pais" class="form-select" required>
                                        <option value="" disabled selected>Selecciona tu país</option>
                                        <?php foreach ($paises as $pais): ?>
                                            <option value="<?= htmlspecialchars($pais['id']) ?>" class="country-item">
                                                <?= htmlspecialchars($pais['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>


                        </div>



                        <div class="col-md-6">
                            <label for="foto" class="form-label fw-bold">Foto del Estudiante</label>
                            <input type="file" id="foto" name="foto" class="form-control" accept="image/*" required
                                onchange="previewImage(event)">
                        </div>

                        <div class="col-md-12 text-center">
                            <label class="form-label fw-bold">Vista previa:</label><br>
                            <img id="foto_preview" src="#" alt="Vista previa" class="img-thumbnail rounded-4 shadow"
                                style="max-width: 200px; display: none;">
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



    

    // Función para filtrar los países en el select
    function filterCountries() {
        const filter = document.getElementById('buscar_pais').value.toUpperCase(); // Obtener el valor del input
        const select = document.getElementById('pais');
        const options = select.getElementsByClassName('country-item'); // Obtener las opciones del select

        // Iterar sobre las opciones y mostrar/ocultar según el filtro
        for (let i = 0; i < options.length; i++) {
            const option = options[i];
            const text = option.textContent || option.innerText;

            // Mostrar la opción si el texto contiene el filtro
            if (text.toUpperCase().indexOf(filter) > -1) {
                option.style.display = "";
            } else {
                option.style.display = "none";
            }
        }
    }

</script>

<?php include_once("../componentes/footer.php"); ?>