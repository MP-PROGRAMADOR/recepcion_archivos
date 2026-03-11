<?php
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

// --- LÓGICA DE PAGINACIÓN ---


// Contar total
$total_stmt = $pdo->query("SELECT COUNT(DISTINCT pais_id) as total FROM estudiantes");
$total_filas = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];


// Obtener datos
try {
    $stmt = $pdo->prepare("SELECT p.id, p.nombre, COUNT(e.id) as estudiantes 
                           FROM paises p
                           LEFT JOIN estudiantes e ON p.id = e.pais_id
                           GROUP BY p.id
                           HAVING estudiantes > 0
                           ORDER BY p.id DESC
                           ");
 
    $stmt->execute();
    $paises = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<main class="content" id="mainContent">
    <div class="container mt-4">

        <?php if (isset($_SESSION['exito'])): ?>
            <div id="alerta-exito" class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>¡Éxito!</strong> <?= htmlspecialchars($_SESSION['exito']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['exito']); ?>
        <?php endif; ?>

        <h3><i class="bi bi-globe-americas me-2"></i>Países con Estudiantes</h3>

        <div class="card shadow rounded-4 mt-3">
            <div class="card-body">




                <div class="row mb-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filtrar por</label>
                        <select id="tipoFiltro" class="form-select" onchange="controlFiltroUI()">
                            <option value="nombre" selected>Nombre del País</option>
                            <option value="id">ID del País</option>
                            <option value="estudiantes_mayor">Más de X estudiantes</option>
                            <option value="estudiantes_menor">Menos de X estudiantes</option>
                            <option value="orden_az">Nombre (A-Z)</option>
                            <option value="orden_za">Nombre (Z-A)</option>
                            <option value="mas_inscritos">Más inscritos primero</option>
                        </select>
                    </div>

                    <div class="col-md-4 me-4">
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
                        <button class="btn btn-success d-flex btn-sm px-3" onclick="imprimirFiltrado()">
                            <i class="bi bi-printer-fill me-2"></i> Imprimir
                        </button>
                    </div>


                </div>



                <div class="table-responsive">
                  <table class="table table-striped table-hover align-middle text-center" id="tablaRecepcion">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Estudiantes</th>
                            </tr>
                        </thead>
                        <tbody id="contenidoTabla">
                           
                                <?php foreach ($paises as $pais): ?>
                                    <tr class="fila-datos">
                                        <td><?= htmlspecialchars($pais['id']) ?></td>
                                        <td ><?= htmlspecialchars($pais['nombre']) ?></td>
                                        <td><?= htmlspecialchars($pais['estudiantes']) ?></td>
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
    // 1. CONTROL DE LA INTERFAZ (UI)
    function controlFiltroUI() {
        const tipo = $('#tipoFiltro').val();
        const input = $('#valorFiltro');

        // Resetear estados
        input.prop('disabled', false).val('');

        if (tipo.includes('estudiantes')) {
            input.attr('type', 'number').attr('placeholder', 'Ej: 10');
        } else if (tipo.includes('orden') || tipo === 'mas_inscritos') {
            input.prop('disabled', true).attr('placeholder', 'No requiere valor');
        } else {
            input.attr('type', 'text').attr('placeholder', 'Escribe el valor...');
        }
    }

    // 2. APLICAR FILTROS Y ORDENAMIENTO VISUAL/
    function aplicarFiltro() {
        const tipo = $('#tipoFiltro').val();
        const valor = $('#valorFiltro').val().toLowerCase();
        const filas = $('#contenidoTabla tr.fila-datos');
        let visibles = 0;

        // Lógica de filtrado
        filas.each(function() {
            const fila = $(this);
            const id = fila.find('td:eq(0)').text().toLowerCase();
            const nombre = fila.find('td:eq(1)').text().toLowerCase();
            const numEstudiantes = parseInt(fila.find('td:eq(2)').text()) || 0;

            let cumple = false;

            switch (tipo) {
                case 'nombre':
                    cumple = nombre.includes(valor);
                    break;
                case 'id':
                    cumple = (id === valor || valor === "");
                    break;
                case 'estudiantes_mayor':
                    cumple = (numEstudiantes >= (parseInt(valor) || 0));
                    break;
                case 'estudiantes_menor':
                    cumple = (numEstudiantes <= (parseInt(valor) || 999999));
                    break;
                default:
                    cumple = true; // Para los casos de ordenamiento simple
            }

            fila.toggle(cumple);
            if (cumple) visibles++;
        });

        // Lógica de ordenamiento (si aplica)
        if (tipo.includes('orden') || tipo === 'mas_inscritos') {
            ordenarTablaVisual(tipo);
        }

        // Mostrar/Ocultar mensaje de "No encontrado"
        $('#filaNoResultados').toggle(visibles === 0);
    }

    //3. ORDENAMIENTO DINÁMICO DE FILAS/
    function ordenarTablaVisual(metodo) {
        const tbody = $('#contenidoTabla');
        const filas = tbody.find('tr.fila-datos').toArray();

        filas.sort(function(a, b) {
            const nomA = $(a).find('td:eq(1)').text().trim();
            const nomB = $(b).find('td:eq(1)').text().trim();
            const numA = parseInt($(a).find('td:eq(2)').text()) || 0;
            const numB = parseInt($(b).find('td:eq(2)').text()) || 0;

            if (metodo === 'orden_az') return nomA.localeCompare(nomB);
            if (metodo === 'orden_za') return nomB.localeCompare(nomA);
            if (metodo === 'mas_inscritos') return numB - numA;
            return 0;
        });

        $.each(filas, function(i, fila) {
            tbody.append(fila);
        });
    }

    // 4. IMPRIMIR (Conexión con FPDF) Envía los filtros actuales al archivo PHP mediante la URL

    function imprimirFiltrado() {
        const tipo = $('#tipoFiltro').val();
        const valor = $('#valorFiltro').val();

        // Construimos la URL para el archivo que genera el PDF
        const baseDir = "../php/imprimir_paises.php"; // Ajusta la ruta a tu archivo FPDF
        const params = `?tipo=${encodeURIComponent(tipo)}&valor=${encodeURIComponent(valor)}`;

        window.open(baseDir + params, "_blank");
    }

    // 5. LIMPIAR TODO
    function limpiarFiltros() {
        $('#tipoFiltro').val('nombre');
        $('#valorFiltro').val('');
        $('#busqueda').val('');
        controlFiltroUI();
        $('#contenidoTabla tr.fila-datos').show();
        $('#filaNoResultados').hide();
    }
</script>


<?php include_once("../componentes/footer.php"); ?>