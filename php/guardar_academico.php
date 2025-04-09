<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitizar entrada
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';

    // Validación básica
    if (empty($nombre)) {
        $_SESSION['error'] = 'El nombre del Año Académico es obligatorio.';
        header("Location: ../admin/registrar_academico.php");
        exit;
    }

    // Validar formato XXXX-YYYY
    if (!preg_match('/^\d{4}-\d{4}$/', $nombre)) {
        $_SESSION['error'] = 'El formato del Año Académico debe ser YYYY-YYYY (ej. 2023-2024).';
        header("Location: ../admin/registrar_academico.php");
        exit;
    }

    // Separar los años
    list($anio1, $anio2) = explode('-', $nombre);
    if ((int)$anio1 >= (int)$anio2) {
        $_SESSION['error'] = 'El primer año debe ser menor que el segundo (ej. 2023-2024).';
        header("Location: ../admin/registrar_academico.php");
        exit;
    }

    // Longitud innecesaria ya que el formato está fijo, pero la dejamos por seguridad
    if (strlen($nombre) > 50) {
        $_SESSION['error'] = 'El nombre del Año Académico no puede exceder los 50 caracteres.';
        header("Location: ../admin/registrar_academico.php");
        exit;
    }

    try {
        // Insertar en la base de datos
        $stmt = $pdo->prepare("INSERT INTO anios_academicos (nombre) VALUES (:nombre)");
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();

        $_SESSION['exito'] = 'Año Académico registrado exitosamente.';
        header("Location: ../admin/academico.php");
        exit;

    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            $_SESSION['error'] = 'El Año Académico ingresado ya existe.';
        } else {
            $_SESSION['error'] = 'Error al guardar el Año Académico: ' . $e->getMessage();
        }
        header("Location: ../admin/registrar_academico.php");
        exit;
    }

} else {
    $_SESSION['error'] = 'Acceso no permitido.';
    header("Location: ../admin/registrar_academico.php");
    exit;
}
