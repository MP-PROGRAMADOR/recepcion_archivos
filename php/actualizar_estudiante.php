<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once("../config/conexion.php");

// Verificamos si el ID del estudiante está presente y es válido
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    $_SESSION['errores'][] = "ID del estudiante no válido.";
    header("Location: ../admin/estudiantes.php");
    exit;
}

$id = (int) $_POST['id']; // ID del estudiante

// Recolectar y sanitizar datos del formulario
$nombre_completo = trim($_POST['nombre_completo'] ?? '');
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
$pais_id = $_POST['pais'] ?? '';
$foto_actual = $_POST['foto_actual'] ?? null;

$errores = [];

// Validaciones básicas
if (empty($nombre_completo)) $errores[] = "El nombre completo es obligatorio.";
if (empty($fecha_nacimiento)) $errores[] = "La fecha de nacimiento es obligatoria.";
if (empty($pais_id)) $errores[] = "Debe seleccionar un país.";

// Validar duplicado (excepto el actual estudiante)
$sql = "SELECT COUNT(*) FROM estudiantes WHERE nombre_completo = :nombre AND id != :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':nombre' => $nombre_completo, ':id' => $id]);
if ($stmt->fetchColumn() > 0) {
    $errores[] = "Ya existe otro estudiante con ese nombre completo.";
}

// Manejo de imagen
$nombre_foto_final = $foto_actual;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto_tmp = $_FILES['foto']['tmp_name'];
    $nombre_foto = basename($_FILES['foto']['name']);
    $ext = strtolower(pathinfo($nombre_foto, PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $permitidas)) {
        $errores[] = "Formato de imagen no válido.";
    } else {
        $nombre_foto_final = uniqid("estudiante_") . "." . $ext;
        $ruta_destino = "upload/" . $nombre_foto_final;

        if (!move_uploaded_file($foto_tmp, $ruta_destino)) {
            $errores[] = "Error al subir la nueva foto.";
        } else {
            // Eliminar foto anterior si existe
            if ($foto_actual && file_exists("upload/" . $foto_actual)) {
                unlink("upload/" . $foto_actual);
            }
        }
    }
}

// Si hay errores, redirigimos
if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    header("Location: ../admin/editar_estudiantes.php?id=$id");
    exit;
}

// Actualizar en base de datos
try {
    $sql = "UPDATE estudiantes 
            SET nombre_completo = :nombre, 
                fecha_nacimiento = :fecha, 
                pais_id = :pais, 
                ruta_foto = :foto 
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre_completo,
        ':fecha' => $fecha_nacimiento,
        ':pais' => $pais_id,
        ':foto' => $nombre_foto_final,
        ':id' => $id
    ]);

    $_SESSION['exito'] = "Estudiante actualizado correctamente.";
    header("Location: ../admin/estudiantes.php");
    exit;
} catch (PDOException $e) {
    $_SESSION['errores'][] = "Error al actualizar: " . $e->getMessage();
    header("Location: ../admin/editar_estudiantes.php?id=$id");
    exit;
}
