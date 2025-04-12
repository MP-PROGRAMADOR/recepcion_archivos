<?php
session_start();
include_once("../config/conexion.php");

// Carpeta base donde se guardan los archivos
$carpeta_base = 'upload';

// Función para mover un archivo subido
function moverArchivo($campo_nombre, $archivo, $directorio_base = 'upload')
{
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "Error al subir el archivo '$campo_nombre'.";
        return false;
    }

    $ext_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $ext_permitidas)) {
        $_SESSION['error'] = "Extensión no permitida en '$campo_nombre'. Solo se permiten: JPG, PNG, GIF, WEBP.";
        return false;
    }

    if ($archivo['size'] > 2 * 1024 * 1024) {
        $_SESSION['error'] = "El archivo '$campo_nombre' excede el límite de 2MB.";
        return false;
    }

    // Crear subcarpeta 'upload/configuracion' si no existe
    $carpeta_destino = $directorio_base . '/configuracion';
    if (!is_dir($carpeta_destino)) {
        if (!mkdir($carpeta_destino, 0777, true)) {
            $_SESSION['error'] = "No se pudo crear la carpeta de destino '$carpeta_destino'.";
            return false;
        }
    }

    // Sanitizar nombre de archivo
    $nombre_archivo = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($archivo['name']));
    $ruta_final = $carpeta_destino . '/' . $nombre_archivo;

    if (file_exists($ruta_final)) {
        unlink($ruta_final);
    }
    // Mover archivo
    if (move_uploaded_file($archivo['tmp_name'], $ruta_final)) {
        return $ruta_final; // Devolver la ruta completa
    } else {
        $_SESSION['error'] = "Error al mover el archivo '$campo_nombre'.";
        return false;
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_sitio = htmlspecialchars(trim($_POST['nombre_sitio']));
    $color_primario = htmlspecialchars(trim($_POST['color_primario']));
    $descripcion = htmlspecialchars(trim($_POST['descripcion']));

    if (empty($nombre_sitio) || empty($color_primario) || empty($descripcion)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header('Location: ../admin/configuracion.php');
        exit;
    }

    $query = $pdo->query("SELECT logo, img_estudiante, img_admin FROM configuracion WHERE id = 1");
    $config = $query->fetch(PDO::FETCH_ASSOC);

    $logo_ruta = (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK)
        ? moverArchivo('logo', $_FILES['logo'], $carpeta_base)
        : $config['logo'];

    $img_estudiante_ruta = (isset($_FILES['img_estudiante']) && $_FILES['img_estudiante']['error'] === UPLOAD_ERR_OK)
        ? moverArchivo('img_estudiante', $_FILES['img_estudiante'], $carpeta_base)
        : $config['img_estudiante'];

    $img_admin_ruta = (isset($_FILES['img_admin']) && $_FILES['img_admin']['error'] === UPLOAD_ERR_OK)
        ? moverArchivo('img_admin', $_FILES['img_admin'], $carpeta_base)
        : $config['img_admin'];

    if (!$logo_ruta || !$img_estudiante_ruta || !$img_admin_ruta) {
        header('Location: ../admin/configuracion.php');
        exit;
    }

    try {
        $sql = "UPDATE configuracion 
                SET nombre_sitio = :nombre_sitio, 
                    logo = :logo, 
                    color_primario = :color_primario, 
                    descripcion = :descripcion, 
                    img_estudiante = :img_estudiante, 
                    img_admin = :img_admin 
                WHERE id = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre_sitio' => $nombre_sitio,
            ':logo' => $logo_ruta,
            ':color_primario' => $color_primario,
            ':descripcion' => $descripcion,
            ':img_estudiante' => $img_estudiante_ruta,
            ':img_admin' => $img_admin_ruta,
        ]);

        $_SESSION['success'] = "Configuración actualizada correctamente.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error al actualizar: " . $e->getMessage();
    }

    header('Location: ../admin/configuracion.php');
    exit;
}
?>
