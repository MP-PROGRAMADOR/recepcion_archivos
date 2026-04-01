<?php
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

// =========================
// PAGINACIÓN
// =========================
$por_pagina = 20;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina_actual > 1) ? ($pagina_actual * $por_pagina) - $por_pagina : 0;

// =========================
// FILTROS
// =========================
$tipoFiltro  = $_GET['tipo'] ?? '';
$valorFiltro = $_GET['valor'] ?? '';

$where = [];
$params = [];

if (!empty($valorFiltro)) {

    if ($tipoFiltro === 'nombre') {
        $where[] = "e.nombre_completo LIKE :valor";
        $params[':valor'] = "%$valorFiltro%";
    }

    if ($tipoFiltro === 'pais') {
        $where[] = "p.nombre LIKE :valor";
        $params[':valor'] = "%$valorFiltro%";
    }

    if ($tipoFiltro === 'ciudad') {
        $where[] = "c.nombre LIKE :valor";
        $params[':valor'] = "%$valorFiltro%";
    }

    if ($tipoFiltro === 'fecha_fin') {
        $where[] = "e.anio_fin_carrera = :valor";
        $params[':valor'] = $valorFiltro;
    }
}

$whereSQL = "";
if (!empty($where)) {
    $whereSQL = "WHERE " . implode(" AND ", $where);
}

// =========================
// ORDENACIÓN
// =========================
$orderSQL = "ORDER BY e.creado_en DESC";

if ($tipoFiltro === 'orden_az') {
    $orderSQL = "ORDER BY e.nombre_completo ASC";
}

if ($tipoFiltro === 'orden_za') {
    $orderSQL = "ORDER BY e.nombre_completo DESC";
}

// =========================
// CONTAR REGISTROS FILTRADOS
// =========================
try {
    $sqlTotal = "SELECT COUNT(*) as total
                 FROM estudiantes e
                 INNER JOIN paises p ON e.pais_id = p.id
                 LEFT JOIN ciudades c ON e.ciudad_id = c.id
                 $whereSQL";

    $total_stmt = $pdo->prepare($sqlTotal);
    $total_stmt->execute($params);
    $total_filas = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_paginas = ceil($total_filas / $por_pagina);
} catch (PDOException $e) {
    die("Error al contar los estudiantes: " . $e->getMessage());
}

// =========================
// OBTENER ESTUDIANTES
// =========================
try {

    $sql = "SELECT 
                e.id,
                e.nombre_completo,
                e.anio_inicio_carrera,
                e.anio_fin_carrera,
                e.telefono,
                e.codigo_acceso,
                e.foto_perfil,
                e.fecha_nacimiento,
                e.creado_en,
                p.nombre AS pais,
                c.nombre AS ciudad,
                e.ruta_foto,
                e.archivo_beca
            FROM estudiantes e
            INNER JOIN paises p ON e.pais_id = p.id
            LEFT JOIN ciudades c ON e.ciudad_id = c.id
            $whereSQL
            $orderSQL
            ";

    $stmt = $pdo->prepare($sql);

    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }



    $stmt->execute();
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los estudiantes: " . $e->getMessage());
}

// Layout común
include_once("../componentes/sidebar.php");

$rol = $_SESSION['usuario_rol'];
?>



