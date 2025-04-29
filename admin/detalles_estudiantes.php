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
            e.id, e.nombre_completo, e.codigo_acceso, e.fecha_nacimiento,
            e.anio_inicio_carrera, e.anio_fin_carrera, e.telefono,
            e.creado_en, e.pais_id, e.email, e.ciudad_id,
            e.universidad_id, e.idioma_id, e.foto_perfil,
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

    if (!$estudiante)
        die("Estudiante no encontrado.");

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
    
    <!-- ENCABEZADO -->
    <div class="cv-header text-center mb-4 border-bottom pb-3 position-relative">
        <h3 class="fw-bold text-primary"><i class="bi bi-person-vcard me-2"></i>Ficha del Estudiante</h3>
        <div class="mt-3 d-flex justify-content-center gap-2 flex-wrap">
            <a href="estudiantes.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver al Listado
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer-fill"></i> Imprimir CV
            </button>
        </div>
    </div>
    
    <div class="cv-container container py-4 bg-white shadow rounded" style="max-width: 900px; font-size: 0.95rem;">
        <!-- PERFIL Y CONTACTO -->
        <div class="cv-section mb-4 p-3 rounded bg-light">
            <div class="row align-items-center">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    <?php if (!empty($estudiante['foto_perfil']) && file_exists("../php/upload/perfil/" . $estudiante['foto_perfil'])): ?>
                        <img src="../php/upload/perfil/<?= htmlspecialchars($estudiante['foto_perfil']) ?>" class="cv-photo rounded-circle border shadow-sm" style="width: 120px; height: 120px; object-fit: cover;" alt="Foto">
                    <?php else: ?>
                        <div class="text-muted">
                            <i class="bi bi-person-circle fs-1"></i><br>Sin foto
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-9">
                    <h4 class="fw-bold mb-2"><?= htmlspecialchars($estudiante['nombre_completo']) ?></h4>
                    <p class="mb-1"><i class="bi bi-key-fill me-1 text-secondary"></i><strong>Código:</strong> <?= $estudiante['codigo_acceso'] ?></p>
                    <p class="mb-1"><i class="bi bi-envelope-fill me-1 text-secondary"></i><strong>Correo:</strong> <?= htmlspecialchars($estudiante['email']) ?: 'No registrado' ?></p>
                    <p class="mb-0"><i class="bi bi-telephone-fill me-1 text-secondary"></i><strong>Teléfono:</strong> <?= htmlspecialchars($estudiante['telefono']) ?: 'No registrado' ?></p>
                </div>
            </div>
        </div>

        <!-- INFORMACIÓN ACADÉMICA Y PASAPORTE -->
        <div class="row g-4">
            <!-- INFORMACIÓN ACADÉMICA -->
            <div class="col-lg-6">
                <div class="cv-section border p-3 rounded shadow-sm h-100">
                    <h5 class="text-primary"><i class="bi bi-mortarboard-fill me-2"></i>Información Académica</h5>
                    <p><i class="bi bi-building me-1 text-secondary"></i><strong>Universidad:</strong> <?= htmlspecialchars($estudiante['nombre_universidad']) ?></p>
                    <p><i class="bi bi-geo-alt-fill me-1 text-secondary"></i><strong>País:</strong> <?= htmlspecialchars($estudiante['nombre_pais']) ?></p>
                    <p><i class="bi bi-geo-fill me-1 text-secondary"></i><strong>Ciudad:</strong> <?= htmlspecialchars($estudiante['nombre_ciudad']) ?></p>
                    <p><i class="bi bi-calendar-plus me-1 text-secondary"></i><strong>Inicio:</strong> <span id="inicioCarrera"><?= $estudiante['anio_inicio_carrera'] ?></span></p>
                    <p><i class="bi bi-calendar-check me-1 text-secondary"></i><strong>Fin:</strong> <span id="finCarrera"><?= $estudiante['anio_fin_carrera'] ?></span></p>
                    <?php $años = (int) $estudiante['anio_fin_carrera'] - (int) $estudiante['anio_inicio_carrera']; ?>
                    <p><i class="bi bi-hourglass-split me-1 text-secondary"></i><strong>Duración:</strong> <?= $años ?> años</p>

                    <?php if (!empty($estudiante['nombre_idioma'])): ?>
                        <p><i class="bi bi-translate me-1 text-secondary"></i><strong>Idioma:</strong> <?= $estudiante['nombre_idioma'] ?></p>
                        <p><i class="bi bi-clock-history me-1 text-secondary"></i><strong>Duración Idioma:</strong> <?= $estudiante['meses_duracion_idioma'] ?> meses</p>
                        <p><i class="bi bi-patch-check me-1 text-secondary"></i><strong>Total (incluyendo idioma):</strong> <?= number_format($años + ($estudiante['meses_duracion_idioma'] / 12), 2) ?> años</p>
                    <?php else: ?>
                        <p><i class="bi bi-x-circle me-1 text-secondary"></i><strong>Idioma:</strong> No registrado</p>
                    <?php endif; ?>
                    <p><i class="bi bi-flag-fill me-1 text-secondary"></i><strong>Estado:</strong> <span id="estadoCarrera" class="fw-semibold"></span></p>
                </div>
            </div>

            <!-- PASAPORTE -->
            <div class="col-lg-6">
                <div class="cv-section border p-3 rounded shadow-sm h-100">
                    <h5 class="text-primary"><i class="bi bi-pass-fill me-2"></i>Pasaporte</h5>
                    <?php if ($pasaporte): ?>
                        <p><i class="bi bi-123 me-1 text-secondary"></i><strong>Número:</strong> <?= htmlspecialchars($pasaporte['numero_pasaporte']) ?></p>
                        <p><i class="bi bi-calendar-event me-1 text-secondary"></i><strong>Emisión:</strong> <?= date('d/m/Y', strtotime($pasaporte['fecha_emision'])) ?></p>
                        <p><i class="bi bi-calendar-x me-1 text-secondary"></i><strong>Expiración:</strong> <span id="fechaExpPasaporte"><?= date('d/m/Y', strtotime($pasaporte['fecha_expiracion'])) ?></span></p>
                        <p><i class="bi bi-file-earmark-pdf me-1 text-secondary"></i><strong>Archivo:</strong>
                            <?php if (!empty($pasaporte['archivo_url'])): ?>
                                <a href="../php/<?= htmlspecialchars($pasaporte['archivo_url']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="bi bi-eye-fill"></i> Ver PDF
                                </a>
                            <?php else: ?>
                                <span class="text-muted">No disponible</span>
                            <?php endif; ?>
                        </p>
                        <p><i class="bi bi-flag-fill me-1 text-secondary"></i><strong>Estado:</strong> <span id="estadoPasaporte" class="fw-semibold"></span></p>
                    <?php else: ?>
                        <p class="text-muted">No hay datos de pasaporte.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- HISTORIAL ACADÉMICO -->
        <div class="cv-section mt-4">
            <h5 class="text-primary"><i class="bi bi-journal-text me-2"></i>Historial Académico</h5>
            <?php if (!empty($notas)): ?>
                <div class="table-responsive border rounded shadow-sm">
                    <table class="table table-bordered table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Año Académico</th>
                                <th>Observaciones</th>
                                <th>Documento</th>
                                <th>Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notas as $nota): ?>
                                <tr>
                                    <td><?= htmlspecialchars($nota['anio_academico']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($nota['observaciones'])) ?></td>
                                    <td>
                                        <?php if (!empty($nota['archivo_url'])): ?>
                                            <a href="../php/upload/notas/<?= htmlspecialchars($nota['archivo_url']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">
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
                <p class="text-muted">Sin registros académicos.</p>
            <?php endif; ?>
        </div>

        <!-- FECHA DE REGISTRO -->
        <div class="cv-section text-end text-muted mt-4">
            <small><i class="bi bi-clock me-1"></i>Registro del estudiante: <?= date('d/m/Y H:i', strtotime($estudiante['creado_en'])) ?></small>
        </div>
    </div>
