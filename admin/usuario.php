<?php
// Incluir la conexión
require_once '../config/conexion.php';

// Consulta segura de usuarios
try {
    $stmt = $pdo->prepare("SELECT id, nombre, email, creado_en FROM usuarios ORDER BY id DESC");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los usuarios: " . $e->getMessage());
}

// Componentes del layout
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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenidoTabla">
                        <?php if (!empty($usuarios)): ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?= htmlspecialchars($usuario['id']) ?></td>
                                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($usuario['creado_en']))) ?></td>
                                    <td>
                                        <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-warning me-1" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="eliminar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No hay usuarios registrados</td>
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
    $(document).ready(function () {
        function cargarUsuarios(query = '') {
            $.ajax({
                url: 'buscar_usuarios.php',
                method: 'POST',
                data: { busqueda: query },
                success: function (data) {
                    $('#contenidoTabla').html(data);
                },
                error: function () {
                    alert("Ocurrió un error al cargar los usuarios.");
                }
            });
        }

        // Cargar al inicio
        cargarUsuarios();

        // Búsqueda en tiempo real
        $('#busqueda').on('keyup', function () {
            const texto = $(this).val();
            cargarUsuarios(texto);
        });
    });
</script>

<?php include_once("../componentes/footer.php"); ?>
