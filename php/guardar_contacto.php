<?php
session_start();

// Validar sesión activa de estudiante
if (!isset($_SESSION['estudiante'])) {
    header("Location: index.php");
    exit();
}

$estudianteSesion = $_SESSION['estudiante'];

require_once '../config/conexion.php';

// Validar si los datos del formulario están disponibles
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Filtrar y sanitizar los datos recibidos
    $estudiante_id = filter_var($_POST['estudiante_id'], FILTER_SANITIZE_NUMBER_INT);
    $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
    $telefono = filter_var($_POST['telefono'], FILTER_SANITIZE_STRING);

    // Arreglo de errores
    $errores = [];

    // Validar que los campos no estén vacíos
    if (empty($correo) || empty($telefono)) {
        $errores[] = "Todos los campos son requeridos.";
    }

    // Validar formato de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Correo electrónico no válido.";
    }

    // Validar formato del teléfono (mínimo 8 dígitos)
    if (!preg_match("/^\d{7,15}$/", $telefono)) {
        $errores[] = "Número de teléfono no válido. Debe tener entre 7 y 15 dígitos.";
    }

    // Si hay errores, mostrar mensaje y redirigir
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        header("Location: editar_contacto.php");
        exit();
    }

    // Preparar la actualización en la base de datos
    try {
        $stmt = $pdo->prepare("UPDATE estudiantes SET email = ?, telefono = ? WHERE id = ?");
        $stmt->execute([$correo, $telefono, $estudiante_id]);

        // Verificar si la actualización fue exitosa
        if ($stmt->rowCount() > 0) {
            $_SESSION['exito'] = "Información de contacto actualizada exitosamente.";
        } else {
            $_SESSION['errores'] = ["No se realizaron cambios en la información de contacto."];
        }

    } catch (PDOException $e) {
        // Manejar cualquier error en la base de datos
        $_SESSION['errores'] = ["Error de conexión: " . htmlspecialchars($e->getMessage())];
    }

    // Redirigir de vuelta al formulario
    header("Location: editar_contacto.php");
    exit();
}
?>
