<?php
include_once("../componentes/header.php");

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
            LIMIT :inicio, :por_pagina";

    $stmt = $pdo->prepare($sql);

    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }

    $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);

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
    <?php if (isset($_SESSION['mensaje'])): ?>
    <div id="alerta-sesion" class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-dismissible fade show mt-3"
        role="alert">
        <?= $_SESSION['mensaje'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>

    <script>
    // Espera 6 segundos y luego cierra la alerta automáticamente
    setTimeout(function() {
        var alerta = document.getElementById('alerta-sesion');
        if (alerta) {
            alerta.classList.remove('show');
            alerta.classList.add('fade');
            setTimeout(function() {
                alerta.remove();
            }, 500); // Dar tiempo a la animación de Bootstrap
        }
    }, 6000);
    </script>

    <?php
        unset($_SESSION['mensaje']);
        unset($_SESSION['tipo_mensaje']);
        ?>
    <?php endif; ?>

    <canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
    <div class="container-fluid">


        <?php if (isset($_SESSION['mensaje'])): ?>
        <div id="alerta-sesion" class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-dismissible fade show mt-3"
            role="alert">
            <?= $_SESSION['mensaje'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>

        <script>
        // Espera 6 segundos y luego cierra la alerta automáticamente
        setTimeout(function() {
            var alerta = document.getElementById('alerta-sesion');
            if (alerta) {
                alerta.classList.remove('show');
                alerta.classList.add('fade');
                setTimeout(function() {
                    alerta.remove();
                }, 500); // Dar tiempo a la animación de Bootstrap
            }
        }, 6000);
        </script>

        <?php
            unset($_SESSION['mensaje']);
            unset($_SESSION['tipo_mensaje']);
            ?>
        <?php endif; ?>





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
    <div class="col-md-2 d-grid">
        <button class="btn btn-primary" onclick="aplicarFiltro()">
            <i class="bi bi-funnel-fill"></i> Filtrar
        </button>
    </div>

    <!-- BOTÓN LIMPIAR -->
    <div class="col-md-1 d-grid">
        <button class="btn btn-secondary" onclick="limpiarFiltros()">
            <i class="bi bi-x-circle"></i>
        </button>
    </div>

    <!-- BOTÓN IMPRIMIR -->
 <div class="col-md-2 d-grid">
    <button class="btn btn-success" onclick="imprimirFiltrado()">
        <i class="bi bi-printer-fill"></i> Imprimir
    </button>
</div>


</div>




                <div class="row mb-3">
                    <div class="col-md-4">
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
                            <?php if (!empty($estudiantes)): ?>
                            <?php foreach ($estudiantes as $est): ?>
                            <tr>
                                <td><?= htmlspecialchars($est['id']) ?></td>
                                <td><?= htmlspecialchars($est['nombre_completo']) ?></td>


                                <td><?= htmlspecialchars($est['codigo_acceso']) ?></td>
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


                                    <!-- Botón de eliminación -->
                                    <button type="button" class="btn btn-danger btn-sm eliminar-btn bi bi-trash"
                                        data-bs-toggle="modal" data-bs-target="#confirmarEliminarModal"
                                        data-id="<?= htmlspecialchars($est['id']); ?>"
                                        data-nombre="<?= htmlspecialchars($est['nombre_completo'] ?? ''); ?>">
                                    </button>

                                    <!-- Modal de eliminación -->
                                    <div class="modal fade" id="confirmarEliminarModal" tabindex="-1"
                                        aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-danger border-4 shadow-lg rounded-lg">

                                                <!-- Header -->
                                                <div
                                                    class="modal-header bg-danger text-white border-bottom border-danger">
                                                    <h5 class="modal-title fw-bold" id="modalLabel">
                                                        ⚠️ Confirmar Eliminación Definitiva
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                </div>

                                                <!-- Body -->
                                                <div class="modal-body p-4">
                                                    <p id="modal-mensaje" class="fs-5 text-dark"></p>
                                                    <p class="text-danger mt-5">
                                                        ¡Esta acción es irreversible y eliminará el registro de la base
                                                        de datos!
                                                    </p>
                                                </div>

                                                <!-- Footer -->
                                                <div class="modal-footer p-3 bg-light d-flex justify-content-between">
                                                    <button type="button"
                                                        class="btn btn-secondary shadow-sm bi bi-x-circle text-white"
                                                        data-bs-dismiss="modal">
                                                        Cancelar
                                                    </button>
                                                    <a id="btn-eliminar-final" href="#"
                                                        class="btn btn-danger shadow-md bi bi-trash">
                                                        Sí, Eliminar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <script
                                        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
                                    </script>


                                    <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const modal = document.getElementById('confirmarEliminarModal');

                                        modal.addEventListener('show.bs.modal', function(event) {
                                            const button = event.relatedTarget;
                                            const estudianteId = button.getAttribute('data-id');
                                            const estudianteNombre = button.getAttribute('data-nombre');

                                            const modalMensaje = modal.querySelector('#modal-mensaje');
                                            const btnEliminar = modal.querySelector(
                                                '#btn-eliminar-final');

                                            // Mostrar nombre si existe, sino solo el ID
                                            const nombreDestacado = estudianteNombre ?
                                                `<span class="text-primary fw-bold">${estudianteNombre}</span>` :
                                                `ID: ${estudianteId}`;

                                            modalMensaje.innerHTML =
                                                `¿Está seguro que desea eliminar al estudiante ${nombreDestacado}?`;

                                            // Actualizar href del botón eliminar
                                            btnEliminar.href =
                                                `eliminar_estudiante.php?id=${estudianteId}`;
                                        });
                                    });
                                    </script>



                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted">No hay estudiantes registrados</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>


                </div>













                <!-- PAGINACION -->
              <!-- PAGINACIÓN -->
