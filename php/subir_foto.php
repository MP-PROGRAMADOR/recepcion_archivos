<?php
session_start();
include '../config/conexion.php'; // conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $id_estudiante = $_SESSION['id']; // El ID del estudiante (guardado en la sesión)
        
        $foto = $_FILES['foto'];
        $nombreOriginal = basename($foto['name']);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        // Validar extensión
        $extensionesPermitidas = ['jpg', 'jpeg', 'png'];
        if (!in_array($extension, $extensionesPermitidas)) {
            $_SESSION['error'] = "Formato no permitido. Solo JPG o PNG.";
            header('Location: perfil.php');
            exit;
        }

        // Validar tamaño (2MB máximo)
        if ($foto['size'] > 2 * 1024 * 1024) {
            $_SESSION['error'] = "La imagen supera el tamaño máximo permitido de 2MB.";
            header('Location: perfil.php');
            exit;
        }

        // Crear nombre único para la imagen
        $nombreNuevo = 'foto_perfil_' . $id_estudiante . '_' . time() . '.' . $extension;

        // Ruta de guardado (carpeta física)
        $rutaDestino = '../php/upload/perfil/' . $nombreNuevo;

        // Mover el archivo
        if (move_uploaded_file($foto['tmp_name'], $rutaDestino)) {
            // Guardar solo el NOMBRE del archivo en la base de datos
            $stmt = $pdo->prepare("UPDATE estudiantes SET foto_perfil = :foto_perfil WHERE id = :id");
            $stmt->bindParam(':foto_perfil', $nombreNuevo);
            $stmt->bindParam(':id', $id_estudiante);
            $stmt->execute();

            $_SESSION['success'] = "Foto subida correctamente.";
            header('Location: ../estudiante/panel_estudiante.php');
            exit;
        } else {
            $_SESSION['error'] = "Error al mover la imagen.";
            header('Location: perfil.php');
            exit;
        }
    } else {
        $_SESSION['error'] = "No se seleccionó ninguna imagen o hubo un error.";
        header('Location: perfil.php');
        exit;
    }
}
?>

