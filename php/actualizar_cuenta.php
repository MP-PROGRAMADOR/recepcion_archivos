<?php
session_start();
require_once '../config/conexion.php';

/* =========================================
   FUNCIÓN GLOBAL DE LOG
========================================= */
function registrarLog($pdo, $usuario_id, $accion, $modulo, $descripcion, $resultado = 'EXITO', $registro_id = null) {

    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $navegador = $_SERVER['HTTP_USER_AGENT'] ?? null;

    $stmt = $pdo->prepare("
        INSERT INTO log_actividades
        (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $usuario_id,
        $accion,
        $modulo,
        $registro_id,
        $descripcion,
        $ip,
        $navegador,
        $resultado
    ]);
}

/* =========================================
   VALIDAR SESIÓN
========================================= */
if (!isset($_SESSION['usuario_id'])) {
    registrarLog($pdo, 0, 'ACCESO_DENEGADO', 'Cuentas Bancarias', 'Intento de acceso sin sesión', 'ERROR');
    header('Location: ../index.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

/* =========================================
   VALIDAR MÉTODO
========================================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    registrarLog($pdo, $usuario_id, 'ACCESO_INVALIDO', 'Cuentas Bancarias', 'Acceso sin método POST', 'ERROR');
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: ../admin/cuentas_bancarias.php");
    exit;
}

try {

    /* =========================================
       OBTENER DATOS
    ========================================= */
    $tipo_cuenta = $_POST['tipo_cuenta'] ?? null;
    $banco = $_POST['banco'] ?? null;
    $numero_cuenta = $_POST['numero_cuenta'] ?? null;
    $tarjeta_visa = $_POST['tarjeta_visa'] ?? null;
    $fecha_caducidad_tarjeta = $_POST['fecha_caducidad_tarjeta'] ?? null;
    $estudiante_id = $_POST['estudiante_id'] ?? null;

    /* =========================================
       VALIDACIÓN
    ========================================= */
    if (!$tipo_cuenta || !$banco || !$numero_cuenta || !$tarjeta_visa || !$estudiante_id) {

        registrarLog(
            $pdo,
            $usuario_id,
            'ERROR_VALIDACION',
            'Cuentas Bancarias',
            'Campos obligatorios incompletos al actualizar cuenta bancaria',
            'ERROR',
            $estudiante_id
        );

        $_SESSION['error'] = "Todos los campos obligatorios deben estar completos.";
        header("Location: ../admin/editar_cuenta.php");
        exit;
    }

    // Si no tiene VISA, la fecha queda NULL
    if ($tarjeta_visa === 'no') {
        $fecha_caducidad_tarjeta = null;
    }

    /* =========================================
       ACTUALIZACIÓN
    ========================================= */
    $sql = "UPDATE cuentas_bancarias 
            SET tipo_cuenta = :tipo_cuenta, 
                banco = :banco, 
                numero_cuenta = :numero_cuenta, 
                tarjeta_visa = :tarjeta_visa, 
                fecha_caducidad_tarjeta = :fecha_caducidad_tarjeta 
            WHERE estudiante_id = :estudiante_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':tipo_cuenta' => $tipo_cuenta,
        ':banco' => $banco,
        ':numero_cuenta' => $numero_cuenta,
        ':tarjeta_visa' => $tarjeta_visa,
        ':fecha_caducidad_tarjeta' => $fecha_caducidad_tarjeta,
        ':estudiante_id' => $estudiante_id
    ]);

    registrarLog(
        $pdo,
        $usuario_id,
        'ACTUALIZAR',
        'Cuentas Bancarias',
        'Cuenta bancaria actualizada para estudiante ID: ' . $estudiante_id,
        'EXITO',
        $estudiante_id
    );

    $_SESSION['exito'] = "La cuenta bancaria ha sido actualizada correctamente.";
    header("Location: ../admin/cuentas_bancarias.php");
    exit;

} catch (PDOException $e) {

    registrarLog(
        $pdo,
        $usuario_id,
        'ERROR_SISTEMA',
        'Cuentas Bancarias',
        $e->getMessage(),
        'ERROR',
        $estudiante_id ?? null
    );

    $_SESSION['error'] = "Error al actualizar la cuenta.";
    header("Location: ../admin/editar_cuenta.php");
    exit;
}