<?php
session_start();


require_once '../config/conexion.php';

// 🔒 Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
$navegador = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
$accion = "CREAR";
$modulo = "Estudiantes";
$registro_id = null;

// ✅ Generar código acceso
function generarCodigoAcceso($nombre_completo, $fecha_nacimiento, $id) {
    $nombre_completo = strtoupper(trim($nombre_completo));
    $palabras = preg_split('/\s+/', $nombre_completo);
    $iniciales = '';

    foreach ($palabras as $p) {
        if ($p) $iniciales .= substr($p, 0, 1);
    }

    $iniciales = substr($iniciales, 0, 4);
    $fecha = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
    $fecha_nac = $fecha ? $fecha->format('dmy') : '000000';
    $letra = chr(rand(65, 90));

    return "{$iniciales}-{$fecha_nac}-{$id}{$letra}";
}

// ✅ Registrar logs sin permitir usuario null
function registrar_log($pdo, $usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado) {
    if ($usuario_id) {
        $stmt = $pdo->prepare("INSERT INTO log_actividades
            (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado)
            VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado]);
    }
}

try {

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception('Acceso denegado.');
    }

    unset($_SESSION['exito'], $_SESSION['error']);

    $campos = ['nombre_completo','fecha_nacimiento','anio_inicio_carrera','anio_fin_carrera','pais','ciudad','universidad'];

    foreach ($campos as $campo) {
        if (empty($_POST[$campo])) {
            throw new Exception("El campo {$campo} es obligatorio.");
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
        throw new Exception("El año de inicio debe ser menor que el de finalización.");
    }

    // 📁 Subida archivo beca
    $ruta_beca = null;
    if (!empty($_FILES['beca_file']['name'])) {

        $ext = pathinfo($_FILES['beca_file']['name'], PATHINFO_EXTENSION);
        $nuevo = 'beca_' . time() . '_' . uniqid() . '.' . $ext;
        $dir = 'upload/becas/';

        if (!is_dir($dir)) mkdir($dir, 0777, true);

        if (!move_uploaded_file($_FILES['beca_file']['tmp_name'], $dir.$nuevo)) {
            throw new Exception('No se pudo guardar el archivo.');
        }

        $ruta_beca = $dir.$nuevo;
    }

    // 🔄 TRANSACCIÓN
    $pdo->beginTransaction();

    // Insertar estudiante
    $stmt = $pdo->prepare("INSERT INTO estudiantes
        (nombre_completo, fecha_nacimiento, anio_inicio_carrera, anio_fin_carrera, pais_id, ciudad_id, universidad_id, archivo_beca)
        VALUES (?,?,?,?,?,?,?,?)");

    $stmt->execute([
        $nombre_completo,
        $fecha_nacimiento,
        $anio_inicio,
        $anio_fin,
        $pais_id,
        $ciudad_id,
        $universidad_id,
        $ruta_beca
    ]);

    $estudiante_id = $pdo->lastInsertId();
    $registro_id = $estudiante_id;

    // Generar código acceso
    $codigo = generarCodigoAcceso($nombre_completo, $fecha_nacimiento, $estudiante_id);
    $pdo->prepare("UPDATE estudiantes SET codigo_acceso=? WHERE id=?")
        ->execute([$codigo, $estudiante_id]);

    registrar_log($pdo, $usuario_id, $accion, $modulo, $registro_id,
        "Estudiante creado ID $estudiante_id Código $codigo",
        $ip, $navegador, "EXITO");

    // -------------------- CUENTA BANCARIA OPCIONAL ----------------
    if (($_POST['tiene_cuenta'] ?? '') === 'si') {

        $tipo_cuenta = trim($_POST['tipo_cuenta'] ?? '');
        $banco = trim($_POST['banco'] ?? '');
        $numero = trim($_POST['numero_cuenta'] ?? '');
        $tarjeta = $_POST['tarjeta_visa'] ?? null;
        $fecha_tarjeta = ($tarjeta === 'si') ? ($_POST['fecha_caducidad_tarjeta'] ?? null) : null;

        if ($numero) {
            $stmt = $pdo->prepare("INSERT INTO cuentas_bancarias
                (estudiante_id, tipo_cuenta, banco, numero_cuenta, tarjeta_visa, fecha_caducidad_tarjeta)
                VALUES (?,?,?,?,?,?)");

            $stmt->execute([
                $estudiante_id,
                $tipo_cuenta,
                $banco,
                $numero,
                $tarjeta,
                $fecha_tarjeta
            ]);

            registrar_log($pdo, $usuario_id, "CREAR", "Cuentas Bancarias",
                $estudiante_id,
                "Cuenta bancaria creada para estudiante $estudiante_id",
                $ip, $navegador, "EXITO");
        }
    }

    $pdo->commit();

    $_SESSION['exito'] = "Estudiante registrado correctamente.";
    header("Location: ../admin/estudiantes.php");
    exit();

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    registrar_log($pdo, $usuario_id, $accion, $modulo, $registro_id,
        "Error al registrar estudiante: ".$e->getMessage(),
        $ip, $navegador, "ERROR");

    $_SESSION['error'] = $e->getMessage();
    header("Location: ../admin/registrar_estudiantes.php");
    exit();
}
?>