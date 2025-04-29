<?php
include_once("../config/conexion.php");

if (isset($_GET['pais_id'])) {
    $pais_id = (int) $_GET['pais_id'];

    $stmt = $pdo->prepare("SELECT id, nombre FROM ciudades WHERE pais_id = ? ORDER BY nombre ASC");
    $stmt->execute([$pais_id]);

    echo '<option value="" disabled selected>Selecciona tu ciudad</option>';
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $ciudad) {
        echo "<option value='{$ciudad['id']}'>" . htmlspecialchars($ciudad['nombre']) . "</option>";
    }
}