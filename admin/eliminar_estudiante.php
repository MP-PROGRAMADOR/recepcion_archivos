

<?php
session_start(); // Iniciar sesión
require_once '../config/conexion.php'; 

// Verificar que se haya recibido un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensaje'] = "No se proporcionó ID de estudiante.";
    $_SESSION['tipo_mensaje'] = "danger";
    header("Location: estudiante.php");
    exit();
}

$estudiante_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($estudiante_id === false || $estudiante_id === null) {
    $_SESSION['mensaje'] = "ID de estudiante no válido.";
    $_SESSION['tipo_mensaje'] = "danger";
    header("Location: estudiante.php");
    exit();
}

try {
    // Obtener nombre del estudiante antes de eliminar
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

    // Eliminar estudiante
    $sql_delete = "DELETE FROM estudiantes WHERE id = :id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $resultado = $stmt_delete->execute([':id' => $estudiante_id]);

    if ($resultado) {
        $_SESSION['mensaje'] = "Se eliminó a $nombre_eliminado.";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar el estudiante.";
        $_SESSION['tipo_mensaje'] = "danger";
    }

    header("Location: estudiantes.php");
    exit();

} catch (PDOException $e) {
    error_log("Error de eliminación de estudiante: " . $e->getMessage());
    $_SESSION['mensaje'] = "Ocurrió un error en el servidor.";
    $_SESSION['tipo_mensaje'] = "danger";
    header("Location: estudiantes.php");
    exit();
}

