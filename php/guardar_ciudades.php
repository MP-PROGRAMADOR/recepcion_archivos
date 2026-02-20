<?php
session_start();
include_once('../config/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errores = [];

    // Validar nombre
    if (empty($_POST['nombre'])) {
        $errores[] = 'El nombre de la ciudad es obligatorio.';
    } else {
        $nombre_ciudad = trim($_POST['nombre']);
        if (strlen($nombre_ciudad) < 3) {
            $errores[] = 'El nombre de la ciudad debe tener al menos 3 caracteres.';
        }
    }

    // Validar país
    if (empty($_POST['pais_id'])) {
        $errores[] = 'El país es obligatorio.';
    } else {
        $pais_id = (int) $_POST['pais_id'];

        $query = "SELECT id FROM paises WHERE id = :pais_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':pais_id', $pais_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $errores[] = 'El país seleccionado no es válido.';
        }
    }

    if (count($errores) > 0) {
        $_SESSION['errores'] = $errores;
        header('Location: ../admin/registrar_ciudades.php');
        exit;
    }

    // 🔍 Verificar duplicados (misma ciudad + mismo país)
    $query = "SELECT id FROM ciudades 
              WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(:nombre)) 
              AND pais_id = :pais_id
              LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre_ciudad, PDO::PARAM_STR);
    $stmt->bindParam(':pais_id', $pais_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['errores'] = ['Esta ciudad ya existe para el país seleccionado.'];
        header('Location: ../admin/registrar_ciudades.php');
        exit;
    }

    // Guardar ciudad
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
        header('Location: ../admin/registrar_ciudades.php');
        exit;
    }
}
?>
