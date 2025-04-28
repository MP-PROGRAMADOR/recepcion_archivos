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
        <i class="bi bi-passport-fill me-2"></i> Pasaportes
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
<div class="navbar-top">
  <button class="menu-toggle d-md-none" onclick="toggleSidebar()">☰</button>
  <span class="fw-semibold">Bienvenido al panel</span>
</div>
