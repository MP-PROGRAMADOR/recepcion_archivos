<?php
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

// Configuración de paginación
$por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$inicio = ($pagina_actual > 1) ? ($pagina_actual * $por_pagina) - $por_pagina : 0;

/* ===== CAPTURAR FILTROS ===== */
$tipo  = $_GET['tipo']  ?? '';
$valor = trim($_GET['valor'] ?? '');

/* ===== MAPEO DE COLUMNAS FILTRABLES ===== */
$columnas = [
    'titular'        => 'est.nombre_completo',
    'banco'          => 'cb.banco',
    'tipo_cuenta'    => 'cb.tipo_cuenta',
    'numero_cuenta'  => 'cb.numero_cuenta',
    'tarjeta_visa'   => 'cb.tarjeta_visa',
    'fecha_caducidad'=> 'cb.fecha_caducidad_tarjeta'
];

/* ===== CONSULTA BASE ===== */
$sqlBase = "
    FROM cuentas_bancarias cb
    LEFT JOIN estudiantes est ON cb.estudiante_id = est.id
    WHERE 1=1
";

$params = [];

/* ===== APLICAR FILTROS ===== */
if ($valor !== '' && isset($columnas[$tipo])) {
    $sqlBase .= " AND {$columnas[$tipo]} LIKE :valor";
    $params[':valor'] = "%$valor%";
}

/* ===== CONTAR TOTAL FILTRADO ===== */
$sqlTotal = "SELECT COUNT(*) as total " . $sqlBase;
$stmtTotal = $pdo->prepare($sqlTotal);

foreach ($params as $k => $v) {
    $stmtTotal->bindValue($k, $v);
}

$stmtTotal->execute();
$total_filas = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_filas / $por_pagina);

/* ===== CONSULTA PRINCIPAL ===== */
$sql = "SELECT cb.*, est.nombre_completo AS titular " . $sqlBase;

/* ===== ORDENAMIENTO ===== */
if ($tipo === 'orden_az') {
    $sql .= " ORDER BY est.nombre_completo ASC";
} elseif ($tipo === 'orden_za') {
    $sql .= " ORDER BY est.nombre_completo DESC";
} else {
    $sql .= " ORDER BY cb.id DESC";
}

$sql .= " LIMIT :inicio, :por_pagina";

$stmt = $pdo->prepare($sql);

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}

$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);

