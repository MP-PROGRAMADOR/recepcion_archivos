<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 🔹 Recoger datos
    $estudiante_id = $_POST['estudiante_id'] ?? '';
    $tipo_cuenta   = trim($_POST['tipo_cuenta'] ?? '');
    $banco         = trim($_POST['banco'] ?? '');
    $numero_cuenta = trim($_POST['numero_cuenta'] ?? '');
    $tarjeta_visa  = trim($_POST['tarjeta_visa'] ?? '');
    $fecha_cad     = $_POST['fecha_caducidad_tarjeta'] ?? null;

    // 🔹 Validación básica
    if (empty($estudiante_id) || empty($tipo_cuenta) || empty($banco) || empty($numero_cuenta)) {
        $_SESSION['error'] = 'Debe completar todos los campos obligatorios.';
        header('Location: ../admin/cuentas_bancarias.php');
        exit;
    }

    try {

        // ✅ Verificar si ya tiene cuenta
        $verificar = $pdo->prepare("SELECT id FROM cuentas_bancarias WHERE estudiante_id = ?");
        $verificar->execute([$estudiante_id]);

        if ($verificar->rowCount() > 0) {
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

        $_SESSION['exito'] = 'Cuenta bancaria registrada correctamente.';
        header('Location: ../admin/cuentas_bancarias.php');
        exit;

    } catch (PDOException $e) {

        $_SESSION['error'] = 'Error al guardar la cuenta.';
        header('Location: ../admin/cuentas_bancarias.php');
        exit;
    }

} else {
    header('Location: ../admin/cuentas_bancarias.php');
    exit;
}
?>