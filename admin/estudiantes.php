<?php
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

// =========================
// PAGINACIÓN
// =========================
$por_pagina = 20;
$pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$inicio = ($pagina_actual > 1) ? ($pagina_actual * $por_pagina) - $por_pagina : 0;

// =========================
// FILTROS
// =========================
$tipoFiltro = $_GET['tipo'] ?? '';
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
                'danger' => 'bi-x-circle-fill',
                'warning' => 'bi-exclamation-triangle-fill',
                'info' => 'bi-info-circle-fill'
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
                setTimeout(function () {
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

                <div class="row mb-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filtrar por</label>
                        <select id="tipoFiltro" class="form-select form-select-sm" onchange="controlFiltroUI()">
                            <option value="nombre">Nombre</option>
                            <option value="pais">País</option>
                            <option value="ciudad">Ciudad</option>
                            <option value="fecha_fin">Año Finalización</option>
                            <option value="orden_az">Orden Alfabético (A-Z)</option>
                            <option value="orden_za">Orden Alfabético (Z-A)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Valor</label>
                        <input type="text" id="valorFiltro" class="form-control form-control-sm"
                            placeholder="Escribe el valor y añade el filtro">
                    </div>

                    <div class="col-md-2 d-grid">
                        <button class="btn btn-primary btn-sm" onclick="agregarFiltroAcumulado()">
                            <i class="bi bi-plus-circle"></i> Añadir Filtro
                        </button>
                    </div>

                    <div class="col-md-1 d-grid">
                        <button class="btn btn-danger btn-sm" onclick="limpiarTodosLosFiltros()" title="Limpiar Todo">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>

                    <div class="col-md-2 d-grid">
                        <button class="btn btn-success btn-sm" onclick="imprimirFiltrado()">
                            <i class="bi bi-printer-fill"></i> Imprimir Reporte
                        </button>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12 d-flex flex-wrap gap-2" id="contenedorEtiquetas">
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
                                    <td>
                                        <?= htmlspecialchars($est['id']) ?>
                                    </td>
                                    <td class="text-start">
                                        <?= htmlspecialchars($est['nombre_completo']) ?>
                                    </td>
                                    <td class="text-start">
                                        <?= htmlspecialchars($est['codigo_acceso']) ?>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($est['fecha_nacimiento'])) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($est['pais']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($est['ciudad']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($est['anio_inicio_carrera']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($est['anio_fin_carrera']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($est['telefono']) ?>
                                    </td>

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

            </div>
        </div>

    </div>
</main>






<script>
    // Variables globales
    let filtrosAplicados = [];

    const mapeoColumnas = {
        'nombre': 1,
        'pais': 4,
        'ciudad': 5,
        'fecha_fin': 7
    };

    // Control de interfaz: Oculta el input si es un ordenamiento, o le da foco si es un filtro
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
    function agregarFiltroAcumulado() {
        const selector = document.getElementById("tipoFiltro");
        const tipo = selector.value;
        const tipoTexto = selector.options[selector.selectedIndex].text;
        const valor = document.getElementById("valorFiltro").value.trim();

        // ACCIÓN: Si es ordenamiento alfabético, ejecutamos el orden en DataTables de inmediato
        if (tipo === "orden_az") {
            table.column(1).order('asc').draw(); // Columna 1 es el Nombre
            return;
        }
        if (tipo === "orden_za") {
            table.column(1).order('desc').draw();
            return;
        }

        // LÓGICA NORMAL: Para filtros que sí requieren texto
        if (!valor) return;

        const existe = filtrosAplicados.some(f => f.tipo === tipo && f.valor.toLowerCase() === valor.toLowerCase());
        if (existe) {
            alert("Este filtro ya ha sido añadido.");
            return;
        }

        filtrosAplicados.push({ tipo, tipoTexto, valor });
        document.getElementById("valorFiltro").value = "";

        dibujarEtiquetas();
        procesarFiltrosEnDataTables();
    }

    function dibujarEtiquetas() {
        const contenedor = document.getElementById("contenedorEtiquetas");
        contenedor.innerHTML = "";

        filtrosAplicados.forEach((filtro, index) => {
            const badge = document.createElement("span");
            badge.className = "badge bg-light text-dark border d-flex align-items-center gap-2 p-2 shadow-sm rounded-pill";
            badge.innerHTML = `
                <strong>${filtro.tipoTexto}:</strong> ${filtro.valor}
                <button type="button" class="btn-close" style="font-size: 0.65rem;" onclick="eliminarFiltro(${index})"></button>
            `;
            contenedor.appendChild(badge);
        });
    }

    function eliminarFiltro(index) {
        filtrosAplicados.splice(index, 1);
        dibujarEtiquetas();
        procesarFiltrosEnDataTables();
    }
</script>

<script>
    // Aplica las búsquedas cruzadas acumulativas en las columnas de DataTables
    function procesarFiltrosEnDataTables() {
        // 1. Limpiar búsquedas previas de la tabla
        table.columns().search('');

        // 2. Agrupar valores por columna (Por si eligen p. ej. Dos países distintos)
        const filtrosPorColumna = {};
        filtrosAplicados.forEach(filtro => {
            const numColumna = mapeoColumnas[filtro.tipo];
            if (!filtrosPorColumna[numColumna]) {
                filtrosPorColumna[numColumna] = [];
            }
            filtrosPorColumna[numColumna].push(filtro.valor);
        });

        // 3. Mandar los datos agrupados a DataTables mediante expresiones regulares
        Object.keys(filtrosPorColumna).forEach(colIdx => {
            const valores = filtrosPorColumna[colIdx];
            const busquedaRegex = `(${valores.join('|')})`; // Resulta en: (Valor1|Valor2)
            table.column(colIdx).search(busquedaRegex, true, false);
        });

        // 4. Redibujar tabla
        table.draw();
    }

    // Resetea por completo el estado de los filtros y la tabla
    function limpiarTodosLosFiltros() {
        filtrosAplicados = [];
        document.getElementById("valorFiltro").value = "";
        dibujarEtiquetas();
        table.columns().search('').draw();
    }
</script>


<script>
    // Envía todos los filtros acumulados a la URL de FPDF en formato JSON
    function imprimirFiltrado() {
        // Convertimos el array de objetos a una cadena JSON limpia
        const filtrosJSON = JSON.stringify(filtrosAplicados);
        
        // Creamos la URL codificando el JSON para que viaje de forma segura por GET
        const url = `../php/imprimir_estudiantes.php?filtros=${encodeURIComponent(filtrosJSON)}`;
        
        // Abrimos el reporte oficial FPDF en una pestaña nueva
        window.open(url, "_blank");
    }
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
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
    document.addEventListener('DOMContentLoaded', function () {
        const eliminarModal = document.getElementById('confirmarEliminarModal');

        eliminarModal.addEventListener('show.bs.modal', function (event) {
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