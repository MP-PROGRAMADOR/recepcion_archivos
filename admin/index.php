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
          <h6 class="card-title">Total de  Notas</h6>
          <h3><i class="bi bi-journal-text me-2"></i><?php echo $totales['total_pasaportes'];?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-start border-warning border-4">
        <div class="card-body">
          <h6 class="card-title">Total de  Paises</h6>
          <h3><i class="bi bi-flag-fill me-2"></i><?php echo $totales['total_notas'];?></h3>
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
        <div class="card-header"><i class="bi bi-table"></i> Últimos Exámenes</div>
        <div class="card-body table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Estudiante</th>
                <th>Fecha</th>
                <th>Resultado</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Juan Pérez</td>
                <td>2025-04-06</td>
                <td>Aprobado</td>
              </tr>
              <tr>
                <td>2</td>
                <td>María López</td>
                <td>2025-04-05</td>
                <td>Reprobado</td>
              </tr>
              <tr>
                <td>3</td>
                <td>Carlos Ruiz</td>
                <td>2025-04-04</td>
                <td>Aprobado</td>
              </tr>
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