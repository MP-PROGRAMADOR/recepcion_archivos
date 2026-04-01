<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$rol = $_SESSION['usuario_rol'] ?? '';

// Lógica para detectar la página actual
$pagina_actual = basename($_SERVER['PHP_SELF']);

/**
 * Función auxiliar para determinar si un enlace debe estar activo
 * Esto permite que si estás en "editar_cuenta.php", el menú "Cuentas Bancarias" siga resaltado.
 */
function es_activo($archivo_enlace, $pagina_actual)
{
  // Si la página actual es la misma que el enlace
  if ($archivo_enlace === $pagina_actual) {
    return 'active';
  }

  // Casos especiales: Resaltar el padre cuando estás en una página de edición/detalle
  $relaciones = [
    'cuentas_bancarias.php' => ['editar_cuenta.php', 'ver_cuenta.php'],
    'estudiantes.php'       => ['editar_estudiante.php', 'registrar_estudiante.php'],
    'usuario.php'           => ['editar_usuario.php'],
    'pasaportes.php'        => ['editar_pasaporte.php'],
    'notas.php'             => ['subir_notas.php']
  ];

  if (isset($relaciones[$archivo_enlace]) && in_array($pagina_actual, $relaciones[$archivo_enlace])) {
    return 'active';
  }

  return '';
}
?>

<style>
  /* Estilos para el resaltado del menú */
  .sidebar .nav-link {
    transition: all 0.3s ease;
    border-radius: 8px;
    margin: 0 10px;
    padding: 10px 15px;
  }

  .sidebar .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
  }

  /* Clase de enlace activo */
  .sidebar .nav-link.active {
    background-color: #0d6efd !important;
    /* Azul Bootstrap */
    color: #fff !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    font-weight: 600;
  }

  .sidebar .nav-link.active i {
    color: #fff !important;
  }
</style>

