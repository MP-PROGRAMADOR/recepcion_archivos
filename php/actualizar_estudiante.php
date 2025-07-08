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
$ciudad_id = intval($_POST['ciudad'] ?? 0);
$universidad_id = intval($_POST['universidad'] ?? 0);
$anio_inicio = intval($_POST['anio_inicio_carrera'] ?? 0);
$anio_fin = intval($_POST['anio_fin_carrera'] ?? 0);

// Validaciones
if ($nombre === '') $errores[] = "El nombre es obligatorio.";
if ($fecha === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    $errores[] = "Fecha inválida.";
} elseif ($fecha > date('Y-m-d')) {
    $errores[] = "La fecha no puede ser futura.";
}
if ($pais_id <= 0) $errores[] = "País no válido.";
if ($ciudad_id <= 0) $errores[] = "Ciudad no válida.";
if ($universidad_id <= 0) $errores[] = "Universidad no válida.";
if ($anio_inicio <= 1900 || $anio_inicio > date('Y')) $errores[] = "Año de inicio no válido.";
if ($anio_fin < $anio_inicio || $anio_fin > date('Y') + 10) $errores[] = "Año de fin no válido.";

// Duplicado
$nombre_normalizado = strtolower(preg_replace('/\s+/', ' ', $nombre));
$stmt = $pdo->prepare("SELECT COUNT(*) FROM estudiantes WHERE LOWER(TRIM(REPLACE(nombre_completo, '  ', ' '))) = ? AND id != ?");
$stmt->execute([$nombre_normalizado, $id]);
if ($stmt->fetchColumn() > 0) {
    $errores[] = "Ya existe otro estudiante con ese nombre.";
}

// Obtener código de acceso
$stmt = $pdo->prepare("SELECT codigo_acceso FROM estudiantes WHERE id = ?");
$stmt->execute([$id]);
$codigo = $stmt->fetchColumn();

// ----------------- PROCESAR FOTO PERFIL ------------------
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

        if (!$codigo) {
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

// ----------------- PROCESAR ARCHIVO DE BECA ------------------
$ruta_beca = null;
if (!empty($_FILES['archivo_beca']['name'])) {
    $permitidos_beca = [
        'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg', 'image/png'
    ];
    $beca = $_FILES['archivo_beca'];

    if (!in_array($beca['type'], $permitidos_beca)) $errores[] = "Archivo de beca no permitido.";
    if ($beca['size'] > 3 * 1024 * 1024) $errores[] = "El archivo de beca no debe superar los 3MB.";
    if (!is_uploaded_file($beca['tmp_name'])) $errores[] = "Error al cargar el archivo de beca.";

    if (empty($errores)) {
        $extension = pathinfo($beca['name'], PATHINFO_EXTENSION);
        $directorio_beca = __DIR__ . '/upload/becas/';
        if (!is_dir($directorio_beca)) mkdir($directorio_beca, 0777, true);

        $nombre_beca = "beca-$codigo.$extension";
        $ruta_beca_relativa = "upload/becas/$nombre_beca";
        $ruta_beca_completa = $directorio_beca . $nombre_beca;

        if (move_uploaded_file($beca['tmp_name'], $ruta_beca_completa)) {
            $ruta_beca = $ruta_beca_relativa;
        }
    }
}

// ----------------- ERRORES ------------------
if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    header("Location: ../admin/editar_estudiante.php?id=$id");
    exit;
}

// ----------------- ACTUALIZAR BD ------------------
try {
    $query = "UPDATE estudiantes SET nombre_completo = ?, fecha_nacimiento = ?, pais_id = ?, ciudad_id = ?, universidad_id = ?, anio_inicio_carrera = ?, anio_fin_carrera = ?";
    $params = [$nombre, $fecha, $pais_id, $ciudad_id, $universidad_id, $anio_inicio, $anio_fin];

    if ($ruta_foto !== null) {
        $query .= ", ruta_foto = ?";
        $params[] = $ruta_foto;
    }

    if ($ruta_beca !== null) {
        $query .= ", archivo_beca = ?";
        $params[] = $ruta_beca;
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
