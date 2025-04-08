<?php
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");
?>

<main class="content" id="mainContent">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="bi bi-mortarboard-fill me-2"></i>Listado de Estudiantes</h3>
        <a href="registrar_estudiante.php" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i> Nuevo Estudiante
        </a>
    </div>

    <div class="card shadow rounded-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="busqueda" class="form-label fw-bold">Buscar estudiante</label>
                    <input type="text" class="form-control" id="busqueda"
                        placeholder="Buscar por ID, nombre o código de acceso...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle text-center" id="tablaEstudiantes">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Código de Acceso</th>
                            <th>Fecha de Nacimiento</th>
                            <th>Foto</th>
                            <th>Registrado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenidoTabla">
                        <!-- Contenido dinámico AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Bootstrap Icons y Script -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        function cargarEstudiantes(query = '') {
            $.ajax({
                url: 'buscar_estudiantes.php',
                method: 'POST',
                data: { busqueda: query },
                success: function (data) {
                    $('#contenidoTabla').html(data);
                }
            });
        }

        // Cargar estudiantes al inicio
        cargarEstudiantes();

        // Búsqueda en tiempo real
        $('#busqueda').on('keyup', function () {
            let texto = $(this).val();
            cargarEstudiantes(texto);
        });
    });
</script>

<?php
include_once("../componentes/footer.php");
?>