$stmt->execute();
$cuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <div class="container mt-4">

        <!-- INICIO DE LA ALERTA -->
        <?php include_once("../componentes/alerta.php"); ?>
        <!-- FIN DE LA ALERTA -->

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="bi bi-bank2 me-2"></i>Listado de Cuentas Bancarias</h3>
            <button class="btn btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#modalCuenta">
                <i class="bi bi-credit-card-2-front me-1"></i> Nueva Cuenta
            </button>
        </div>

        <div class="card shadow rounded-4">
            <div class="card-body">


                <div class="row mb-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filtrar por</label>
                        <select id="tipoFiltro" class="form-select" onchange="controlFiltroUI()">
                            <option value="titular">Nombre del titular</option>
                            <option value="banco">Banco</option>
                            <option value="tipo_cuenta">Tipo de cuenta</option>
                            <option value="numero_cuenta">Número de cuenta</option>
                            <option value="tarjeta_visa">Tarjeta Visa</option>
                            <option value="fecha_caducidad">Fecha caducidad</option>
                            <option value="orden_az">Orden A-Z</option>
                            <option value="orden_za">Orden Z-A</option>
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
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-success btn-sm" onclick="imprimirFiltrado()">
                            <i class="bi bi-printer-fill"></i> Imprimir
                        </button>
                    </div>


                </div>


                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="busqueda" class="form-label fw-bold">
                            <i class="bi bi-search me-1"></i>Buscar Cuenta
                        </label>
                        <input type="text" class="form-control" id="busqueda"
                            placeholder="Buscar por número o banco...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-center" id="tablaCuentas">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="bi bi-hash me-1"></i>ID</th>
                                <th class="text-start"><i class="bi bi-bank me-1"></i>Nombre Completo</th>
                                <th><i class="bi bi-bank me-1"></i>Banco</th>
                                <th><i class="bi bi-credit-card-2-front me-1"></i>N° Cuenta</th>
                                <th><i class="bi bi-wallet2 me-1"></i>Tipo Cuenta</th>
                                <th><i class="bi bi-credit-card me-1"></i>Tarjeta Visa</th>
                                <th><i class="bi bi-calendar-check me-1"></i>Caducidad de Visa</th>
                                <?php if(strtolower($rol) === 'administrador'): ?>
                                <th><i class="bi bi-gear me-1"></i>Acciones</th>

                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="contenidoTabla">
                            <?php if (!empty($cuentas)): ?>
                            <?php foreach ($cuentas as $cuenta): ?>
                            <tr>
                                <td><i class="bi bi-hash me-1"></i><?= htmlspecialchars($cuenta['id']) ?></td>
                                <td class="text-start"><i
                                        class="bi bi-person-vcard me-1"></i><?= htmlspecialchars($cuenta['titular']) ?>
                                <td><i class="bi bi-bank me-1"></i><?= htmlspecialchars($cuenta['banco']) ?></td>
                                <td><i
                                        class="bi bi-credit-card-2-front me-1"></i><?= htmlspecialchars($cuenta['numero_cuenta']) ?>
                                </td>

                                </td>
                                <td><i class="bi bi-wallet2 me-1"></i><?= htmlspecialchars($cuenta['tipo_cuenta']) ?>
                                </td>
                                <td><i
                                        class="bi bi-credit-card me-1"></i><?= !empty($cuenta['tarjeta_visa']) ? htmlspecialchars($cuenta['tarjeta_visa']) : 'No dispone' ?>
                                </td>
                                <td><i
                                        class="bi bi-calendar-check me-1"></i><?= !empty($cuenta['fecha_caducidad_tarjeta']) ? date('d/m/Y', strtotime($cuenta['fecha_caducidad_tarjeta'])) : 'No definida' ?>
                                </td>
                                <?php if(strtolower($rol) === 'administrador'): ?>
                                <td>
                                    <a href="editar_cuenta.php?id=<?= $cuenta['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                </td>
                                <?php endif; ?>

                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No hay cuentas bancarias registradas</td>
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
                        <li class="page-item"><a class="page-link" href="?pagina=<?= $pagina_actual - 1 ?>">&laquo;</a>
                        </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item <?= $i == $pagina_actual ? 'active' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                        <?php if ($pagina_actual < $total_paginas): ?>
                        <li class="page-item"><a class="page-link" href="?pagina=<?= $pagina_actual + 1 ?>">&raquo;</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>



