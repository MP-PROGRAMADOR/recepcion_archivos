<?php
session_start();

// Mostrar errores en modo desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir la conexión PDO
require_once '../config/conexion.php';

/**
 * Función para generar un código de acceso único basado en el nombre, fecha de nacimiento y el ID insertado.
 */
function generarCodigoAcceso($nombre_completo, $fecha_nacimiento, $id)
{
    $nombre_completo = strtoupper(trim($nombre_completo));
    $palabras = preg_split('/\s+/', $nombre_completo);
    $iniciales = '';

    foreach ($palabras as $palabra) {
        if (!empty($palabra)) {
            $iniciales .= substr($palabra, 0, 1);
        }
    }

    $iniciales = substr($iniciales, 0, 4); // Solo las primeras 4 iniciales

    $fecha = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
    $fecha_nac = $fecha ? $fecha->format('dmy') : '000000';

    $letra_random = chr(rand(65, 90)); // Letra aleatoria entre A y Z

    return "{$iniciales}-{$fecha_nac}-{$id}{$letra_random}";
}

try {
    // Verificar si la solicitud es POST
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception('Acceso denegado. Método incorrecto.');
    }

    // Validar que todos los campos requeridos estén presentes
    $campos = ['nombre_completo', 'fecha_nacimiento', 'anio_inicio_carrera', 'anio_fin_carrera', 'pais', 'ciudad', 'universidad'];
    foreach ($campos as $campo) {
        if (empty($_POST[$campo])) {
            throw new Exception("El campo '{$campo}' es obligatorio.");
        }
    }

    // Recoger y limpiar datos del formulario
    $nombre_completo = trim($_POST['nombre_completo']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $fecha_inicio_carrera = (int)$_POST['anio_inicio_carrera'];
    $fecha_fin_carrera = (int)$_POST['anio_fin_carrera'];
    $pais_id = (int)$_POST['pais'];
    $ciudad_id = (int)$_POST['ciudad'];
    $universidad_id = (int)$_POST['universidad'];

    // Validar que el año de inicio de carrera sea menor que el año de finalización
    if ($fecha_inicio_carrera >= $fecha_fin_carrera) {
        throw new Exception("El año de inicio de carrera debe ser menor que el año de finalización.");
    }

    // Iniciar la transacción
    $pdo->beginTransaction();

    // Preparar la consulta de inserción
    $sql = "INSERT INTO estudiantes 
            (nombre_completo, fecha_nacimiento, anio_inicio_carrera, anio_fin_carrera, pais_id, ciudad_id, universidad_id) 
            VALUES 
            (:nombre_completo, :fecha_nacimiento, :anio_inicio_carrera, :anio_fin_carrera, :pais_id, :ciudad_id, :universidad_id)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre_completo' => $nombre_completo,
        ':fecha_nacimiento' => $fecha_nacimiento,
        ':anio_inicio_carrera' => $fecha_inicio_carrera,
        ':anio_fin_carrera' => $fecha_fin_carrera,
        ':pais_id' => $pais_id,
        ':ciudad_id' => $ciudad_id,
        ':universidad_id' => $universidad_id
    ]);

    // Verificar si la inserción fue exitosa
    if ($stmt->rowCount() === 0) {
        throw new Exception('No se pudo registrar al estudiante. Intente nuevamente.');
    }

    // Obtener el ID del nuevo estudiante
    $estudiante_id = $pdo->lastInsertId();

    // Generar el código de acceso único
    $codigo_acceso = generarCodigoAcceso($nombre_completo, $fecha_nacimiento, $estudiante_id);

    // Actualizar el estudiante con su código de acceso
    $updateSql = "UPDATE estudiantes SET codigo_acceso = :codigo_acceso WHERE id = :id";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([
        ':codigo_acceso' => $codigo_acceso,
        ':id' => $estudiante_id
    ]);

    // Confirmar la transacción
    $pdo->commit();

    // Mensaje de éxito
    $_SESSION['mensaje'] = "¡Estudiante registrado exitosamente!";
    $_SESSION['tipo_mensaje'] = "success";

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Mensaje de error
    $_SESSION['mensaje'] = "Error: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = "danger";
}

// Redirigir siempre al listado de estudiantes
header("Location: ../estudiante/panel_estudiante.php");
exit();
?>
