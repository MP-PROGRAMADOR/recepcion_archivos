<?php
session_start();
require_once '../config/conexion.php';

$usuario_id = $_SESSION['usuario_id'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
$navegador = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
$accion = "CREAR";
$modulo = "Cuentas Bancarias";
$registro_id = null;
$resultado = "EXITO";
$descripcion = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 🔹 Recoger datos
    $estudiante_id = $_POST['estudiante_id'] ?? '';
    $tipo_cuenta   = trim($_POST['tipo_cuenta'] ?? '');
    $banco         = trim($_POST['banco'] ?? '');
    $numero_cuenta = trim($_POST['numero_cuenta'] ?? '');
    $tarjeta_visa  = trim($_POST['tarjeta_visa'] ?? '');
    $fecha_cad     = $_POST['fecha_caducidad_tarjeta'] ?? null;

    // 🔹 Validación básica
    $errores = [];
    if (empty($estudiante_id) || empty($tipo_cuenta) || empty($banco) || empty($numero_cuenta)) {
        $errores[] = 'Debe completar todos los campos obligatorios.';
    }

    if (!empty($errores)) {
        $resultado = "ERROR";
        $descripcion = implode(" | ", $errores);

        $log = $pdo->prepare("INSERT INTO log_actividades 
            (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $log->execute([$usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado]);

        $_SESSION['error'] = $errores[0];
        header('Location: ../admin/cuentas_bancarias.php');
        exit;
    }

    try {
        // ✅ Verificar si ya tiene cuenta
        $verificar = $pdo->prepare("SELECT id FROM cuentas_bancarias WHERE estudiante_id = ?");
        $verificar->execute([$estudiante_id]);

        if ($verificar->rowCount() > 0) {
            $resultado = "ERROR";
            $descripcion = "Estudiante ID $estudiante_id ya tiene cuenta bancaria.";

            $log = $pdo->prepare("INSERT INTO log_actividades 
                (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $log->execute([$usuario_id, $accion, $modulo, null, $descripcion, $ip, $navegador, $resultado]);

            $_SESSION['error'] = 'Este estudiante ya tiene una cuenta bancaria registrada.';
            header('Location: ../admin/cuentas_bancarias.php');
            exit;
        }

        // ✅ Insertar cuenta
        $stmt = $pdo->prepare("INSERT INTO cuentas_bancarias
            (estudiante_id, tipo_cuenta, banco, numero_cuenta, tarjeta_visa, fecha_caducidad_tarjeta)
            VALUES (?,?,?,?,?,?)");
        $stmt->execute([
            $estudiante_id,
            $tipo_cuenta,
            $banco,
            $numero_cuenta,
            $tarjeta_visa ?: null,
            $fecha_cad ?: null
        ]);

        $registro_id = $pdo->lastInsertId();
        $descripcion = "Cuenta bancaria registrada para Estudiante ID $estudiante_id, Cuenta ID $registro_id.";

        $log = $pdo->prepare("INSERT INTO log_actividades 
            (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $log->execute([$usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado]);

        $_SESSION['exito'] = 'Cuenta bancaria registrada correctamente.';
        header('Location: ../admin/cuentas_bancarias.php');
        exit;

    } catch (PDOException $e) {
        $resultado = "ERROR";
        $descripcion = "Error al guardar cuenta bancaria para Estudiante ID $estudiante_id. Error: " . $e->getMessage();

        $log = $pdo->prepare("INSERT INTO log_actividades 
            (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $log->execute([$usuario_id, $accion, $modulo, null, $descripcion, $ip, $navegador, $resultado]);

        $_SESSION['error'] = 'Error al guardar la cuenta.';
        header('Location: ../admin/cuentas_bancarias.php');
        exit;
    }

} else {
    header('Location: ../admin/cuentas_bancarias.php');
    exit;
}
?>