<?php
session_start();

// Mostrar errores en modo desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir la conexión PDO
require_once '../config/conexion.php';

// Datos para log
$usuario_id = $_SESSION['usuario_id'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
$navegador = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
$accion = "CREAR";
$modulo = "Estudiantes";
$registro_id = null;
$resultado = "EXITO";
$descripcion = "";

function generarCodigoAcceso($nombre_completo, $fecha_nacimiento, $id) {
    $nombre_completo = strtoupper(trim($nombre_completo));
    $palabras = preg_split('/\s+/', $nombre_completo);
    $iniciales = '';
    foreach ($palabras as $palabra) {
        if (!empty($palabra)) $iniciales .= substr($palabra, 0, 1);
    }
    $iniciales = substr($iniciales, 0, 4);
    $fecha = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
    $fecha_nac = $fecha ? $fecha->format('dmy') : '000000';
    $letra_random = chr(rand(65, 90));
    return "{$iniciales}-{$fecha_nac}-{$id}{$letra_random}";
}

// Función para registrar logs
function registrar_log($pdo, $usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado) {
    $stmt_log = $pdo->prepare("INSERT INTO log_actividades 
        (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_log->execute([$usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado]);
}

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception('Acceso denegado. Método incorrecto.');
    }

    $campos = ['nombre_completo','fecha_nacimiento','anio_inicio_carrera','anio_fin_carrera','pais','ciudad','universidad'];
    foreach ($campos as $campo) {
        if (empty($_POST[$campo])) {
            throw new Exception("El campo '{$campo}' es obligatorio.");
        }
    }

    $nombre_completo = trim($_POST['nombre_completo']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $anio_inicio = (int)$_POST['anio_inicio_carrera'];
    $anio_fin = (int)$_POST['anio_fin_carrera'];
    $pais_id = (int)$_POST['pais'];
    $ciudad_id = (int)$_POST['ciudad'];
    $universidad_id = (int)$_POST['universidad'];

    if ($anio_inicio >= $anio_fin) {
        throw new Exception("El año de inicio de carrera debe ser menor que el año de finalización.");
    }

    // Procesar archivo de beca
    $ruta_beca = null;
    if (isset($_FILES['beca_file']) && $_FILES['beca_file']['error'] === UPLOAD_ERR_OK) {
        $nombre_original = $_FILES['beca_file']['name'];
        $tmp_path = $_FILES['beca_file']['tmp_name'];
        $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
        $nombre_nuevo = 'beca_' . time() . '_' . uniqid() . '.' . $extension;
        $ruta_destino = 'upload/becas/' . $nombre_nuevo;
        if (!file_exists('upload/becas/')) mkdir('upload/becas/', 0777, true);
        if (!move_uploaded_file($tmp_path, $ruta_destino)) {
            throw new Exception('No se pudo guardar el archivo de beca.');
        }
        $ruta_beca = $ruta_destino;
    }

    // Iniciar transacción
    $pdo->beginTransaction();

    // Insertar estudiante
    $sql = "INSERT INTO estudiantes 
        (nombre_completo, fecha_nacimiento, anio_inicio_carrera, anio_fin_carrera, pais_id, ciudad_id, universidad_id, archivo_beca) 
        VALUES (:nombre_completo, :fecha_nacimiento, :anio_inicio_carrera, :anio_fin_carrera, :pais_id, :ciudad_id, :universidad_id, :archivo_beca)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre_completo' => $nombre_completo,
        ':fecha_nacimiento' => $fecha_nacimiento,
        ':anio_inicio_carrera' => $anio_inicio,
        ':anio_fin_carrera' => $anio_fin,
        ':pais_id' => $pais_id,
        ':ciudad_id' => $ciudad_id,
        ':universidad_id' => $universidad_id,
        ':archivo_beca' => $ruta_beca
    ]);

    $estudiante_id = $pdo->lastInsertId();
    $registro_id = $estudiante_id;

    // Generar código de acceso
    $codigo_acceso = generarCodigoAcceso($nombre_completo, $fecha_nacimiento, $estudiante_id);
    $updateStmt = $pdo->prepare("UPDATE estudiantes SET codigo_acceso = :codigo_acceso WHERE id = :id");
    $updateStmt->execute([':codigo_acceso' => $codigo_acceso, ':id' => $estudiante_id]);

    // Registrar log de creación de estudiante
    $descripcion = "Estudiante registrado. ID: $estudiante_id, Código: $codigo_acceso";
    registrar_log($pdo, $usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado);

    // -------------------- Guardar datos bancarios ------------------
    function limpiar($dato) { return htmlspecialchars(trim($dato)); }
    $tiene_cuenta = $_POST['tiene_cuenta'] ?? null;

    if ($tiene_cuenta === 'si') {
        $tipo_cuenta = limpiar($_POST['tipo_cuenta'] ?? '');
        $banco = limpiar($_POST['banco'] ?? '');
        $tiene_cuenta_numero = $_POST['tiene_cuenta_numero'] ?? '';

        if ($tiene_cuenta_numero === 'si') {
            $numero_cuenta = limpiar($_POST['numero_cuenta'] ?? '');
            $tarjeta_visa = $_POST['tarjeta_visa'] ?? null;
            $fecha_caducidad_tarjeta = null;
            if ($tarjeta_visa === 'si') {
                $fecha_caducidad_tarjeta = $_POST['fecha_caducidad_tarjeta'] ?? null;
            } else {
                $tarjeta_visa = null;
            }

            $sql_cuenta = "INSERT INTO cuentas_bancarias 
                (estudiante_id, tipo_cuenta, banco, numero_cuenta, tarjeta_visa, fecha_caducidad_tarjeta)
                VALUES (:estudiante_id, :tipo_cuenta, :banco, :numero_cuenta, :tarjeta_visa, :fecha_caducidad_tarjeta)";
            $stmt_cuenta = $pdo->prepare($sql_cuenta);
            $stmt_cuenta->execute([
                ':estudiante_id' => $estudiante_id,
                ':tipo_cuenta' => $tipo_cuenta,
                ':banco' => $banco,
                ':numero_cuenta' => $numero_cuenta,
                ':tarjeta_visa' => $tarjeta_visa,
                ':fecha_caducidad_tarjeta' => $fecha_caducidad_tarjeta
            ]);

            // Registrar log de cuenta bancaria
            $descripcion = "Cuenta bancaria registrada para Estudiante ID $estudiante_id, Número de cuenta: $numero_cuenta";
            registrar_log($pdo, $usuario_id, "CREAR", "Cuentas Bancarias", $estudiante_id, $descripcion, $ip, $navegador, $resultado);
        }
    }

    $pdo->commit();

    $_SESSION['mensaje'] = "¡Estudiante registrado exitosamente!";
    $_SESSION['tipo_mensaje'] = "success";
    header("Location: ../admin/estudiantes.php");
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();

    $resultado = "ERROR";
    $descripcion = "Error al registrar estudiante: " . $e->getMessage();
    registrar_log($pdo, $usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado);

    $_SESSION['mensaje'] = "Error: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = "danger";
    header("Location: ../admin/registrar_estudiantes.php");
    exit();
}
?>