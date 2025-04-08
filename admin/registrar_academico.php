<?php

include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");
?>



<!-- Contenido principal -->
<main class="content" id="mainContent">

    <div class="card mt-4">
        <h2>Formulario de Registro de Estudiante</h2>
        <form action="../php/guardar_estudiantes.php" method="POST" enctype="multipart/form-data">
            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" id="nombre_completo" name="nombre_completo" required><br><br>


            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required><br><br>

            <label for="pais">País:</label>
            <select id="pais" name="pais" required>
                <option value="" disabled selected>Selecciona tu país</option>
                <option value="Guinea Ecuatorial">Guinea Ecuatorial</option>
                <option value="España">España</option>
                <option value="México">México</option>
                <option value="Colombia">Colombia</option>
                <option value="Argentina">Argentina</option>
                <!-- Agregar más países según sea necesario -->
            </select><br><br>

            <label for="foto">Foto:</label>
            <input type="file" id="foto" name="foto" accept="image/*" required onchange="previewImage(event)"><br><br>

            <!-- Vista previa de la foto -->
            <img id="foto_preview" src="#" alt="Vista previa" style="display:none;"><br><br>

            <button type="submit">Registrar Estudiante</button>
        </form>
    </div>
</main>
<?php
include_once("../componentes/sidebar.php");
?>