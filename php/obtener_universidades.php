<?php
include_once("../config/conexion.php");

$ciudad_id = isset($_GET['ciudad_id']) ? (int)$_GET['ciudad_id'] : 0;

$stmt = $pdo->prepare("SELECT id, nombre FROM universidades WHERE ciudad_id = ? ORDER BY nombre");
$stmt->execute([$ciudad_id]);

foreach ($stmt as $uni) {
    echo "<option value='{$uni['id']}'>" . htmlspecialchars($uni['nombre']) . "</option>";
}