<!-- MODAL REGISTRAR CUENTA -->
<div class="modal fade" id="modalCuenta" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-credit-card"></i> Registrar Cuenta Bancaria</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="../php/guardar_cuentas.php" method="POST">
                <div class="modal-body">

                    <!-- 🔎 Buscador -->
                    <div class="mb-3">
                        <label class="fw-bold">
                            <i class="bi bi-search"></i> Buscar estudiante
                        </label>
                        <input type="text" id="buscarEstudiante" class="form-control form-control-lg"
                            placeholder="Escriba el nombre del estudiante...">

                        <div id="resultadosBusqueda" class="list-group mt-2 shadow-sm"></div>
                    </div>

                    <!-- Estudiante seleccionado -->
                    <div id="seleccionadoBox" class="alert alert-info d-none">
                        <i class="bi bi-person-check-fill"></i>
                        <strong>Estudiante seleccionado:</strong>
                        <span id="nombreSeleccionado"></span>
                    </div>

                    <input type="hidden" name="estudiante_id" id="estudiante_id">
                    <input type="hidden" name="fecha_nacimiento" id="fecha_nacimiento">

                    <hr>

                    <!-- Tipo cuenta -->
                    <div class="row">
                        <div class="mb-6">
                            <label class="fw-bold">
                                <i class="bi bi-wallet2"></i> Tipo de cuenta
                            </label>
                            <select id="tipo_cuenta" name="tipo_cuenta" class="form-select" required>
                                <option value="">Seleccione</option>
                                <option value="departamental">🏛️ Departamental</option>
                                <option value="propia">👤 Propia</option>
                            </select>
                        </div>

                    </div>

                    <!-- Banco -->
                    <div class="mb-3 d-none" id="grupoBanco">
                        <label class="fw-bold">
                            <i class="bi bi-bank"></i> Banco
                        </label>
                        <select id="banco" name="banco" class="form-select">
                            <option value="" disabled selected>Selecciona banco</option>
                            <option value="ecobank">🏦 ECOBANK</option>
                            <option value="sgbge">🏦 SGBGE</option>
                            <option value="cceibank">🏦 CCEIBANK</option>
                            <option value="embajada">🏛️ EMBAJADA</option>
                        </select>
                    </div>

                    <!-- Cuenta + tarjeta -->
                    <div id="grupoCuenta" class="d-none">

                        <div class="mb-3">
                            <label class="fw-bold">
                                <i class="bi bi-credit-card-2-front"></i> Número de cuenta
                            </label>
                            <input type="text" name="numero_cuenta" class="form-control" placeholder="Ej: 00123456789">
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">
                                <i class="bi bi-credit-card"></i> Tarjeta VISA
                            </label>
                            <select id="tarjeta_visa" name="tarjeta_visa" class="form-select">
                                <option value="" disabled selected>Selecciona</option>
                                <option value="si">✔ Sí</option>
                                <option value="no">✖ No</option>
                            </select>
                        </div>

                    </div>

                    <!-- Fecha caducidad -->
                    <div class="mb-3 d-none" id="grupoFecha">
                        <label class="fw-bold">
                            <i class="bi bi-calendar-event"></i> Fecha caducidad tarjeta
                        </label>
                        <input type="date" name="fecha_caducidad_tarjeta" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>



<script>
//script para controlar la visibilidad
document.addEventListener("DOMContentLoaded", function() {

    const tipoCuenta = document.getElementById("tipo_cuenta");
    const banco = document.getElementById("banco");
    const tarjeta = document.getElementById("tarjeta_visa");

    const grupoBanco = document.getElementById("grupoBanco");
    const grupoCuenta = document.getElementById("grupoCuenta");
    const grupoFecha = document.getElementById("grupoFecha");

    const modal = document.getElementById("modalCuenta");

    // 🔄 Resetear al abrir modal
    modal.addEventListener('show.bs.modal', () => {
        grupoBanco.classList.add("d-none");
        grupoCuenta.classList.add("d-none");
        grupoFecha.classList.add("d-none");

        tipoCuenta.value = "";
        if (banco) banco.value = "";
        if (tarjeta) tarjeta.value = "";
    });

    // Mostrar banco
    tipoCuenta.addEventListener("change", function() {
        if (this.value !== "") {
            grupoBanco.classList.remove("d-none");
        } else {
            grupoBanco.classList.add("d-none");
        }

        grupoCuenta.classList.add("d-none");
        grupoFecha.classList.add("d-none");
    });

    // Mostrar cuenta y tarjeta
    banco.addEventListener("change", function() {
        grupoCuenta.classList.remove("d-none");
        grupoFecha.classList.add("d-none");
    });

    // Mostrar fecha si tiene tarjeta
    tarjeta.addEventListener("change", function() {
        if (this.value === "si") {
            grupoFecha.classList.remove("d-none");
        } else {
            grupoFecha.classList.add("d-none");
        }
    });

});
</script>



<script>
const buscador = document.getElementById('buscarEstudiante');
const resultados = document.getElementById('resultadosBusqueda');
const seleccionadoBox = document.getElementById('seleccionadoBox');
const estudianteInput = document.getElementById('estudiante_id');
const fechaNacimientoInput = document.getElementById('fecha_nacimiento');

