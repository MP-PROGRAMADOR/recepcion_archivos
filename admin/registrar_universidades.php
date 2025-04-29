<?php
session_start();
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");
?>

<main class="content" id="mainContent">
<canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
    <div class="container mt-4">

        <!-- INICIO DE LA ALERTA DE ERRORES -->
        <?php
        if (isset($_SESSION['errores']) && is_array($_SESSION['errores'])):
        ?>
        <div id="alerta-errores"
            class="alert alert-danger alert-dismissible shadow-sm fade show d-flex align-items-start gap-2 p-3 mt-3 border border-danger-subtle rounded-3"
            role="alert" style="animation: fadeIn 0.5s ease-in-out;">
            <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0 mt-1"></i>
            <div>
                <strong>Se detectaron errores:</strong>
                <ul class="mb-0 mt-1">
                    <?php foreach ($_SESSION['errores'] as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <button type="button" class="btn-close ms-auto mt-1" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>

        <script>
            // Ocultar automáticamente luego de 6 segundos
            setTimeout(() => {
                const alerta = document.getElementById('alerta-errores');
                if (alerta) {
                    alerta.classList.remove('show');
                    alerta.classList.add('fade');
                    setTimeout(() => alerta.remove(), 500); // Lo remueve del DOM
                }
            }, 9000);
        </script>

        <style>
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
        <?php
        unset($_SESSION['errores']); // Limpiar errores de la sesión
        endif;
        ?>
        <!-- FIN DE LA ALERTA -->

        <div class="card shadow rounded-4">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="bi bi-university fs-4 me-2"></i>
                <h5 class="mb-0">Registrar Nueva Universidad</h5>
            </div>
            <div class="card-body">
                <form action="../php/guardar_universidades.php" method="POST" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre_universidad" class="form-label fw-bold">Nombre de la Universidad</label>
                            <input type="text" id="nombre_universidad" name="nombre" class="form-control" placeholder="Ej: Universidad Nacional" required>
                        </div>

                        <div class="col-md-6">
                            <label for="ciudad_id" class="form-label fw-bold">Ciudad</label>
                            <select name="ciudad_id" class="form-select" required>
                                <option value="">Seleccionar Ciudad</option>
                                <!-- Aquí irían las opciones dinámicas de ciudades, obtenidas de la base de datos -->
                                <?php
                                try {
                                    // Conexión y consulta segura usando PDO
                                    include_once('../config/conexion.php');
                                    $query = "SELECT id, nombre FROM ciudades";
                                    $stmt = $pdo->query($query);

                                    // Mostrar las opciones de ciudad
                                    while ($ciudad = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='" . htmlspecialchars($ciudad['id'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($ciudad['nombre'], ENT_QUOTES, 'UTF-8') . "</option>";
                                    }
                                } catch (PDOException $e) {
                                    echo "<option value=''>Error al cargar ciudades</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success me-2">
                            <i class="bi bi-save-fill me-1"></i> Guardar
                        </button>
                        <a href="universidades.php" class="btn btn-secondary">
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
