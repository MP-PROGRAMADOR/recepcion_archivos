<?php
session_start();
require_once '../config/conexion.php';

// Habilitar errores de PDO para ver qué falla exactamente
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ciudades.php");
    exit();
}

$ciudad_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

try {
    $sql_nombre = "SELECT nombre FROM ciudades WHERE id = :id";
    $stmt_nombre = $pdo->prepare($sql_nombre);
    $stmt_nombre->execute([':id' => $ciudad_id]);
    $nombre_ciudad = $stmt_nombre->fetchColumn();

    if (!$nombre_ciudad) {
        $_SESSION['error'] = "La ciudad no existe en la base de datos.";
        header("Location: ciudades.php");
        exit();
    }

    $pdo->beginTransaction();

    // PASO A: ¿Hay estudiantes con ciudad_id directo? (Si existe esa columna)
    // Los ponemos en NULL para que no bloqueen el borrado de la ciudad
    $sql_est_ciudad = "UPDATE estudiantes SET ciudad_id = NULL WHERE ciudad_id = :id";
    $stmt_est_ciudad = $pdo->prepare($sql_est_ciudad);
    $stmt_est_ciudad->execute([':id' => $ciudad_id]);

    // PASO B: Desvincular estudiantes de las universidades de esta ciudad
    $sql_desvincular_uni = "UPDATE estudiantes SET universidad_id = NULL 
                            WHERE universidad_id IN (SELECT id FROM universidades WHERE ciudad_id = :id)";
    $stmt_desvincular_uni = $pdo->prepare($sql_desvincular_uni);
    $stmt_desvincular_uni->execute([':id' => $ciudad_id]);

    // PASO C: Eliminar las universidades primero
    $sql_del_unis = "DELETE FROM universidades WHERE ciudad_id = :id";
    $stmt_unis = $pdo->prepare($sql_del_unis);
    $stmt_unis->execute([':id' => $ciudad_id]);

    // PASO D: Eliminar la ciudad finalmente
    $sql_del_ciudad = "DELETE FROM ciudades WHERE id = :id";
    $stmt_ciudad = $pdo->prepare($sql_del_ciudad);
    $resultado = $stmt_ciudad->execute([':id' => $ciudad_id]);

    $pdo->commit();

    if ($resultado) {
        $_SESSION['exito'] = "La ciudad '$nombre_ciudad' y sus dependencias han sido eliminadas.";
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // ESTO ES CLAVE: Guarda el error real en la sesión para que sepas por qué no borra
    $_SESSION['error'] = "Error técnico: " . $e->getMessage();
    error_log("Error de eliminación: " . $e->getMessage());
}

header("Location: ciudades.php");
exit();
