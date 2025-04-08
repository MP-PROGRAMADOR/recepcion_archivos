<?php
require_once '../config/conexion.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de estudiante no válido.");
}

$estudiante_id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("
        SELECT e.id, e.nombre_completo, e.fecha_nacimiento, e.ruta_foto, e.creado_en, p.nombre AS pais
        FROM estudiantes e
        INNER JOIN paises p ON e.pais_id = p.id
        WHERE e.id = ?
    ");
    $stmt->execute([$estudiante_id]);
    $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$estudiante) {
        die("Estudiante no encontrado.");
    }

    $stmt = $pdo->prepare("
        SELECT aa.nombre AS anio_academico, n.observaciones, n.archivo_url, n.fecha_subida
        FROM notas n
        INNER JOIN anios_academicos aa ON n.anio_academico_id = aa.id
        WHERE n.estudiante_id = ?
        ORDER BY aa.nombre DESC
    ");
    $stmt->execute([$estudiante_id]);
    $notas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        SELECT numero_pasaporte, fecha_emision, fecha_expiracion, archivo_url
        FROM pasaportes
        WHERE estudiante_id = ?
        ORDER BY fecha_subida DESC
        LIMIT 1
    ");
    $stmt->execute([$estudiante_id]);
    $pasaporte = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
?>

<?php include_once("../componentes/header.php"); ?>
<?php include_once("../componentes/sidebar.php"); ?>

<main class="content" id="mainContent">
    <div class="cv-container">

        <div class="cv-header text-center">
            <h3><i class="bi bi-person-vcard me-2"></i>Ficha del Estudiante</h3>
        </div>

        <!-- Información Personal -->
        <div class="cv-section">
            <h5><i class="bi bi-person-circle me-2"></i>Información Personal</h5>
            <div class="row">
                <div class="col-md-4 text-center mb-3">
                    <?php if (!empty($estudiante['ruta_foto']) && file_exists("../php/" . $estudiante['ruta_foto'])): ?>
                        <img src="../php/<?= htmlspecialchars($estudiante['ruta_foto']) ?>" class="cv-photo" alt="Foto">
                    <?php else: ?>
                        <div class="text-muted">
                            <i class="bi bi-person-circle fs-1"></i><br>Sin foto
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <p class="cv-label">Código del Estudiante: <span class="cv-value"><?= $estudiante['id'] ?></span></p>
                    <p class="cv-label">Nombre Completo: <span class="cv-value"><?= htmlspecialchars($estudiante['nombre_completo']) ?></span></p>
                    <p class="cv-label">Fecha de Nacimiento: <span class="cv-value"><?= date('d/m/Y', strtotime($estudiante['fecha_nacimiento'])) ?></span></p>
                    <p class="cv-label">País: <span class="cv-value"><?= htmlspecialchars($estudiante['pais']) ?></span></p>
                    <p class="cv-label">Fecha de Registro: <span class="cv-value"><?= date('d/m/Y H:i', strtotime($estudiante['creado_en'])) ?></span></p>
                </div>
            </div>
        </div>

        <!-- Notas Académicas -->
        <div class="cv-section">
            <h5><i class="bi bi-journal-text me-2"></i>Notas Académicas</h5>
            <?php if (!empty($notas)): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Año</th>
                                <th>Observaciones</th>
                                <th>Archivo</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notas as $nota): ?>
                                <tr>
                                    <td><?= htmlspecialchars($nota['anio_academico']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($nota['observaciones'])) ?></td>
                                    <td>
                                        <?php if ($nota['archivo_url']): ?>
                                            <a href="../php/<?= htmlspecialchars($nota['archivo_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-earmark-text"></i> Ver
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">No disponible</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($nota['fecha_subida'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">No hay notas registradas.</p>
            <?php endif; ?>
        </div>

        <!-- Pasaporte -->
        <div class="cv-section">
            <h5><i class="bi bi-passport me-2"></i>Pasaporte</h5>
            <?php if ($pasaporte): ?>
                <p class="cv-label">Número: <span class="cv-value"><?= htmlspecialchars($pasaporte['numero_pasaporte']) ?></span></p>
                <p class="cv-label">Fecha de Emisión: <span class="cv-value"><?= date('d/m/Y', strtotime($pasaporte['fecha_emision'])) ?></span></p>
                <p class="cv-label">Fecha de Expiración: <span class="cv-value"><?= date('d/m/Y', strtotime($pasaporte['fecha_expiracion'])) ?></span></p>
                <p class="cv-label">Archivo:
                    <?php if ($pasaporte['archivo_url']): ?>
                        <a href="../php/<?= htmlspecialchars($pasaporte['archivo_url']) ?>" target="_blank" class="btn btn-sm btn-outline-dark">
                            <i class="bi bi-eye-fill me-1"></i>Ver PDF
                        </a>
                    <?php else: ?>
                        <span class="text-muted">No disponible</span>
                    <?php endif; ?>
                </p>
            <?php else: ?>
                <p class="text-muted">No hay pasaporte registrado.</p>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php include_once("../componentes/footer.php"); ?>