// 🔎 Buscar
buscador.addEventListener('keyup', () => {
    let texto = buscador.value;

    if (texto.length < 2) {
        resultados.innerHTML = "";
        return;
    }

    fetch("../php/buscar_estudiantes.php?q=" + texto)
        .then(res => res.text())
        .then(data => resultados.innerHTML = data);
});

// seleccionar estudiante
document.addEventListener('click', e => {
    if (e.target.classList.contains('estudiante-item')) {
        e.preventDefault();

        let id = e.target.dataset.id;
        let nombre = e.target.dataset.nombre;
        let fecha = e.target.dataset.fecha;

        estudianteInput.value = id;
        fechaNacimientoInput.value = fecha;

        seleccionadoBox.innerHTML = `
            <div class="alert alert-success d-flex justify-content-between align-items-center">
                <div>
                    <strong>${nombre}</strong><br>
                    <small>Fecha nacimiento: ${fecha}</small>
                </div>
                <button type="button" class="btn btn-sm btn-danger" id="quitarSeleccion">Quitar</button>
            </div>
        `;

        resultados.innerHTML = "";
        buscador.value = "";
    }
});

// quitar selección
document.addEventListener('click', e => {
    if (e.target.id === 'quitarSeleccion') {
        estudianteInput.value = "";
        fechaNacimientoInput.value = "";
        seleccionadoBox.innerHTML = "";
    }
});
</script>




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
// =============================
// APLICAR FILTRO
// =============================
function aplicarFiltro() {

    const tipo = document.getElementById("tipoFiltro").value;
    const input = document.getElementById("valorFiltro");
    const valor = input.value.trim();

    const url = new URL(window.location);

    // Siempre establecer tipo
    url.searchParams.set("tipo", tipo);

    // Si es ordenamiento → no necesita valor
    if (tipo === "orden_az" || tipo === "orden_za") {
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


// =============================
// CONTROL VISUAL DEL INPUT
// =============================
function controlFiltroUI() {

    const tipo = document.getElementById("tipoFiltro").value;
    const input = document.getElementById("valorFiltro");

    // Ordenamiento → ocultar input
    if (tipo === "orden_az" || tipo === "orden_za") {
        input.style.display = "none";
        input.value = "";
        return;
    }

    // Fecha → cambiar a type date
    if (tipo === "fecha_caducidad") {
        input.type = "date";
    } else {
        input.type = "text";
    }

    input.style.display = "block";
    input.focus();
}


// =============================
// RESTAURAR FILTROS AL CARGAR
// =============================
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


// =============================
// LIMPIAR FILTROS
// =============================
function limpiarFiltros() {

    const url = new URL(window.location);

    url.searchParams.delete("tipo");
    url.searchParams.delete("valor");
    url.searchParams.set("pagina", 1);

    window.location.href = url.toString();
}


// =============================
// IMPRIMIR FILTRADO
// =============================
function imprimirFiltrado() {

    const tipo = document.getElementById("tipoFiltro").value;
    const valor = document.getElementById("valorFiltro").value.trim();

    let url = `../php/imprimir_cuentas.php?tipo=${encodeURIComponent(tipo)}`;

    if (valor !== "" && tipo !== "orden_az" && tipo !== "orden_za") {
        url += `&valor=${encodeURIComponent(valor)}`;
    }

    window.open(url, "_blank");
}


// =============================
// BÚSQUEDA RÁPIDA LOCAL
// =============================
document.getElementById("busqueda")?.addEventListener("keyup", function() {

    const valor = this.value.toLowerCase();
    const filas = document.querySelectorAll("#contenidoTabla tr");

    filas.forEach(fila => {
        fila.style.display = fila.textContent.toLowerCase().includes(valor) ?
            "" :
            "none";
    });

});
</script>


<?php include_once("../componentes/footer.php"); ?>