<?php
session_start();
require_once '../config/conexion.php'; // Asegúrate de que aquí se crea la conexión $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y obtener datos del formulario
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $idioma = isset($_POST['idioma']) ? intval($_POST['idioma']) : 0;
    $meses_duracion = isset($_POST['meses_duracion']) ? intval($_POST['meses_duracion']) : 0;

    if ($id > 0 && $idioma > 0 && $meses_duracion > 0) {
        try {
            $sql = "UPDATE estudiantes 
                    SET idioma = :idioma, meses_duracion = :meses_duracion 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idioma', $idioma, PDO::PARAM_INT);
            $stmt->bindParam(':meses_duracion', $meses_duracion, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Idioma actualizado correctamente.";
            } else {
                $_SESSION['error'] = "Error al actualizar los datos.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Todos los campos son obligatorios y deben ser válidos.";
    }

    header('Location: ../estudiante/panel_estudiante.php'); // Cambia esta ruta por la adecuada
    exit;
} else {
    $_SESSION['error'] = "Acceso no autorizado.";
    header('Location: ../estudiante/index.php');
    exit;
}
