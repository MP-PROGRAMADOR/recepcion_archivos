<?php
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

$por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina_actual > 1) ? ($pagina_actual * $por_pagina) - $por_pagina : 0;

$tipo  = $_GET['tipo'] ?? '';
$valor = trim($_GET['valor'] ?? '');

$sql = "SELECT l.*, u.nombre AS nombre_usuario
        FROM log_actividades l
        INNER JOIN usuarios u ON l.usuario_id = u.id
        WHERE 1=1";

$params = [];

/* ==============================
   FILTROS
============================== */

if ($tipo === 'usuario' && $valor !== '') {
    $sql .= " AND u.nombre LIKE :valor";
    $params[':valor'] = "%$valor%";
}

if ($tipo === 'accion' && $valor !== '') {
    $sql .= " AND l.accion LIKE :valor";
    $params[':valor'] = "%$valor%";
}

if ($tipo === 'modulo' && $valor !== '') {
    $sql .= " AND l.modulo LIKE :valor";
    $params[':valor'] = "%$valor%";
}

if ($tipo === 'resultado' && $valor !== '') {
    $sql .= " AND l.resultado = :valor";
    $params[':valor'] = $valor;
}

if ($tipo === 'fecha' && $valor !== '') {
    $sql .= " AND DATE(l.fecha) = :valor";
    $params[':valor'] = $valor;
}

/* ==============================
   ORDEN
============================== */

if ($tipo === 'fecha_desc') {
    $sql .= " ORDER BY l.fecha DESC";
} elseif ($tipo === 'fecha_asc') {
    $sql .= " ORDER BY l.fecha ASC";
} else {
    $sql .= " ORDER BY l.id DESC";
}

$sql .= " LIMIT :inicio, :por_pagina";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}

$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);

$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ==============================
   TOTAL REGISTROS
============================== */

$count_sql = "SELECT COUNT(*) as total
              FROM log_actividades l
              INNER JOIN usuarios u ON l.usuario_id = u.id
              WHERE 1=1";

if ($tipo === 'usuario' && $valor !== '') {
    $count_sql .= " AND u.nombre LIKE :valor";
}
if ($tipo === 'accion' && $valor !== '') {
    $count_sql .= " AND l.accion LIKE :valor";
}
if ($tipo === 'modulo' && $valor !== '') {
    $count_sql .= " AND l.modulo LIKE :valor";
}
if ($tipo === 'resultado' && $valor !== '') {
    $count_sql .= " AND l.resultado = :valor";
}
if ($tipo === 'fecha' && $valor !== '') {
    $count_sql .= " AND DATE(l.fecha) = :valor";
}

$total_stmt = $pdo->prepare($count_sql);

if (!empty($params)) {
    $total_stmt->bindValue(':valor', $params[':valor']);
}

$total_stmt->execute();
$total_filas = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_filas / $por_pagina);
?>

