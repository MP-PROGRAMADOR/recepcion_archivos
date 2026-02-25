<?php
// No debe haber NADA antes de <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/conexion.php';

// Tiempo de inactividad en segundos (5 minutos)
$tiempo_inactividad = 7 * 60; // 300 segundos

// Comprobar si ya existe la última actividad
if (isset($_SESSION['ultima_actividad'])) {
    $tiempo_transcurrido = time() - $_SESSION['ultima_actividad'];
    if ($tiempo_transcurrido > $tiempo_inactividad) {
        // Registrar log de cierre por inactividad antes de destruir sesión
        if (isset($_SESSION['usuario_id'])) {
            $stmt_log = $pdo->prepare("
                INSERT INTO log_actividades 
                (usuario_id, accion, modulo, descripcion, ip_address, navegador, resultado)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt_log->execute([
                $_SESSION['usuario_id'],
                'LOGOUT',
                'Sistema',
                'Cierre de sesión por inactividad',
                $_SERVER['REMOTE_ADDR'] ?? 'Desconocida',
                $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido',
                'EXITO'
            ]);
        }

        // Destruir sesión y redirigir
        session_unset();
        session_destroy();
        header("Location: ../index.php?mensaje=sesion_expirada");
        exit;
    }
}

// Actualizar última actividad
$_SESSION['ultima_actividad'] = time();

// Comprobar que el usuario sigue logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}



// ----------------- Registrar log de visualización de estadísticas -----------------
try {
    $stmt_log = $pdo->prepare("
        INSERT INTO log_actividades 
        (usuario_id, accion, modulo, descripcion, ip_address, navegador, resultado)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt_log->execute([
        $_SESSION['usuario_id'],
        'VISUALIZAR',
        'Estadísticas',
        'Visualización de estadísticas de estudiantes por país',
        $_SERVER['REMOTE_ADDR'] ?? 'Desconocida',
        $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido',
        'EXITO'
    ]);
} catch (PDOException $e) {
    // No interrumpir la página si falla el log
}

// ----------------- Consulta de datos -----------------
$query = "
  SELECT p.nombre AS pais, COUNT(e.id) AS estudiantes
  FROM paises p
  LEFT JOIN estudiantes e ON e.pais_id = p.id
  GROUP BY p.id
  ORDER BY estudiantes DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$paises = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convertir los datos para pasarlos a JavaScript
$datos_geochart = [['Ciudad', 'Alumnos']];
foreach ($paises as $pais) {
  $datos_geochart[] = [$pais['pais'], (int)$pais['estudiantes']];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard | Recepción de Archivos</title>
  <link href="../config/css/bootstrap.min.css" rel="stylesheet" />
  <link href="../config/css/css2.css" rel="stylesheet" />
  <link href="../config/css/bootstrap-icons.css" rel="stylesheet" />
  <link rel="icon" type="image/x-icon" href="favicon.ico" />
  <link rel="icon" type="image/svg+xml" href="assets/icons/favicon.svg" />
  <link rel="icon" href="../config/img/logo_pais.svg" type="image/png">



  <!-- Google Charts -->
  <script type="text/javascript" src="../config/js/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', { 'packages': ['geochart'] });
    google.charts.setOnLoadCallback(drawRegionsMap);

    function drawRegionsMap() {
      var data = google.visualization.arrayToDataTable(<?= json_encode($datos_geochart, JSON_UNESCAPED_UNICODE) ?>);
      var options = {};
      var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));
      chart.draw(data, options);
    }
  </script>  

  <style>
    * {
      box-sizing: border-box;
    }
  body {
      background: #f8f9fa;
      min-height: 100vh;
      color: linear-gradient(135deg, #1e1e2f, #2a2a40);
      font-family: 'Segoe UI', sans-serif;
      overflow-x: hidden;
    }

   

    .layout {
      display: flex;
      height: 100vh;
    }

    /* Sidebar fijo */
    .sidebar {
      width: 250px;
      background-color: #1e293b;
      color: #fff;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      padding: 1rem 0.5rem;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      transition: transform 0.3s ease;
      z-index: 1050;
      overflow-y: auto;
    }

    .sidebar.collapsed {
      transform: translateX(-100%);
    }

    .sidebar h4 {
      font-weight: bold;
      text-align: center;
      margin-bottom: 2rem;
      color: #fff;
    }

    .sidebar .nav-link {
      color: #cbd5e1;
      padding: 10px 15px;
      border-radius: 6px;
      transition: 0.2s;
    }

    .sidebar .nav-link:hover {
      background-color: #334155;
      color: #fff;
    }

    .sidebar .nav-link.active {
      background-color: #0d6efd;
      color: #fff !important;
    }

    .section-title {
      font-size: 0.8rem;
      text-transform: uppercase;
      color: #94a3b8;
      padding: 0.5rem 1rem 0.2rem;
    }

    /* Navbar superior fija */
    .navbar-top {
      height: 60px;
      background-color: #fff;
      border-bottom: 1px solid #dee2e6;
      position: fixed;
      top: 0;
      left: 250px;
      right: 0;
      z-index: 1040;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 1rem;
      transition: left 0.3s ease;
    }

    .collapsed+.navbar-top {
      left: 0;
    }

    /* Botón toggle sidebar */
    .menu-toggle {
      background: none;
      border: none;
      font-size: 1.5rem;
      color: #0d6efd;
    }

    /* Contenido principal */
    .content {
      flex-grow: 1;
      margin-left: 250px;
      padding: 80px 20px 20px;
      height: 100vh;
      overflow-y: auto;
      transition: margin-left 0.3s ease;
    }

    .collapsed~.content {
      margin-left: 0;
    }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .navbar-top {
        left: 0;
      }

      .content {
        margin-left: 0;
      }
    }

    .cv-container {
      max-width: 800px;
      margin: 30px auto;
      background: #fff;
      padding: 40px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
      border-radius: 10px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .cv-header {
      border-bottom: 2px solid #0d6efd;
      padding-bottom: 10px;
      margin-bottom: 30px;
    }

    .cv-section {
      margin-bottom: 30px;
    }

    .cv-section h5 {
      border-left: 5px solid #0d6efd;
      padding-left: 10px;
      margin-bottom: 20px;
      color: #0d6efd;
    }

    .cv-photo {
      max-width: 150px;
      border-radius: 10px;
      border: 2px solid #dee2e6;
    }

    .cv-label {
      font-weight: 600;
      color: #495057;
    }

    .cv-value {
      margin-bottom: 10px;
    }

    /*
    ESTILOS DE LA CONFIGURACION DEL SITIO WEB 
     */
    /* Estilos para el formulario de configuración */
    .config-form {
      max-width: 600px;
      margin: 0 auto;
      padding: 20px;
      background-color: #f9f9f9;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .config-form .form-group {
      margin-bottom: 20px;
    }

    .config-form label {
      font-size: 16px;
      font-weight: bold;
      color: #333;
      display: block;
      margin-bottom: 8px;
    }

    .config-form input[type="text"],
    .config-form textarea,
    .config-form input[type="color"] {
      width: 100%;
      padding: 10px;
      font-size: 14px;
      border: 1px solid #ddd;
      border-radius: 5px;
      box-sizing: border-box;
    }

    .config-form textarea {
      min-height: 100px;
    }

    .config-form .btn-submit {
      background-color: #4CAF50;
      color: white;
      padding: 12px 24px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .config-form .btn-submit:hover {
      background-color: #45a049;
    }
    .card {
      background-color: #f8f9fa;
      border: none;
    }

    .card-header {
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    /* Canvas decorativo (líneas suaves animadas) */
    canvas#bgCanvas {
      position: fixed;
      top: 0; left: 0;
      width: 100vw;
      height: 100vh;
      z-index: -1;
    }
  </style>

 



</head>

<body>