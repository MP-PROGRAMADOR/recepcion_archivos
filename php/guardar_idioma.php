<?php
require_once '../config/conexion.php'; // Conexión PDO
session_start();

// Inicializar arreglo de errores
$errores = [];

// Validar que se recibió el formulario vía POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar y validar el nombre del idioma
    $nombre = trim($_POST['nombre'] ?? '');

    if (empty($nombre)) {
        $errores[] = "El nombre del idioma es obligatorio.";
    } else {
        $nombre = filter_var($nombre, FILTER_SANITIZE_STRING);

        // Verificar si el idioma ya existe
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM idiomas WHERE nombre = ?");
            $stmt->execute([$nombre]);
            $existe = $stmt->fetchColumn();

            if ($existe > 0) {
                $errores[] = "El idioma '{$nombre}' ya está registrado.";
            }
        } catch (Exception $e) {
            $errores[] = "Error al verificar duplicados: " . $e->getMessage();
        }
    }

    // Si hay errores, redirigir con mensaje
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        header("Location: ../admin/registrar_idiomas.php");
        exit();
    }

    // Si no hay errores, insertar el idioma
    try {
        $stmt = $pdo->prepare("INSERT INTO idiomas (nombre) VALUES (?)");
        $stmt->execute([$nombre]);

        $_SESSION['exito'] = "Idioma registrado correctamente.";
        header("Location: ../admin/idioma.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['errores'] = ["Error al guardar el idioma: " . $e->getMessage()];
        header("Location: ../admin/registrar_idioma.php");
        exit();
    }
} else {
    $_SESSION['errores'] = ["Acceso no permitido."];
    header("Location: ../admin/registrar_idioma.php");
    exit();
}
