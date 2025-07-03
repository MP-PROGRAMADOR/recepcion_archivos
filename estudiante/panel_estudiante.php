<?php
session_start();

// Validar sesión
if (!isset($_SESSION['estudiante'], $_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

require_once('../config/conexion.php');

$id_estudiante = $_SESSION['id'];
$codigo_acceso = $_SESSION['estudiante']['codigo_acceso'];

try {
    // Verificar si el estudiante tiene una foto de perfil
    $stmt = $pdo->prepare("SELECT foto_perfil FROM estudiantes WHERE id = ?");
    $stmt->execute([$id_estudiante]);
    $fotoPerfil = $stmt->fetchColumn();

    if ($fotoPerfil === false) {
        // Estudiante no encontrado
        header("Location: index.php");
        exit;
    }

    if (empty($fotoPerfil)) {
        // Sin foto de perfil
        header("Location: perfil.php");
        exit;
    }

    // Obtener datos del estudiante
    $stmt = $pdo->prepare("SELECT 
    e.id, 
    e.codigo_acceso, 
    e.nombre_completo, 
    e.fecha_nacimiento, 
    e.creado_en, 
    e.email, 
    e.telefono, 
    e.foto_perfil, 
    p.nombre AS pais,
    c.nombre AS ciudad
FROM estudiantes e
INNER JOIN paises p ON e.pais_id = p.id
INNER JOIN ciudades c ON e.ciudad_id = c.id
WHERE e.codigo_acceso = ?;
    ");





    $stmt->execute([$codigo_acceso]);
    $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$estudiante) {
        throw new Exception("Estudiante no encontrado.");
    }

    $estudiante_id = $estudiante['id'];

    // Obtener pasaporte del estudiante
    $stmt = $pdo->prepare("SELECT * FROM pasaportes WHERE estudiante_id = ?");
    $stmt->execute([$estudiante_id]);
    $pasaporte = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener archivos de notas
    $stmt = $pdo->prepare("
        SELECT 'Nota' AS tipo, id, archivo_url, fecha_subida
        FROM notas
        WHERE estudiante_id = ?
    ");
    $stmt->execute([$estudiante_id]);
    $notas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener archivos de pasaportes
    $stmt = $pdo->prepare("
        SELECT 'Pasaporte' AS tipo, id, archivo_url, fecha_subida
        FROM pasaportes
        WHERE estudiante_id = ?
    ");
    $stmt->execute([$estudiante_id]);
    $pasaportes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Combinar y ordenar archivos por fecha
    $archivos = array_merge($notas, $pasaportes);
    usort($archivos, function ($a, $b) {
        return strtotime($b['fecha_subida']) - strtotime($a['fecha_subida']);
    });

    // Verificar si el estudiante ya subió un pasaporte
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pasaportes WHERE estudiante_id = ?");
    $stmt->execute([$estudiante_id]);
    $pasaporteExiste = $stmt->fetchColumn();

    /* -----------datos bancarios--------- */

    // Obtener cuenta bancaria del estudiante
    $stmt = $pdo->prepare("SELECT * FROM cuentas_bancarias WHERE estudiante_id = :id LIMIT 1");
    $stmt->execute([':id' => $estudiante_id]);
    $cuenta_bancaria = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Manejo de errores generales
    error_log("Error: " . $e->getMessage());
    echo "Ocurrió un error al cargar los datos. Inténtalo más tarde.";
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Panel de usuario - <?= htmlspecialchars($estudiante['nombre_completo']) ?>. Actualiza tu perfil y sube archivos importantes.">
    <meta name="robots" content="index, follow">
    <title>Panel - <?= htmlspecialchars($estudiante['nombre_completo']) ?></title>

    <!-- Enlace al CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Mejoras en el estilo y seguridad -->
    <style>
        body {
            padding-top: 50px;
        }

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

<body class="bg-light">
    <canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-mortarboard-fill me-2"></i>
                Usuario: <?= htmlspecialchars($estudiante['nombre_completo']) ?>
            </a>

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
                        <a id="btnSubirNotas" class="nav-link text-white" href="#">
                            <i class="bi bi-upload me-1"></i> Subir Notas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a id="btnRegistrarIdioma" class="nav-link text-white" href="#">
                            <i class="bi bi-upload me-1"></i> Registrar Idioma
                        </a>
                    </li>
                    <!--   <li class="nav-item">
          <a id="btnActualizarIdioma" class="nav-link text-white" href="#">
            <i class="bi bi-upload me-1"></i> Actualizar Idioma
          </a>
        </li> -->

                    <li class="nav-item">
                        <a id="btnActualizarContacto" class="nav-link text-white" href="#">
                            <i class="bi bi-person-lines-fill me-1"></i> Actualizar Contactos
                        </a>
                    </li>

                    <li class="nav-item">
                        <a id="btnActualizarPerfil" class="nav-link text-white" href="#">
                            <i class="bi bi-person-circle me-1"></i> Actualizar Perfil
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-white" href="../php/logout_estudiante.php">
                            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>



    <div class="container my-4 ">

        <?php include_once('alerta.php') ?>
        <!-- FIN DE LA ALERTA -->


        <div class="row g-4">
            <!-- Columna de información del estudiante -->
            <div class="col-md-6">
                <div class="accordion" id="accordionEstudiante">

                    <!-- Información Personal -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingPerfil">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapsePerfil" aria-expanded="true" aria-controls="collapsePerfil">
                                <i class="bi bi-person-fill me-2"></i> Información Personal
                            </button>
                        </h2>
                        <div id="collapsePerfil" class="accordion-collapse collapse show"
                            aria-labelledby="headingPerfil" data-bs-parent="#accordionEstudiante">
                            <div class="accordion-body">

                                <?php
                                $fotoRuta = '../php/upload/perfil/' . $estudiante['foto_perfil'];
                                if (!empty($estudiante['foto_perfil']) && file_exists($fotoRuta)): ?>
                                    <img src="<?= $fotoRuta ?>" alt="Foto de perfil" class="foto-estudiante img-fluid rounded mb-3">
                                <?php else: ?>
                                    <p class="text-muted">No se ha subido ningún perfil</p>
                                <?php endif; ?>


                                <div class="mb-2">
                                    <label class="form-label">Código de acceso:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                        <input type="text" class="form-control"
                                            value="<?= htmlspecialchars($estudiante['codigo_acceso']) ?>" disabled>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Nombre completo:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                                        <input type="text" class="form-control"
                                            value="<?= htmlspecialchars($estudiante['nombre_completo']) ?>" disabled>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Fecha de nacimiento:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-date-fill"></i></span>
                                        <input type="text" class="form-control"
                                            value="<?= htmlspecialchars($estudiante['fecha_nacimiento']) ?>" disabled>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">País:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-globe-americas"></i></span>
                                        <input type="text" class="form-control"
                                            value="<?= htmlspecialchars($estudiante['pais']) ?>" disabled>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Ciudad:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                        <input type="text" class="form-control"
                                            value="<?= htmlspecialchars($estudiante['ciudad']) ?>" disabled>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Registrado en:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-clock-history"></i></span>
                                        <input type="text" class="form-control"
                                            value="<?= date('d/m/Y H:i', strtotime($estudiante['creado_en'])) ?>"
                                            disabled>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Correo:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                        <input type="text" class="form-control"
                                            value="<?= htmlspecialchars($estudiante['email']) ?>" disabled>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Teléfono:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                        <input type="text" class="form-control"
                                            value="<?= htmlspecialchars($estudiante['telefono']) ?>" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Bancaria -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingBanco">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseBanco" aria-expanded="false" aria-controls="collapseBanco">
                                <i class="bi bi-bank me-2"></i> Información Bancaria
                            </button>
                        </h2>
                        <div id="collapseBanco" class="accordion-collapse collapse" aria-labelledby="headingBanco"
                            data-bs-parent="#accordionEstudiante">
                            <div class="accordion-body">

                                <div class="mb-2">
                                    <label class="form-label">Banco:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-bank2"></i></span>
                                        <input type="text" class="form-control"
                                            value="<?= htmlspecialchars($cuenta_bancaria['banco']) ?>" disabled>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Nº Cuenta:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                        <input type="text" class="form-control"
                                            value="<?= htmlspecialchars($cuenta_bancaria['numero_cuenta']) ?>" disabled>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Tarjeta Visa:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-credit-card-2-front"></i></span>
                                        <input type="text" class="form-control"
                                            value="<?= !empty($cuenta_bancaria['tarjeta_visa']) ? htmlspecialchars($cuenta_bancaria['tarjeta_visa']) : 'No dispone' ?>"
                                            disabled>
                                    </div>
                                </div>

                                <?php if (!empty($cuenta_bancaria['tarjeta_visa'])): ?>
                                    <div class="mb-2">
                                        <label class="form-label">Fecha de caducidad:</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-calendar-check-fill"></i></span>
                                            <input type="text" class="form-control"
                                                value="<?= !empty($cuenta_bancaria['fecha_caducidad_tarjeta']) ? htmlspecialchars($cuenta_bancaria['fecha_caducidad_tarjeta']) : 'No disponible' ?>"
                                                disabled>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Columna de archivos subidos -->
            <div class="col-md-6">
                <div class="accordion" id="accordionArchivos">

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingInfo-box">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseInfo-box" aria-expanded="false"
                                aria-controls="collapseInfo-box">
                                <i class="bi bi-files me-2"></i> Información Archivos Subidos
                            </button>
                        </h2>
                        <div id="collapseInfo-box" class="accordion-collapse collapse" aria-labelledby="headingInfo-box"
                            data-bs-parent="#accordionArchivos">
                            <div class="accordion-body">



                                <div class="info-box">
                                    <?php if (!empty($archivos)): ?>
                                        <div class="shadow-sm border-0">
                                            <div class="card-header bg-white border-bottom-0">
                                                <h5 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Archivos subidos
                                                </h5>
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
                                                                                class="file-preview rounded border img-fluid">
                                                                        <?php else: ?>
                                                                            <?= basename($archivo['archivo_url']) ?>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td><?= date('d/m/Y H:i', strtotime($archivo['fecha_subida'])) ?>
                                                                    </td>
                                                                    <td>
                                                                        <a href="../php/upload/<?= $archivo['tipo'] === 'Nota' ? 'notas' : 'pasaportes' ?>/<?= htmlspecialchars($archivo['archivo_url']) ?>"
                                                                            class="btn btn-outline-primary btn-sm" download>
                                                                            <i class="bi bi-download"></i> Descargar PDF
                                                                        </a>
                                                                    </td>
                                                                </tr>
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
                    </div>
                </div>
            </div>
        </div>
        <!-- **************************** FORMULAIOS DINAMICOS ***************************** -->

        <div class="row g-4 mt-5">
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

    </div>

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
                        <li><i class="bi bi-send-fill text-success me-2"></i> Dale al botón
                            <strong>Enviar</strong>.
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
        document.addEventListener("DOMContentLoaded", function() {
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
                eliminarBtn.addEventListener("click", function() {
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
                btnSubirPasaporte.addEventListener("click", function() {
                    cargarFormulario('formulario_pasaporte.php');

                });
            }

            // Botón subir notas
            if (btnSubirNotas) {
                btnSubirNotas.addEventListener("click", function() {
                    cargarFormulario('formulario_notas.php');
                });
            }
            //boton actualizar_pasaporte
            if (btnActualizarPasaporte) {
                btnActualizarPasaporte.addEventListener("click", function() {
                    cargarFormulario('formulario_actualizar_pasaporte.php');
                });
            }
            //boton actualizar_pasaporte
            if (btnActualizarContacto) {
                btnActualizarContacto.addEventListener("click", function() {
                    cargarFormulario('formulario_actualizar_contacto.php');
                });
            }
            //boton actualizar_pasaporte
            if (btnActualizarPerfil) {
                btnActualizarPerfil.addEventListener("click", function() {
                    cargarFormulario('formulario_actualizar_perfil.php');
                });
            }
            //boton actuRegistrarIdioma
            if (btnRegistrarIdioma) {
                btnRegistrarIdioma.addEventListener("click", function() {
                    cargarFormulario('formulario_registrar_idioma.php');
                });
            }
            //boton actuActualizarIdioma
            if (btnActualizarIdioma) {
                btnActualizarIdioma.addEventListener("click", function() {
                    cargarFormulario('formulario_actualizar_idioma.php');
                });
            }


        });
    </script>

    <script src="../config/js/canva.js"></script>
</body>

</html>