</main>

<!-- JS dinámico -->
<script>
(function calcularCarrera() {
    const inicio = parseInt(document.getElementById('inicioCarrera')?.textContent);
    const fin = parseInt(document.getElementById('finCarrera')?.textContent);
    const actual = new Date().getFullYear();
    const restante = fin - actual;
    const estado = document.getElementById('estadoCarrera');

    if (!estado || isNaN(inicio) || isNaN(fin)) return;

    estado.classList.remove("text-info", "text-warning", "text-success");

    if (restante > 0) {
        estado.textContent = `Faltan ${restante} año(s) para finalizar`;
        estado.classList.add("text-info");
    } else if (restante === 0) {
        estado.textContent = "FINALISTA";
        estado.classList.add("text-warning");
    } else {
        estado.textContent = "Carrera finalizada";
        estado.classList.add("text-success");
    }
})();

(function calcularPasaporte() {
    const fechaExpStr = document.getElementById('fechaExpPasaporte')?.textContent;
    if (!fechaExpStr) return;

    const [dia, mes, anio] = fechaExpStr.split('/');
    const fechaExp = new Date(anio, mes - 1, dia);
    const hoy = new Date();
    const estado = document.getElementById('estadoPasaporte');
    if (!estado) return;

    estado.classList.remove("text-danger", "text-success");

    if (fechaExp < hoy) {
        estado.textContent = "Pasaporte expirado";
        estado.classList.add("text-danger");
    } else {
        const diff = fechaExp.getFullYear() - hoy.getFullYear();
        estado.textContent = `Faltan ${diff} año(s) para expirar`;
        estado.classList.add("text-success");
    }
})();
</script>
 

<?php include_once("../componentes/footer.php"); ?>


<!--  PARA IMPRIMIR CV -->
<style>
    .cv-container {
        max-width: 900px;
        margin: auto;
        background: #fff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    /* Botón imprimir */
    #printBtn {
        position: absolute;
        top: 20px;
        right: 20px;
    }

    /* Encabezado */
    .cv-header {
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 15px;
    }

    .cv-header h3 {
        font-weight: 700;
    }

    /* Foto perfil */
    .cv-photo {
        width: 140px;
        height: 140px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #0d6efd;
    }

    /* Secciones */
    .cv-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
        border-left: 5px solid #0d6efd;
    }

    .cv-section h5 {
        font-weight: 600;
        color: #0d6efd;
        margin-bottom: 15px;
    }

    .cv-section p {
        margin-bottom: 8px;
    }

    /* Tabla */
    table.table {
        font-size: 0.9rem;
    }

    .table td,
    .table th {
        vertical-align: middle !important;
    }

    /* Impresión */
    @media print {
        body {
            background: #fff !important;
            padding: 0;
        }

        .cv-container {
            box-shadow: none;
            border: none;
            padding: 20px;
            margin: 0;
            page-break-inside: avoid;
        }

        #printBtn,
        .btn,
        a.btn {
            display: none !important;
        }
    }
</style>