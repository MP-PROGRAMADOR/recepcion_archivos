<?php
session_start();
include_once('../php/conexion.php');

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errores = [];

    // Validación del nombre de la ciudad
    if (empty($_POST['nombre'])) {
        $errores[] = 'El nombre de la ciudad es obligatorio.';
    } else {
        $nombre_ciudad = trim($_POST['nombre']);
        if (strlen($nombre_ciudad) < 3) {
            $errores[] = 'El nombre de la ciudad debe tener al menos 3 caracteres.';
        }
    }

    // Validación del país
    if (empty($_POST['pais_id'])) {
        $errores[] = 'El país es obligatorio.';
    } else {
        $pais_id = (int) $_POST['pais_id'];
        // Verificar si el país existe en la base de datos
        $query = "SELECT id FROM paises WHERE id = :pais_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':pais_id', $pais_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $errores[] = 'El país seleccionado no es válido.';
        }
    }

    // Si hay errores, redirigir y mostrar los mensajes
    if (count($errores) > 0) {
        $_SESSION['errores'] = $errores;
        header('Location: ../admin/registrar_ciudad.php'); // Redirigir al formulario
        exit;
    }

    // Si todo es válido, guardar la ciudad
    $query = "INSERT INTO ciudades (nombre, pais_id) VALUES (:nombre, :pais_id)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre_ciudad, PDO::PARAM_STR);
    $stmt->bindParam(':pais_id', $pais_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['exito'] = 'Ciudad registrada con éxito.';
        header('Location: ../admin/ciudades.php');
        exit;
    } else {
        $_SESSION['errores'] = ['Hubo un error al registrar la ciudad.'];
        header('Location: ../admin/registrar_ciudad.php');
        exit;
    }
}
?>
