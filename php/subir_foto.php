<?php
session_start();
include '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verificar campos obligatorios
    if (
        isset($_FILES['foto']) && $_FILES['foto']['error'] === 0 &&
        !empty($_POST['email']) &&
        !empty($_POST['telefono']) &&
        !empty($_POST['carrera_actual']) &&
        !empty($_POST['ciudad_actual'])
    ) {

        $id_estudiante = $_SESSION['id'];

        $foto = $_FILES['foto'];
        $nombreOriginal = basename($foto['name']);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        // Sanitizar datos
        $email = trim($_POST['email']);
        $telefono = trim($_POST['telefono']);
        $carrera_actual = trim($_POST['carrera_actual']);
        $ciudad_actual = trim($_POST['ciudad_actual']);

        /* ================= VALIDACIONES ================= */

        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "El correo electrónico no es válido.";
            header('Location: ../estudiante/perfil.php');
            exit;
        }

        // Validar teléfono
        if (!preg_match("/^\+?[0-9\s\-]{7,15}$/", $telefono)) {
            $_SESSION['error'] = "El número de teléfono no es válido.";
            header('Location: ../estudiante/perfil.php');
            exit;
        }

        // Validar longitud mínima
        if (strlen($carrera_actual) < 3) {
            $_SESSION['error'] = "La carrera no es válida.";
            header('Location: ../estudiante/perfil.php');
            exit;
        }

        if (strlen($ciudad_actual) < 2) {
            $_SESSION['error'] = "La ciudad no es válida.";
            header('Location: ../estudiante/perfil.php');
            exit;
        }

        // Validar extensión imagen
        $extensionesPermitidas = ['jpg', 'jpeg', 'png'];
        if (!in_array($extension, $extensionesPermitidas)) {
            $_SESSION['error'] = "Formato no permitido. Solo JPG o PNG.";
            header('Location: ../estudiante/perfil.php');
            exit;
        }

        // Validar tamaño máximo 2MB
        if ($foto['size'] > 2 * 1024 * 1024) {
            $_SESSION['error'] = "La imagen supera el tamaño máximo permitido de 2MB.";
            header('Location: ../estudiante/perfil.php');
            exit;
        }

        /* ================= SUBIR IMAGEN ================= */

        $nombreNuevo = 'foto_perfil_' . $id_estudiante . '_' . time() . '.' . $extension;
        $rutaDestino = '../php/upload/perfil/' . $nombreNuevo;

        if (move_uploaded_file($foto['tmp_name'], $rutaDestino)) {

            // Actualizar base de datos
            $stmt = $pdo->prepare("
                UPDATE estudiantes SET
                    foto_perfil = :foto_perfil,
                    email = :email,
                    telefono = :telefono,
                    carrera_actual = :carrera_actual,
                    ciudad_actual = :ciudad_actual
                WHERE id = :id
            ");

            $stmt->bindParam(':foto_perfil', $nombreNuevo);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':carrera_actual', $carrera_actual);
            $stmt->bindParam(':ciudad_actual', $ciudad_actual);
            $stmt->bindParam(':id', $id_estudiante);
            $stmt->execute();

            $_SESSION['success'] = "Perfil actualizado correctamente.";
            header('Location: ../estudiante/panel_estudiante.php');
            exit;

        } else {
            $_SESSION['error'] = "Error al mover la imagen.";
            header('Location: ../estudiante/perfil.php');
            exit;
        }

    } else {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header('Location: ../estudiante/perfil.php');
        exit;
    }
}
?>