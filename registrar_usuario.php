<?php

include_once("componentes/header.php");
include_once("componentes/sidebar.php");
?>



<!-- Contenido principal -->
<div class="main-content" id="main-content">
    
    <div class="card mt-4">
        <h2>Registrar Usuario</h2>
        <form action="guardar_usuario.php" method="POST" novalidate>
            <div class="mb-3">
                <label for="nombre_usuario" class="form-label">Nombre de usuario</label>
                <input type="text" name="nombre_usuario" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
        <button type="submit" class="btn btn-primary">Guardar usuario</button>
        <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
      </form>
    </div>
</div>
<?php
include_once("componentes/sidebar.php");
?>