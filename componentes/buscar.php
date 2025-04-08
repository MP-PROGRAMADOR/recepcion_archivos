


<?php
require 'conexion.php';

$busqueda = $_POST['busqueda'] ?? '';

try {
    $pdo = Conexion::conectar();

    $sql = "SELECT * FROM usuarios WHERE 
            id LIKE :busqueda OR
            nombre_usuario REGEXP :regex OR
            email REGEXP :regex
            ORDER BY fecha_creacion DESC";

    $stmt = $pdo->prepare($sql);

    $regex = ".*" . preg_quote($busqueda, '/') . ".*";
    $stmt->bindValue(':busqueda', "%$busqueda%");
    $stmt->bindValue(':regex', $regex);
    $stmt->execute();

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($usuarios) {
        foreach ($usuarios as $usuario) {
            echo "<tr>";
            echo "<td>{$usuario['id']}</td>";
            echo "<td>{$usuario['nombre_usuario']}</td>";
            echo "<td>{$usuario['email']}</td>";
            echo "<td>" . date("d/m/Y H:i", strtotime($usuario['fecha_creacion'])) . "</td>";
            echo "<td>
                    <a href='editar_usuario.php?id={$usuario['id']}' class='btn btn-sm btn-warning me-1'>
                        <i class='bi bi-pencil-square'></i>
                    </a>
                    <a href='eliminar_usuario.php?id={$usuario['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Seguro que deseas eliminar este usuario?\")'>
                        <i class='bi bi-trash-fill'></i>
                    </a>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No se encontraron usuarios.</td></tr>";
    }

} catch (PDOException $e) {
    echo "<tr><td colspan='5'>Error al buscar usuarios: " . $e->getMessage() . "</td></tr>";
}
