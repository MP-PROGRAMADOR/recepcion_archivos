<?php

include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

require_once '../config/conexion.php';

// Configuración de paginación
$por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina_actual > 1) ? ($pagina_actual * $por_pagina) - $por_pagina : 0;

// Contar el total de años académicos con estudiantes inscritos
$total_stmt = $pdo->query("SELECT COUNT(DISTINCT n.anio_academico_id) as total 
                           FROM notas n");
$total_filas = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_filas / $por_pagina);

// Obtener años académicos con estudiantes y el total de estudiantes
try {
    $stmt = $pdo->prepare("
        SELECT 
            a.id AS anio_id, 
            a.nombre AS anio_nombre, 
            COUNT(DISTINCT n.estudiante_id) AS total_estudiantes 
        FROM 
            anios_academicos a
        LEFT JOIN 
            notas n ON n.anio_academico_id = a.id
        GROUP BY 
            a.id, a.nombre
        HAVING 
            total_estudiantes > 0
        ORDER BY 
            a.nombre DESC
        LIMIT :inicio, :por_pagina
    ");
    $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
    $stmt->execute();
    $aniosAcademicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los años académicos: " . $e->getMessage());
}
?>

<main class="content" id="mainContent">
    <div class="container mt-4">
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="bi bi-calendar-check me-2"></i>Listado de Años Académicos con Estudiantes Inscritos</h3>
        </div>

        <div class="card shadow rounded-4">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="busqueda" class="form-label fw-bold">
                            <i class="bi bi-search me-1"></i>Buscar Año Académico
                        </label>
                        <input type="text" class="form-control" id="busqueda" placeholder="Buscar por ID o nombre...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-center" id="tablaAnio">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="bi bi-hash me-1"></i>ID</th>
                                <th><i class="bi bi-calendar-check me-1"></i>Año Académico</th>
                                <th><i class="bi bi-person-fill me-1"></i>Total de Estudiantes</th>
                            </tr>
                        </thead>
                        <tbody id="contenidoTabla">
                            <?php if (!empty($aniosAcademicos)): ?>
                                <?php foreach ($aniosAcademicos as $anio): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($anio['anio_id']) ?></td>
                                        <td><?= htmlspecialchars($anio['anio_nombre']) ?></td>
                                        <td><?= htmlspecialchars($anio['total_estudiantes']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No hay años académicos con estudiantes registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <?php if ($total_paginas > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php if ($pagina_actual > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pagina=<?= $pagina_actual - 1 ?>">&laquo;</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <li class="page-item <?= $i == $pagina_actual ? 'active' : '' ?>">
                                    <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($pagina_actual < $total_paginas): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pagina=<?= $pagina_actual + 1 ?>">&raquo;</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<!-- Bootstrap Icons y jQuery -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Buscador funcional -->
<script>
    $(document).ready(function() {
        $('#busqueda').on('keyup', function() {
            let valor = $(this).val().toLowerCase();
            $('#contenidoTabla tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().includes(valor));
            });
        });
    });
</script>

<?php include_once("../componentes/footer.php"); ?>
