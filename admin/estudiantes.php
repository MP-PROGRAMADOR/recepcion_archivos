<?php
session_start(); // Asegúrate de que esto esté al principio del archivo
// Conexión
require_once '../config/conexion.php';

// Consulta de estudiantes con JOIN a países
// Configuración de paginación
$por_pagina = 4;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina_actual > 1) ? ($pagina_actual * $por_pagina) - $por_pagina : 0;

// Contar total de estudiantes
try {
    $total_stmt = $pdo->query("SELECT COUNT(*) as total FROM estudiantes");
    $total_filas = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_paginas = ceil($total_filas / $por_pagina);
} catch (PDOException $e) {
    die("Error al contar los estudiantes: " . $e->getMessage());
}

// Obtener estudiantes con límite y offset (incluyendo país)
try {
    $stmt = $pdo->prepare("
        SELECT e.id, e.nombre_completo, e.codigo_acceso, e.fecha_nacimiento, e.creado_en, 
               p.nombre AS pais, e.ruta_foto
        FROM estudiantes e
        INNER JOIN paises p ON e.pais_id = p.id
        ORDER BY e.creado_en DESC
        LIMIT :inicio, :por_pagina
    ");
    $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
    $stmt->execute();
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los estudiantes: " . $e->getMessage());
}



// Layout común
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
            <h3><i class="bi bi-mortarboard-fill me-2"></i>Listado de Estudiantes</h3>
            <a href="registrar_estudiantes.php" class="btn btn-primary rounded-3">
                <i class="bi bi-person-plus-fill me-1"></i> Nuevo Estudiante
            </a>
        </div>

        <div class="card shadow rounded-4">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="busqueda" class="form-label fw-bold">Buscar estudiante</label>
                        <input type="text" class="form-control" id="busqueda" placeholder="Buscar por nombre o país...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-center" id="tablaEstudiantes">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Código de Acceso</th>
                                <th>Fecha de Nacimiento</th>
                                <th>Registro</th>
                                <th>País</th>
                                <th>Foto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="contenidoTabla">
                            <?php if (!empty($estudiantes)): ?>
                                <?php foreach ($estudiantes as $est): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($est['id']) ?></td>
                                        <td><?= htmlspecialchars($est['nombre_completo']) ?></td>
                                        <td><?= htmlspecialchars($est['codigo_acceso']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($est['fecha_nacimiento'])) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($est['creado_en'])) ?></td>
                                        <td><?= htmlspecialchars($est['pais']) ?></td>
                                        <td>
                                            <?php
                                            $foto = $est['ruta_foto'];
                                            $rutaRelativa = '../php/upload/' . basename($foto);
                                            $rutaServidor = __DIR__ . '/../php/upload/' . basename($foto);
                                            ?>

                                            <?php if (!empty($foto) && file_exists($rutaServidor)): ?>
                                                <img src="<?= $rutaRelativa ?>" class="rounded-circle shadow"
                                                    alt="Foto de <?= htmlspecialchars($est['nombre_completo']) ?>" width="50"
                                                    height="50">
                                            <?php else: ?>
                                                <span class="text-muted">Sin foto</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <p><strong>DEBUG ID:</strong> <?= $_GET['id'] ?? 'No ID' ?></p>

                                            <a href="editar_estudiantes.php?id=<?= htmlspecialchars($est['id']) ?>"
                                                class="btn btn-warning btn-sm" title="Editar">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <a href="detalles_estudiantes.php?id=<?= htmlspecialchars($est['id']) ?>"
                                                class="btn btn-success btn-sm" title="Detalles">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No hay estudiantes registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
                <!-- PAGINACION -->
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

                <!-- FIN DE LA PAGINACION -->

            </div>
        </div>
    </div>
</main>

<!-- Bootstrap Icons & jQuery -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Buscador en tiempo real -->
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