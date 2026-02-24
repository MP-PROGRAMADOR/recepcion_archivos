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
    registrarLog($pdo, 0, 'ACCESO_DENEGADO', 'Ciudades', 'Intento de acceso sin sesión', 'ERROR');
    header('Location: ../index.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

/* =========================================
   VALIDAR MÉTODO POST
========================================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    registrarLog($pdo, $usuario_id, 'ACCESO_INVALIDO', 'Ciudades', 'Acceso al archivo sin método POST', 'ERROR');
    $_SESSION['errores'] = ['Acceso no permitido.'];
    header('Location: ../admin/ciudades.php');
    exit;
}

/* =========================================
   RECIBIR DATOS
========================================= */
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$pais_id = isset($_POST['pais_id']) ? (int)$_POST['pais_id'] : 0;

/* =========================================
   VALIDACIONES
========================================= */
$errores = [];

if ($id <= 0) {
    $errores[] = 'ID de ciudad inválido.';
}

if (empty($nombre)) {
    $errores[] = 'El nombre de la ciudad es obligatorio.';
} elseif (strlen($nombre) < 3) {
    $errores[] = 'El nombre de la ciudad debe tener al menos 3 caracteres.';
}

if ($pais_id <= 0) {
    $errores[] = 'Debe seleccionar un país válido.';
}

if (!empty($errores)) {

    registrarLog(
        $pdo,
        $usuario_id,
        'ERROR_VALIDACION',
        'Ciudades',
        'Error al actualizar ciudad ID ' . $id . ': ' . implode(', ', $errores),
        'ERROR',
        $id
    );

    $_SESSION['errores'] = $errores;
    header('Location: ../admin/editar_ciudad.php?id=' . $id);
    exit;
}

/* =========================================
   PROCESO DE ACTUALIZACIÓN
========================================= */
try {

    // Verificar duplicados
    $stmtDup = $pdo->prepare("
        SELECT id 
        FROM ciudades 
        WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(:nombre)) 
          AND pais_id = :pais_id 
          AND id != :id
    ");
    $stmtDup->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmtDup->bindParam(':pais_id', $pais_id, PDO::PARAM_INT);
    $stmtDup->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtDup->execute();

    if ($stmtDup->rowCount() > 0) {

        registrarLog(
            $pdo,
            $usuario_id,
            'ACTUALIZAR',
            'Ciudades',
            'Intento duplicado al actualizar ciudad: ' . $nombre,
            'ERROR',
            $id
        );

        $_SESSION['errores'] = ['Ya existe una ciudad con ese nombre en el país seleccionado.'];
        header('Location: ../admin/editar_ciudad.php?id=' . $id);
        exit;
    }

    // Actualizar ciudad
    $stmt = $pdo->prepare("UPDATE ciudades SET nombre = :nombre, pais_id = :pais_id WHERE id = :id");
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':pais_id', $pais_id, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    registrarLog(
        $pdo,
        $usuario_id,
        'ACTUALIZAR',
        'Ciudades',
        'Ciudad actualizada correctamente: ' . $nombre,
        'EXITO',
        $id
    );

    $_SESSION['exito'] = 'Ciudad actualizada exitosamente.';
    header('Location: ../admin/ciudades.php');
    exit;

} catch (PDOException $e) {

    registrarLog(
        $pdo,
        $usuario_id,
        'ERROR_SISTEMA',
        'Ciudades',
        $e->getMessage(),
        'ERROR',
        $id
    );

    $_SESSION['errores'] = ["Error al actualizar la ciudad."];
    header('Location: ../admin/editar_ciudad.php?id=' . $id);
    exit;
}