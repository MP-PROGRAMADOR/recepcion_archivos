<?php
session_start();
require_once '../config/conexion.php'; // Asegúrate que la conexión PDO esté aquí

// Carpeta donde se guardarán las imágenes
$carpeta_destino = __DIR__ . "/upload/perfil/";
$web_ruta = "upload/perfil/";

// Verifica si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estudiante_id = isset($_POST['estudiante_id']) ? (int) $_POST['estudiante_id'] : 0;

    // Verifica si se subió un archivo
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['foto_perfil'];

        // Validar tamaño máximo (2MB)
        if ($archivo['size'] > 2 * 1024 * 1024) {
            $_SESSION['error'] = 'La imagen no debe superar los 2MB.';
            header("Location: .../estudiante/panel_estudiante.php");
            exit();
        }

        // Validar tipo de imagen
        $permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!in_array($archivo['type'], $permitidos)) {
            $_SESSION['error'] = 'Formato de imagen no permitido.';
            header("Location: ../estudiante/panel_estudiante.php");
            exit();
        }

        // Crear nombre único
        $ext = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombre_archivo = "foto_perfil_{$estudiante_id}_" . time() . "." . $ext;
        $ruta_completa = $carpeta_destino . $nombre_archivo;

        // Mover archivo
        if (!move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
            $_SESSION['error'] = 'Error al subir la imagen.';
            header("Location: .../estudiante/panel_estudiante.php");
            exit();
        }

        // Actualizar en la base de datos
        try {
            $stmt = $pdo->prepare("UPDATE estudiantes SET foto_perfil = :foto WHERE id = :id");
            $stmt->bindParam(':foto', $nombre_archivo, PDO::PARAM_STR);
            $stmt->bindParam(':id', $estudiante_id, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['success'] = 'Foto de perfil actualizada correctamente.';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Error al actualizar en la base de datos: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = 'No se seleccionó ninguna imagen válida.';
    }

    // Redirigir de vuelta al formulario (ajusta según tu ruta real)
    header("Location: ../estudiante/panel_estudiante.php");
    exit();
}
