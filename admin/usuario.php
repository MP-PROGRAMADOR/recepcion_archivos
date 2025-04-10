<?php
session_start();
require_once '../config/conexion.php';

// Configuración de paginación
$por_pagina = 5;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina_actual > 1) ? ($pagina_actual * $por_pagina) - $por_pagina : 0;

// Total de usuarios
try {
    $total_stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $total_usuarios = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_paginas = ceil($total_usuarios / $por_pagina);
} catch (PDOException $e) {
    die("Error al contar usuarios: " . $e->getMessage());
}

// Usuarios paginados
try {
    $stmt = $pdo->prepare("SELECT id, nombre, email, creado_en FROM usuarios ORDER BY id DESC LIMIT :inicio, :limite");
    $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $stmt->bindValue(':limite', $por_pagina, PDO::PARAM_INT);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los usuarios: " . $e->getMessage());
}

include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");
?>

<main class="content" id="mainContent">
        <!-- INICIO DE LA ALERTA -->
        <?php


        if (isset($_SESSION['exito']) && !empty($_SESSION['exito'])):
            ?>
            <div id="alerta-exito"
                class="alert alert-success alert-dismissible shadow-sm fade show d-flex align-items-start gap-2 p-3 mt-3 border border-success-subtle rounded-3"
                role="alert" style="animation: fadeIn 0.5s ease-in-out;">
                <i class="bi bi-check-circle-fill fs-4 flex-shrink-0 mt-1"></i>
                <div>
                    <strong>¡Éxito!</strong>
                    <p class="mb-0 mt-1"><?= htmlspecialchars($_SESSION['exito']) ?></p>
                </div>
                <button type="button" class="btn-close ms-auto mt-1" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>

            <script>
                // Ocultar automáticamente luego de 6 segundos
                setTimeout(() => {
                    const alerta = document.getElementById('alerta-exito');
                    if (alerta) {
                        alerta.classList.remove('show');
                        alerta.classList.add('fade');
                        setTimeout(() => alerta.remove(), 500); // Lo remueve del DOM
                    }
                }, 6000);
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
            unset($_SESSION['exito']); // Limpiar mensaje de éxito de la sesión
        endif;
        ?>

        <!-- FIN DE LA ALERTA -->
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
                    <input type="text" class="form-control" id="busqueda" placeholder="Buscar por ID, nombre o correo...">
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
                                        <a href="eliminar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-danger d-none" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">No hay usuarios registrados</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($pagina_actual > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?pagina=<?= $pagina_actual - 1 ?>">&laquo;</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pagina_actual < $total_paginas): ?>
                            <li class="page-item">
                                <a class="page-link" href="?pagina=<?= $pagina_actual + 1 ?>">&raquo;</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Bootstrap Icons y Script -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Buscador -->
<script>
    $(document).ready(function () {
        $('#busqueda').on('keyup', function () {
            let valor = $(this).val().toLowerCase();
            $('#contenidoTabla tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().includes(valor));
            });
        });
    });
</script>

<?php include_once("../componentes/footer.php"); ?>
