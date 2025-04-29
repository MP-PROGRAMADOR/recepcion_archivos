<?php

include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

// Obtener ID
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['errores'] = ['ID de estudiante inválido.'];
    header("Location: estudiantes.php");
    exit;
}

// Consultar datos del estudiante
$stmt = $pdo->prepare("SELECT * FROM estudiantes WHERE id = ?");
$stmt->execute([$id]);
$estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$estudiante) {
    die("Estudiante no encontrado.");
}

// Consultar países
$paises = $pdo->query("SELECT id, nombre FROM paises ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

// Consultar ciudades del país del estudiante
$ciudades = $pdo->prepare("SELECT id, nombre FROM ciudades WHERE pais_id = ?");
$ciudades->execute([$estudiante['pais_id']]);
$ciudades = $ciudades->fetchAll(PDO::FETCH_ASSOC);

// Consultar universidades de la ciudad del estudiante
$universidades = $pdo->prepare("SELECT id, nombre FROM universidades WHERE ciudad_id = ?");
$universidades->execute([$estudiante['ciudad_id']]);
$universidades = $universidades->fetchAll(PDO::FETCH_ASSOC);
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

        <!-- Card -->
        <div class="card shadow rounded-4">
            <div class="card-header bg-warning text-dark d-flex align-items-center">
                <i class="bi bi-pencil-square fs-4 me-2"></i>
                <h5 class="mb-0">Editar Estudiante</h5>
            </div>

            <div class="card-body">
                <form action="../php/actualizar_estudiante.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <input type="hidden" name="id" value="<?= $estudiante['id'] ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre_completo" class="form-label">Nombre Completo</label>
                            <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" value="<?= htmlspecialchars($estudiante['nombre_completo']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="<?= $estudiante['fecha_nacimiento'] ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="anio_inicio_carrera" class="form-label">Año de Inicio</label>
                            <input type="number" id="anio_inicio_carrera" name="anio_inicio_carrera" class="form-control" value="<?= $estudiante['anio_inicio_carrera'] ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="anio_fin_carrera" class="form-label">Año de Fin</label>
                            <input type="number" id="anio_fin_carrera" name="anio_fin_carrera" class="form-control" value="<?= $estudiante['anio_fin_carrera'] ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="pais" class="form-label">País</label>
                            <select id="pais" name="pais" class="form-select" required>
                                <option value="" disabled>Selecciona un país</option>
                                <?php foreach ($paises as $pais): ?>
                                    <option value="<?= $pais['id'] ?>" <?= $pais['id'] == $estudiante['pais_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($pais['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <select id="ciudad" name="ciudad" class="form-select" required>
                                <?php foreach ($ciudades as $ciudad): ?>
                                    <option value="<?= $ciudad['id'] ?>" <?= $ciudad['id'] == $estudiante['ciudad_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ciudad['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="universidad" class="form-label">Universidad</label>
                            <select id="universidad" name="universidad" class="form-select" required>
                                <?php foreach ($universidades as $uni): ?>
                                    <option value="<?= $uni['id'] ?>" <?= $uni['id'] == $estudiante['universidad_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($uni['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-4 p-4">
                        <!-- Botón Actualizar -->
                        <button type="submit" class="btn btn-warning me-auto">
                            <i class="bi bi-pencil-square me-1"></i> Actualizar
                        </button>

                        <!-- Botón Cancelar -->
                        <a href="estudiantes.php" class="btn btn-secondary ms-auto">
                            <i class="bi bi-x-circle-fill me-1"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- Bootstrap Icons + SweetAlert -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Preview JS -->
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#pais").on("change", function() {
            const paisId = $(this).val();

            if (paisId) {
                $.get("../php/obtener_ciudades.php", {
                    pais_id: paisId
                }, function(data) {
                    $("#ciudad").html(data);
                    $("#universidad").html('<option value="">Selecciona una ciudad primero</option>');
                }).fail(function() {
                    alert("Error al cargar ciudades");
                });
            }
        });

        $("#ciudad").on("change", function() {
            const ciudadId = $(this).val();

            if (ciudadId) {
                $.get("../php/obtener_universidades.php", {
                    ciudad_id: ciudadId
                }, function(data) {
                    $("#universidad").html(data);
                }).fail(function() {
                    alert("Error al cargar universidades");
                });
            }
        });
    });
</script>

<!-- Animación para alerta -->
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

<?php include_once("../componentes/footer.php"); ?>