<main class="content" id="mainContent">


    <canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
    <div class="container-fluid">


        <?php if (!empty($_SESSION['mensaje'])):

            $tipo = $_SESSION['tipo_mensaje'] ?? 'info';

            // Íconos según tipo
            $iconos = [
                'success' => 'bi-check-circle-fill',
                'danger'  => 'bi-x-circle-fill',
                'warning' => 'bi-exclamation-triangle-fill',
                'info'    => 'bi-info-circle-fill'
            ];

            $icono = $iconos[$tipo] ?? 'bi-info-circle-fill';
        ?>

            <div id="alerta-sesion"
                class="alert alert-<?= htmlspecialchars($tipo) ?> alert-dismissible fade show mt-3 d-flex align-items-center gap-2"
                role="alert">

                <i class="bi <?= $icono ?> fs-5"></i>

                <div>
                    <?= htmlspecialchars($_SESSION['mensaje']) ?>
                </div>

                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>

            <script>
                setTimeout(function() {
                    const alerta = document.getElementById('alerta-sesion');
                    if (alerta) {
                        alerta.classList.remove('show');
                        alerta.classList.add('fade');
                        setTimeout(() => alerta.remove(), 500);
                    }
                }, 6000);
            </script>

        <?php
            unset($_SESSION['mensaje']);
            unset($_SESSION['tipo_mensaje']);
        endif;
        ?>




        <!-- FIN DE LA ALERTA -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="bi bi-mortarboard-fill me-2"></i>Listado de Estudiantes</h3>
            <a href="registrar_estudiantes.php" class="btn btn-primary rounded-3">
                <i class="bi bi-person-plus-fill me-1"></i> Nuevo Estudiante
            </a>
        </div>

        <div class="card shadow rounded-4">
            <div class="card-body">


                <div class="row mb-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filtrar por</label>
                        <select id="tipoFiltro" class="form-select" onchange="controlFiltroUI()">
                            <option value="nombre">Nombre</option>
                            <option value="pais">País</option>
                            <option value="ciudad">Ciudad</option>
                            <option value="fecha_fin">Fecha Finalización</option>
                            <option value="orden_az">Orden Alfabético (A-Z)</option>
                            <option value="orden_za">Orden Alfabético (Z-A)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Valor</label>
                        <input type="text" id="valorFiltro" class="form-control"
                            placeholder="Escribe el valor a filtrar">
                    </div>

                    <!-- BOTÓN FILTRAR -->
                    <div class="col-md-1 d-grid">
                        <button class="btn btn-primary btn-sm" onclick="aplicarFiltro()">
                            <i class="bi bi-funnel-fill"></i> Filtrar
                        </button>
                    </div>

                    <!-- BOTÓN LIMPIAR -->
                    <div class="col-md-1 d-grid">
                        <button class="btn btn-danger btn-sm" onclick="limpiarFiltros()">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>

                    <!-- BOTÓN IMPRIMIR -->
                    <div class="col-md-1 d-grid">
                        <button class="btn btn-success btn-sm" onclick="imprimirFiltrado()">
                            <i class="bi bi-printer-fill"></i> Imprimir
                        </button>
                    </div>
                </div>


                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-center" id="tablaRecepcion">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th class="text-start">Nombre</th>
                                <th class="text-start">Código de Acceso</th>

                                <th>Fecha de Nacimiento</th>
                                <th>Pais</th>
                                <th>Ciudad</th>
                                <th>Fecha De Inicio</th>
                                <th>Fecha De Fin</th>
                                <th>Telefono</th>
                                <th>Adjudicacion</th>
                                <th>Foto</th> <!-- Columna Foto ahora después del Teléfono -->
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="contenidoTabla">

                            <?php foreach ($estudiantes as $est): ?>
                                <tr>
                                    <td><?= htmlspecialchars($est['id']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($est['nombre_completo']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($est['codigo_acceso']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($est['fecha_nacimiento'])) ?></td>
                                    <td><?= htmlspecialchars($est['pais']) ?></td>
                                    <td><?= htmlspecialchars($est['ciudad']) ?></td>
                                    <td><?= htmlspecialchars($est['anio_inicio_carrera']) ?></td>
                                    <td><?= htmlspecialchars($est['anio_fin_carrera']) ?></td>
                                    <td><?= htmlspecialchars($est['telefono']) ?></td>

                                    <td>
                                        <?php if (!empty($est['archivo_beca']) && file_exists('../php/' . $est['archivo_beca'])): ?>
                                            <a href="../php/<?= htmlspecialchars($est['archivo_beca']) ?>" target="_blank"
                                                class="btn btn-outline-info btn-sm">
                                                <i class="bi bi-file-earmark-arrow-down"></i> Ver
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">No disponible</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Foto del perfil -->
                                    <td>
                                        <?php if (!empty($est['foto_perfil']) && file_exists('../php/upload/perfil/' . $est['foto_perfil'])): ?>
                                            <img src="../php/upload/perfil/<?= htmlspecialchars($est['foto_perfil']) ?>"
                                                alt="Foto de Perfil"
                                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                        <?php else: ?>
                                            <span class="text-muted">NINGÚN PERFIL</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Acciones -->
                                    <td>
                                        <?php if (strtolower($rol) === 'administrador'): ?>
                                            <a href="editar_estudiantes.php?id=<?= htmlspecialchars($est['id']) ?>"
                                                class="btn btn-warning btn-sm" title="Editar">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="detalles_estudiantes.php?id=<?= htmlspecialchars($est['id']) ?>"
                                            class="btn btn-success btn-sm" title="Detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>


                                        <?php if (strtolower($rol) === 'administrador' || strtolower($rol) === 'tecnico-eliminar'): ?>

                                            <!-- Botón de eliminación -->
                                            <button type="button" class="btn btn-danger btn-sm eliminar-btn bi bi-trash"
                                                data-bs-toggle="modal" data-bs-target="#confirmarEliminarModal"
                                                data-id="<?= htmlspecialchars($est['id']); ?>"
                                                data-nombre="<?= htmlspecialchars($est['nombre_completo'] ?? ''); ?>">
                                            </button>

                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

                <!-- FIN DE LA PAGINACION -->

            </div>
        </div>
    </div>
</main>






<script>
    function aplicarFiltro() {

        const tipo = document.getElementById("tipoFiltro").value;
        const valor = document.getElementById("valorFiltro").value.trim();

        const url = new URL(window.location);

        if (tipo) {
            url.searchParams.set("tipo", tipo);
        }

        if (valor && tipo !== "orden_az" && tipo !== "orden_za") {
            url.searchParams.set("valor", valor);
        } else {
            url.searchParams.delete("valor");
        }

        // reiniciar a página 1 al filtrar
        url.searchParams.set("pagina", 1);

        window.location.href = url.toString();
    }
</script>

<script>
    function controlFiltroUI() {
        const tipo = document.getElementById("tipoFiltro").value;
        const input = document.getElementById("valorFiltro");

        if (tipo === "orden_az" || tipo === "orden_za") {
            input.style.display = "none";
            input.value = "";
        } else {
            input.style.display = "block";
            input.focus();
        }
    }
</script>

<script>
    window.addEventListener("DOMContentLoaded", () => {

        const params = new URLSearchParams(window.location.search);

        const tipo = params.get("tipo");
        const valor = params.get("valor");

        if (tipo) {
            document.getElementById("tipoFiltro").value = tipo;
        }

        if (valor) {
            document.getElementById("valorFiltro").value = valor;
        }

        controlFiltroUI();
    });
</script>


<script>
    //limpiar filtros
    function limpiarFiltros() {
        const url = new URL(window.location);
        url.searchParams.delete("tipo");
        url.searchParams.delete("valor");
        url.searchParams.set("pagina", 1);
        window.location.href = url.toString();
    }
</script>


<script>
    // imprimir segun filtro
    function imprimirFiltrado() {
        const tipo = document.getElementById("tipoFiltro").value;
        const valor = document.getElementById("valorFiltro").value.trim();

        // Abrir nueva ventana con los filtros como parámetros GET
        const url = `../php/imprimir_estudiantes.php?tipo=${encodeURIComponent(tipo)}&valor=${encodeURIComponent(valor)}`;
        window.open(url, "_blank");
    }
</script>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


<!-- Modal de eliminación -->
<div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar a <strong id="nombreEstudiante"></strong>?
                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> Cancelar</button>
                <form id="formEliminar" method="POST" action="./eliminar_estudiante.php">
                    <input type="hidden" name="id" id="idEstudiante">
                    <button type="submit" class="btn btn-danger">Sí, Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>




<script>
    document.addEventListener('DOMContentLoaded', function() {
        const eliminarModal = document.getElementById('confirmarEliminarModal');

        eliminarModal.addEventListener('show.bs.modal', function(event) {
            // El botón que disparó el modal
            const boton = event.relatedTarget;

            // Extraer la información de los atributos data-*
            const id = boton.getAttribute('data-id');
            const nombre = boton.getAttribute('data-nombre');

            // Actualizar el contenido del modal
            const modalBodyNombre = eliminarModal.querySelector('#nombreEstudiante');
            const modalInputId = eliminarModal.querySelector('#idEstudiante');

            modalBodyNombre.textContent = nombre;
            modalInputId.value = id;
        });
    });
</script>





<?php include_once("../componentes/footer.php"); ?>