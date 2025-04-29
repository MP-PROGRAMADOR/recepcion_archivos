<?php
session_start();
require_once '../config/conexion.php'; // Incluye el archivo de conexión

// Verificamos si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recuperamos los valores del formulario y limpiamos cualquier entrada no deseada
    $estudiante_id = isset($_POST['estudiante_id']) ? (int) $_POST['estudiante_id'] : 0;
    $correo = filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL);
    $telefono = trim($_POST['telefono']);

    // Validamos los campos
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Correo electrónico inválido';
        header('Location: ../estudiante/panel_estudiante.php');
        exit();
    }

    // Validar teléfono (solo números y longitud entre 7 y 15)
    if (!preg_match('/^\d{7,15}$/', $telefono)) {
        $_SESSION['error'] = 'Número de teléfono inválido (debe tener entre 7 y 15 dígitos)';
        header('Location: ../estudiante/panel_estudiante.php');
        exit();
    }

    // Usamos PDO para actualizar los datos de contacto
    try {
        // Preparamos la consulta de actualización
        $sql = "UPDATE estudiantes SET email = :email, telefono = :telefono WHERE id = :estudiante_id";
        $stmt = $pdo->prepare($sql);

        // Vinculamos los parámetros
        $stmt->bindParam(':email', $correo, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);

        // Ejecutamos la consulta
        $stmt->execute();

        // Si todo salió bien, mostramos un mensaje de éxito
        $_SESSION['success'] = 'Datos de contacto actualizados correctamente';
        header('Location: ../estudiante/panel_estudiante.php');
        exit();
    } catch (PDOException $e) {
        // Si ocurre un error con la base de datos
        $_SESSION['error'] = 'Error al actualizar los datos: ' . $e->getMessage();
        header('Location: ../estudiante/panel_estudiante.php');
        exit();
    }
}
?>











