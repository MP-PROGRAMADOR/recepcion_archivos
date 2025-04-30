<?php
session_start();
require_once '../config/conexion.php'; // Asegúrate de que este archivo devuelva una conexión $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y limpiar datos del formulario
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $idioma_id = isset($_POST['idioma_id']) ? intval($_POST['idioma_id']) : 0;
    $meses_duracion = isset($_POST['meses_duracion']) ? intval($_POST['meses_duracion']) : 0;

    if ($id > 0 && $idioma_id > 0 && $meses_duracion > 0) {
        try {
            $sql = "UPDATE idiomas_estudiante 
                    SET idioma_id = :idioma_id, meses_duracion = :meses_duracion 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idioma_id', $idioma_id, PDO::PARAM_INT);
            $stmt->bindParam(':meses_duracion', $meses_duracion, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Idioma actualizado correctamente.";
            } else {
                $_SESSION['error'] = "Error al actualizar el idioma.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
    }

    header('Location: ../estudiante/panel_estudiante.php'); // Redirige según tu sistema
    exit;
} else {
    $_SESSION['error'] = "Acceso no autorizado.";
    header('Location: ../estudiante/index.php');
    exit;
}