<?php
require_once '../config/conexion.php'; // Conexión PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $codigo = trim($_POST['codigo']);

  try {
    $stmt = $pdo->prepare("SELECT * FROM estudiantes WHERE codigo_acceso = ?");
    $stmt->execute([$codigo]);
    $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($estudiante) {
      session_start();
      $_SESSION['estudiante'] = $estudiante;
      header("Location: ../estudiante/panel_estudiante.php");
      exit();
    } else {
      echo "<script>alert('Código inválido'); window.location.href='../estudiante/index.php';</script>";
    }
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
?>
