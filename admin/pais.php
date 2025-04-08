<?php
// Incluir la conexión
require_once '../config/conexion.php';

// Consulta segura de usuarios
try {
    $stmt = $pdo->prepare("SELECT id, nombre FROM pais ORDER BY id DESC");
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
        <h3><i class="bi bi-people-fill me-2"></i>Listado de País</h3>
        <a href="registrar_pais.php" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i> Nuevo País
        </a>
    </div>

    <div class="card shadow rounded-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="busqueda" class="form-label fw-bold">Buscar Pais</label>
                    <input type="text" class="form-control" id="busqueda"
                        placeholder="Buscar por ID, nombre o correo...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle text-center" id="tablaPais">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th> 
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenidoTabla">
                        <?php if (!empty($usuarios)): ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?= htmlspecialchars($usuario['id']) ?></td>
                                    <td><?= htmlspecialchars($usuario['nombre']) ?></td> 
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
                                <td colspan="5" class="text-center">No hay País registrados</td>
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

 

<?php include_once("../componentes/footer.php"); ?>