<?php if ($total_paginas > 1): ?>

<?php
// construir query manteniendo filtros
$queryParams = $_GET;
?>

<nav aria-label="Paginación de estudiantes">
    <ul class="pagination justify-content-center flex-wrap">

        <!-- BOTÓN ANTERIOR -->
        <?php if ($pagina_actual > 1): 
            $queryParams['pagina'] = $pagina_actual - 1;
        ?>
        <li class="page-item">
            <a class="page-link" href="?<?= http_build_query($queryParams) ?>">
                &laquo;
            </a>
        </li>
        <?php endif; ?>

        <?php
        // limitar número de páginas visibles
        $rango = 2;
        $inicio = max(1, $pagina_actual - $rango);
        $fin = min($total_paginas, $pagina_actual + $rango);

        // primera página
        if ($inicio > 1) {
            $queryParams['pagina'] = 1;
            echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryParams) . '">1</a></li>';

            if ($inicio > 2) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // páginas centrales
        for ($i = $inicio; $i <= $fin; $i++):
            $queryParams['pagina'] = $i;
        ?>
            <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                <a class="page-link" href="?<?= http_build_query($queryParams) ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <?php
        // última página
        if ($fin < $total_paginas) {

            if ($fin < $total_paginas - 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            $queryParams['pagina'] = $total_paginas;
            echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryParams) . '">' . $total_paginas . '</a></li>';
        }
        ?>

        <!-- BOTÓN SIGUIENTE -->
        <?php if ($pagina_actual < $total_paginas): 
            $queryParams['pagina'] = $pagina_actual + 1;
        ?>
        <li class="page-item">
            <a class="page-link" href="?<?= http_build_query($queryParams) ?>">
                &raquo;
            </a>
        </li>
        <?php endif; ?>

    </ul>
</nav>

<?php endif; ?>


                <!-- FIN DE LA PAGINACION -->

            </div>
        </div>
    </div>
</main>

<!-- Bootstrap Icons & jQuery -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="../config/js/jquery-3.6.0.min.js"></script>




<!-- Buscador en tiempo real -->

<script>
$(document).ready(function() {
    $("#busqueda").on("keyup", function() {
        let valor = $(this).val().toLowerCase();

        $("#contenidoTabla tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().includes(valor));
        });
    });
});
</script>




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






<?php include_once("../componentes/footer.php"); ?>