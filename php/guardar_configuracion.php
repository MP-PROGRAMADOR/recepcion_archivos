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

 
// Definir la carpeta donde se guardarán las imágenes
$directorio_destino = __DIR__ . './upload/configuracion/';
if (!is_dir($directorio_destino)) {
    mkdir($directorio_destino, 0777, true);
} else {
    chmod($directorio_destino, 0777);
}

// Función para mover el archivo subido
function moverArchivo($archivo, $directorio_destino)
{
    // Verificar si el archivo fue subido sin errores
    if ($archivo['error'] == 0) {
        // Verificar la extensión del archivo
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        // Validar si la extensión es válida
        if (!in_array($extension, $extensiones_permitidas)) {
            $_SESSION['error'] = "Extensión de archivo no permitida. Solo se permiten archivos JPG, JPEG, PNG, GIF, WEBP.";
            return false;
        }

        // Verificar el tamaño del archivo (2MB máximo)
        $tamano_maximo = 2 * 1024 * 1024; // 2MB
        if ($archivo['size'] > $tamano_maximo) {
            $_SESSION['error'] = "El archivo excede el tamaño máximo permitido (2MB).";
            return false;
        }

        // Obtener el nombre del archivo y la ruta de destino
        $nombre_archivo = basename($archivo['name']);
        $ruta_archivo = $nombre_archivo;

        // Mover el archivo a la carpeta de destino
        if (move_uploaded_file($archivo['tmp_name'], $ruta_archivo)) {
            return $ruta_archivo;
        } else {
            $_SESSION['error'] = "Error al mover el archivo.";
            return false; // Error al mover el archivo
        }
    }
    $_SESSION['error'] = "Error al subir el archivo.";
    return false; // Error al subir el archivo
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitizar y validar los datos del formulario
    $nombre_sitio = htmlspecialchars(trim($_POST['nombre_sitio']));
    $color_primario = htmlspecialchars(trim($_POST['color_primario']));
    $descripcion = htmlspecialchars(trim($_POST['descripcion']));

    // Validar que los campos no estén vacíos
    if (empty($nombre_sitio) || empty($color_primario) || empty($descripcion)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header('Location: ../admin/configuracion.php');
        exit;
    }

    // Verificar si se subió una imagen de logo y moverla
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $logo_ruta = moverArchivo($_FILES['logo'], $directorio_destino);
        if (!$logo_ruta) {
            header('Location: ../admin/configuracion.php');
            exit;
        }
    } else {
        $logo_ruta = $config['logo']; // Usar el logo actual si no se sube uno nuevo
    }

    // Verificar si se subió una imagen de estudiante y moverla
    if (isset($_FILES['img_estudiante']) && $_FILES['img_estudiante']['error'] == 0) {
        $img_estudiante_ruta = moverArchivo($_FILES['img_estudiante'], $directorio_destino);
        if (!$img_estudiante_ruta) {
            header('Location: ../admin/configuracion.php');
            exit;
        }
    } else {
        $img_estudiante_ruta = $config['img_estudiante']; // Usar la imagen actual si no se sube una nueva
    }

    // Verificar si se subió una imagen de admin y moverla
    if (isset($_FILES['img_admin']) && $_FILES['img_admin']['error'] == 0) {
        $img_admin_ruta = moverArchivo($_FILES['img_admin'], $directorio_destino);
        if (!$img_admin_ruta) {
            header('Location: ../admin/configuracion.php');
            exit;
        }
    } else {
        $img_admin_ruta = $config['img_admin']; // Usar la imagen actual si no se sube una nueva
    }

    // Actualizar la configuración en la base de datos
    try {
        $sql = "UPDATE configuracion SET nombre_sitio = :nombre_sitio, logo = :logo, color_primario = :color_primario, descripcion = :descripcion, img_estudiante = :img_estudiante, img_admin = :img_admin WHERE id = 1";
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
        header('Location: ../admin/configuracion.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error al actualizar la configuración: " . $e->getMessage();
        header('Location: ../admin/configuracion.php');
        exit;
    }
}
?>
