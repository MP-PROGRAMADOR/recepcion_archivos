<?php
// Incluir el archivo de conexión a la base de datos
include '../config/conexion.php';  // Asegúrate de tener la conexión configurada correctamente

// Consulta para obtener todos los usuarios
$stmt = $pdo->query("SELECT * FROM usuarios");

// Verificar si hay usuarios en la base de datos
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php

include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");
?>

<main class="content" id="mainContent">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="bi bi-people-fill me-2"></i>Listado de Usuarios</h3>
        <a href="registrar_usuario.php" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i> Nuevo Usuario
        </a>
    </div>

    <div class="card shadow rounded-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="busqueda" class="form-label fw-bold">Buscar usuario</label>
                    <input type="text" class="form-control" id="busqueda"
                        placeholder="Buscar por ID, nombre o correo...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle text-center" id="tablaUsuarios">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Fecha de Creación</th>
                        </tr>
                    </thead>
                    <tbody id="contenidoTabla">
                        <?php if (!empty($usuarios)): ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo $usuario['id']; ?></td>
                                    <td><?php echo $usuario['nombre']; ?></td>
                                    <td><?php echo $usuario['email']; ?></td>
                                    <td><?php echo $usuario['creado_en']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No hay usuarios registrados</td>
                            </tr>
                        <?php endif; ?>
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
    $(document).ready(function() {
        function cargarUsuarios(query = '') {
            $.ajax({
                url: 'buscar_usuarios.php',
                method: 'POST',
                data: {
                    busqueda: query
                },
                success: function(data) {
                    $('#contenidoTabla').html(data);
                }
            });
        }

        // Cargar usuarios al inicio
        cargarUsuarios();

        // Búsqueda en tiempo real
        $('#busqueda').on('keyup', function() {
            let texto = $(this).val();
            cargarUsuarios(texto);
        });
    });
</script>
<?php

include_once("../componentes/footer.php");
?>