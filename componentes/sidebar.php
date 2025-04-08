
  <!-- Botón para mostrar el menú en móviles -->
  <button class="menu-toggle" onclick="toggleSidebar()">
    ☰
  </button>

  <!-- Sidebar -->
  <nav class="sidebar" id="sidebar">
    <div class="logo">📂 Recepción</div>
    <a href="../admin/index.php" class="active">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
        <path d="M8.354 1.146a.5.5 0 0 0-.708 0L1 7.793V14.5a.5.5 0 0 0 .5.5H6v-5h4v5h4.5a.5.5 0 0 0 .5-.5V7.793l-6.646-6.647z"/>
      </svg>
      Panel Principal
    </a>
    <a href="#">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-passport" viewBox="0 0 16 16">
        <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h4V1H2z"/>
        <path d="M9 1v14h5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9z"/>
      </svg>
      Módulo Pasaportes
    </a>
    <a href="#">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-text" viewBox="0 0 16 16">
        <path d="M5 4a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5A.5.5 0 0 1 5 4zm0 2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5A.5.5 0 0 1 5 6zm0 2a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3A.5.5 0 0 1 5 8z"/>
        <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2A2 2 0 0 1 4 0h6.5L14 4.5zM10 0v4a1 1 0 0 0 1 1h4l-5-5z"/>
      </svg>
      Módulo Notas Académicas
    </a>
    <a href="../admin/usuario.php">👨‍🎓 usuarios</a>
    <a href="#">👨‍🎓 Estudiantes</a>
    <a href="#">👤 Administradores</a>
    <a href="#">⚙️ Configuración</a>
    <a href="#">🚪 Cerrar Sesión</a>
  </nav>
<!-- Main -->
<div class="flex-grow-1 d-flex flex-column overflow-hidden">
  <nav class="navbar navbar-expand-lg border-bottom shadow-sm sticky-top">
    <div class="container-fluid">
      <button class="btn btn-outline-light d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#sidebar">
        <i class="bi bi-list"></i>
      </button>
      <span class="navbar-brand fw-bold"><?= $titulo ?? 'Panel de Administración' ?></span>
      <div class="ms-auto d-flex gap-2 align-items-center">
        <span class="fw-semibold">Admin</span>
        <a href="#" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Cerrar
          sesión</a>
      </div>
    </div>
  </nav>



  <!-- Contenido principal -->
  <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4"></main>
