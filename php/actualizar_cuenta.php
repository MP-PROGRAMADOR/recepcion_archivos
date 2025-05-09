<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
     

        // Validar y obtener los datos del formulario
        $tipo_cuenta = $_POST['tipo_cuenta'] ?? null;
        $banco = $_POST['banco'] ?? null;
        $numero_cuenta = $_POST['numero_cuenta'] ?? null;
        $tarjeta_visa = $_POST['tarjeta_visa'] ?? null;
        $fecha_caducidad_tarjeta = $_POST['fecha_caducidad_tarjeta'] ?? null;
        $estudiante_id = $_POST['estudiante_id'] ?? null;

        // Validación básica
        if (!$tipo_cuenta || !$banco || !$numero_cuenta || !$tarjeta_visa || !$estudiante_id) {
            $_SESSION['error'] = "Todos los campos obligatorios deben estar completos.";
            header("Location: ../admin/editar_cuenta.php");
            exit;
        }

        // Si no tiene tarjeta VISA, dejar fecha de caducidad como NULL
        if ($tarjeta_visa === 'no') {
            $fecha_caducidad_tarjeta = null;
        }

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

        $_SESSION['exito'] = "La cuenta bancaria ha sido actualizada correctamente.";
        header("Location: ../admin/cuentas_bancarias.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar la cuenta: " . $e->getMessage();
        header("Location: ../admin/editar_cuenta.php"  );
        exit;
    }
} else {
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: ../admin/cuentas_bancarias.php");
    exit;
}
