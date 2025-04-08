<?php
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");
?>

<main class="content" id="mainContent">
    <div class="container mt-4">
        <div class="card shadow rounded-4">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="bi bi-person-plus-fill fs-4 me-2"></i>
                <h5 class="mb-0">Registrar Nuevo Usuario</h5>
            </div>
            <div class="card-body">
                <form action="../php/guardar_usuarios.php" method="POST" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre_usuario" class="form-label fw-bold">Nombre completo</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Juan Pérez" required>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold">Correo electrónico</label>
                            <input type="email" name="email" class="form-control" placeholder="usuario@correo.com" required>
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label fw-bold">Contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="********" required>
                        </div>

                        <div class="col-md-6">
                            <label for="contrasena_confirmada" class="form-label fw-bold">Repetir contraseña</label>
                            <input type="password" name="contrasena_confirmada" class="form-control" placeholder="********" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success me-2">
                            <i class="bi bi-save-fill me-1"></i> Guardar
                        </button>
                        <a href="usuarios.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle-fill me-1"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<?php
include_once("../componentes/footer.php");
?>
