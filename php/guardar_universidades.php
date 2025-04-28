<?php
session_start();
include_once("../config/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que los campos no estén vacíos
    if (empty($_POST['nombre']) || empty($_POST['ciudad_id'])) {
        $_SESSION['errores'][] = "Todos los campos son obligatorios.";
        header("Location: ../admin/registrar_universidad.php");
        exit;
    }

    $nombre = trim($_POST['nombre']);
    $ciudad_id = (int) $_POST['ciudad_id']; // Convertir a entero para evitar inyecciones

    // Validar el nombre de la universidad (max 100 caracteres)
    if (strlen($nombre) > 100) {
        $_SESSION['errores'][] = "El nombre de la universidad no puede exceder los 100 caracteres.";
        header("Location: ../admin/registrar_universidad.php");
        exit;
    }

    try {
        // Sentencia preparada para insertar la universidad
        $query = "INSERT INTO universidades (nombre, ciudad_id) VALUES (:nombre, :ciudad_id)";
        $stmt = $pdo->prepare($query);

        // Bind de parámetros para evitar inyecciones SQL
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':ciudad_id', $ciudad_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Universidad registrada con éxito.";
            header("Location: ../admin/universidades.php");
        } else {
            $_SESSION['errores'][] = "Hubo un problema al registrar la universidad.";
            header("Location: ../admin/registrar_universidad.php");
        }
    } catch (PDOException $e) {
        $_SESSION['errores'][] = "Error en la base de datos: " . $e->getMessage();
        header("Location: ../admin/registrar_universidad.php");
    }
}
?>
