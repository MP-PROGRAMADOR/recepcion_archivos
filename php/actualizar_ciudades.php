<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ID de la ciudad a actualizar
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $pais_id = isset($_POST['pais_id']) ? (int)$_POST['pais_id'] : 0;

    // Validación
    $errores = [];

    if ($id <= 0) {
        $errores[] = 'ID de ciudad inválido.';
    }

    if (empty($nombre)) {
        $errores[] = 'El nombre de la ciudad es obligatorio.';
    } elseif (strlen($nombre) < 3) {
        $errores[] = 'El nombre de la ciudad debe tener al menos 3 caracteres.';
    }

    if ($pais_id <= 0) {
        $errores[] = 'Debe seleccionar un país válido.';
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        header('Location: ../admin/editar_ciudad.php?id=' . $id);
        exit;
    }

    try {
        // Verificar duplicados (nombre + país) excluyendo el registro actual
        $stmtDup = $pdo->prepare("
            SELECT id 
            FROM ciudades 
            WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(:nombre)) 
              AND pais_id = :pais_id 
              AND id != :id
        ");
        $stmtDup->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmtDup->bindParam(':pais_id', $pais_id, PDO::PARAM_INT);
        $stmtDup->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtDup->execute();

        if ($stmtDup->rowCount() > 0) {
            $_SESSION['errores'] = ['Ya existe una ciudad con ese nombre en el país seleccionado.'];
            header('Location: ../admin/editar_ciudad.php?id=' . $id);
            exit;
        }

        // Actualizar la ciudad
        $stmt = $pdo->prepare("UPDATE ciudades SET nombre = :nombre, pais_id = :pais_id WHERE id = :id");
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':pais_id', $pais_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['exito'] = 'Ciudad actualizada exitosamente.';
        header('Location: ../admin/ciudades.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['errores'] = ["Error al actualizar la ciudad: " . $e->getMessage()];
        header('Location: ../admin/editar_ciudad.php?id=' . $id);
        exit;
    }

} else {
    $_SESSION['errores'] = ['Acceso no permitido.'];
    header('Location: ../admin/ciudades.php');
    exit;
}
