<?php
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

// Configuración de paginación
$por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$inicio = ($pagina_actual > 1) ? ($pagina_actual * $por_pagina) - $por_pagina : 0;

// Contar el total de cuentas bancarias
$total_stmt = $pdo->query("SELECT COUNT(*) as total FROM cuentas_bancarias");
$total_filas = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_filas / $por_pagina);

// Obtener cuentas bancarias con paginación
$stmt = $pdo->prepare("SELECT cb.*, 
                              est.nombre_completo AS titular
                       FROM cuentas_bancarias cb
                       LEFT JOIN estudiantes est ON cb.estudiante_id = est.id
                       ORDER BY cb.id DESC
                       LIMIT :inicio, :por_pagina");
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
$stmt->execute();
$cuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="content" id="mainContent">
    <canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
    <div class="container mt-4">

        <!-- INICIO DE LA ALERTA -->
        <?php include_once("../componentes/alerta.php"); ?>
        <!-- FIN DE LA ALERTA -->

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="bi bi-bank2 me-2"></i>Listado de Cuentas Bancarias</h3>
        </div>

        <div class="card shadow rounded-4">
            <div class="card-body">
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
                                <th><i class="bi bi-bank me-1"></i>Banco</th>
                                <th><i class="bi bi-credit-card-2-front me-1"></i>N° Cuenta</th>
                                <th><i class="bi bi-person-vcard me-1"></i>Titular</th>
                                <th><i class="bi bi-wallet2 me-1"></i>Tipo Cuenta</th>
                                <th><i class="bi bi-credit-card me-1"></i>Tarjeta Visa</th>
                                <th><i class="bi bi-calendar-check me-1"></i>Caducidad de Visa</th>
                                <th><i class="bi bi-gear me-1"></i>Acciones</th>

                            </tr>
                        </thead>
                        <tbody id="contenidoTabla">
                            <?php if (!empty($cuentas)): ?>
                                <?php foreach ($cuentas as $cuenta): ?>
                                    <tr>
                                        <td><i class="bi bi-hash me-1"></i><?= htmlspecialchars($cuenta['id']) ?></td>
                                        <td><i class="bi bi-bank me-1"></i><?= htmlspecialchars($cuenta['banco']) ?></td>
                                        <td><i
                                                class="bi bi-credit-card-2-front me-1"></i><?= htmlspecialchars($cuenta['numero_cuenta']) ?>
                                        </td>
                                        <td><i class="bi bi-person-vcard me-1"></i><?= htmlspecialchars($cuenta['titular']) ?>
                                        </td>
                                        <td><i class="bi bi-wallet2 me-1"></i><?= htmlspecialchars($cuenta['tipo_cuenta']) ?>
                                        </td>
                                        <td><i
                                                class="bi bi-credit-card me-1"></i><?= !empty($cuenta['tarjeta_visa']) ? htmlspecialchars($cuenta['tarjeta_visa']) : 'No dispone' ?>
                                        </td>
                                        <td><i
                                                class="bi bi-calendar-check me-1"></i><?= !empty($cuenta['fecha_caducidad_tarjeta']) ? date('d/m/Y', strtotime($cuenta['fecha_caducidad_tarjeta'])) : 'No definida' ?>
                                        </td>
                                        <td>
                                            <a href="editar_cuenta.php?id=<?= $cuenta['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </a>
                                        </td>


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
<script>
    $(document).ready(function () {
        $('#busqueda').on('keyup', function () {
            let valor = $(this).val().toLowerCase();
            $('#contenidoTabla tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().includes(valor));
            });
        });
    });
</script>
<?php include_once("../componentes/footer.php"); ?>