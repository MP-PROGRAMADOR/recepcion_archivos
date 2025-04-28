<?php include_once("../componentes/header.php"); ?>
<?php

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de estudiante no válido.");
}

$estudiante_id = intval($_GET['id']);

try {
    // Datos personales del estudiante
    $stmt = $pdo->prepare("
      SELECT 
    e.id,
    e.nombre_completo,
    e.codigo_acceso,
    e.fecha_nacimiento,
    e.anio_inicio_carrera,
    e.anio_fin_carrera,
    e.telefono,
    e.creado_en,
    e.pais_id,
    e.email,
    e.ciudad_id,
    e.universidad_id,
    e.idioma_id,
    p.nombre AS nombre_pais,
    c.nombre AS nombre_ciudad,
    u.nombre AS nombre_universidad,
    i.nombre AS nombre_idioma,
    i.meses_duracion AS meses_duracion_idioma
FROM estudiantes e
LEFT JOIN paises p ON e.pais_id = p.id
LEFT JOIN ciudades c ON e.ciudad_id = c.id
LEFT JOIN universidades u ON e.universidad_id = u.id
LEFT JOIN idiomas i ON e.idioma_id = i.id
WHERE e.id = ?


    ");
    $stmt->execute([$estudiante_id]);
    $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$estudiante) {
        die("Estudiante no encontrado.");
    }

    // Notas académicas
    $stmt = $pdo->prepare("
        SELECT aa.nombre AS anio_academico, n.observaciones, n.archivo_url, n.fecha_subida
        FROM notas n
        INNER JOIN anios_academicos aa ON n.anio_academico_id = aa.id
        WHERE n.estudiante_id = ?
        ORDER BY aa.nombre DESC
    ");
    $stmt->execute([$estudiante_id]);
    $notas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pasaporte
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


<?php include_once("../componentes/sidebar.php"); ?>

<main class="content" id="mainContent">
    <div class="cv-container">

        <!-- Encabezado -->
        <div class="cv-header text-center mb-4">
            <h3><i class="bi bi-person-vcard me-2"></i>Ficha del Estudiante</h3>
            <div class="mt-3 d-flex justify-content-center gap-2">
                <a href="estudiantes.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al Listado
                </a>

            </div>
        </div>

        <!-- Información Personal -->
        <div class="cv-section">
            <h5><i class="bi bi-person-circle me-2"></i>Datos Personales</h5>
            <div class="row">
                <div class="col-md-4 text-center mb-3">
                    <?php if (!empty($estudiante['foto_perfil']) && file_exists("../php/" . $estudiante['foto_perfil'])): ?>
                        <img src="../php/<?= htmlspecialchars($estudiante['foto_perfil']) ?>" class="cv-photo img-thumbnail" alt="Foto">
                    <?php else: ?>
                        <div class="text-muted">
                            <i class="bi bi-person-circle fs-1"></i><br>Sin foto
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <p class="cv-label">Código del Estudiante: <span class="cv-value"><?= $estudiante['codigo_acceso'] ?></span></p>
                    <p class="cv-label">Nombre Completo: <span class="cv-value"><?= htmlspecialchars($estudiante['nombre_completo']) ?></span></p>
                    <p class="cv-label">Fecha de Nacimiento: <span class="cv-value"><?= date('d/m/Y', strtotime($estudiante['fecha_nacimiento'])) ?></span></p>

                    <!-- Verificación de Email -->
                    <p class="cv-label">Correo Electronico: <span class="cv-value"><?= !empty($estudiante['email']) ? htmlspecialchars($estudiante['email']) : 'No se ha subido' ?></span></p>

                    <!-- Verificación de Teléfono -->
                    <p class="cv-label">Telefono: <span class="cv-value"><?= !empty($estudiante['telefono']) ? htmlspecialchars($estudiante['telefono']) : 'No se ha subido' ?></span></p>

                    <p class="cv-label">País de Estudios: <span class="cv-value"><?= htmlspecialchars($estudiante['nombre_pais']) ?></span></p>
                    <p class="cv-label">En la Ciudad: <span class="cv-value"><?= htmlspecialchars($estudiante['nombre_ciudad']) ?></span></p>
                    <p class="cv-label">En la Universidad: <span class="cv-value"><?= htmlspecialchars($estudiante['nombre_universidad']) ?></span></p>

                    <!-- Año de Inicio y Año de Finalización -->
                    <p class="cv-label">Año de Inicio: <span class="cv-value"><?= htmlspecialchars($estudiante['anio_inicio_carrera']) ?></span></p>
                    <p class="cv-label">Año de Finalización: <span class="cv-value"><?= htmlspecialchars($estudiante['anio_fin_carrera']) ?></span></p>

                    <!-- Calcular años de carrera -->
                    <?php
                    $anio_inicio = (int)$estudiante['anio_inicio_carrera'];
                    $anio_fin = (int)$estudiante['anio_fin_carrera'];
                    $años_de_carrera = $anio_fin - $anio_inicio;
                    ?>
                    <p class="cv-label">Años de Carrera: <span class="cv-value"><?= $años_de_carrera ?></span></p>

                    <!-- Idioma -->
                    <?php if (!empty($estudiante['nombre_idioma'])): ?>
                        <p class="cv-label">Idioma: <span class="cv-value"><?= htmlspecialchars($estudiante['nombre_idioma']) ?></span></p>
                        <p class="cv-label">Meses de Duración: <span class="cv-value"><?= htmlspecialchars($estudiante['meses_duracion_idioma']) ?></span></p>

                        <!-- Sumar meses de idioma a los años de carrera -->
                        <?php
                        $total_meses = (int)$estudiante['meses_duracion_idioma'];
                        $total_años = $años_de_carrera + ($total_meses / 12);
                        ?>
                        <p class="cv-label">Años de Carrera + Idioma: <span class="cv-value"><?= number_format($total_años, 2) ?> años</span></p>
                    <?php else: ?>
                        <p class="cv-label">Idioma: <span class="cv-value">No escogió idioma</span></p>
                    <?php endif; ?>

                    <p class="cv-label">Fecha de Registro: <span class="cv-value"><?= date('d/m/Y H:i', strtotime($estudiante['creado_en'])) ?></span></p>
                </div>

            </div>
        </div>



        <!-- Documentación Oficial -->
        <div class="cv-section">
            <h5><i class="bi bi-person-vcard-fill"></i>Pasaporte</h5>
            <?php if ($pasaporte): ?>
                <p class="cv-label">Número de Pasaporte: <span class="cv-value"><?= htmlspecialchars($pasaporte['numero_pasaporte']) ?></span></p>
                <p class="cv-label">Fecha de Emisión: <span class="cv-value"><?= date('d/m/Y', strtotime($pasaporte['fecha_emision'])) ?></span></p>
                <p class="cv-label">Fecha de Expiración: <span class="cv-value"><?= date('d/m/Y', strtotime($pasaporte['fecha_expiracion'])) ?></span></p>
                <p class="cv-label">Documento:
                    <?php if ($pasaporte['archivo_url']): ?>
                        <a href="../php/<?= htmlspecialchars($pasaporte['archivo_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye-fill me-1"></i>Ver PDF
                        </a>
                    <?php else: ?>
                        <span class="text-muted">No disponible</span>
                    <?php endif; ?>
                </p>
            <?php else: ?>
                <p class="text-muted">No hay información de pasaporte disponible.</p>
            <?php endif; ?>
        </div>


        <!-- Historial Académico -->
        <div class="cv-section">
            <h5><i class="bi bi-journal-text me-2"></i>Historial Académico</h5>
            <?php if (!empty($notas)): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Año Académico</th>
                                <th>Observaciones</th>
                                <th>Documento</th>
                                <th>Fecha de Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notas as $nota): ?>
                                <tr>
                                    <td><?= htmlspecialchars($nota['anio_academico']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($nota['observaciones'])) ?></td>
                                    <td>
                                        <?php if (!empty($nota['archivo_url'])): ?>
                                            <a href="../php/upload/notas/<?= htmlspecialchars($nota['archivo_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-earmark-text"></i> Ver Documento
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
                <p class="text-muted">No se encontraron registros académicos.</p>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php include_once("../componentes/footer.php"); ?>