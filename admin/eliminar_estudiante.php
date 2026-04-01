<?php
session_start();
require_once '../config/conexion.php'; 

// 1. Cambiamos $_GET por $_POST
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $_SESSION['mensaje'] = "No se proporcionó ID de estudiante.";
    $_SESSION['tipo_mensaje'] = "danger";
    header("Location: estudiantes.php");
    exit();
}

// 2. Cambiamos INPUT_GET por INPUT_POST
$estudiante_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($estudiante_id === false || $estudiante_id === null) {
    $_SESSION['mensaje'] = "ID de estudiante no válido.";
    $_SESSION['tipo_mensaje'] = "danger";
    header("Location: estudiantes.php");
    exit();
}

try {
    // El resto de tu código TRY/CATCH está perfecto...
    $sql_select_nombre = "SELECT nombre_completo FROM estudiantes WHERE id = :id";
    $stmt_nombre = $pdo->prepare($sql_select_nombre);
    $stmt_nombre->execute([':id' => $estudiante_id]);
    $nombre_eliminado = $stmt_nombre->fetchColumn();

    if (!$nombre_eliminado) {
        $_SESSION['mensaje'] = "El estudiante no existe.";
        $_SESSION['tipo_mensaje'] = "danger";
        header("Location: estudiantes.php");
        exit();
    }

    $sql_delete = "DELETE FROM estudiantes WHERE id = :id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $resultado = $stmt_delete->execute([':id' => $estudiante_id]);

    if ($resultado) {
        $_SESSION['mensaje'] = "Se eliminó a $nombre_eliminado correctamente.";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar el estudiante.";
        $_SESSION['tipo_mensaje'] = "danger";
    }

    header("Location: estudiantes.php");
    exit();

} catch (PDOException $e) {
    error_log("Error de eliminación: " . $e->getMessage());
    $_SESSION['mensaje'] = "Error en el servidor.";
    $_SESSION['tipo_mensaje'] = "danger";
    header("Location: estudiantes.php");
    exit();
}