<?php
include_once("../componentes/header.php");


// Consulta para obtener los datos requeridos de estudiantes, idiomas, universidades, ciudades y países
try {
    $stmt = $pdo->prepare("        SELECT 
            e.nombre_completo AS estudiante, 
            i.nombre AS idioma, 
            i.meses_duracion AS duracion_idioma, 
            u.nombre AS universidad, 
            c.nombre AS ciudad, 
            p.nombre AS pais
        FROM estudiantes e
        INNER JOIN idiomas i ON e.idioma_id = i.id
        INNER JOIN universidades u ON e.universidad_id = u.id
        INNER JOIN ciudades c ON e.ciudad_id = c.id
        INNER JOIN paises p ON e.pais_id = p.id
        ORDER BY e.nombre_completo ASC
    ");
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="bi bi-mortarboard-fill me-2"></i>Listado de idiomas</h3>
            <a href="registrar_idioma.php" class="btn btn-primary rounded-3">
                <i class="bi bi-person-plus-fill me-1"></i> Nuevo Idioma
            </a>
        </div>

        <div class="card shadow rounded-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Estudiante</th>
                                <th>Idioma</th>
                                <th>Duración del Idioma (Meses)</th>
                                <th>Universidad</th>
                                <th>Ciudad</th>
                                <th>País</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($estudiantes)): ?>
                                <?php foreach ($estudiantes as $est): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($est['estudiante']) ?></td>
                                        <td><?= htmlspecialchars($est['idioma']) ?></td>
                                        <td><?= htmlspecialchars($est['duracion_idioma']) ?> meses</td>
                                        <td><?= htmlspecialchars($est['universidad']) ?></td>
                                        <td><?= htmlspecialchars($est['ciudad']) ?></td>
                                        <td><?= htmlspecialchars($est['pais']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay estudiantes registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once("../componentes/footer.php"); ?>
