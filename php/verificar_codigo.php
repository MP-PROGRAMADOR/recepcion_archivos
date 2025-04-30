<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $codigo = trim($_POST['codigo']);

  try {
    $stmt = $pdo->prepare("SELECT * FROM estudiantes WHERE codigo_acceso = ?");
    $stmt->execute([$codigo]);
    $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($estudiante) {
      $_SESSION['estudiante'] = $estudiante;
      $_SESSION['id'] = $estudiante['id'];
      header("Location: ../estudiante/panel_estudiante.php");
      exit();
    } else {
      $_SESSION['error'] = "El código de acceso es incorrecto.";
      header("Location: ../estudiante/index.php");
      exit();
    }
  } catch (Exception $e) {
    $_SESSION['error'] = "Error del sistema: " . $e->getMessage();
    header("Location: ../estudiante/index.php");
    exit();
  }
}

?>
