<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/conexion.php';

$errores = [];
$id = intval($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre_completo'] ?? '');
$fecha = trim($_POST['fecha_nacimiento'] ?? '');
$pais_id = intval($_POST['pais'] ?? 0);

// Validaciones básicas
if ($nombre === '') $errores[] = "El nombre es obligatorio.";
if ($fecha === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    $errores[] = "Fecha inválida.";
} elseif ($fecha > date('Y-m-d')) {
    $errores[] = "La fecha no puede ser futura.";
}
if ($pais_id <= 0) $errores[] = "País no válido.";

// Validar duplicado (ignorando el estudiante actual)
$nombre_normalizado = strtolower(preg_replace('/\s+/', ' ', $nombre));
$stmt = $pdo->prepare("SELECT COUNT(*) FROM estudiantes WHERE LOWER(TRIM(REPLACE(nombre_completo, '  ', ' '))) = ? AND id != ?");
$stmt->execute([$nombre_normalizado, $id]);
if ($stmt->fetchColumn() > 0) {
    $errores[] = "Ya existe otro estudiante con ese nombre.";
}

// Procesar imagen si se subió
$ruta_foto = null;
if (!empty($_FILES['foto']['name'])) {
    $permitidos = ['image/jpeg', 'image/png', 'image/webp'];
    $foto = $_FILES['foto'];

    if (!in_array($foto['type'], $permitidos)) $errores[] = "Imagen no válida.";
    if ($foto['size'] > 2 * 1024 * 1024) $errores[] = "Máximo 2MB.";
    if (!is_uploaded_file($foto['tmp_name'])) $errores[] = "Error al cargar imagen.";

    if (empty($errores)) {
        $extension = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $directorio = __DIR__ . '/upload/perfil/';
        if (!is_dir($directorio)) mkdir($directorio, 0777, true);

        // Obtener código de acceso actual o generar uno si no hay
        $stmt = $pdo->prepare("SELECT codigo_acceso FROM estudiantes WHERE id = ?");
        $stmt->execute([$id]);
        $codigo = $stmt->fetchColumn();

        if (!$codigo) {
            // Generar código si no existe
            $iniciales_nombre = implode('', array_map(fn($w) => strtoupper($w[0]), explode(' ', $nombre)));
            $stmt = $pdo->prepare("SELECT nombre FROM paises WHERE id = ?");
            $stmt->execute([$pais_id]);
            $iniciales_pais = implode('', array_map(fn($w) => strtoupper($w[0]), explode(' ', $stmt->fetchColumn())));
            $anio = date('y');
            $codigo = "$iniciales_nombre-$iniciales_pais-$anio-$id";
        }

        $nombre_archivo = "perfil-$codigo.$extension";
        $ruta_relativa = "upload/perfil/$nombre_archivo";
        $ruta_completa = $directorio . $nombre_archivo;

        if (move_uploaded_file($foto['tmp_name'], $ruta_completa)) {
            $ruta_foto = $ruta_relativa;
        }
    }
}

// Si hay errores, redirigir
if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    header("Location: ../admin/editar_estudiante.php?id=$id");
    exit;
}

// Actualizar en la base de datos
try {
    $query = "UPDATE estudiantes SET nombre_completo = ?, fecha_nacimiento = ?, pais_id = ?";
    $params = [$nombre, $fecha, $pais_id];

    if ($ruta_foto !== null) {
        $query .= ", ruta_foto = ?";
        $params[] = $ruta_foto;
    }

    $query .= " WHERE id = ?";
    $params[] = $id;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    $_SESSION['exito'] = "Estudiante actualizado correctamente.";
    header("Location: ../admin/estudiantes.php");
    exit;
} catch (PDOException $e) {
    $_SESSION['errores'] = ["Error al actualizar: " . $e->getMessage()];
    header("Location: ../admin/editar_estudiante.php?id=$id");
    exit;
}
?>
