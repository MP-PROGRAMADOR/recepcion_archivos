<?php

include_once("../componentes/header.php");

//require_once '../config/conexion.php';

// Consulta de estudiantes con JOIN a países
// Configuración de paginación
$por_pagina = 4;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina_actual > 1) ? ($pagina_actual * $por_pagina) - $por_pagina : 0;

// Contar total de estudiantes
try {
    $total_stmt = $pdo->query("SELECT COUNT(*) as total FROM pasaportes");
    $total_filas = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_paginas = ceil($total_filas / $por_pagina);
} catch (PDOException $e) {
    die("Error al contar los estudiantes: " . $e->getMessage());
}

// Obtener estudiantes con límite y offset (incluyendo país)
try {
    $stmt = $pdo->prepare("
SELECT 
    n.id,
    n.observaciones,
    n.archivo_url,
    n.fecha_subida,
    e.nombre_completo,
    e.codigo_acceso,
    a.nombre
FROM 
    notas n
INNER JOIN 
    estudiantes e ON n.estudiante_id = e.id
INNER JOIN 
    anios_academicos a ON n.anio_academico_id = a.id
ORDER BY 
    n.fecha_subida DESC
LIMIT :inicio, :por_pagina;

    ");
    $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
    $stmt->execute();
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los estudiantes: " . $e->getMessage());
}



// Layout común
include_once("../componentes/sidebar.php");
?>

<main class="content" id="mainContent">
    <canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
    <div class="container-fluid">
        <!-- INICIO DE LA ALERTA -->
        <?php


        if (isset($_SESSION['exito']) && !empty($_SESSION['exito'])):
        ?>
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
                // Ocultar automáticamente luego de 6 segundos
                setTimeout(() => {
                    const alerta = document.getElementById('alerta-exito');
                    if (alerta) {
                        alerta.classList.remove('show');
                        alerta.classList.add('fade');
                        setTimeout(() => alerta.remove(), 500); // Lo remueve del DOM
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
        <?php
            unset($_SESSION['exito']); // Limpiar mensaje de éxito de la sesión
        endif;
        ?>

        <!-- FIN DE LA ALERTA -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="bi bi-mortarboard-fill me-2"></i>Listado de Notas</h3>

        </div>

        <div class="card shadow rounded-4">
            <div class="card-body">






                <div class="row mb-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filtrar por</label>
                        <select id="tipoFiltro" class="form-select" onchange="controlFiltroUI()">
                            <option value="" disabled selected>Seleccione filtro</option>
                            <option value="estudiante">Nombre del Estudiante</option>
                            <option value="anio">Año Académico</option>
                            <option value="observaciones">Observaciones</option>
                            <option value="orden_nombre_az">Nombre (A-Z)</option>
                            <option value="orden_fecha_reciente">Más recientes primero</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Valor</label>
                        <input type="text" id="valorFiltro" class="form-control" placeholder="Escribe el valor a filtrar">
                    </div>

                    <div class="col-md-1 d-grid">
                        <button class="btn btn-primary btn-sm px-3" onclick="aplicarFiltro()">
                            <i class="bi bi-funnel-fill"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-1 d-grid">
                        <button class="btn btn-danger btn-sm px-2" onclick="limpiarFiltros()">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <div class="col-md-1 d-grid">
                        <button class="btn btn-success d-flex btn-sm px-3" onclick="imprimirFiltrado()">
                            <i class="bi bi-printer-fill me-2"></i> Imprimir
                        </button>
                    </div>
                </div>



                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="busqueda" class="form-label fw-bold">Buscar Notas</label>
                        <input type="text" class="form-control" id="busqueda" placeholder="Buscar por nombre o Codigo de Estudiante...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-center" id="tablaEstudiantes">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th class="text-start">Nombre Completo</th>
                                <th>Año Academico</th>
                                <th class="text-start">Observaciones</th>
                                <th>Fecha de Subida</th>
                                <th>Archivo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="contenidoTabla">
                            <?php if (!empty($estudiantes)): ?>
                                <?php foreach ($estudiantes as $est): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($est['id']) ?></td>
                                        <td class="text-start"><?= htmlspecialchars($est['nombre_completo']) ?></td>
                                        <td><?= htmlspecialchars($est['nombre']) ?></td>
                                        <td class="text-start"><?= htmlspecialchars(mb_strimwidth($est['observaciones'], 0, 50, '...')) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($est['fecha_subida'])) ?></td>
                                        <td>

                                            <?php
                                            $foto = $est['archivo_url'];
                                            // Ruta relativa para mostrar en el navegador
                                            $rutaRelativa = '../php/upload/notas/' . basename($foto);
                                            // Ruta del servidor para verificar existencia del archivo
                                            $rutaServidor = __DIR__ . '/../php/upload/notas/' . basename($foto);
                                            // Obtener la extensión del archivo
                                            $extension = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
                                            ?>

                                            <?php if (!empty($foto) && file_exists($rutaServidor)): ?>
                                                <?php if ($extension === 'pdf'): ?>
                                                    <!-- Ícono PDF -->
                                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-1"></i>
                                                <?php else: ?>
                                                    <!-- Imagen cuadrada (para cualquier otro tipo de archivo) -->
                                                    <img src="<?= $rutaRelativa ?>" class="img-thumbnail shadow"
                                                        alt="Archivo de <?= htmlspecialchars($est['nombre_completo']) ?>" width="60" height="60">
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Sin Archivo</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>

                                            <?php if (!empty($foto) && file_exists($rutaServidor)): ?>
                                                <?php if ($extension === 'pdf'): ?>
                                                    <a href="<?= $rutaRelativa ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye-fill me-1"></i> Ver
                                                    </a>
                                                <?php else: ?>
                                                    <!-- O puedes poner un botón para ver la imagen si quieres -->
                                                    <a href="<?= $rutaRelativa ?>" target="_blank" class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-eye-fill me-1"></i> Ver
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No hay Notas registradas</td>
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

                <!-- FIN DE LA PAGINACION -->

            </div>
        </div>
    </div>
</main>

<!-- Bootstrap Icons & jQuery -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function controlFiltroUI() {
        const filtro = document.getElementById('tipoFiltro').value;
        const inputValor = document.getElementById('valorFiltro');
        inputValor.disabled = filtro.startsWith('orden_');
        if (inputValor.disabled) inputValor.value = "";
    }

    function aplicarFiltro() {
        const tipo = document.getElementById('tipoFiltro').value;
        const valor = document.getElementById('valorFiltro').value.toLowerCase();
        const tabla = document.getElementById('contenidoTabla');
        const filas = Array.from(tabla.getElementsByTagName('tr'));
        let coincidencias = 0;

        if (!tipo.startsWith('orden_')) {
            filas.forEach(fila => {
                if (fila.cells.length < 2) return; // Saltar fila de "no hay datos"

                let textoCelda = "";
                // Mapeo de columnas: 0:ID, 1:Nombre, 2:Año, 3:Observaciones
                if (tipo === "id") textoCelda = fila.cells[0].textContent.toLowerCase();
                else if (tipo === "estudiante" || tipo === "") textoCelda = fila.cells[1].textContent.toLowerCase();
                else if (tipo === "anio") textoCelda = fila.cells[2].textContent.toLowerCase();
                else if (tipo === "observaciones") textoCelda = fila.cells[3].textContent.toLowerCase();

                if (textoCelda.includes(valor)) {
                    fila.style.display = "";
                    coincidencias++;
                } else {
                    fila.style.display = "none";
                }
            });
        } else {
            filas.forEach(f => f.style.display = "");
            coincidencias = filas.length;
            ordenarTabla(tipo);
        }
        manejarMensajeVacio(coincidencias);
    }

    function ordenarTabla(metodo) {
        const tabla = document.getElementById('contenidoTabla');
        const filas = Array.from(tabla.querySelectorAll('tr')).filter(f => f.cells.length > 1);

        const sortedRows = filas.sort((a, b) => {
            if (metodo === 'orden_nombre_az') {
                return a.cells[1].textContent.trim().localeCompare(b.cells[1].textContent.trim());
            } else if (metodo === 'orden_fecha_reciente') {
                // Compara las fechas (columna 4) convirtiéndolas a formato comparable
                return new Date(b.cells[4].textContent) - new Date(a.cells[4].textContent);
            }
            return 0;
        });

        sortedRows.forEach(row => tabla.appendChild(row));
    }

    function manejarMensajeVacio(count) {
        const tabla = document.getElementById('contenidoTabla');
        let filaVacia = document.getElementById('sin-resultados-msg');
        if (count === 0) {
            if (!filaVacia) {
                filaVacia = document.createElement('tr');
                filaVacia.id = 'sin-resultados-msg';
                filaVacia.innerHTML = `<td colspan="8" class="text-center text-muted py-4"> <i class="bi bi-search me-2"></i> No se encontraron coincidencias</td>`;
                tabla.appendChild(filaVacia);
            }
        } else if (filaVacia) {
            filaVacia.remove();
        }
    }

    function limpiarFiltros() {
        document.getElementById('tipoFiltro').value = "";
        document.getElementById('valorFiltro').value = "";
        document.getElementById('valorFiltro').disabled = false;
        const filas = document.querySelectorAll('#contenidoTabla tr');
        filas.forEach(f => f.style.display = "");
        const msg = document.getElementById('sin-resultados-msg');
        if (msg) msg.remove();
    }

    function imprimirFiltrado() {
        const tipo = document.getElementById('tipoFiltro').value;
        const valor = document.getElementById('valorFiltro').value;
        // Ajusta esta ruta a tu nuevo archivo de impresión de notas
        window.open(`../php/imprimir_notas_estudiantes.php?tipo=${tipo}&valor=${encodeURIComponent(valor)}`, '_blank');
    }
</script>

<!-- Buscador en tiempo real -->
<script>
    $(document).ready(function() {
        $("#busqueda").on("keyup", function() {
            let valor = $(this).val().toLowerCase();
            let filas = $("#contenidoTabla tr:not(#sin-resultados-busqueda)");
            
            // 1. Filtrar las filas
            filas.filter(function() {
                $(this).toggle($(this).text().toLowerCase().includes(valor));
            });

            // 2. Verificar si hay filas visibles
            let visibles = filas.filter(":visible").length;
            
            // 3. Manejar el mensaje de "No se encontraron resultados"
            if (visibles === 0) {
                if ($("#sin-resultados-busqueda").length === 0) {
                    $("#contenidoTabla").append(
                        `<tr id="sin-resultados-busqueda">
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-search me-2"></i> No se encontraron coincidencias para "${$(this).val()}"
                            </td>
                        </tr>`
                    );
                }
            } else {
                $("#sin-resultados-busqueda").remove();
            }
        });
    });
</script>

<?php include_once("../componentes/footer.php"); ?>