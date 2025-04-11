<?php

include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");
require_once '../config/conexion.php';
$stmt = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM estudiantes) AS total_estudiantes,
        (SELECT COUNT(*) FROM notas) AS total_pasaportes,
        (SELECT COUNT(*) FROM paises) AS total_notas
");
$totales = $stmt->fetch(PDO::FETCH_ASSOC);


// Traer los últimos 4 pasaportes
$queryPasaportes = $pdo->query("
    SELECT e.nombre_completo, p.archivo_url, p.fecha_subida, 'PASAPORTE' AS tipo
    FROM pasaportes p
    JOIN estudiantes e ON e.id = p.estudiante_id
    ORDER BY p.fecha_subida DESC
    LIMIT 4
");

// Traer los últimos 4 archivos de notas
$queryNotas = $pdo->query("
    SELECT e.nombre_completo, n.archivo_url, n.fecha_subida, 'NOTAS' AS tipo
    FROM notas n
    JOIN estudiantes e ON e.id = n.estudiante_id
    ORDER BY n.fecha_subida DESC
    LIMIT 4
");

// Unir los resultados
$archivos = array_merge($queryPasaportes->fetchAll(PDO::FETCH_ASSOC), $queryNotas->fetchAll(PDO::FETCH_ASSOC));
?>

?>

<main class="content" id="mainContent">


  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Resumen</h4>
    <button id="collapseSidebar" class="btn btn-outline-secondary d-none d-lg-inline"><i
        class="bi bi-layout-sidebar-inset"></i></button>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card shadow-sm border-start border-primary border-4">
        <div class="card-body">
          <h6 class="card-title">Total de Estudiantes</h6>
          <h3><i class="bi bi-people me-2"></i><?php echo $totales['total_estudiantes'];  ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-start border-success border-4">
        <div class="card-body">
          <h6 class="card-title">Total de Notas</h6>
          <h3><i class="bi bi-journal-text me-2"></i><?php echo $totales['total_pasaportes']; ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-start border-warning border-4">
        <div class="card-body">
          <h6 class="card-title">Total de Paises</h6>
          <h3><i class="bi bi-flag-fill me-2"></i><?php echo $totales['total_notas']; ?></h3>
        </div>
      </div>
    </div>
  </div>


  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header"><i class="bi bi-geo-alt"></i>Estudiantes en los Diferentes paises </div>
        <div class="card-body">
          <div id="regions_div" style="width: 90%; height: 100%;"></div>
        </div>
      </div>
    </div>


    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header"><i class="bi bi-table"></i>Últimos Archivos Subidos</div>
        <div class="card-body table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Estudiante</th>
                <th>Tipo de Archivo</th>
                <th>Fecha de subida</th>
                <th>Archivo</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($archivos as $index => $archivo): ?>
                <tr>
                  <td><?= $index + 1 ?></td>
                  <td><?= htmlspecialchars($archivo['nombre_completo']) ?></td>
                  <td>
                    <span class="badge bg-<?= $archivo['tipo'] === 'PASAPORTE' ? 'primary' : 'success' ?>">
                      <?= $archivo['tipo'] ?>
                    </span>
                  </td>
                  <td><?= date("d/m/Y H:i", strtotime($archivo['fecha_subida'])) ?></td>
                  <td>
                    <?php
                    $rutaBase = $archivo['tipo'] === 'PASAPORTE' ? 'php/upload/pasaportes/' : 'php/upload/notas/';
                    $archivoUrl = htmlspecialchars($archivo['archivo_url']);
                    ?>
                    <a href="<?= $rutaBase . $archivoUrl ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                      Ver archivo
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>



</main>


<?=
include_once("../componentes/footer.php");
?>