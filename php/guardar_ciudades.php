<?php
session_start();
include_once('../config/conexion.php');

$usuario_id = $_SESSION['usuario_id'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
$navegador = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
$accion = "CREAR";
$modulo = "Ciudades";
$registro_id = null; // Se llenará después de insertar
$resultado = "EXITO";
$descripcion = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errores = [];

    // Validar nombre
    if (empty($_POST['nombre'])) {
        $errores[] = 'El nombre de la ciudad es obligatorio.';
    } else {
        $nombre_ciudad = trim($_POST['nombre']);
        if (strlen($nombre_ciudad) < 3) {
            $errores[] = 'El nombre de la ciudad debe tener al menos 3 caracteres.';
        }
    }

    // Validar país
    if (empty($_POST['pais_id'])) {
        $errores[] = 'El país es obligatorio.';
    } else {
        $pais_id = (int) $_POST['pais_id'];
        $query = "SELECT id FROM paises WHERE id = :pais_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':pais_id', $pais_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $errores[] = 'El país seleccionado no es válido.';
        }
    }

    // Guardar log de errores si los hay
    if (count($errores) > 0) {
        $resultado = "ERROR";
        $descripcion = implode(" | ", $errores);

        $log = $pdo->prepare("INSERT INTO log_actividades 
            (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $log->execute([$usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado]);

        $_SESSION['errores'] = $errores;
        header('Location: ../admin/registrar_ciudades.php');
        exit;
    }

    // 🔍 Verificar duplicados (misma ciudad + mismo país)
    $query = "SELECT id FROM ciudades 
              WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(:nombre)) 
              AND pais_id = :pais_id
              LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre_ciudad, PDO::PARAM_STR);
    $stmt->bindParam(':pais_id', $pais_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $resultado = "ERROR";
        $descripcion = "Esta ciudad ya existe para el país seleccionado.";

        // Guardar log
        $log = $pdo->prepare("INSERT INTO log_actividades 
            (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $log->execute([$usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado]);

        $_SESSION['errores'] = ['Esta ciudad ya existe para el país seleccionado.'];
        header('Location: ../admin/registrar_ciudades.php');
        exit;
    }

    // Guardar ciudad
    $query = "INSERT INTO ciudades (nombre, pais_id) VALUES (:nombre, :pais_id)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre_ciudad, PDO::PARAM_STR);
    $stmt->bindParam(':pais_id', $pais_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $registro_id = $pdo->lastInsertId();
        $descripcion = "Ciudad registrada: $nombre_ciudad, País ID: $pais_id, ID: $registro_id";

        // Guardar log de éxito
        $log = $pdo->prepare("INSERT INTO log_actividades 
            (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $log->execute([$usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado]);

        $_SESSION['exito'] = 'Ciudad registrada con éxito.';
        header('Location: ../admin/ciudades.php');
        exit;
    } else {
        $resultado = "ERROR";
        $descripcion = "Hubo un error al registrar la ciudad: $nombre_ciudad, País ID: $pais_id";

        // Guardar log
        $log = $pdo->prepare("INSERT INTO log_actividades 
            (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $log->execute([$usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado]);

        $_SESSION['errores'] = ['Hubo un error al registrar la ciudad.'];
        header('Location: ../admin/registrar_ciudades.php');
        exit;
    }
}
?>