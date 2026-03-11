<?php
include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

// Configuración de paginación


// Filtros
$tipo = $_GET['tipo'] ?? '';
$valor = trim($_GET['valor'] ?? '');

// 1️⃣ Base de la consulta
$sql = "
    SELECT u.id, u.nombre, u.email, u.creado_en, r.nombre AS rol
    FROM usuarios u
    INNER JOIN rol r ON u.rol_id = r.id
    WHERE 1=1
";

$params = [];

// 2️⃣ Aplicar filtros
if ($valor !== '' && in_array($tipo, ['nombre','rol'])) {
    if ($tipo === 'nombre') {
        $sql .= " AND u.nombre LIKE :valor";
    } elseif ($tipo === 'rol') {
        $sql .= " AND r.nombre LIKE :valor";
    }
    $params[':valor'] = "%$valor%";
}

// 3️⃣ Ordenamiento
if ($tipo === 'orden_az') {
    $sql .= " ORDER BY u.nombre ASC";
} elseif ($tipo === 'orden_za') {
    $sql .= " ORDER BY u.nombre DESC";
} else {
    $sql .= " ORDER BY u.id DESC";
}



try {
    $stmt = $pdo->prepare($sql);

    // Bind de parámetros de filtro
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }



    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los usuarios: " . $e->getMessage());
}

// 5️⃣ Contar total de usuarios según filtro para paginación
$total_sql = "SELECT COUNT(*) as total FROM usuarios u INNER JOIN rol r ON u.rol_id = r.id WHERE 1=1";
$total_params = [];

if ($valor !== '' && in_array($tipo, ['nombre','rol'])) {
    if ($tipo === 'nombre') {
        $total_sql .= " AND u.nombre LIKE :valor";
    } else {
        $total_sql .= " AND r.nombre LIKE :valor";
    }
    $total_params[':valor'] = "%$valor%";
}

try {
    $total_stmt = $pdo->prepare($total_sql);
    foreach ($total_params as $key => $val) {
        $total_stmt->bindValue($key, $val);
    }
    $total_stmt->execute();
    $total_usuarios = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
  
} catch (PDOException $e) {
    die("Error al contar usuarios: " . $e->getMessage());
}

// Rol del usuario logueado
$rol = $_SESSION['usuario_rol'];
?>

<main class="content" id="mainContent">
    <canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="bi bi-people-fill me-2"></i>Listado de Usuarios</h3>
        <a href="registrar_usuario.php" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i> Nuevo Usuario
        </a>
    </div>

    <div class="card shadow rounded-4">
        <div class="card-body">



        <div class="row mb-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filtrar por</label>
                        <select id="tipoFiltro" class="form-select" onchange="controlFiltroUI()">
                            <option value="nombre">Nombre</option>
                            <option value="rol">Rol</option>
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
                            <th class="text-start">Email</th>
                            <th class="text-start">Rol</th>
                            <th>Fecha de Creación</th>
                            <?php if(strtolower($rol) === 'administrador'): ?>
                            <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody id="contenidoTabla">
                      
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['id']) ?></td>
                            <td class="text-start"><?= htmlspecialchars($usuario['nombre']) ?></td>
                            <td class="text-start"><?= htmlspecialchars($usuario['email']) ?></td>
                            <td class="text-start"><?= htmlspecialchars($usuario['rol']) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($usuario['creado_en']))) ?></td>

                            <?php if(strtolower($rol) === 'administrador'): ?>
                            <td>
                                <a href="editar_usuario.php?id=<?= $usuario['id'] ?>"
                                    class="btn btn-sm btn-warning me-1" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="eliminar_usuario.php?id=<?= $usuario['id'] ?>"
                                    class="btn btn-sm btn-danger d-none" title="Eliminar"
                                    onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                       
                    </tbody>
                </table>
            </div>

         
        </div>
    </div>
</main>





<script>
// Aplica el filtro y recarga la página con parámetros GET
function aplicarFiltro() {
    const tipo = document.getElementById("tipoFiltro").value;
    const valor = document.getElementById("valorFiltro").value.trim();

    const url = new URL(window.location);

    // Establecer tipo
    if (tipo) {
        url.searchParams.set("tipo", tipo);
    }

    // Establecer valor solo si no es orden
    if (valor && tipo !== "orden_az" && tipo !== "orden_za") {
        url.searchParams.set("valor", valor);
    } else {
        url.searchParams.delete("valor");
    }

    // Reiniciar a página 1 al filtrar
    url.searchParams.set("pagina", 1);

    window.location.href = url.toString();
}

// Controla la UI del input según el tipo de filtro
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

    if (tipo) {
        document.getElementById("tipoFiltro").value = tipo;
    }

    if (valor) {
        document.getElementById("valorFiltro").value = valor;
    }

    controlFiltroUI();
});

// Limpiar filtros y recargar
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

    // Abrir nueva ventana con filtros como parámetros GET
    const url = `../php/imprimir_usuarios.php?tipo=${encodeURIComponent(tipo)}&valor=${encodeURIComponent(valor)}`;
    window.open(url, "_blank");
}

// Filtro de búsqueda rápida en la tabla (opcional)
document.getElementById("busqueda")?.addEventListener("keyup", function () {
    const valor = this.value.toLowerCase();
    const filas = document.querySelectorAll("#contenidoTabla tr");

    filas.forEach(fila => {
        fila.style.display = fila.textContent.toLowerCase().includes(valor) ? "" : "none";
    });
});
</script>

<?php include_once("../componentes/footer.php"); ?>