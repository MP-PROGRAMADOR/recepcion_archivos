<?php
session_start();
if (!isset($_SESSION['estudiante'])) {
    header("Location: ../estudiante/index.php");
    exit();
}

include_once('../config/conexion.php');

function sanitize_input($data)
{
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


    // Función para validar las fechas del pasaporte
    function validarFechasPasaporte($fecha_emision, $fecha_expiracion)
    {
        $fecha_emision_obj = DateTime::createFromFormat('Y-m-d', $fecha_emision);
        $fecha_expiracion_obj = DateTime::createFromFormat('Y-m-d', $fecha_expiracion);
        $fecha_actual = new DateTime();

        // Validar que las fechas sean válidas
        if (!$fecha_emision_obj || !$fecha_expiracion_obj) {
            return "Las fechas proporcionadas no son válidas.";
        }

        // La fecha de emisión no puede ser posterior a la fecha de expiración
        if ($fecha_emision_obj > $fecha_expiracion_obj) {
            return "La fecha de emisión no puede ser posterior a la fecha de expiración.";
        }

        // La fecha de expiración no puede ser más de 10 años después de la fecha de emisión
        $fecha_maxima_expiracion = clone $fecha_emision_obj;
        $fecha_maxima_expiracion->modify('+10 years');
        if ($fecha_expiracion_obj > $fecha_maxima_expiracion) {
            return "La fecha de expiración no puede ser más de 10 años después de la fecha de emisión.";
        }

        // La fecha de expiración debe ser al menos 6 meses posterior a la fecha actual
        $fecha_minima_expiracion = clone $fecha_actual;
        $fecha_minima_expiracion->modify('+6 months');
        if ($fecha_expiracion_obj < $fecha_minima_expiracion) {
            return "La fecha de expiración debe ser al menos 6 meses después de la fecha actual.";
        }

        // Si todas las validaciones son correctas
        return null; // Sin error
    }

    // Inicializar errores
    $errores = [];


    // Validaciones
    if (empty($numero_pasaporte)) {
        $errores[] = "El número de pasaporte es obligatorio.";
    }

    if (empty($fecha_emision)) {
        $errores[] = "La fecha de emisión es obligatoria.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_emision)) {
        $errores[] = "El formato de la fecha de emisión no es válido. Usa YYYY-MM-DD.";
    }

    if (empty($fecha_expiracion)) {
        $errores[] = "La fecha de expiración es obligatoria.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_expiracion)) {
        $errores[] = "El formato de la fecha de expiración no es válido. Usa YYYY-MM-DD.";
    }

    // Validar las fechas del pasaporte
    $validacion_fechas = validarFechasPasaporte($fecha_emision, $fecha_expiracion);
    if ($validacion_fechas) {
        $errores[] = $validacion_fechas;
    }





    // Validar extensión del archivo (solo PDF)
    $archivo_extension = strtolower(pathinfo($archivo_nombre, PATHINFO_EXTENSION));
    if ($archivo_extension !== 'pdf') {
        $errores[] = "Solo se permiten archivos PDF.";

    }

    // Comprobar si el estudiante ya ha subido un pasaporte
    $verifica = $pdo->prepare("SELECT COUNT(*) FROM pasaportes WHERE estudiante_id = :estudiante_id");
    $verifica->bindParam(':estudiante_id', $estudiante_id);
    $verifica->execute();
    $ya_tiene = $verifica->fetchColumn();

    if ($ya_tiene > 0) {
        $errores[] = "Ya has subido un pasaporte. Si deseas cambiarlo, utiliza la opción de actualizar.";
         
    }

    // Crear carpetas si no existen
    $directorio_upload = 'upload';
    $directorio_pasaportes = $directorio_upload . '/pasaportes';

    if (!is_dir($directorio_upload)) {
        mkdir($directorio_upload, 0777, true);
    }

    if (!is_dir($directorio_pasaportes)) {
        mkdir($directorio_pasaportes, 0777, true);
    }


    // Guardar archivo
    $nombre_archivo_final = 'pasaporte_' . $estudiante_id . '_' . time() . '.pdf';
    $ruta_archivo = $directorio_pasaportes . '/' . $nombre_archivo_final;

    if (!move_uploaded_file($archivo_tmp, $ruta_archivo)) {
        $errores[] = "Error al subir el archivo.";

    }
    
    // Si hay errores, redirigir con los errores en sesión
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        header("Location: ../estudiante/panel_estudiante.php");
        exit;
    }

    // Insertar en base de datos (almacenar solo el nombre del archivo)
    try {
        $stmt = $pdo->prepare("INSERT INTO pasaportes 
            (estudiante_id, numero_pasaporte, fecha_emision, fecha_expiracion, archivo_url) 
            VALUES 
            (:estudiante_id, :numero_pasaporte, :fecha_emision, :fecha_expiracion, :archivo_url)");

        $stmt->bindParam(':estudiante_id', $estudiante_id);
        $stmt->bindParam(':numero_pasaporte', $numero_pasaporte);
        $stmt->bindParam(':fecha_emision', $fecha_emision);
        $stmt->bindParam(':fecha_expiracion', $fecha_expiracion);

        // Guardamos solo el nombre del archivo
        $stmt->bindParam(':archivo_url', $nombre_archivo_final);
        $stmt->execute();

        header("Location: ../estudiante/panel_estudiante.php");
        exit;

    } catch (PDOException $e) {
        echo "Error al guardar en la base de datos: " . $e->getMessage();
    }
}
?>