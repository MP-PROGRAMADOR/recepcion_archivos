<?php
// Iniciar sesión de forma segura
// Comienza la sesión si no está ya iniciada
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

/* // Validar la variable $_SESSION['favicon']
$favico = isset($_SESSION["config"]) ? $_SESSION["config"] : 'favicon.ico'; // Ruta predeterminada
$favico = htmlspecialchars($favico); // Asegura que no haya inyecciones de código

// Obtener la extensión del archivo
$extension = strtolower(pathinfo($favico, PATHINFO_EXTENSION));

// Establecer el tipo MIME según la extensión del archivo
switch ($extension) {
  case 'ico':
      $mime_type = 'image/x-icon';
      break;
  case 'png':
      $mime_type = 'image/png';
      break;
  case 'svg':
      $mime_type = 'image/svg+xml';
      break;
  default:
      // Si la extensión no es válida, usamos el favicon por defecto
      $favico = 'favicon.ico';
      $mime_type = 'image/x-icon';
      break;
}

$favico = $_SESSION["config"]; */
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
  <!-- Favicon clásico (.ico) -->
  <link rel="icon" type="image/x-icon" href="favicon.ico">

  <!-- O si usas un PNG -->
  <link rel="icon" type="image/png" href=" ">

  <!-- Para SVG (opcional) -->
  <link rel="icon" type="image/svg+xml" href="assets/icons/favicon.svg">

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      height: 100vh;
      padding: .5rem;
      overflow: hidden;
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
  </style>

  <?php

  require_once '../config/conexion.php';

  // Consulta para obtener los países y la cantidad de estudiantes en cada país
  $query = "
    SELECT p.nombre AS pais, COUNT(e.id) AS estudiantes
    FROM paises p
    LEFT JOIN estudiantes e ON e.pais_id = p.id
    GROUP BY p.id
    ORDER BY estudiantes DESC;
";


  $stmt = $pdo->prepare($query);
  $stmt->execute();
  $paises = $stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <script type="text/javascript" src="../config/js/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['geochart'],
    });
    google.charts.setOnLoadCallback(drawRegionsMap);

    function drawRegionsMap() {
      var data = google.visualization.arrayToDataTable([
        ['Ciudad', 'Alumnos'],
        <?php
        // Generar el array con los países y la cantidad de estudiantes
        foreach ($paises as $pais) {
          echo "['" . addslashes($pais['pais']) . "', " . $pais['estudiantes'] . "],\n";
        }
        ?>
      ]);

      var options = {};

      var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

      chart.draw(data, options);
    }
  </script>



</head>

<body>