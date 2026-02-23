<?php
require '../config/conexion.php';

$q = $_GET['q'] ?? '';

$stmt = $pdo->prepare("SELECT id, nombre_completo, fecha_nacimiento 
                       FROM estudiantes 
                       WHERE nombre_completo LIKE ? 
                       LIMIT 3");
$stmt->execute(["%$q%"]);

$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($resultados as $row) {
    echo '<a href="#" class="list-group-item list-group-item-action estudiante-item d-flex justify-content-between align-items-center mb-2"
   data-id="'.$row['id'].'"
   data-nombre="'.$row['nombre_completo'].'"
   data-fecha="'.$row['fecha_nacimiento'].'">
    
    <span class="fw-bold">'.$row['nombre_completo'].'</span>
    <small class="text-muted">'.$row['fecha_nacimiento'].'</small>
    
</a>';
}