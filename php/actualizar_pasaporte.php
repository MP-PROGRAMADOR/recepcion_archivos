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
    // Recoger los datos del formulario
    $numero_pasaporte = sanitize_input($_POST['numero_pasaporte']);
    $fecha_emision = $_POST['fecha_emision'];
    $fecha_expiracion = $_POST['fecha_expiracion'];

    // Validar y procesar el archivo
    $archivo = $_FILES['archivo'];
    $archivo_nombre = $archivo['name'];
    $archivo_tmp = $archivo['tmp_name'];
    $archivo_error = $archivo['error'];

    // Inicializar errores
    $errores = [];
    $hay_dato_para_actualizar = false;

    // Validaciones de los campos: solo si no están vacíos
    if (!empty($numero_pasaporte)) {
        $hay_dato_para_actualizar = true;
        if (empty($numero_pasaporte)) {
            $errores[] = "El número de pasaporte es obligatorio.";
        }
    }

    if (!empty($fecha_emision)) {
        $hay_dato_para_actualizar = true;
        if (empty($fecha_emision)) {
            $errores[] = "La fecha de emisión es obligatoria.";
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_emision)) {
            $errores[] = "El formato de la fecha de emisión no es válido. Usa YYYY-MM-DD.";
        }
    }

    if (!empty($fecha_expiracion)) {
        $hay_dato_para_actualizar = true;
        if (empty($fecha_expiracion)) {
            $errores[] = "La fecha de expiración es obligatoria.";
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_expiracion)) {
            $errores[] = "El formato de la fecha de expiración no es válido. Usa YYYY-MM-DD.";
        }
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

    // Validar las fechas del pasaporte
    $validacion_fechas = validarFechasPasaporte($fecha_emision, $fecha_expiracion);
    if (!$validacion_fechas) {
        $errores[] = $validacion_fechas;
    }

    // Validar extensión del archivo (solo PDF)
    if (!empty($archivo_nombre)) {
        $archivo_extension = strtolower(pathinfo($archivo_nombre, PATHINFO_EXTENSION));
        if ($archivo_extension !== 'pdf') {
            $errores[] = "Solo se permiten archivos PDF.";
        }
    }

    // Si no hay campos para actualizar, mostrar mensaje de error
    if (!$hay_dato_para_actualizar) {
        $errores[] = "Debes ingresar al menos un campo para actualizar.";
    }

    // Si existen errores, redirigir con los errores en sesión
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        header("Location: ../estudiante/panel_estudiante.php");
        exit;
    }

    // Validar si el estudiante ya tiene un pasaporte en la base de datos
    $verifica = $pdo->prepare("SELECT * FROM pasaportes WHERE estudiante_id = :estudiante_id");
    $verifica->bindParam(':estudiante_id', $estudiante_id);
    $verifica->execute();
    $pasaporte = $verifica->fetch();

    // Crear carpetas si no existen
    $directorio_upload = 'upload';
    $directorio_pasaportes = $directorio_upload . '/pasaportes';
    if (!is_dir($directorio_upload)) {
        mkdir($directorio_upload, 0777, true);
    }

    if (!is_dir($directorio_pasaportes)) {
        mkdir($directorio_pasaportes, 0777, true);
    }

    // Si se ha subido un archivo, procesarlo
    if (!empty($archivo_nombre)) {
        $nombre_archivo_final = 'pasaporte_' . $estudiante_id . '_' . time() . '.pdf';
        $ruta_archivo = $directorio_pasaportes . '/' . $nombre_archivo_final;
        if (!move_uploaded_file($archivo_tmp, $ruta_archivo)) {
            $errores[] = "Error al subir el archivo.";
        }
    } else {
        $nombre_archivo_final = $pasaporte['archivo_url']; // Mantener el archivo existente si no se sube uno nuevo
    }

    // Si no hay errores, actualizar los datos en la base de datos
    if (empty($errores)) {
        try {
            // Preparar la actualización de los datos
            $update_query = "UPDATE pasaportes SET ";
            $update_params = [];
            $update_conditions = [];

            // Actualizar solo los campos no vacíos
            if (!empty($numero_pasaporte)) {
                $update_conditions[] = "numero_pasaporte = :numero_pasaporte";
                $update_params[':numero_pasaporte'] = $numero_pasaporte;
            }

            if (!empty($fecha_emision)) {
                $update_conditions[] = "fecha_emision = :fecha_emision";
                $update_params[':fecha_emision'] = $fecha_emision;
            }

            if (!empty($fecha_expiracion)) {
                $update_conditions[] = "fecha_expiracion = :fecha_expiracion";
                $update_params[':fecha_expiracion'] = $fecha_expiracion;
            }

            // Solo actualizar el archivo si se subió uno nuevo
            if (!empty($archivo_nombre)) {
                $update_conditions[] = "archivo_url = :archivo_url";
                $update_params[':archivo_url'] = $nombre_archivo_final;
            }

            // Si hay algo que actualizar
            if (!empty($update_conditions)) {
                $update_query .= implode(', ', $update_conditions);
                $update_query .= " WHERE estudiante_id = :estudiante_id";
                $update_params[':estudiante_id'] = $estudiante_id;

                $stmt = $pdo->prepare($update_query);
                $stmt->execute($update_params);
            }

            header("Location: ../estudiante/panel_estudiante.php");
            exit;

        } catch (PDOException $e) {
            echo "Error al actualizar en la base de datos: " . $e->getMessage();
        }
    }
}
?>
