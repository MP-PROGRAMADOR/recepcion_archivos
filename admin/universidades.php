<?php
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");




try {
    // Contar universidades que tienen al menos un estudiante
    $conteo_stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT u.id) AS total
        FROM universidades u
        INNER JOIN estudiantes e ON u.id = e.universidad_id
    ");
    $conteo_stmt->execute();
    $total_filas = $conteo_stmt->fetch(PDO::FETCH_ASSOC)['total'];


    // Obtener datos paginados solo con universidades que tienen estudiantes
    $stmt = $pdo->prepare("
        SELECT u.id, u.nombre AS universidad, 
               c.nombre AS ciudad, 
               p.nombre AS pais, 
               COUNT(e.id) AS total_estudiantes
        FROM universidades u
        INNER JOIN ciudades c ON u.ciudad_id = c.id
        INNER JOIN paises p ON c.pais_id = p.id
        INNER JOIN estudiantes e ON u.id = e.universidad_id
        GROUP BY u.id
        ORDER BY u.id DESC
       
    ");

    $stmt->execute();
    $universidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener universidades con estudiantes: " . $e->getMessage());
}
?>


<main class="content" id="mainContent">
    <canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
    <div class="container mt-4">
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
        <?php
            unset($_SESSION['exito']); // Limpiar mensaje de éxito de la sesión
        endif;
        ?>
        <!-- FIN DE LA ALERTA -->

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="bi bi-university me-2"></i>Listado de Universidades Con estudiantes</h3>
            <a href="registrar_universidades.php" class="btn btn-primary">
                <i class="bi bi-person-plus-fill me-1"></i> Nueva Universidad
            </a>
        </div>

        <div class="card shadow rounded-4">
            <div class="card-body">


                <div class="row mb-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filtrar por</label>
                        <select id="tipoFiltro" class="form-select" onchange="controlFiltroUI()">
                            <option value="" disabled selected>Nombre</option>
                            <option value="ciudad">Ciudad</option>
                            <option value="pais">País</option>
                            <option value="orden_az">Nombre (A-Z)</option>
                            <option value="orden_za">Nombre (Z-A)</option>
                            <option value="mayor_estudiantes">Más estudiantes primero</option>
                            <option value="menor_estudiantes">Menos estudiantes primero</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Valor</label>
                        <input type="text" id="valorFiltro" class="form-control"
                            placeholder="Escribe el valor a filtrar">
                    </div>

                    <!-- BOTÓN FILTRAR -->
                    <div class="col-md-1 d-grid">
                        <button class="btn btn-primary d-flex btn-sm px-3 me-2" onclick="aplicarFiltro()">
                            <i class="bi bi-funnel-fill me-2"></i> Filtrar
                        </button>
                    </div>

                    <!-- BOTÓN LIMPIAR -->
                    <div class="col-md-1 d-grid">
                        <button class="btn btn-danger btn-sm px-2 ms-2" onclick="limpiarFiltros()">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>

                    <!-- BOTÓN IMPRIMIR -->
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
                                <th class="text-start"><i class="bi bi-building me-1"></i>Universidad</th>
                                <th><i class="bi bi-geo-alt me-1"></i>Ciudad</th>
                                <th><i class="bi bi-flag me-1"></i>País</th>
                                <th><i class="bi bi-person-fill me-1"></i>Total Estudiantes</th>
                            </tr>
                        </thead>
                        <tbody id="contenidoTabla">
                           
                                <?php foreach ($universidades as $universidad): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($universidad['id']) ?></td>
                                        <td class="text-start"><?= htmlspecialchars($universidad['universidad']) ?></td>
                                        <td><?= htmlspecialchars($universidad['ciudad']) ?></td>
                                        <td><?= htmlspecialchars($universidad['pais']) ?></td>
                                        <td><?= htmlspecialchars($universidad['total_estudiantes']) ?></td>
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
    // Función para manejar la interfaz de usuario al cambiar el tipo de filtro
    function controlFiltroUI() {
        const filtro = document.getElementById('tipoFiltro').value;
        const inputValor = document.getElementById('valorFiltro');

        // Si el usuario elige una opción de "Orden", deshabilitamos el input de texto
        if (filtro.startsWith('orden_') || filtro.includes('estudiantes')) {
            inputValor.value = "";
            inputValor.disabled = true;
            inputValor.placeholder = "Ordenamiento activo...";
        } else {
            inputValor.disabled = false;
            inputValor.placeholder = "Escribe el valor a filtrar";
        }
    }

    function aplicarFiltro() {
        const tipo = document.getElementById('tipoFiltro').value;
        const valor = document.getElementById('valorFiltro').value.toLowerCase();
        const tabla = document.getElementById('contenidoTabla');
        const filas = Array.from(tabla.getElementsByTagName('tr'));

        // 1. Lógica de Filtrado (Mostrar/Ocultar filas)
        let coincidencias = 0;

        // Solo filtramos si no es una opción de ordenamiento puro
        if (!tipo.startsWith('orden_') && !tipo.includes('estudiantes')) {
            filas.forEach(fila => {
                if (fila.cells.length < 5) return; // Ignorar fila de "no hay resultados"

                let textoCelda = "";
                if (tipo === "nombre" || tipo === "") textoCelda = fila.cells[1].textContent.toLowerCase();
                if (tipo === "ciudad") textoCelda = fila.cells[2].textContent.toLowerCase();
                if (tipo === "pais") textoCelda = fila.cells[3].textContent.toLowerCase();

                if (textoCelda.includes(valor)) {
                    fila.style.display = "";
                    coincidencias++;
                } else {
                    fila.style.display = "none";
                }
            });
        } else {
            // Si es ordenamiento, nos aseguramos que todas sean visibles antes de ordenar
            filas.forEach(f => {
                if (f.id !== 'sin-resultados') f.style.display = "";
            });
            coincidencias = filas.length;
            ordenarTabla(tipo);
        }

        manejarMensajeVacio(coincidencias);
    }

    function ordenarTabla(metodo) {
        const tabla = document.getElementById('contenidoTabla');
        const filas = Array.from(tabla.querySelectorAll('tr:not(#sin-resultados)'));

        const sortedRows = filas.sort((a, b) => {
            const valA = a.cells[metodo.includes('estudiantes') ? 4 : 1].textContent.trim();
            const valB = b.cells[metodo.includes('estudiantes') ? 4 : 1].textContent.trim();

            switch (metodo) {
                case 'orden_az':
                    return valA.localeCompare(valB);
                case 'orden_za':
                    return valB.localeCompare(valA);
                case 'mayor_estudiantes':
                    return parseInt(valB) - parseInt(valA);
                case 'menor_estudiantes':
                    return parseInt(valA) - parseInt(valB);
                default:
                    return 0;
            }
        });

        sortedRows.forEach(row => tabla.appendChild(row));
    }

    function manejarMensajeVacio(count) {
        const tabla = document.getElementById('contenidoTabla');
        let filaVacia = document.getElementById('sin-resultados');

        if (count === 0) {
            if (!filaVacia) {
                filaVacia = document.createElement('tr');
                filaVacia.id = 'sin-resultados';
                filaVacia.innerHTML = `<td colspan="5" class="text-center text-muted py-4">No se encontraron coincidencias para su búsqueda</td>`;
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
        document.getElementById('busqueda').value = "";

        const filas = document.querySelectorAll('#contenidoTabla tr');
        filas.forEach(f => f.style.display = "");

        const filaVacia = document.getElementById('sin-resultados');
        if (filaVacia) filaVacia.remove();
    }

    function imprimirFiltrado() {
        const tipo = document.getElementById('tipoFiltro').value;
        const valor = document.getElementById('valorFiltro').value;

        const url = `../php/imprimir_universidades.php?tipo=${tipo}&valor=${encodeURIComponent(valor)}`;
        window.open(url, '_blank');
    }

    // Opcional: Filtro rápido por input "Buscar Universidad"
    document.getElementById('busqueda').addEventListener('keyup', function() {
        const valor = this.value.toLowerCase();
        const filas = document.querySelectorAll('#contenidoTabla tr:not(#sin-resultados)');
        let count = 0;

        filas.forEach(fila => {
            const texto = fila.textContent.toLowerCase();
            if (texto.includes(valor)) {
                fila.style.display = "";
                count++;
            } else {
                fila.style.display = "none";
            }
        });
        manejarMensajeVacio(count);
    });
</script>



<?php include_once("../componentes/footer.php"); ?>