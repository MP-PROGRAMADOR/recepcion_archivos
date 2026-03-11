<?php
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

// Configuración de paginación


// Filtros
$tipo  = $_GET['tipo'] ?? '';
$valor = trim($_GET['valor'] ?? '');

// Consulta base
$sql = "SELECT c.id, c.nombre, COUNT(e.id) as estudiantes
        FROM ciudades c
        LEFT JOIN estudiantes e ON c.id = e.ciudad_id
        WHERE 1=1";

$params = [];

// Columnas filtrables
$columnas = [
    'nombre' => 'c.nombre'
];

// Aplicar filtro por valor
if ($valor !== '' && isset($columnas[$tipo])) {
    $sql .= " AND {$columnas[$tipo]} LIKE :valor";
    $params[':valor'] = "%$valor%";
}

// Agrupar y ordenar
$sql .= " GROUP BY c.id";

if ($tipo === 'orden_az') {
    $sql .= " ORDER BY c.nombre ASC";
} elseif ($tipo === 'orden_za') {
    $sql .= " ORDER BY c.nombre DESC";
} else {
    $sql .= " ORDER BY c.id DESC";
}



try {
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }

    $stmt->execute();
    $ciudades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Contar total de filas filtradas
    $total_stmt = $pdo->prepare("SELECT COUNT(DISTINCT c.id) as total
                                 FROM ciudades c
                                 LEFT JOIN estudiantes e ON c.id = e.ciudad_id
                                 WHERE 1=1" . ($valor !== '' && isset($columnas[$tipo]) ? " AND {$columnas[$tipo]} LIKE :valor" : ""));
    if ($valor !== '' && isset($columnas[$tipo])) {
        $total_stmt->bindValue(':valor', "%$valor%");
    }
    $total_stmt->execute();
    $total_filas = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
  
} catch (PDOException $e) {
    die("Error al obtener las ciudades: " . $e->getMessage());
}
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
                }, 5000);
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
            <h3><i class="bi bi-buildings-fill me-2"></i>Listado de Ciudades con Estudiantes Inscritos</h3>
            <a href="registrar_ciudades.php" class="btn btn-primary">
                <i class="bi bi-person-plus-fill me-1"></i> Nueva ciudad
            </a>
        </div>

        <div class="card shadow rounded-4">
            <div class="card-body">

                <div class="row mb-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filtrar por</label>
                        <select id="tipoFiltro" class="form-select" onchange="controlFiltroUI()">
                            <option value="nombre">Nombre de ciudad</option>
                            <option value="orden_az">Orden Alfabético (A-Z)</option>
                            <option value="orden_za">Orden Alfabético (Z-A)</option>
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

                    <div class="col-md-1 d-grid">
                        <button class="btn btn-success d-flex btn-sm px-3" onclick="imprimirFiltrado()">
                            <i class="bi bi-printer-fill me-2"></i> Imprimir
                        </button>
                    </div>

                </div>



              

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-center" id="tablaRecepcion">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="bi bi-hash me-1"></i>ID</th>
                                <th><i class="bi bi-geo-alt-fill me-1"></i>Nombre</th>
                                <th><i class="bi bi-person-fill me-1"></i>Estudiantes</th>
                                <th><i class="bi bi-gear-fill me-1"></i>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="contenidoTabla">
                          
                                <?php foreach ($ciudades as $ciudad): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($ciudad['id']) ?></td>
                                        <td><?= htmlspecialchars($ciudad['nombre']) ?></td>
                                        <td><?= htmlspecialchars($ciudad['estudiantes']) ?></td>
                                        <td>
                                            <!-- Botón Editar -->
                                            <a href="editar_ciudad.php?id=<?= htmlspecialchars($ciudad['id']) ?>"
                                                class="btn btn-warning btn-sm" title="Editar ciudad">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <!-- Botón Eliminar -->
                                            <button type="button"
                                                class="btn btn-danger btn-sm bi bi-trash"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEliminarCiudad"
                                                data-id="<?= $ciudad['id']; ?>"
                                                data-nombre="<?= htmlspecialchars($ciudad['nombre']); ?>">
                                            </button>


                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                          
                        </tbody>

                    </table>
                </div>

               
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="modalEliminarCiudad" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger border-3 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle-fill"></i> ¡Atención!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="fs-5">¿Estás seguro de que deseas eliminar la ciudad de:</p>
                <h4 id="nombreCiudadEliminar" class="text-primary fw-bold"></h4>
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-info-circle"></i> <strong>Aviso:</strong> También se eliminarán todas las <strong>universidades</strong> registradas en esta ciudad.
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> <i class="bi bi-x-circle me-1"></i> Cancelar</button>
                <a id="btnConfirmarEliminar" href="#" class="btn btn-danger shadow">
                    <i class="bi bi-trash"></i> Sí, Eliminar todo
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalEliminar = document.getElementById('modalEliminarCiudad');
        if (modalEliminar) {
            modalEliminar.addEventListener('show.bs.modal', function(event) {
                const boton = event.relatedTarget; // Botón que hizo clic
                const id = boton.getAttribute('data-id');
                const nombre = boton.getAttribute('data-nombre');

                // Llenamos el modal con los datos
                modalEliminar.querySelector('#nombreCiudadEliminar').textContent = nombre;
                modalEliminar.querySelector('#btnConfirmarEliminar').href = `eliminar_ciudad.php?id=${id}`;
            });
        }
    });
</script>




<script>
    // Aplica el filtro y recarga la página con parámetros GET
    function aplicarFiltro() {
        const tipo = document.getElementById("tipoFiltro").value;
        const valor = document.getElementById("valorFiltro").value.trim();

        const url = new URL(window.location);

        if (tipo) url.searchParams.set("tipo", tipo);

        if (valor && tipo !== "orden_az" && tipo !== "orden_za") {
            url.searchParams.set("valor", valor);
        } else {
            url.searchParams.delete("valor");
        }

        url.searchParams.set("pagina", 1);
        window.location.href = url.toString();
    }

    // Mostrar/ocultar input según tipo de filtro
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

    // Cargar valores del filtro al cargar la página
    window.addEventListener("DOMContentLoaded", () => {
        const params = new URLSearchParams(window.location.search);
        const tipo = params.get("tipo");
        const valor = params.get("valor");

        if (tipo) document.getElementById("tipoFiltro").value = tipo;
        if (valor) document.getElementById("valorFiltro").value = valor;

        controlFiltroUI();
    });

    // Limpiar filtros
    function limpiarFiltros() {
        const url = new URL(window.location);
        url.searchParams.delete("tipo");
        url.searchParams.delete("valor");
        url.searchParams.set("pagina", 1);
        window.location.href = url.toString();
    }

    // Imprimir filtrado
    function imprimirFiltrado() {
        const tipo = document.getElementById("tipoFiltro").value;
        const valor = document.getElementById("valorFiltro").value.trim();
        const url = `../php/imprimir_ciudades.php?tipo=${encodeURIComponent(tipo)}&valor=${encodeURIComponent(valor)}`;
        window.open(url, "_blank");
    }
</script>




<?php include_once("../componentes/footer.php"); ?>