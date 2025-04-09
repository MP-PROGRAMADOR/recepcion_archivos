<?php
session_start();
if (!isset($_SESSION['estudiante'])) {
    header("Location: ../estudiante/index.php");
    exit();
}

include_once('../config/conexion.php');

function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Obtener el ID del estudiante desde la sesión
$estudiante_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_pasaporte = sanitize_input($_POST['numero_pasaporte']);
    $fecha_emision = $_POST['fecha_emision'];
    $fecha_expiracion = $_POST['fecha_expiracion'];

    $archivo = $_FILES['archivo'];
    $archivo_nombre = $archivo['name'];
    $archivo_tmp = $archivo['tmp_name'];
    $archivo_error = $archivo['error'];

    // Validar campos obligatorios
    if (empty($numero_pasaporte) || empty($fecha_emision) || empty($fecha_expiracion) || empty($archivo_nombre)) {
        echo "Todos los campos son obligatorios.";
        exit;
    }

    // Validar extensión del archivo (solo PDF)
    $archivo_extension = strtolower(pathinfo($archivo_nombre, PATHINFO_EXTENSION));
    if ($archivo_extension !== 'pdf') {
        echo "Solo se permiten archivos PDF.";
        exit;
    }

    // Comprobar si el estudiante ya ha subido un pasaporte
    $verifica = $pdo->prepare("SELECT COUNT(*) FROM pasaportes WHERE estudiante_id = :estudiante_id");
    $verifica->bindParam(':estudiante_id', $estudiante_id);
    $verifica->execute();
    $ya_tiene = $verifica->fetchColumn();

    if ($ya_tiene > 0) {
        echo "Ya has subido un pasaporte. Si deseas cambiarlo, utiliza la opción de actualizar.";
        exit;
    }

    // Crear carpeta si no existe
    if (!is_dir('pasaportes')) {
        mkdir('pasaportes', 0777, true);
    }

    // Guardar archivo
    $nombre_archivo_final = 'pasaporte_' . $estudiante_id . '_' . time() . '.pdf';
    $ruta_archivo = 'pasaportes/' . $nombre_archivo_final;

    if (!move_uploaded_file($archivo_tmp, $ruta_archivo)) {
        echo "Error al subir el archivo.";
        exit;
    }

    // Insertar en base de datos
    try {
        $stmt = $pdo->prepare("INSERT INTO pasaportes 
            (estudiante_id, numero_pasaporte, fecha_emision, fecha_expiracion, archivo_url) 
            VALUES 
            (:estudiante_id, :numero_pasaporte, :fecha_emision, :fecha_expiracion, :archivo_url)");
        
        $stmt->bindParam(':estudiante_id', $estudiante_id);
        $stmt->bindParam(':numero_pasaporte', $numero_pasaporte);
        $stmt->bindParam(':fecha_emision', $fecha_emision);
        $stmt->bindParam(':fecha_expiracion', $fecha_expiracion);
        $stmt->bindParam(':archivo_url', $ruta_archivo);
        $stmt->execute();

        header("Location: ../estudiante/panel_estudiante.php");
        exit;

    } catch (PDOException $e) {
        echo "Error al guardar en la base de datos: " . $e->getMessage();
    }
}
?>

