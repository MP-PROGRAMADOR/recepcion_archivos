<?php
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");
?>

<main class="content" id="mainContent">
    <canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
    <div class="container  col-md-6  mt-4">

        <!-- ALERTA PERSONALIZADA -->
        <?php include_once("../componentes/alerta.php"); ?>

        <div class="card shadow rounded-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="bi bi-translate fs-4 me-2"></i>
                    <h5 class="mb-0">Registrar Nuevo Idioma</h5>
                </div>
            </div>

            <div class="card-body ">
                <form action="../php/guardar_idioma.php" class="p-4" method="POST" novalidate>
                    <div class="row g-3">
                      
                            <label for="nombre_idioma" class="form-label fw-bold">
                                <i class="bi bi-flag-fill me-1 text-secondary"></i>Nombre del idioma
                            </label>
                            <input type="text" id="nombre_idioma" name="nombre" class="form-control" placeholder="Ej: Español" required>
                        
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success me-2">
                            <i class="bi bi-save-fill me-1"></i> Guardar
                        </button>
                        <a href="idiomas.php" class="btn btn-secondary">
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

<?php include_once("../componentes/footer.php"); ?>
