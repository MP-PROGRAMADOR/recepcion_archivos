<?php
session_start();
if (!isset($_SESSION['estudiante'])) {
    header("Location: index.php");
    exit();
}

$estudiantes = $_SESSION['estudiante'];
include_once('../config/conexion.php');

// Consultar datos del estudiante
$stmt = $pdo->prepare("SELECT e.id, e.codigo_acceso, e.nombre_completo, e.fecha_nacimiento, e.creado_en, e.ruta_foto, p.nombre AS pais 
                       FROM estudiantes e 
                       INNER JOIN paises p ON e.pais_id = p.id 
                       WHERE e.codigo_acceso = ?");
$stmt->execute([$estudiantes['codigo_acceso']]);
$estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$estudiante) {
    die("Estudiante no encontrado.");
}

$estudiante_id = $estudiante['id'];

// Consultar archivos de notas
$stmt_notas = $pdo->prepare("SELECT 'Nota' AS tipo, id, archivo_url, fecha_subida 
                             FROM notas 
                             WHERE estudiante_id = ? 
                             ORDER BY fecha_subida DESC");
$stmt_notas->execute([$estudiante_id]);
$notas = $stmt_notas->fetchAll(PDO::FETCH_ASSOC);

// Consultar archivos de pasaportes
$stmt_pasaportes = $pdo->prepare("SELECT 'Pasaporte' AS tipo, id, archivo_url, fecha_subida 
                                  FROM pasaportes 
                                  WHERE estudiante_id = ? 
                                  ORDER BY fecha_subida DESC");
$stmt_pasaportes->execute([$estudiante_id]);
$pasaportes = $stmt_pasaportes->fetchAll(PDO::FETCH_ASSOC);

// Combinar ambos arrays en uno solo
$archivos = array_merge($notas, $pasaportes);

// Ordenar por fecha_subida descendente
usort($archivos, function ($a, $b) {
    return strtotime($b['fecha_subida']) - strtotime($a['fecha_subida']);
});
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel - <?= htmlspecialchars($estudiante['nombre_completo']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .foto-estudiante {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .form-label {
            font-weight: 600;
        }

        .file-preview {
            max-width: 100px;
            max-height: 100px;
            object-fit: contain;
        }

        .nav-link-logout {
            color: white;
            text-decoration: none;
        }

        .nav-link-logout:hover {
            text-decoration: underline;
        }

        .form-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 2rem;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="bi bi-mortarboard-fill me-2"></i> Usuario:
                <?= htmlspecialchars($estudiante['nombre_completo']) ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a id="btnSubirPasaporte" class="nav-link" href="#"><i class="bi bi-upload me-1"></i>Subir
                            Pasaporte</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-upload me-1"></i>Subir Notas</a>
                    </li>

                </ul>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link text-white" href="logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar
                            sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container my-4 ">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="info-box d-flex">
                    <img src="../php/<?= htmlspecialchars($estudiante['ruta_foto']) ?>" alt="Foto"
                        class="foto-estudiante me-3">
                    <div class="w-100">
                        <div class="mb-2">
                            <label class="form-label">Código de acceso:</label>
                            <input type="text" class="form-control"
                                value="<?= htmlspecialchars($estudiante['codigo_acceso']) ?>" disabled>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Nombre completo:</label>
                            <input type="text" class="form-control"
                                value="<?= htmlspecialchars($estudiante['nombre_completo']) ?>" disabled>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Fecha de nacimiento:</label>
                            <input type="text" class="form-control"
                                value="<?= htmlspecialchars($estudiante['fecha_nacimiento']) ?>" disabled>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">País:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($estudiante['pais']) ?>"
                                disabled>
                        </div>
                        <div>
                            <label class="form-label">Registrado en:</label>
                            <input type="text" class="form-control"
                                value="<?= date('d/m/Y H:i', strtotime($estudiante['creado_en'])) ?>" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box">
                    <?php if (!empty($archivos)): ?>
                        <div class=" shadow-sm border-0">
                            <div class="card-header bg-white border-bottom-0">
                                <h5 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Archivos subidos</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Archivo</th>
                                                <th>Fecha</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($archivos as $archivo):
                                                $ext = pathinfo($archivo['archivo_url'], PATHINFO_EXTENSION);
                                                $esImagen = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                $esPDF = strtolower($ext) === 'pdf';
                                                ?>
                                                <tr>
                                                    <td><span
                                                            class="badge bg-secondary"><?= htmlspecialchars($archivo['tipo']) ?></span>
                                                    </td>
                                                    <td>
                                                        <?php if ($esImagen): ?>
                                                            <img src="../php/<?= $archivo['archivo_url'] ?>"
                                                                class="file-preview rounded border">
                                                        <?php else: ?>
                                                            <?= basename($archivo['archivo_url']) ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= date('d/m/Y H:i', strtotime($archivo['fecha_subida'])) ?></td>
                                                    <td>
                                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#modalArchivo<?= $archivo['id'] ?>">
                                                            <i class="bi bi-eye"></i> Ver
                                                        </button>
                                                    </td>
                                                </tr>

                                                <!-- Modal -->
                                                <div class="modal fade" id="modalArchivo<?= $archivo['id'] ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Detalles del Archivo</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="accordion" id="accordion<?= $archivo['id'] ?>">
                                                                    <div class="accordion-item">
                                                                        <h2 class="accordion-header">
                                                                            <button class="accordion-button collapsed"
                                                                                type="button" data-bs-toggle="collapse"
                                                                                data-bs-target="#collapse<?= $archivo['id'] ?>">
                                                                                Información del archivo
                                                                            </button>
                                                                        </h2>
                                                                        <div id="collapse<?= $archivo['id'] ?>"
                                                                            class="accordion-collapse collapse">
                                                                            <div class="accordion-body">
                                                                                <p><strong>ID Estudiante:</strong>
                                                                                    <?= $estudiante['id'] ?></p>
                                                                                <p><strong>Nombre:</strong>
                                                                                    <?= $estudiante['nombre_completo'] ?></p>
                                                                                <p><strong>Tipo:</strong>
                                                                                    <?= htmlspecialchars($archivo['tipo']) ?>
                                                                                </p>
                                                                                <p><strong>Fecha:</strong>
                                                                                    <?= date('d/m/Y H:i', strtotime($archivo['fecha_subida'])) ?>
                                                                                </p>
                                                                                <p><strong>Archivo:</strong>
                                                                                    <?= basename($archivo['archivo_url']) ?></p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <?php if ($esImagen): ?>
                                                                    <img src="../php/<?= $archivo['archivo_url'] ?>"
                                                                        class="img-fluid border rounded" alt="Vista previa">
                                                                    <a href="../php/<?= $archivo['archivo_url'] ?>"
                                                                        class="btn btn-success mt-3" download>Descargar Imagen</a>
                                                                <?php elseif ($esPDF): ?>
                                                                    <iframe src="../php/<?= $archivo['archivo_url'] ?>" width="100%"
                                                                        height="400px"></iframe>
                                                                    <a href="../php/<?= $archivo['archivo_url'] ?>"
                                                                        class="btn btn-danger mt-3" download>Descargar PDF</a>
                                                                <?php else: ?>
                                                                    <a href="../php/<?= $archivo['archivo_url'] ?>"
                                                                        class="btn btn-secondary mt-3" download>Descargar
                                                                        Archivo</a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle me-2"></i>No se han subido archivos aún.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row g-4"> 

            <button id="eliminarBtn" class="btn btn-danger m-2" style="display: none;"> <i class="bi bi-trash"></i> Eliminar Formulario</button>
            <div id="formularioPasaporte" class="mt-3"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const contenedor = document.getElementById("formularioPasaporte");
            const agregarBtn = document.getElementById("btnSubirPasaporte");
            const eliminarBtn = document.getElementById("eliminarBtn");

            agregarBtn.addEventListener("click", function () {
                if (!contenedor.hasChildNodes()) {
                    contenedor.innerHTML = `
                    
                    <!-- Formulario de pasaporte -->
                    <div class="form-section border rounded p-4 shadow-sm bg-white">
                        <h5 class="d-flex justify-content-between mb-4 text-primary">
                            <i class="bi bi-passport me-2"></i>Formulario de Pasaporte
                        </h5>
                        <form action="guardar_pasaporte.php" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <!-- Número de Pasaporte -->
                                <div class="col-md-6 mb-4">
                                    <label for="numero" class="form-label fw-semibold text-dark">N° de Pasaporte</label>
                                    <input type="text" name="numero" id="numero" class="form-control border rounded shadow-sm" required placeholder="Ej: A12345678">
                                </div>

                                <!-- Imagen del pasaporte -->
                                <div class="col-md-6 mb-4">
                                    <label for="archivo" class="form-label">Selecciona archivo (PDF, JPG, PNG)</label>
                                    <input type="file" name="archivo" id="archivo" class="form-control" required>
                                    <small class="text-muted">Solo se permiten archivos .jpg, .jpeg o .png</small>
                                </div>

                                <!-- Fecha de emisión -->
                                <div class="col-md-6 mb-4">
                                    <label for="fecha_emision" class="form-label fw-semibold text-dark">Fecha de Emisión</label>
                                    <input type="date" name="fecha_emision" id="fecha_emision" class="form-control border rounded shadow-sm" required>
                                </div>

                                <!-- Fecha de expiración -->
                                <div class="col-md-6 mb-4">
                                    <label for="fecha_expiracion" class="form-label fw-semibold text-dark">Fecha de Expiración</label>
                                    <input type="date" name="fecha_expiracion" id="fecha_expiracion" class="form-control border rounded shadow-sm" required>
                                </div>
                            </div>

                            <!-- Botón guardar -->
                            <button type="submit" class="btn btn-success px-4 shadow-sm">
                                <i class="bi bi-save me-2"></i>Guardar pasaporte
                            </button>
                        </form>
                    </div>
                `;
                }

                if (contenedor.children.length > 0) {
                    eliminarBtn.style.display = "inline-block";
                }
            });

            eliminarBtn.addEventListener("click", function () {
                contenedor.innerHTML = "";
                eliminarBtn.style.display = "none";
            });
        });

        function eliminarFormularioClase() {
            const contenedor = document.getElementById("formularioPasaporte");
            contenedor.innerHTML = "";
            document.getElementById("eliminarBtn").style.display = "none";
        }
    </script>







</body>

</html>