<main class="content" id="mainContent">
    <canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
    <div class="container mt-4">

        <!-- INICIO DE LA ALERTA -->
        <?php if (isset($_SESSION['exito']) && !empty($_SESSION['exito'])): ?>
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
        setTimeout(() => {
            const alerta = document.getElementById('alerta-exito');
            if (alerta) {
                alerta.classList.remove('show');
                alerta.classList.add('fade');
                setTimeout(() => alerta.remove(), 500);
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
        <?php unset($_SESSION['exito']); ?>
        <?php endif; ?>
        <!-- FIN DE LA ALERTA -->

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="bi bi-buildings-fill me-2"></i>Listado de Acciones de Usuarios</h3>
        </div>

        <div class="card shadow rounded-4">
            <div class="card-body">

                <div class="row mb-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filtrar por</label>
                        <select id="tipoFiltro" class="form-select" onchange="controlFiltroUI()">
                            <option value="usuario">Usuario</option>
                            <option value="accion">Tipo de acción</option>
                            <option value="modulo">Módulo</option>
                            <option value="resultado">Resultado</option>
                            <option value="fecha">Fecha</option>
                            <option value="fecha_desc">Más recientes primero</option>
                            <option value="fecha_asc">Más antiguos primero</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Valor</label>
                        <input type="text" id="valorFiltro" class="form-control"
                            placeholder="Escribe el valor a filtrar">
                    </div>

                    <div class="col-md-1 d-grid">
                        <button class="btn btn-primary btn-sm" onclick="aplicarFiltro()">
                            <i class="bi bi-funnel-fill"></i> Filtrar
                        </button>
                    </div>

                    <div class="col-md-1 d-grid">
                        <button class="btn btn-danger btn-sm" onclick="limpiarFiltros()">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>

                    <div class="col-md-2 d-grid">
                        <button class="btn btn-success btn-sm" onclick="imprimirFiltrado()">
                            <i class="bi bi-printer-fill"></i> Imprimir
                        </button>
                    </div>

                </div>



                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="busqueda" class="form-label fw-bold">
                            <i class="bi bi-search me-1"></i>Buscar Ciudad
                        </label>
                        <input type="text" class="form-control" id="busqueda" placeholder="Buscar por ID o nombre...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-center" id="tablaCiudad">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Módulo</th>
                                <th>Descripción</th>
                                <th>Resultado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody id="contenidoTabla">
                            <?php if (!empty($logs)): ?>
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= $log['id'] ?></td>
                                <td><?= htmlspecialchars($log['nombre_usuario']) ?></td>
                                <td><?= htmlspecialchars($log['accion']) ?></td>
                                <td><?= htmlspecialchars($log['modulo']) ?></td>
                                <td><?= htmlspecialchars($log['descripcion']) ?></td>
                                <td>
                                    <?php if ($log['resultado'] === 'EXITO'): ?>
                                    <span class="badge bg-success">Éxito</span>
                                    <?php else: ?>
                                    <span class="badge bg-danger">Error</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date("d/m/Y H:i", strtotime($log['fecha'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No hay registros encontrados
                                </td>
                            </tr>
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
                        <li class="page-item <?= $i == $pagina_actual ? 'active' : '' ?>">
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
    </div>
</main>

<!-- Bootstrap Icons y jQuery -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Buscador funcional -->
<script>
$(document).ready(function() {
    $('#busqueda').on('keyup', function() {
        let valor = $(this).val().toLowerCase();
        $('#contenidoTabla tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().includes(valor));
        });
    });
});
</script>


<script>
// ===============================
// Aplicar Filtro
// ===============================
function aplicarFiltro() {

    const tipo = document.getElementById("tipoFiltro").value;
    const valor = document.getElementById("valorFiltro").value.trim();

    const url = new URL(window.location);

    // Guardamos tipo
    if (tipo) {
        url.searchParams.set("tipo", tipo);
    }

    // Si es ordenamiento no necesita valor
    if (tipo === "fecha_desc" || tipo === "fecha_asc") {
        url.searchParams.delete("valor");
    } else {
        if (valor !== "") {
            url.searchParams.set("valor", valor);
        } else {
            url.searchParams.delete("valor");
        }
    }

    // Reiniciar paginación
    url.searchParams.set("pagina", 1);

    window.location.href = url.toString();
}

// ===============================
// Control dinámico del input
// ===============================
function controlFiltroUI() {

    const tipo = document.getElementById("tipoFiltro").value;
    const input = document.getElementById("valorFiltro");

    if (tipo === "fecha") {
        input.type = "date";
        input.style.display = "block";
        input.value = "";
        input.focus();

    } else if (tipo === "resultado") {

        // Convertimos input en select dinámicamente
        input.type = "text";
        input.placeholder = "EXITO o ERROR";
        input.style.display = "block";
        input.value = "";
        input.focus();

    } else if (tipo === "fecha_desc" || tipo === "fecha_asc") {

        input.style.display = "none";
        input.value = "";

    } else {

        input.type = "text";
        input.placeholder = "Escribe el valor a filtrar";
        input.style.display = "block";
        input.value = "";
        input.focus();
    }
}

// ===============================
// Cargar filtros al iniciar
// ===============================
window.addEventListener("DOMContentLoaded", () => {

    const params = new URLSearchParams(window.location.search);
    const tipo = params.get("tipo");
    const valor = params.get("valor");

    if (tipo) {
        document.getElementById("tipoFiltro").value = tipo;
    }

    controlFiltroUI();

    if (valor) {
        document.getElementById("valorFiltro").value = valor;
    }
});

// ===============================
// Limpiar Filtros
// ===============================
function limpiarFiltros() {

    const url = new URL(window.location);

    url.searchParams.delete("tipo");
    url.searchParams.delete("valor");
    url.searchParams.set("pagina", 1);

    window.location.href = url.toString();
}

// ===============================
// Imprimir filtrado
// ===============================
function imprimirFiltrado() {

    const tipo = document.getElementById("tipoFiltro").value;
    const valor = document.getElementById("valorFiltro").value.trim();

    const url = `../php/imprimir_logs.php?tipo=${encodeURIComponent(tipo)}&valor=${encodeURIComponent(valor)}`;

    window.open(url, "_blank");
}
</script>

<?php include_once("../componentes/footer.php"); ?>