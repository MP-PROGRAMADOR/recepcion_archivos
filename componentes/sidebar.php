<!-- Sidebar -->
<div class="layout">
<aside class="sidebar bg-dark text-white vh-100 p-3" id="sidebar">
    <h5 class="mb-4">
      <i class="bi bi-mortarboard-fill me-2"></i> Gestión Académica
    </h5>

    <ul class="nav flex-column">

      <!-- Dashboard -->
      <li class="nav-item mb-2">
        <a href="../admin/index.php" class="nav-link text-white">
          <i class="bi bi-speedometer me-2"></i> Dashboard
        </a>
      </li>

      <!-- Sección Administración -->
      <li class="text-uppercase text-secondary small fw-bold mt-4 mb-2">Administración</li>

      <li class="nav-item mb-2">
        <a href="../admin/usuario.php" class="nav-link text-white">
          <i class="bi bi-person-badge-fill me-2"></i> Usuarios
        </a>
      </li>

      <li class="nav-item mb-2">
        <a href="../admin/pais.php" class="nav-link text-white">
          <i class="bi bi-geo-alt-fill me-2"></i> País
        </a>
      </li>

      <li class="nav-item mb-2">
        <a href="../admin/estudiantes.php" class="nav-link text-white">
          <i class="bi bi-people-fill me-2"></i> Estudiantes
        </a>
      </li>

      <li class="nav-item mb-2">
        <a href="academico.php" class="nav-link text-white">
          <i class="bi bi-calendar2-week-fill me-2"></i> Año Académico
        </a>
      </li>

      <!-- Sección Configuración -->
      <li class="text-uppercase text-secondary small fw-bold mt-4 mb-2">Configuración</li>

      <li class="nav-item mb-2">
        <a href="configuracion.php" class="nav-link text-white">
          <i class="bi bi-gear-fill me-2"></i> Ajustes
        </a>
      </li>

      <!-- Cierre de sesión -->
      <li class="nav-item mt-4">
        <a href="logout.php" class="nav-link text-danger">
          <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
        </a>
      </li>
    </ul>
  </aside>
  </nav>

  <!-- Navbar superior -->
  <div class="navbar-top">
    <button class="menu-toggle d-md-none" onclick="toggleSidebar()">☰</button>
    <span class="fw-semibold">Bienvenido al panel</span>
  </div>