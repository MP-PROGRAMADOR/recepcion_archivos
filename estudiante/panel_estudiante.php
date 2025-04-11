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

// Obtener los datos del pasaporte
$stmt = $pdo->prepare("SELECT * FROM pasaportes WHERE estudiante_id = :estudiante_id");
$stmt->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
$stmt->execute();

// Obtener los resultados
$pasaporte = $stmt->fetch(PDO::FETCH_ASSOC);






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


/* Verificar que el usuario ya inguesó un pasaporte */
// Comprobar si el estudiante ya ha subido un pasaporte
$verifica = $pdo->prepare("SELECT COUNT(*) FROM pasaportes WHERE estudiante_id = :estudiante_id");
$verifica->bindParam(':estudiante_id', $estudiante_id);
$verifica->execute();
$pasaporteExiste = $verifica->fetchColumn();

 





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
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="bi bi-mortarboard-fill me-2"></i> Usuario:
                <?= htmlspecialchars($estudiante['nombre_completo']) ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>


            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php if ($pasaporteExiste): ?>
                        <li class="nav-item">
                            <a id="btnActualizarPasaporte" class="nav-link text-white" href="#">
                                <i class="bi bi-pencil-square me-1"></i> Actualizar Pasaporte
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a id="btnSubirPasaporte" class="nav-link text-white" href="#">
                                <i class="bi bi-upload me-1"></i> Subir Pasaporte
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a id="btnSubirNotas" class="nav-link text-white" href="#"><i
                                class="bi bi-upload me-1"></i>Subir Notas</a>

                    </li>

                </ul>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link text-white" href="../php/logout_estudiante.php"><i
                                class="bi bi-box-arrow-right"></i> Cerrar
                            sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container my-4 ">

        <!-- INICIO DE LA ALERTA DE ERRORRES -->
        <?php


        if (isset($_SESSION['errores']) && is_array($_SESSION['errores'])):
            ?>
            <div id="alerta-errores"
                class="alert alert-danger alert-dismissible shadow-sm fade show d-flex align-items-start gap-2 p-3 mt-3 border border-danger-subtle rounded-3"
                role="alert" style="animation: fadeIn 0.5s ease-in-out;">
                <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0 mt-1"></i>
                <div>
                    <strong>Se detectaron errores:</strong>
                    <ul class="mb-0 mt-1">
                        <?php foreach ($_SESSION['errores'] as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="button" class="btn-close ms-auto mt-1" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>

            <script>
                // Ocultar automáticamente luego de 6 segundos
                setTimeout(() => {
                    const alerta = document.getElementById('alerta-errores');
                    if (alerta) {
                        alerta.classList.remove('show');
                        alerta.classList.add('fade');
                        setTimeout(() => alerta.remove(), 500); // Lo remueve del DOM
                    }
                }, 9000);
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
            unset($_SESSION['errores']); // Limpiar errores de la sesión
        endif;
        ?>


        <!-- FIN DE LA ALERTA -->


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
                        <div class="shadow-sm border-0">
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
                                                            <img src="../php/upload/<?= $archivo['tipo'] === 'Nota' ? 'notas' : 'pasaportes' ?>/<?= $archivo['archivo_url'] ?>"
                                                                class="file-preview rounded border">
                                                        <?php else: ?>
                                                            <?= basename($archivo['archivo_url']) ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= date('d/m/Y H:i', strtotime($archivo['fecha_subida'])) ?></td>
                                                    <td>
                                                        <!-- En pantallas grandes y medianas, mostrar el botón de modal -->
                                                        <button class="btn btn-outline-primary btn-sm d-none d-md-inline-block"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalArchivo<?= $archivo['id'] ?>">
                                                            <i class="bi bi-eye"></i> Ver
                                                        </button>
                                                        <!-- En dispositivos móviles, mostrar enlace de descarga con icono -->
                                                        <a href="../php/upload/<?= $archivo['tipo'] === 'Nota' ? 'notas' : 'pasaportes' ?>/<?= htmlspecialchars($archivo['archivo_url']) ?>"
                                                            class="btn btn-outline-primary btn-sm d-block d-md-none" download>
                                                            <i class="bi bi-download"></i> Descargar PDF
                                                        </a>
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
                                                                                <div class="row">
                                                                                    <!-- Columna 1: Información del Estudiante -->
                                                                                    <div class="col-md-6">
                                                                                        <p><strong>ID Estudiante:</strong>
                                                                                            <?= $estudiantes['codigo_acceso'] ?>
                                                                                        </p>
                                                                                        <p><strong>Nombre:</strong>
                                                                                            <?= $estudiante['nombre_completo'] ?>
                                                                                        </p>
                                                                                        <p><strong>Tipo:</strong>
                                                                                            <?= htmlspecialchars($archivo['tipo']) ?>
                                                                                        </p>
                                                                                        <p><strong>Fecha:</strong>
                                                                                            <?= date('d/m/Y H:i', strtotime($archivo['fecha_subida'])) ?>
                                                                                        </p>
                                                                                        <p><strong>Archivo:</strong>
                                                                                            <?= basename($archivo['archivo_url']) ?>
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer d-flex justify-content-center">
                                                                <a href="../php/upload/<?= $archivo['tipo'] === 'Nota' ? 'notas' : 'pasaportes' ?>/<?= htmlspecialchars($archivo['archivo_url']) ?>"
                                                                    class="btn btn-success" target="_blank">
                                                                    <i class="bi bi-eye"></i> Ver PDF
                                                                </a>
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


        <div class="row mt-5">

            <div class="  border-0 rounded-4">
                <div class="card-body text-end">
                    <button id="eliminarBtn" class="btn btn-danger btn-lg px-4 py-2 rounded-pill"
                        style="display: none;">
                        <i class="bi bi-trash me-2"></i> Eliminar Formulario
                    </button>
                </div>
                <div id="formularioPasaporteNota" class="colum-12 "></div>
            </div>


        </div>


        <!--  <div class="row g-4">
            <div class="card">

                <button id="eliminarBtn" class="btn btn-danger " style="display: none;">
                    <i class="bi bi-trash"></i> Eliminar Formulario
                </button>
            </div>
            <div id="formularioPasaporteNota" class="row"></div>
        </div>
 -->
        <!-- MODAL PARA ORIENTAR AL USUARIO EN CASO DE ESTAR ACTIVO UN FORMULARIO Y SOLICITA OTRO -->
        <div class="modal fade" id="modalPasosFormulario" tabindex="-1" aria-labelledby="modalPasosLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-warning shadow">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title" id="modalPasosLabel">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> ¡Atención!
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-dark">
                        <p><i class="bi bi-info-circle-fill me-2 text-primary"></i> Pasos para solicitar un nuevo
                            formulario:</p>
                        <ol class="ps-3">
                            <li><i class="bi bi-pencil-fill text-secondary me-2"></i> Rellene el formulario que
                                solicitaste previamente.</li>
                            <li><i class="bi bi-send-fill text-success me-2"></i> Dale al botón <strong>Enviar</strong>.
                            </li>
                            <li><i class="bi bi-trash-fill text-danger me-2"></i> Da clic sobre el botón
                                <strong>Eliminar formulario</strong>.
                            </li>
                            <li><i class="bi bi-plus-circle-fill text-info me-2"></i> Y finalmente llama al nuevo
                                formulario.</li>
                        </ol>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-warning" data-bs-dismiss="modal">
                            <i class="bi bi-check-circle-fill me-1"></i>Entendido
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const contenedor = document.getElementById("formularioPasaporteNota");
                const eliminarBtn = document.getElementById("eliminarBtn");
                const btnSubirPasaporte = document.getElementById("btnSubirPasaporte");
                const btnSubirNotas = document.getElementById("btnSubirNotas");
                const btnActualizarPasaporte = document.getElementById("btnActualizarPasaporte");

                function mostrarModalPasos() {
                    const modal = new bootstrap.Modal(document.getElementById('modalPasosFormulario'));
                    modal.show();
                }

                // Eliminar formulario
                if (eliminarBtn) {
                    eliminarBtn.addEventListener("click", function () {
                        contenedor.innerHTML = "";
                        eliminarBtn.style.display = "none";
                    });
                }

                // Función general para cargar formularios
                function cargarFormulario(ruta) {
                    if (!contenedor.hasChildNodes()) {
                        fetch(ruta)
                            .then(response => {
                                if (!response.ok) throw new Error('Error al cargar el formulario');
                                return response.text();
                            })
                            .then(html => {
                                contenedor.innerHTML = html;
                                eliminarBtn.style.display = "inline-block";
                            })
                            .catch(error => {
                                contenedor.innerHTML = `<div class="alert alert-danger">Hubo un problema al cargar el formulario: ${error.message}</div>`;
                            });
                    } else {
                        mostrarModalPasos();
                    }
                }

                // Botón subir pasaporte
                if (btnSubirPasaporte) {
                    btnSubirPasaporte.addEventListener("click", function () {
                        cargarFormulario('formulario_pasaporte.php');

                    });
                }

                // Botón subir notas
                if (btnSubirNotas) {
                    btnSubirNotas.addEventListener("click", function () {
                        cargarFormulario('formulario_notas.php');
                    });
                }
                //boton actualizar_pasaporte
                if (btnActualizarPasaporte) {
                    btnActualizarPasaporte.addEventListener("click", function () {
                        cargarFormulario('formulario_actualizar_pasaporte.php');
                    });
                }


            });
        </script>












</body>

</html>