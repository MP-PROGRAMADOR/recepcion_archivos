<?php

include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

// Consultas a la base de datos
try {
    $stmt = $pdo->prepare("SELECT id, nombre FROM paises ORDER BY nombre ASC");
    $stmt->execute();
    $paises = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   // echo "<script>Swal.fire('Error', 'Error al cargar los países: " . $e->getMessage() . "', 'error');</script>";
    $paises = [];
}
?>
<main class="content" id="mainContentGuin">
    <canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
    <div class="container mt-4">
        <?php include_once("../componentes/alerta.php"); ?>
        <div class="card shadow rounded-4">
            <div class="card-header bg-success text-white d-flex align-items-center">
                <i class="bi bi-person-lines-fill fs-4 me-2"></i>
                <h5 class="mb-0">Formulario de Registro de Estudiante</h5>
            </div>
            <div class="card-body">

                <form action="../php/guardar_estudiantes.php" method="POST" enctype="multipart/form-data"
                    class="needs-validation" novalidate>
                    <div class="row g-3">
                        <!-- Nombre Completo -->
                        <div class="col-md-6 mb-2">
                            <label for="nombre_completo" class="form-label fw-bold">Nombre Completo</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                <input type="text" id="nombre_completo" name="nombre_completo" class="form-control"
                                    required placeholder="Ej: María López">
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, ingresa tu nombre completo.</div>
                            </div>
                        </div>
                        <!-- Fecha de Nacimiento -->
                        <div class="col-md-6 mb-2">
                            <label for="fecha_nacimiento" class="form-label fw-bold">Fecha de Nacimiento</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-calendar-date-fill"></i></span>
                                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control"
                                    required>
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, selecciona tu fecha de nacimiento.</div>
                            </div>
                        </div>
                        <!-- Año de Inicio de Carrera -->
                        <div class="col-md-6 mb-2">
                            <label for="anio_inicio_carrera" class="form-label fw-bold">Año de Inicio de Carrera</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-calendar-event-fill"></i></span>
                                <input type="number" id="anio_inicio_carrera" name="anio_inicio_carrera"
                                    class="form-control" required placeholder="Ej: 2025">
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, ingresa el año de inicio de carrera.</div>
                            </div>
                        </div>
                        <!-- Año de fin de Carrera -->
                        <div class="col-md-6 mb-2">
                            <label for="anio_fin_carrera" class="form-label fw-bold">Año de fin de Carrera</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-calendar-event-fill"></i></span>
                                <input type="number" id="anio_fin_carrera" name="anio_fin_carrera" class="form-control"
                                    required placeholder="Ej: 2030">
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, ingresa el año de fin de carrera.</div>
                            </div>
                        </div>
                        <!-- País de Estudios -->
                        <div class="col-md-6 mb-2">
                            <label for="pais" class="form-label fw-bold">País de Estudios</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                <select id="pais" name="pais" class="form-select" required>
                                    <option value="" disabled selected>Selecciona tu país</option>
                                    <?php foreach ($paises as $pais): ?>
                                        <option value="<?= htmlspecialchars($pais['id']) ?>">
                                            <?= htmlspecialchars($pais['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, selecciona un país.</div>
                            </div>
                        </div>
                        <!-- Ciudad de Estudios -->
                        <div class="col-md-6 mb-2">
                            <label for="ciudad" class="form-label fw-bold">Ciudad de Estudios</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-geo-fill"></i></span>
                                <select id="ciudad" name="ciudad" class="form-select" required>
                                    <option value="" disabled selected>Selecciona tu ciudad</option>
                                </select>
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, selecciona una ciudad.</div>
                            </div>
                        </div>
                        <!-- Universidad -->
                        <div class="col-md-6 mb-2">
                            <label for="universidad" class="form-label fw-bold">Universidad</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <select id="universidad" name="universidad" class="form-select" required>
                                    <option value="" disabled selected>Selecciona tu universidad</option>
                                </select>
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, selecciona una universidad.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Cuenta Bancaria -->
                    <hr class="my-4">
                    <h5 class="mb-3 text-success"><i class="bi bi-credit-card-2-front-fill me-2"></i>Información de
                        Cuenta Bancaria</h5>

                    <div id="form-container" class="row g-3"> 
                            <!-- Campo inicial: ¿Tiene cuenta? -->
                            <div class="col-md-6 mb-2">
                                <label for="tiene_cuenta" class="form-label fw-bold">¿Tiene cuenta?</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text"><i class="bi bi-question-circle"></i></span>
                                    <select id="tiene_cuenta" name="tiene_cuenta" class="form-select" required>
                                        <option value="" disabled selected>¿Tiene cuenta?</option>
                                        <option value="si">SÍ</option>
                                        <option value="no">NO</option>
                                    </select>
                                </div>
                            </div> 
                    </div>
                    <!-- Botones -->
                    <div class="d-flex justify-content-between mb-4 p-4">
                        <button type="submit" class="btn btn-success me-2">
                            <i class="bi bi-check-circle-fill me-1"></i> Registrar
                        </button>
                        <a href="estudiantes.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle-fill me-1"></i> Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</main>
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Cargar ciudades al seleccionar un país
    document.getElementById('pais').addEventListener('change', function () {
        var paisId = this.value;
        if (paisId) {
            fetch(`../php/get_ciudades.php?pais_id=${paisId}`)
                .then(response => response.json())
                .then(data => {
                    var ciudadSelect = document.getElementById('ciudad');
                    ciudadSelect.innerHTML = '<option value="" disabled selected>Selecciona tu ciudad</option>';
                    data.ciudades.forEach(function (ciudad) {
                        ciudadSelect.innerHTML += `<option value="${ciudad.id}">${ciudad.nombre}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error al cargar las ciudades:', error);
                    Swal.fire('Error', 'No hay ciudades disponibles para este País.', 'error');
                });
        }
    });

    // Cargar universidades al seleccionar una ciudad
    document.getElementById('ciudad').addEventListener('change', function () {
        var ciudadId = this.value;
        if (ciudadId) {
            fetch(`../php/get_universidades.php?ciudad_id=${ciudadId}`)
                .then(response => response.json())
                .then(data => {
                    var universidadSelect = document.getElementById('universidad');
                    universidadSelect.innerHTML = '<option value="" disabled selected>Selecciona tu universidad</option>';
                    data.universidades.forEach(function (universidad) {
                        universidadSelect.innerHTML += `<option value="${universidad.id}">${universidad.nombre}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error al cargar las universidades:', error);
                    Swal.fire('Error', 'NO hay universidades disponibles para esta ciudad.', 'error');
                });
        }
    });

    // Validación Bootstrap 5
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>

<!-- informacion de la cuenta -->


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const formContainer = document.getElementById('form-container');

        const icon = (className) => `<span class="input-group-text"><i class="bi ${className}"></i></span>`;

        // Escucha inicial
        document.getElementById('tiene_cuenta').addEventListener('change', function () {
            clearFrom('tipo_cuenta');
            if (this.value === 'si') addTipoCuenta();
        });

        function addTipoCuenta() {
            if (document.getElementById('tipo_cuenta')) return;

            const div = document.createElement('div');
            div.className = 'col-md-6 mb-2';
            div.id = 'grupo_tipo_cuenta';
            div.innerHTML = `
            <label for="tipo_cuenta" class="form-label fw-bold">Tipo de Cuenta</label>
            <div class="input-group">
                ${icon('bi-wallet2')}
                <select id="tipo_cuenta" name="tipo_cuenta" class="form-select" required>
                    <option value="" disabled selected>Selecciona tipo de cuenta</option>
                    <option value="departamental">Departamental</option>
                    <option value="propia">Propia</option>
                </select>
            </div>
        `;
            formContainer.appendChild(div);

            document.getElementById('tipo_cuenta').addEventListener('change', function () {
                clearFrom('banco');
                addBanco();
            });
        }

        function addBanco() {
            if (document.getElementById('banco')) return;

            const div = document.createElement('div');
            div.className = 'col-md-6 mb-2';
            div.id = 'grupo_banco';
            div.innerHTML = `
            <label for="banco" class="form-label fw-bold">Banco</label>
            <div class="input-group">
                ${icon('bi-bank2')}
                <select id="banco" name="banco" class="form-select" required>
                    <option value="" disabled selected>Selecciona tipo de banco</option>
                    <option value="ecobank">ECOBANK</option>
                    <option value="sgbge">SGBGE</option>
                    <option value="cceibank">CCEIBANK</option>
                    <option value="embajada">EMBAJADA</option>
                </select>
            </div>
        `;
            formContainer.appendChild(div);

            document.getElementById('banco').addEventListener('change', function () {
                clearFrom('tiene_cuenta_numero');
                addTieneNumeroCuenta();
            });
        }

        function addTieneNumeroCuenta() {
            if (document.getElementById('tiene_cuenta_numero')) return;

            const div = document.createElement('div');
            div.className = 'col-md-6 mb-2';
            div.id = 'grupo_tiene_cuenta_numero';
            div.innerHTML = `
            <label for="tiene_cuenta_numero" class="form-label fw-bold">¿Tiene número de cuenta?</label>
            <div class="input-group">
                ${icon('bi-credit-card')}
                <select id="tiene_cuenta_numero" name="tiene_cuenta_numero" class="form-select" required>
                    <option value="" disabled selected>¿Tiene número de cuenta?</option>
                    <option value="si">SÍ</option>
                    <option value="no">NO</option>
                </select>
            </div>
        `;
            formContainer.appendChild(div);
            document.getElementById('tiene_cuenta_numero').addEventListener('change', function () {
                clearFrom('numero_cuenta');
                if (this.value === 'si') {
                    addNumeroCuenta();
                    addTarjetaVisa();
                }
            });

/* 
            document.getElementById('tiene_cuenta_numero').addEventListener('change', function () {
                clearFrom('numero_cuenta');
                if (this.value === 'si') addNumeroCuenta();
                addTarjetaVisa();
            }); */
        }

        function addNumeroCuenta() {
            if (document.getElementById('numero_cuenta')) return;

            const div = document.createElement('div');
            div.className = 'col-md-6 mb-2';
            div.id = 'grupo_numero_cuenta';
            div.innerHTML = `
            <label for="numero_cuenta" class="form-label fw-bold">Número de Cuenta</label>
            <div class="input-group has-validation">
                ${icon('bi-hash')}
                <input type="text" id="numero_cuenta" name="numero_cuenta" class="form-control" required placeholder="Ej: 1234567890">
            </div>
        `;
            formContainer.appendChild(div);
        }

        function addTarjetaVisa() {
            if (document.getElementById('tarjeta_visa')) return;

            const div = document.createElement('div');
            div.className = 'col-md-6 mb-2';
            div.id = 'grupo_tarjeta_visa';
            div.innerHTML = `
            <label for="tarjeta_visa" class="form-label fw-bold">¿Tiene tarjeta VISA?</label>
            <div class="input-group">
                ${icon('bi-credit-card-2-back')}
                <select id="tarjeta_visa" name="tarjeta_visa" class="form-select" required>
                    <option value="" disabled selected>¿Tiene tarjeta?</option>
                    <option value="si">SÍ</option>
                    <option value="no">NO</option>
                </select>
            </div>
        `;
            formContainer.appendChild(div);

            document.getElementById('tarjeta_visa').addEventListener('change', function () {
                clearFrom('fecha_caducidad_tarjeta');
                if (this.value === 'si') addFechaCaducidad();
            });
        }

        function addFechaCaducidad() {
            if (document.getElementById('fecha_caducidad_tarjeta')) return;

            const div = document.createElement('div');
            div.className = 'col-md-6 mb-2';
            div.id = 'grupo_fecha_caducidad_tarjeta';
            div.innerHTML = `
            <label for="fecha_caducidad_tarjeta" class="form-label fw-bold">Fecha de Caducidad</label>
            <div class="input-group">
                ${icon('bi-calendar-check-fill')}
                <input type="date" id="fecha_caducidad_tarjeta" name="fecha_caducidad_tarjeta" class="form-control" required>
            </div>
        `;
            formContainer.appendChild(div);
        }

        // 🧹 Función para eliminar campos desde cierto punto hacia adelante
        function clearFrom(idBase) {
            const ids = [
                'grupo_tipo_cuenta',
                'grupo_banco',
                'grupo_tiene_cuenta_numero',
                'grupo_numero_cuenta',
                'grupo_tarjeta_visa',
                'grupo_fecha_caducidad_tarjeta'
            ];

            let clear = false;
            ids.forEach(id => {
                if (id.includes(idBase)) clear = true;
                if (clear) {
                    const el = document.getElementById(id);
                    if (el) el.remove();
                }
            });
        }
    });
</script>

<?php include_once("../componentes/footer.php"); ?>