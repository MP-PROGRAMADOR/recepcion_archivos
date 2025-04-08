<?php
require_once '../config/conexion.php';

try {
    $stmt = $pdo->prepare("
        SELECT e.id, e.nombre_completo, e.codigo_acceso, e.fecha_nacimiento, e.creado_en, 
               p.nombre AS pais, e.ruta_foto
        FROM estudiantes e
        INNER JOIN paises p ON e.pais_id = p.id
        ORDER BY e.creado_en DESC
    ");
    $stmt->execute();
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener estudiantes: " . $e->getMessage());
}
?>

<?php include_once("../componentes/header.php"); ?>
<?php include_once("../componentes/sidebar.php"); ?>

<main class="content" id="mainContent">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="bi bi-mortarboard-fill me-2"></i>Listado de Estudiantes</h3>
            <a href="registrar_estudiantes.php" class="btn btn-primary rounded-3">
                <i class="bi bi-person-plus-fill me-1"></i> Nuevo Estudiante
            </a>
        </div>

        <div class="card shadow rounded-4">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="busqueda" class="form-label fw-bold">Buscar estudiante</label>
                        <input type="text" class="form-control" id="busqueda" placeholder="Buscar por nombre o país...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-center" id="tablaEstudiantes">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Código de Acceso</th>
                                <th>Fecha de Nacimiento</th>
                                <th>Registro</th>
                                <th>País</th>
                                <th>Foto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="contenidoTabla">
                            <?php if (!empty($estudiantes)): ?>
                                <?php foreach ($estudiantes as $est): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($est['id']) ?></td>
                                        <td><?= htmlspecialchars($est['nombre_completo']) ?></td>
                                        <td><?= htmlspecialchars($est['codigo_acceso']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($est['fecha_nacimiento'])) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($est['creado_en'])) ?></td>
                                        <td><?= htmlspecialchars($est['pais']) ?></td>
                                        <td>
                                            <?php
                                            $rutaFoto = "../php/" . $est['ruta_foto'];
                                            if (!empty($est['ruta_foto']) && file_exists($rutaFoto)):
                                            ?>
                                                <img src="<?= $rutaFoto ?>" class="rounded-circle shadow" alt="Foto de <?= htmlspecialchars($est['nombre_completo']) ?>" width="50" height="50">
                                            <?php else: ?>
                                                <span class="text-muted">Sin foto</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="editar_estudiante.php?id=<?= $est['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <a href="detalles_estudiante.php?id=<?= $est['id'] ?>" class="btn btn-success btn-sm" title="Detalles">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No hay estudiantes registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</main>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $("#busqueda").on("keyup", function () {
            let value = $(this).val().toLowerCase();
            $("#contenidoTabla tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().includes(value));
            });
        });
    });
</script>

<?php include_once("../componentes/footer.php"); ?>
