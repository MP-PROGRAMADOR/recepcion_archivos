<?php
session_start();
session_destroy();
header('Location: ../estudiante/index.php');
exit;
?>