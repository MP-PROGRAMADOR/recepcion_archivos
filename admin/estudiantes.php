<?php
include_once("../componentes/header.php");
// Asegúrate de que esto esté al principio del archivo


// Consulta de estudiantes con JOIN a países
// Configuración de paginación
$por_pagina = 4;
$pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
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
        SELECT e.id, e.nombre_completo,e.anio_inicio_carrera,e.anio_fin_carrera,e.telefono, e.codigo_acceso,e.foto_perfil, e.fecha_nacimiento, e.creado_en, 
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
include_once("../componentes/sidebar.php");
?>

<main class="content" id="mainContent">
<canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
    <div class="container-fluid">


        <?php if (isset($_SESSION['mensaje'])): ?>
            <div id="alerta-sesion" class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-dismissible fade show mt-3" role="alert">
                <?= $_SESSION['mensaje'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>

            <script>
                // Espera 6 segundos y luego cierra la alerta automáticamente
                setTimeout(function() {
                    var alerta = document.getElementById('alerta-sesion');
                    if (alerta) {
                        alerta.classList.remove('show');
                        alerta.classList.add('fade');
                        setTimeout(function() {
                            alerta.remove();
                        }, 500); // Dar tiempo a la animación de Bootstrap
                    }
                }, 6000);
            </script>

            <?php
            unset($_SESSION['mensaje']);
            unset($_SESSION['tipo_mensaje']);
            ?>
        <?php endif; ?>






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
                    <div class="col-md-4">
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
                                <th>Pais</th>
                                <th>Fecha De Inicio</th>
                                <th>Fecha De Fin</th>
                                <th>Telefono</th>
                                <th>Foto</th> <!-- Columna Foto ahora después del Teléfono -->
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
                                        <td><?= htmlspecialchars($est['pais']) ?></td>
                                        <td><?= htmlspecialchars($est['anio_inicio_carrera']) ?></td>
                                        <td><?= htmlspecialchars($est['anio_fin_carrera']) ?></td>
                                        <td><?= htmlspecialchars($est['telefono']) ?></td>

                                        <!-- Foto del perfil -->
                                        <td>
                                            <?php if (!empty($est['foto_perfil']) && file_exists('../php/upload/perfil/' . $est['foto_perfil'])): ?>
                                                <img src="../php/upload/perfil/<?= htmlspecialchars($est['foto_perfil']) ?>" alt="Foto de Perfil"
                                                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                            <?php else: ?>
                                                <span class="text-muted">NINGÚN PERFIL</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <a href="editar_estudiantes.php?id=<?= htmlspecialchars($est['id']) ?>" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <a href="detalles_estudiantes.php?id=<?= htmlspecialchars($est['id']) ?>" class="btn btn-success btn-sm" title="Detalles">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No hay estudiantes registrados</td>
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
    $(document).ready(function() {
        $("#busqueda").on("keyup", function() {
            let valor = $(this).val().toLowerCase();
            $("#contenidoTabla tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().includes(valor));
            });
        });
    });
</script>

<?php include_once("../componentes/footer.php"); ?>