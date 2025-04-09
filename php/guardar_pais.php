<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';

    // Validación
    if (empty($nombre)) {
        $_SESSION['error'] = 'El nombre del país es obligatorio.';
        header("Location: ../admin/pais.php");
        exit;
    }

    if (strlen($nombre) > 50) {
        $_SESSION['error'] = 'El nombre del país no puede exceder los 50 caracteres.';
        header("Location: ../admin/pais.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO paises (nombre) VALUES (:nombre)");
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();

        $_SESSION['exito'] = 'País registrado exitosamente.';
        header("Location: ../admin/pais.php");
        exit;

    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            $_SESSION['error'] = 'El país ingresado ya existe.';
        } else {
            $_SESSION['error'] = "Error al guardar el país: " . $e->getMessage();
        }
        header("Location: ../admin/pais.php");
        exit;
    }

} else {
    $_SESSION['error'] = 'Acceso no permitido.';
    header("Location: ../admin/pais.php");
    exit;
}
