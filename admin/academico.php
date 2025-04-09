<?php
session_start(); // Iniciar sesión
require_once '../config/conexion.php';

try {
    $stmt = $pdo->prepare("SELECT id, nombre FROM anios_academicos ORDER BY id DESC");
    $stmt->execute();
    $anios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los años académicos: " . $e->getMessage());
}

include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");
?>

<main class="content" id="mainContent">
    <div class="container-fluid">
         <!-- INICIO DE LA ALERTA -->
         <?php


if (isset($_SESSION['exito']) && !empty($_SESSION['exito'])):
    ?>
    <div id="alerta-exito"
        class="alert alert-success alert-dismissible shadow-sm fade show d-flex align-items-start gap-2 p-3 mt-3 border border-success-subtle rounded-3"
        role="alert" style="animation: fadeIn 0.5s ease-in-out;">
        <i class="bi bi-check-circle-fill fs-4 flex-shrink-0 mt-1"></i>
        <div>
            <strong>¡Éxito!</strong>
            <p class="mb-0 mt-1"><?= htmlspecialchars($_SESSION['exito']) ?></p>
        </div>
        <button type="button" class="btn-close ms-auto mt-1" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>

    <script>
        // Ocultar automáticamente luego de 6 segundos
        setTimeout(() => {
            const alerta = document.getElementById('alerta-exito');
            if (alerta) {
                alerta.classList.remove('show');
                alerta.classList.add('fade');
                setTimeout(() => alerta.remove(), 500); // Lo remueve del DOM
            }
        }, 6000);
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
    unset($_SESSION['exito']); // Limpiar mensaje de éxito de la sesión
endif;
?>

<!-- FIN DE LA ALERTA -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="bi bi-calendar3 me-2"></i>Listado de Años Académicos</h3>
            <a href="registrar_academico.php" class="btn btn-primary rounded-3">
                <i class="bi bi-calendar-plus-fill me-1"></i> Nuevo Año Académico
            </a>
        </div>

        <div class="card shadow rounded-4">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="busqueda" class="form-label fw-bold">Buscar Año Académico</label>
                        <input type="text" class="form-control" id="busqueda" placeholder="Buscar por ID o nombre...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-center" id="tablaAcademico">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="contenidoTabla">
                            <?php if (!empty($anios)): ?>
                                <?php foreach ($anios as $anio): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($anio['id']) ?></td>
                                        <td><?= htmlspecialchars($anio['nombre']) ?></td>
                                        <td>
                                            <a href="editar_academico.php?id=<?= $anio['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <a href="eliminar_academico.php?id=<?= $anio['id'] ?>" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este año académico?');">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No hay años académicos registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</main>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- jQuery para el buscador -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $("#busqueda").on("keyup", function () {
            let valor = $(this).val().toLowerCase();
            $("#contenidoTabla tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().includes(valor));
            });
        });
    });
</script>

<?php include_once("../componentes/footer.php"); ?>
