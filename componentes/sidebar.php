
<!-- Sidebar -->
<aside class="sidebar d-md-block" id="sidebar">
  <!-- Botón cerrar en móvil -->
  <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom d-md-none">
    <span class="fw-bold">Menú</span>
    <button class="btn btn-sm btn-light" onclick="toggleSidebar()">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <h5 class="mb-2 mt-2">
    <i class="bi bi-mortarboard-fill me-2"></i> Gestión Académica
  </h5>

  <ul class="nav flex-column mb-4">

    <!-- Dashboard -->
    <li class="nav-item mt-2">
      <a href="../admin/index.php" class="nav-link text-white">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
      </a>
    </li>

    <!-- Sección Administración -->
    <li class="text-uppercase text-secondary small fw-bold mt-2 mb-2">Administración</li>

    <li class="nav-item mb-2">
      <a href="../admin/usuario.php" class="nav-link text-white">
        <i class="bi bi-person-lines-fill me-2"></i> Usuarios
      </a>
    </li>

    <li class="nav-item mb-2">
      <a href="../admin/pais.php" class="nav-link text-white">
        <i class="bi bi-globe2 me-2"></i> Países
      </a>
    </li>

    <li class="nav-item mb-2">
      <a href="../admin/ciudades.php" class="nav-link text-white">
        <i class="bi bi-buildings-fill me-2"></i> Ciudades
      </a>
    </li>

    <li class="nav-item mb-2">
      <a href="../admin/universidades.php" class="nav-link text-white">
        <i class="bi bi-bank2 me-2"></i> Universidades
      </a>
    </li>

    <li class="nav-item mb-2">
      <a href="../admin/idiomas.php" class="nav-link text-white">
        <i class="bi bi-translate me-2"></i> Idiomas
      </a>
    </li>

    <li class="nav-item mb-2">
      <a href="../admin/estudiantes.php" class="nav-link text-white">
        <i class="bi bi-people-fill me-2"></i> Estudiantes
      </a>
    </li>

    <li class="nav-item mb-2">
      <a href="academico.php" class="nav-link text-white">
        <i class="bi bi-calendar3-event-fill me-2"></i> Año Académico
      </a>
    </li>

    <li class="nav-item mb-2">
      <a href="../admin/pasaportes.php" class="nav-link text-white">
      <i class="fas fa-passport"></i> Pasaporte
      </a>
    </li>

    <li class="nav-item mb-2">
      <a href="../admin/notas.php" class="nav-link text-white">
        <i class="bi bi-journal-text me-2"></i> Notas
      </a>
    </li>

    <!-- Sección Configuración -->
    <li class="text-uppercase text-secondary small fw-bold mt-2 mb-2">Configuración</li>

    <!-- Cierre de sesión -->
    <li class="nav-item">
      <a href="../php/logout.php" class="nav-link text-danger">
        <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
      </a>
    </li>
  </ul>
</aside>

<!-- Navbar superior -->
<!-- <div class="navbar-top">
  <button class="menu-toggle d-md-none" onclick="toggleSidebar()">☰</button>
  <span class="fw-semibold">Bienvenido al panel</span>
</div> -->


<!-- Navbar superior -->
<div class="navbar-top d-flex justify-content-between align-items-center px-4 py-2 bg-light shadow-sm">
  <!-- Menú de navegación (para móviles) -->
  <button class="menu-toggle d-md-none btn btn-outline-secondary" onclick="toggleSidebar()">
    <i class="bi bi-list"></i>
  </button>

  <!-- Título y saludo -->
  <div class="d-flex align-items-center">
    <span class="fw-semibold text-dark fs-5 me-3">Bienvenido al Panel</span>

    <!-- Nombre de usuario logueado con avatar -->
    <div class="d-flex align-items-center">
      <i class="bi bi-person-circle me-2 fs-4 text-primary"></i>
      <span class="fw-bold text-dark fs-5"><?= htmlspecialchars( $_SESSION['usuario_nombre']) ?></span>
    </div>
  </div>

  <!-- Barra de opciones de usuario (icono de configuración y cerrar sesión) -->
  <div class="d-flex align-items-center">
    
    
    <a href="../php/logout.php" class="btn btn-outline-danger btn-sm" title="Cerrar sesión">
        <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
      </a>
     
    
  </div>
</div>

<!-- Script para inicializar los tooltips de Bootstrap -->
<script>
  // Inicialización de tooltips para los botones
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
</script>
