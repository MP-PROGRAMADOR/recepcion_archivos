<?php
ob_start();
session_start(); // Obligatorio al usar sesiones
require_once '../config/conexion.php';

$directorio = __DIR__ . '/upload/';
if (!is_dir($directorio)) {
    mkdir($directorio, 0777, true);
} else {
    chmod($directorio, 0777);
}

$errores = [];

$nombre_completo_original = trim($_POST['nombre_completo'] ?? '');
$fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
$pais_id = htmlspecialchars(trim($_POST['pais']));

// Normalizar nombre (para validación de duplicados)
$nombre_normalizado = preg_replace('/\s+/', ' ', strtolower(trim($nombre_completo_original)));

// Validaciones
if ($nombre_completo_original === '') {
    $errores[] = "El nombre completo es obligatorio.";
}

if ($fecha_nacimiento === '') {
    $errores[] = "La fecha de nacimiento es obligatoria.";
} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_nacimiento)) {
    $errores[] = "Formato de fecha no válido. Usa YYYY-MM-DD.";
}

if (!is_numeric($pais_id)) {
    $errores[] = "País no válido.";
}

// Validar duplicado de nombre normalizado (ignorar mayúsculas y espacios extra)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM estudiantes WHERE LOWER(TRIM(REPLACE(nombre_completo, '  ', ' '))) = ?");
$stmt->execute([$nombre_normalizado]);
$existe = $stmt->fetchColumn();

if ($existe > 0) {
    $errores[] = "Ya existe un estudiante con ese nombre (verifica mayúsculas o espacios).";
}

// Validación de imagen
$foto_subida = false;
$nombre_temporal = null;
$extension = null;
$ruta_foto = null;

if (!empty($_FILES['foto']['name'])) {
    $foto = $_FILES['foto'];
    $permitidos = ['image/jpeg', 'image/png', 'image/webp'];

    if (!in_array($foto['type'], $permitidos)) {
        $errores[] = "Solo se permiten imágenes JPG, PNG o WEBP.";
    }

    if ($foto['size'] > 2 * 1024 * 1024) {
        $errores[] = "La imagen no puede superar los 2MB.";
    }

    if (!is_uploaded_file($foto['tmp_name'])) {
        $errores[] = "Error al cargar la imagen.";
    }

    if (empty($errores)) {
        $nombre_temporal = $foto['tmp_name'];
        $extension = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $foto_subida = true;
    }
}

// Si hay errores, redirigir
if (!empty($errores)) {
    // Codificar errores como una sola cadena para pasarlos por GET
    $_SESSION['errores'] = $errores;
    header("Location: ../admin/registrar_estudiantes.php");
    exit;
}


try {
    // Insertar estudiante
    $stmt = $pdo->prepare("INSERT INTO estudiantes (nombre_completo, fecha_nacimiento, pais_id) VALUES (?, ?, ?)");
    $stmt->execute([$nombre_completo_original, $fecha_nacimiento, $pais_id]);
    $estudiante_id = $pdo->lastInsertId();

    // Código: INICIALES_NOMBRE-INICIALES_PAIS-AÑO-ID
    $iniciales_nombre = '';
    foreach (explode(' ', $nombre_completo_original) as $palabra) {
        $iniciales_nombre .= strtoupper(substr($palabra, 0, 1));
    }

    $stmtPais = $pdo->prepare("SELECT nombre FROM paises WHERE id = ?");
    $stmtPais->execute([$pais_id]);
    $pais_nombre = $stmtPais->fetchColumn();

    $iniciales_pais = '';
    foreach (explode(' ', $pais_nombre) as $palabra) {
        $iniciales_pais .= strtoupper(substr($palabra, 0, 1));
    }

    $anio = date('y');
    $codigo_acceso = "{$iniciales_nombre}-{$iniciales_pais}-{$anio}-{$estudiante_id}";

    // Subir imagen
    if ($foto_subida) {
        $nombre_archivo = 'estudiante_' . $estudiante_id . '.' . $extension;
        $ruta_guardada = 'upload/' . $nombre_archivo;
        $ruta_completa = $directorio . $nombre_archivo;

        if (move_uploaded_file($nombre_temporal, $ruta_completa)) {
            $ruta_foto = $ruta_guardada;
        }
    }

    $stmt = $pdo->prepare("UPDATE estudiantes SET codigo_acceso = ?, ruta_foto = ? WHERE id = ?");
    $stmt->execute([$codigo_acceso, $ruta_foto, $estudiante_id]);
    
    $_SESSION['exito'] = "El estudiante fue registrado correctamente con el código: $codigo_acceso";

    header("Location: ../admin/estudiantes.php");
    exit;
} catch (PDOException $e) {
    $errores = $e->getMessage();
    $_SESSION['errores'] = $errores;
    header("Location: ../admin/registrar_estudiantes.php");
    exit;
}
