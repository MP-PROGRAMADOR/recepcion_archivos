 
 
<?php

include_once("../componentes/header.php");

// Generar automáticamente años académicos desde 2000 hasta el año actual
$anio_inicio = 2000;
$anio_actual = (int)date('Y'); // Solo hasta el año actual

try {
    // Obtener los años ya existentes y los años con estudiantes registrados
    $consultaExistentes = $pdo->query("SELECT nombre FROM anios_academicos");
    $existentes = $consultaExistentes->fetchAll(PDO::FETCH_COLUMN);
    
    // Obtener los años académicos con estudiantes registrados
    $consultaAniosConEstudiantes = $pdo->query("
    SELECT DISTINCT anio_academico_id 
    FROM notas WHERE anio_academico_id IS NOT NULL
    ");
    $aniosConEstudiantes = $consultaAniosConEstudiantes->fetchAll(PDO::FETCH_COLUMN);

    // Insertar los años que no existen aún y solo si tienen estudiantes registrados
    for ($anio = $anio_inicio; $anio <= $anio_actual; $anio++) {
        $nombreAnio = "$anio - " . ($anio + 1);
        
        // Verifica si el año ya existe en la base de datos
        if (!in_array($nombreAnio, $existentes)) {
            // Verifica si ya se tiene un año académico con estudiantes registrados
            $insert = $pdo->prepare("INSERT INTO anios_academicos (nombre) VALUES (:nombre)");
            $insert->execute([':nombre' => $nombreAnio]);
            $existentes[] = $nombreAnio;  // Añadir al array de existentes para no volver a insertar
           /*  if (in_array($anio, $aniosConEstudiantes)) {
            } */
        }
    }
} catch (PDOException $e) {
    die("Error al generar años académicos: " . $e->getMessage());
}

// Paginación
$por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina_actual > 1) ? ($pagina_actual * $por_pagina) - $por_pagina : 0;

// Total para paginación
$total_stmt = $pdo->query("SELECT COUNT(*) AS total FROM anios_academicos WHERE id IN (SELECT DISTINCT anio_academico_id FROM notas)");
$total_filas = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_filas / $por_pagina);

// Obtener años académicos con número de estudiantes registrados
try {
    $stmt = $pdo->prepare("
    SELECT 
    a.id AS anio_id,
    a.nombre AS anio_nombre,
    COUNT(DISTINCT n.estudiante_id) AS total_estudiantes
    FROM anios_academicos a
    LEFT JOIN notas n ON n.anio_academico_id = a.id
    WHERE n.anio_academico_id IS NOT NULL
    GROUP BY a.id, a.nombre
    ORDER BY a.nombre DESC
    LIMIT :inicio, :por_pagina
    ");
    $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
    $stmt->execute();
    $aniosAcademicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener años académicos: " . $e->getMessage());
}



include_once("../componentes/sidebar.php");
?>

<main class="content" id="mainContent">
<canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="bi bi-calendar-check me-2"></i>Listado de Años Académicos con Estudiantes</h3>
        </div>

        <div class="card shadow rounded-4">
            <div class="card-body">
               

                <div class="table-responsive">
                   <table class="table table-striped table-hover align-middle text-center" id="tablaRecepcion">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="bi bi-hash me-1"></i>ID</th>
                                <th><i class="bi bi-calendar-event me-1"></i>Año Académico</th>
                                <th><i class="bi bi-person-fill me-1"></i>Estudiantes Inscritos</th>
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
                                    <td colspan="3" class="text-center text-muted">No hay años académicos registrados con estudiantes</td>
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
