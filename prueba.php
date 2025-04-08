<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard | Recepción de Archivos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      height: 100vh;
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

    .collapsed + .navbar-top {
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

    .collapsed ~ .content {
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
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="layout">
  <nav class="sidebar" id="sidebar">
    <h4>📘 Gestión Académica</h4>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a href="dashboard.php" class="nav-link">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>
      <li class="section-title">Administración</li>
      <li><a href="usuarios.php" class="nav-link "><i class="bi bi-people-fill me-2"></i> Usuarios</a></li>
      <li><a href="roles.php" class="nav-link "><i class="bi bi-person-badge-fill me-2"></i> Roles</a></li>

      <li class="section-title">Académico</li>
      <li><a href="estudiantes.php" class="nav-link "><i class="bi bi-person-lines-fill me-2"></i> Estudiantes</a></li>
      <li><a href="docentes.php" class="nav-link "><i class="bi bi-person-video3 me-2"></i> Docentes</a></li>
      <li><a href="asignaturas.php" class="nav-link "><i class="bi bi-journal-text me-2"></i> Asignaturas</a></li>

      <li class="section-title">Evaluaciones</li>
      <li><a href="examenes.php" class="nav-link "><i class="bi bi-journal-check me-2"></i> Exámenes</a></li>
      <li><a href="preguntas.php" class="nav-link "><i class="bi bi-patch-question-fill me-2"></i> Preguntas</a></li>

      <li class="section-title">Reportes</li>
      <li><a href="reportes_general.php" class="nav-link "><i class="bi bi-graph-up me-2"></i> General</a></li>

      <li class="section-title">Configuración</li>
      <li><a href="configuracion.php" class="nav-link "><i class="bi bi-gear-fill me-2"></i> Ajustes</a></li>

      <li class="mt-3"><a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left me-2"></i> Cerrar sesión</a></li>
    </ul>
  </nav>

  <!-- Navbar superior -->
  <div class="navbar-top">
    <button class="menu-toggle d-md-none" onclick="toggleSidebar()">☰</button>
    <span class="fw-semibold">Bienvenido al panel</span>
  </div>

  <!-- Contenido principal -->
  <main class="content" id="mainContent">
    <h2 class="mb-4">Resumen</h2>

    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card shadow-sm border-start border-primary border-4">
          <div class="card-body">
            <h6>Total de Exámenes</h6>
            <h3><i class="bi bi-journal-text me-2"></i>245</h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-start border-success border-4">
          <div class="card-body">
            <h6>Estudiantes Activos</h6>
            <h3><i class="bi bi-people me-2"></i>132</h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-start border-warning border-4">
          <div class="card-body">
            <h6>Escuelas</h6>
            <h3><i class="bi bi-house-door-fill me-2"></i>15</h3>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("show");
  }
</script>

</body>
</html>