<aside class="sidebar d-md-block" id="sidebar">

  <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom d-md-none">
    <span class="fw-bold text-white">Menú</span>
    <button class="btn btn-sm btn-light" onclick="toggleSidebar()">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <div class="px-2">
    <h5 class="mb-2 mt-4 text-white">
      <i class="bi bi-mortarboard-fill me-2"></i> Gestión Académica
    </h5>
  </div>

  <ul class="nav flex-column mb-4 mt-3">

    <li class="nav-item">
      <a href="../admin/index.php" class="nav-link text-white <?= es_activo('index.php', $pagina_actual) ?>">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
      </a>
    </li>

    <li class="text-uppercase text-secondary small fw-bold mt-4 mb-2 px-4">Administración</li>

    <?php if (strtolower($rol) === 'administrador'): ?>
      <li class="nav-item mb-1">
        <a href="../admin/usuario.php" class="nav-link text-white <?= es_activo('usuario.php', $pagina_actual) ?>">
          <i class="bi bi-people me-2"></i> Usuarios
        </a>
      </li>
    <?php endif; ?>

    <li class="nav-item mb-1">
      <a href="../admin/pais.php" class="nav-link text-white <?= es_activo('pais.php', $pagina_actual) ?>">
        <i class="bi bi-globe-americas me-2"></i> Países
      </a>
    </li>

    <li class="nav-item mb-1">
      <a href="../admin/ciudades.php" class="nav-link text-white <?= es_activo('ciudades.php', $pagina_actual) ?>">
        <i class="bi bi-buildings me-2"></i> Ciudades
      </a>
    </li>

    <li class="nav-item mb-1">
      <a href="../admin/universidades.php" class="nav-link text-white <?= es_activo('universidades.php', $pagina_actual) ?>">
        <i class="bi bi-bank me-2"></i> Universidades
      </a>
    </li>

    <li class="nav-item mb-1">
      <a href="../admin/idiomas.php" class="nav-link text-white <?= es_activo('idiomas.php', $pagina_actual) ?>">
        <i class="bi bi-translate me-2"></i> Idiomas
      </a>
    </li>

    <li class="nav-item mb-1">
      <a href="../admin/cuentas_bancarias.php" class="nav-link text-white <?= es_activo('cuentas_bancarias.php', $pagina_actual) ?>">
        <i class="bi bi-credit-card-2-front me-2"></i> Cuentas Bancarias
      </a>
    </li>

    <li class="nav-item mb-1">
      <a href="../admin/estudiantes.php" class="nav-link text-white <?= es_activo('estudiantes.php', $pagina_actual) ?>">
        <i class="bi bi-people-fill me-2"></i> Estudiantes
      </a>
    </li>

    <li class="nav-item mb-1">
      <a href="../admin/academico.php" class="nav-link text-white <?= es_activo('academico.php', $pagina_actual) ?>">
        <i class="bi bi-calendar-check me-2"></i> Año Académico
      </a>
    </li>

    <li class="nav-item mb-1">
      <a href="../admin/pasaportes.php" class="nav-link text-white <?= es_activo('pasaportes.php', $pagina_actual) ?>">
        <i class="bi bi-person-badge me-2"></i> Pasaporte
      </a>
    </li>

    <li class="nav-item mb-1">
      <a href="../admin/notas.php" class="nav-link text-white <?= es_activo('notas.php', $pagina_actual) ?>">
        <i class="bi bi-journal-check me-2"></i> Notas
      </a>
    </li>

   

          <?php if (strtolower($rol) === 'administrador'): ?>
                                     <li class="nav-item mb-1">
      <a href="../admin/auditoria.php" class="nav-link text-white <?= es_activo('auditoria.php', $pagina_actual) ?>">
        <i class="bi bi-shield-check me-2"></i> Auditoría
      </a>
    </li>
                                    <?php endif; ?>

    <li class="text-uppercase text-secondary small fw-bold mt-4 mb-2 px-4">Configuración</li>

    <li class="nav-item">
      <button type="button" class="nav-link text-danger border-0 bg-transparent text-start" data-bs-toggle="modal" data-bs-target="#modalLogout">
        <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
      </button>
    </li>

  </ul>
</aside>

<div class="navbar-top d-flex justify-content-between align-items-center px-4 py-2 bg-light shadow-sm">
  <button class="menu-toggle d-md-none btn btn-outline-secondary" onclick="toggleSidebar()">
    <i class="bi bi-list"></i>
  </button>

  <div class="d-flex align-items-center">
    <span class="fw-semibold text-dark fs-5 me-3 d-none d-sm-inline">Bienvenido al Panel</span>
    <div class="d-flex align-items-center">
      <i class="bi bi-person-circle me-2 fs-4 text-primary"></i>
      <span class="fw-bold text-dark fs-5">
        <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario') ?>
      </span>
    </div>
  </div>

  <div class="d-flex align-items-center">
    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalLogout" title="Cerrar sesión">
      <i class="bi bi-box-arrow-right me-1"></i> <span class="d-none d-sm-inline">Cerrar sesión</span>
    </button>
  </div>
</div>




<div class="modal fade" id="modalLogout" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalLogoutLabel">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar salida
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-4">
        <i class="bi bi-question-circle text-danger" style="font-size: 3rem;"></i>
        <p class="mt-3 fs-5">¿Estás seguro de que deseas cerrar tu sesión actual?</p>
        <small class="text-muted">Cualquier cambio no guardado podría perderse.</small>
      </div>
      <div class="modal-footer justify-content-center border-0 pb-4">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i> Cancelar
        </button>
        <a href="../php/logout.php" class="btn btn-danger px-4">
          <i class="bi bi-box-arrow-right me-1"></i> Sí, cerrar sesión
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  // Inicialización de tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Función para abrir/cerrar sidebar en móvil (debe estar definida en tu JS principal o aquí)
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
  }
</script>