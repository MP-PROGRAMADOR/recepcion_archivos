<?php
// Conexión a base de datos
include_once("../conexion.php");

// Sanitizar búsqueda
$busqueda = '';
if (isset($_POST['busqueda'])) {
    $busqueda = trim($_POST['busqueda']);
}

// Preparar consulta
try {
    $conn = (new Conexion())->getConexion();

    if (!empty($busqueda)) {
        // Búsqueda por ID, nombre o código de acceso
        $sql = "SELECT * FROM estudiantes 
                WHERE id LIKE :busqueda 
                   OR nombre_completo LIKE :busqueda 
                   OR codigo_acceso LIKE :busqueda
                ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':busqueda', "%$busqueda%", PDO::PARAM_STR);
    } else {
        // Sin búsqueda: listar todo
        $sql = "SELECT * FROM estudiantes ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($resultados) {
        foreach ($resultados as $fila) {
            $id = $fila['id'];
            $nombre = htmlspecialchars($fila['nombre_completo']);
            $codigo = htmlspecialchars($fila['codigo_acceso']);
            $fecha_nacimiento = date('d/m/Y', strtotime($fila['fecha_nacimiento']));
            $foto = $fila['foto_url'] ? "<img src='{$fila['foto_url']}' class='img-thumbnail' width='60'>" : "Sin foto";
            $creado = date('d/m/Y H:i', strtotime($fila['creado_en']));

            echo "
                <tr>
                    <td>$id</td>
                    <td>$nombre</td>
                    <td>$codigo</td>
                    <td>$fecha_nacimiento</td>
                    <td>$foto</td>
                    <td>$creado</td>
                    <td>
                        <a href='editar_estudiante.php?id=$id' class='btn btn-sm btn-warning'>
                            <i class='bi bi-pencil-fill'></i>
                        </a>
                        <a href='eliminar_estudiante.php?id=$id' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Seguro que deseas eliminar este estudiante?\")'>
                            <i class='bi bi-trash-fill'></i>
                        </a>
                    </td>
                </tr>
            ";
        }
    } else {
        echo "<tr><td colspan='7'>No se encontraron estudiantes.</td></tr>";
    }

} catch (PDOException $e) {
    echo "<tr><td colspan='7'>Error al buscar estudiantes: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>
