<?php
session_start();
require_once '../config/conexion.php';

// 🔒 Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
$navegador = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
$accion = "CREAR";
$modulo = "Cuentas Bancarias";
$registro_id = null;

// ✅ Función para registrar logs (nunca permite usuario null)
function registrar_log($pdo, $usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado) {
    if ($usuario_id) {
        $stmt = $pdo->prepare("INSERT INTO log_actividades
            (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado)
            VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // limpiar mensajes anteriores
    unset($_SESSION['exito'], $_SESSION['error']);

    // 🔹 Recoger datos
    $estudiante_id = $_POST['estudiante_id'] ?? '';
    $tipo_cuenta   = trim($_POST['tipo_cuenta'] ?? '');
    $banco         = trim($_POST['banco'] ?? '');
    $numero_cuenta = trim($_POST['numero_cuenta'] ?? '');
    $tarjeta_visa  = trim($_POST['tarjeta_visa'] ?? '');
    $fecha_cad     = $_POST['fecha_caducidad_tarjeta'] ?? null;

    // 🔹 Validación
    if (empty($estudiante_id) || empty($tipo_cuenta) || empty($banco) || empty($numero_cuenta)) {

        $_SESSION['error'] = 'Debe completar todos los campos obligatorios.';
        registrar_log($pdo, $usuario_id, $accion, $modulo, null,
            "Error validación al registrar cuenta bancaria.", $ip, $navegador, "ERROR");

        header('Location: ../admin/cuentas_bancarias.php');
        exit;
    }

    try {

        // ✅ Verificar si ya tiene cuenta
        $verificar = $pdo->prepare("SELECT id FROM cuentas_bancarias WHERE estudiante_id = ?");
        $verificar->execute([$estudiante_id]);

        if ($verificar->rowCount() > 0) {

            $_SESSION['error'] = 'Este estudiante ya tiene una cuenta bancaria registrada.';

            registrar_log($pdo, $usuario_id, $accion, $modulo, null,
                "Intento duplicado de cuenta bancaria para estudiante ID $estudiante_id.",
                $ip, $navegador, "ERROR");

            header('Location: ../admin/cuentas_bancarias.php');
            exit;
        }

        // ✅ Insertar cuenta bancaria
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

        registrar_log($pdo, $usuario_id, $accion, $modulo, $registro_id,
            "Cuenta bancaria creada (ID $registro_id) para estudiante ID $estudiante_id.",
            $ip, $navegador, "EXITO");

        $_SESSION['exito'] = 'Cuenta bancaria registrada correctamente.';

        header('Location: ../admin/cuentas_bancarias.php');
        exit;

    } catch (PDOException $e) {

        registrar_log($pdo, $usuario_id, $accion, $modulo, null,
            "Error BD al registrar cuenta bancaria: " . $e->getMessage(),
            $ip, $navegador, "ERROR");

        $_SESSION['error'] = 'Error al guardar la cuenta bancaria.';
        header('Location: ../admin/cuentas_bancarias.php');
        exit;
    }

} else {
    header('Location: ../admin/cuentas_bancarias.php');
    exit;
}
?>