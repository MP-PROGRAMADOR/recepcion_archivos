<?php
session_start();
include '../config/conexion.php'; // conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se han enviado los campos requeridos
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0 && isset($_POST['email']) && isset($_POST['telefono'])) {
        $id_estudiante = $_SESSION['id']; // El ID del estudiante (guardado en la sesión)
        
        $foto = $_FILES['foto'];
        $nombreOriginal = basename($foto['name']);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        // Validar el email
        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "El correo electrónico no es válido.";
            header('Location: ../estudiante/perfil.php');
            exit;
        }

        // Validar el teléfono (puedes ajustar la expresión regular según tu necesidad)
        $telefono = trim($_POST['telefono']);
        if (!preg_match("/^\+?[0-9\s\-]{7,15}$/", $telefono)) {
            $_SESSION['error'] = "El número de teléfono no es válido.";
            header('Location: ../estudiante/perfil.php');
            exit;
        }

        // Validar extensión de la imagen
        $extensionesPermitidas = ['jpg', 'jpeg', 'png'];
        if (!in_array($extension, $extensionesPermitidas)) {
            $_SESSION['error'] = "Formato no permitido. Solo JPG o PNG.";
            header('Location: ../estudiante/perfil.php');
            exit;
        }

        // Validar tamaño (2MB máximo)
        if ($foto['size'] > 2 * 1024 * 1024) {
            $_SESSION['error'] = "La imagen supera el tamaño máximo permitido de 2MB.";
            header('Location: ../estudiante/perfil.php');
            exit;
        }

        // Crear nombre único para la imagen
        $nombreNuevo = 'foto_perfil_' . $id_estudiante . '_' . time() . '.' . $extension;

        // Ruta de guardado (carpeta física)
        $rutaDestino = '../php/upload/perfil/' . $nombreNuevo;

        // Mover el archivo
        if (move_uploaded_file($foto['tmp_name'], $rutaDestino)) {
            // Actualizar la base de datos con el email, teléfono y foto de perfil
            $stmt = $pdo->prepare("UPDATE estudiantes SET foto_perfil = :foto_perfil, email = :email, telefono = :telefono WHERE id = :id");
            $stmt->bindParam(':foto_perfil', $nombreNuevo);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':id', $id_estudiante);
            $stmt->execute();

            $_SESSION['success'] = "Foto, email y teléfono actualizados correctamente.";
            header('Location: ../estudiante/panel_estudiante.php');
            exit;
        } else {
            $_SESSION['error'] = "Error al mover la imagen.";
            header('Location: ../estudiante/perfil.php');
            exit;
        }
    } else {
        $_SESSION['error'] = "No se seleccionó ninguna imagen, o el email y teléfono son obligatorios.";
        header('Location: ../estudiante/perfil.php');
        exit;
    }
}
?>
