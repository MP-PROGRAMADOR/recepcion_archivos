<?php
function mostrarAlerta($tipo, $icono, $titulo, $mensajes, $id, $duracion = 6000) {
    $tipoClase = $tipo === 'danger' ? 'danger' : 'success';
    $iconoClase = $icono === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill';
    $contenido = is_array($mensajes)
        ? '<ul class="mb-0 mt-1">' . implode('', array_map(fn($m) => '<li>' . htmlspecialchars($m) . '</li>', $mensajes)) . '</ul>'
        : '<p class="mb-0 mt-1">' . htmlspecialchars($mensajes) . '</p>';
    ?>
    <div id="<?= $id ?>"
        class="alert alert-<?= $tipoClase ?> alert-dismissible shadow-sm fade show d-flex align-items-start gap-2 p-3 mt-3 border border-<?= $tipoClase ?>-subtle rounded-3"
        role="alert" style="animation: fadeIn 0.5s ease-in-out;">
        <i class="bi <?= $iconoClase ?> fs-4 flex-shrink-0 mt-1"></i>
        <div>
            <strong><?= $titulo ?></strong>
            <?= $contenido ?>
        </div>
        <button type="button" class="btn-close ms-auto mt-1" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <script>
        setTimeout(() => {
            const alerta = document.getElementById('<?= $id ?>');
            if (alerta) {
                alerta.classList.remove('show');
                alerta.classList.add('fade');
                setTimeout(() => alerta.remove(), 500);
            }
        }, <?= $duracion ?>);
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
<?php
}

// Mostrar errores
if (!empty($_SESSION['errores']) && is_array($_SESSION['errores'])) {
    mostrarAlerta('danger', 'error', 'Se detectaron errores:', $_SESSION['errores'], 'alerta-errores', 9000);
    unset($_SESSION['errores']);
}

// Mostrar éxito
if (!empty($_SESSION['exito'])) {
    mostrarAlerta('success', 'success', '¡Éxito!', $_SESSION['exito'], 'alerta-exito', 6000);
    unset($_SESSION['exito']);
}
?>
