<?php
session_start();

// Mostrar errores en modo desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir la conexión PDO
require_once '../config/conexion.php';

// Función para generar el código de acceso
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
    $iniciales = substr($iniciales, 0, 4);

    $fecha = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
    $fecha_nac = $fecha ? $fecha->format('dmy') : '000000';
    $letra_random = chr(rand(65, 90)); // Letra aleatoria entre A y Z

    return "{$iniciales}-{$fecha_nac}-{$id}{$letra_random}";
}

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception('Acceso denegado.');
    }

    // Validaciones
    $campos = ['nombre_completo', 'fecha_nacimiento', 'anio_inicio_carrera', 'anio_fin_carrera', 'pais', 'ciudad', 'universidad'];
    foreach ($campos as $campo) {
        if (empty($_POST[$campo])) {
            throw new Exception('Todos los campos son obligatorios.');
        }
    }

    // Recoger datos
    $nombre_completo = trim($_POST['nombre_completo']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $fecha_inicio_carrera = $_POST['anio_inicio_carrera'];
    $fecha_fin_carrera = $_POST['anio_fin_carrera'];
    $pais_id = (int)$_POST['pais'];
    $ciudad_id = (int)$_POST['ciudad'];
    $universidad_id = (int)$_POST['universidad'];

    // Empezar la transacción
    $pdo->beginTransaction();

    // Insertar el estudiante
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

    // Verificar inserción
    if ($stmt->rowCount() === 0) {
        throw new Exception('No se pudo insertar el estudiante.');
    }

    $estudiante_id = $pdo->lastInsertId();

    // Crear el código de acceso
    $codigo_acceso = generarCodigoAcceso($nombre_completo, $fecha_nacimiento, $estudiante_id);

    // Actualizar con el código generado
    $updateSql = "UPDATE estudiantes SET codigo_acceso = :codigo_acceso WHERE id = :id";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([
        ':codigo_acceso' => $codigo_acceso,
        ':id' => $estudiante_id
    ]);

    $pdo->commit();

    // Todo OK
    $_SESSION['mensaje'] = "¡Estudiante registrado exitosamente!";
    $_SESSION['tipo_mensaje'] = "success";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['mensaje'] = "Error: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = "danger";
}

// Ir siempre a la misma página al final
header("Location: ../admin/estudiantes.php");
exit();
?>

