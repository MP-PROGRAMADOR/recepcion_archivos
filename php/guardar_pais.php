<?php
// Conexión a la base de datos
require_once '../config/conexion.php';

// Validar que el formulario haya sido enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validar y sanitizar entrada
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';

    // Validación básica
    if (empty($nombre)) {
        die('Error: El nombre del país es obligatorio.');
    }

    if (strlen($nombre) > 50) {
        die('Error: El nombre del país no puede exceder los 50 caracteres.');
    }

    try {
        // Insertar el país
        $stmt = $pdo->prepare("INSERT INTO paises (nombre) VALUES (:nombre)");
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();

        // Redirigir con éxito
        header("Location: paises.php?mensaje=registro_exitoso");
        exit;

    } catch (PDOException $e) {
        // Validación de errores SQL (ej. país duplicado)
        if ($e->errorInfo[1] == 1062) {
            die('Error: El país ingresado ya existe.');
        } else {
            die("Error al guardar el país: " . $e->getMessage());
        }
    }

} else {
    // Si no es POST, denegar acceso directo
    die('Acceso no permitido.');
}
?>
