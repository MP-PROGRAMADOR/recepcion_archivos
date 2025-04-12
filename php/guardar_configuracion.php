<?php
// Iniciar sesión
session_start();
include_once("../config/conexion.php");

// Verificar si el usuario tiene permisos de administrador
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['error'] = "Acceso no autorizado. Inicia sesión como administrador.";
    header('Location: ../index.php');
    exit;
}

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

    // Crear subcarpeta con el nombre del campo
    $carpeta_destino = $directorio_base . '/configuracion';
    if (!is_dir($carpeta_destino)) {
        mkdir($carpeta_destino, 0777, true);
    }

    // Sanitizar nombre de archivo
    $nombre_archivo = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($archivo['name']));
    $ruta_final =  $nombre_archivo;

    // Evitar sobrescritura
   /*  $contador = 1;
    $nombre_base = pathinfo($nombre_archivo, PATHINFO_FILENAME);
    $ext = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
    while (file_exists($ruta_final)) {
        $ruta_final = $carpeta_destino . '/' . $nombre_base . "_$contador." . $ext;
        $contador++;
    } */

    // Mover archivo
    if (move_uploaded_file($archivo['tmp_name'], $ruta_final)) {
        return $ruta_final;
    } else {
        $_SESSION['error'] = "Error al mover el archivo '$campo_nombre'.";
        return false;
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar entradas
    $nombre_sitio = htmlspecialchars(trim($_POST['nombre_sitio']));
    $color_primario = htmlspecialchars(trim($_POST['color_primario']));
    $descripcion = htmlspecialchars(trim($_POST['descripcion']));

    if (empty($nombre_sitio) || empty($color_primario) || empty($descripcion)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header('Location: ../admin/configuracion.php');
        exit;
    }

    // Cargar valores actuales (por si no se actualizan imágenes)
    $query = $pdo->query("SELECT logo, img_estudiante, img_admin FROM configuracion WHERE id = 1");
    $config = $query->fetch(PDO::FETCH_ASSOC);

    // Procesar archivos
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
        // Si hay error en alguno, ya se guardó en $_SESSION['error']
        header('Location: ../admin/configuracion.php');
        exit;
    }

    // Actualizar configuración
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
