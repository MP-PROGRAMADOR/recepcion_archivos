<?php

include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");
?>



<!-- Contenido principal -->
<div class="main-content" id="main-content">

    <div class="card mt-4">
    <h2>Formulario de Registro de Estudiante</h2>
    <form action="guardar_estudiante.php" method="POST" enctype="multipart/form-data">
        <label for="nombre_completo">Nombre Completo:</label>
        <input type="text" id="nombre_completo" name="nombre_completo" required><br><br>

        <label for="codigo_acceso">Código de Acceso:</label>
        <input type="text" id="codigo_acceso" name="codigo_acceso" required><br><br>

        <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required><br><br>

        <label for="foto">Foto:</label>
        <input type="file" id="foto" name="foto" accept="image/*" required onchange="previewImage(event)"><br><br>

        <!-- Vista previa de la foto -->
        <img id="foto_preview" src="#" alt="Vista previa" style="display:none;"><br><br>

        <button type="submit">Registrar Estudiante</button>
        <a href="../admin/estudiantes.php" class="btn btn-secondary">Cancelar</a>
    </div>
</div>
<?php
include_once("../componentes/sidebar.php");
?>