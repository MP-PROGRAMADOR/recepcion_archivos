<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

if (!isset($_SESSION['usuario_id'])) {
  header('Location: ../index.php');
  exit;
}



$stmt = $pdo->query("    SELECT 
        (SELECT COUNT(*) FROM estudiantes) AS total_estudiantes,
        (SELECT COUNT(*) FROM notas) AS total_pasaportes,
        (SELECT COUNT(*) FROM paises) AS total_notas
");
$totales = $stmt->fetch(PDO::FETCH_ASSOC);


// Traer los últimos 4 pasaportes
$queryPasaportes = $pdo->query("    SELECT e.nombre_completo, p.archivo_url, p.fecha_subida, 'PASAPORTE' AS tipo
    FROM pasaportes p
    JOIN estudiantes e ON e.id = p.estudiante_id
    ORDER BY p.fecha_subida DESC
    LIMIT 4
");

// Traer los últimos 4 archivos de notas
$queryNotas = $pdo->query("    SELECT e.nombre_completo, n.archivo_url, n.fecha_subida, 'NOTAS' AS tipo
    FROM notas n
    JOIN estudiantes e ON e.id = n.estudiante_id
    ORDER BY n.fecha_subida DESC
    LIMIT 4
");

// Unir los resultados
$archivos = array_merge($queryPasaportes->fetchAll(PDO::FETCH_ASSOC), $queryNotas->fetchAll(PDO::FETCH_ASSOC));
?>


<!-- Fondo canvas animado -->

<main class="content py-4 " id="mainContent">
<canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>


  <!-- Estadísticas -->
  <div class="row g-3 mb-4 mt-5">
    <div class="col-md-4">
      <div class="card shadow-lg border-start border-primary border-4 rounded-3">
        <div class="card-body d-flex align-items-center">
          <h6 class="card-title text-muted fw-semibold">Total de Estudiantes</h6>
          <h3 class="ms-auto text-primary">
            <i class="bi bi-people me-2"></i>
            <?php echo htmlspecialchars($totales['total_estudiantes']); ?>
          </h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-lg border-start border-success border-4 rounded-3">
        <div class="card-body d-flex align-items-center">
          <h6 class="card-title text-muted fw-semibold">Total de Notas</h6>
          <h3 class="ms-auto text-success">
            <i class="bi bi-journal-text me-2"></i>
            <?php echo htmlspecialchars($totales['total_pasaportes']); ?>
          </h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-lg border-start border-warning border-4 rounded-3">
        <div class="card-body d-flex align-items-center">
          <h6 class="card-title text-muted fw-semibold">Total de Países</h6>
          <h3 class="ms-auto text-warning">
            <i class="bi bi-flag-fill me-2"></i>
            <?php echo htmlspecialchars($totales['total_notas']); ?>
          </h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Gráficos y tabla -->
  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card shadow-lg rounded-3">
        <div class="card-header bg-dark text-white"><i class="bi bi-geo-alt"></i> Estudiantes en los Diferentes Países
        </div>
        <div class="card-body">
          <div id="regions_div" style="width: 90%; height: 100%;"></div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card shadow-lg rounded-3">
        <div class="card-header bg-dark text-white"><i class="bi bi-table"></i> Últimos Archivos Subidos</div>
        <div class="card-body table-responsive">
          <table class="table table-hover table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Estudiante</th>
                <th>Tipo de Archivo</th>
                <th>Fecha de Subida</th>
                <th>Archivo</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($archivos as $index => $archivo): ?>
                <tr>
                  <td><?= htmlspecialchars($index + 1) ?></td>
                  <td><?= htmlspecialchars($archivo['nombre_completo']) ?></td>
                  <td>
                    <span class="badge bg-<?= $archivo['tipo'] === 'PASAPORTE' ? 'primary' : 'success' ?>">
                      <?= htmlspecialchars($archivo['tipo']) ?>
                    </span>
                  </td>
                  <td><?= htmlspecialchars(date("d/m/Y H:i", strtotime($archivo['fecha_subida']))) ?></td>
                  <td>
                    <?php
                    $rutaBase = $archivo['tipo'] === 'PASAPORTE' ? 'php/upload/pasaportes/' : 'php/upload/notas/';
                    $archivoUrl = htmlspecialchars($archivo['archivo_url']);
                    ?>
                    <a href="<?= $rutaBase . $archivoUrl ?>" target="_blank"
                      class="btn btn-sm btn-outline-primary rounded-pill shadow-sm">
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