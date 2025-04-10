<!-- Campo de búsqueda para los países -->
<div class="col-md-6">
    <label for="buscar_pais" class="form-label fw-bold">Buscar País</label>
    <input type="text" id="buscar_pais" class="form-control" placeholder="Buscar país" onkeyup="filterCountries()">
</div>

<!-- Selección de País -->
<div class="col-md-6">
    <label for="pais" class="form-label fw-bold">País</label>
    <select id="pais" name="pais" class="form-select" required>
        <option value="" disabled selected>Selecciona tu país</option>
        <?php foreach ($paises as $pais): ?>
            <option value="<?= htmlspecialchars($pais['id']) ?>" class="country-item">
                <?= htmlspecialchars($pais['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<script>
    // Función para filtrar los países en el select
    function filterCountries() {
        const filter = document.getElementById('buscar_pais').value.toUpperCase(); // Obtener el valor del input
        const select = document.getElementById('pais');
        const options = select.getElementsByClassName('country-item'); // Obtener las opciones del select

        // Iterar sobre las opciones y mostrar/ocultar según el filtro
        for (let i = 0; i < options.length; i++) {
            const option = options[i];
            const text = option.textContent || option.innerText;

            // Mostrar la opción si el texto contiene el filtro
            if (text.toUpperCase().indexOf(filter) > -1) {
                option.style.display = "";
            } else {
                option.style.display = "none";
            }
        }
    }
</script>
