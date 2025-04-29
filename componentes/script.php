<?php
// Iniciar sesión de forma segura
if (session_status() === PHP_SESSION_DISABLED) {
    die("⚠️ Las sesiones están deshabilitadas en la configuración del servidor.");
} elseif (session_status() === PHP_SESSION_NONE) {
    session_start();
}
    ?>