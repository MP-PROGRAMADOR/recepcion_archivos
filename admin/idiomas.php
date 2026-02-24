<?php
include_once("../componentes/header.php");
include_once("../config/conexion.php");

// Array de idiomas a insertar
$idiomas = [
    ["Afar", "aa", "aar"],
    ["Abjasio", "ab", "abk"],
    ["Afrikáans", "af", "afr"],
    ["Akan", "ak", "aka"],
    ["Albanés", "sq", "sqi"],
    ["Amárico", "am", "amh"],
    ["Árabe", "ar", "ara"],
    ["Aragonés", "an", "arg"],
    ["Armenio", "hy", "hye"],
    ["Asamés", "as", "asm"],
    ["Avar", "av", "ava"],
    ["Aymara", "ay", "aym"],
    ["Azerí", "az", "aze"],
    ["Bambara", "bm", "bam"],
    ["Bashkir", "ba", "bak"],
    ["Bielorruso", "be", "bel"],
    ["Bengalí", "bn", "ben"],
    ["Bislama", "bi", "bis"],
    ["Bosnio", "bs", "bos"],
    ["Bretón", "br", "bre"],
    ["Búlgaro", "bg", "bul"],
    ["Birmano", "my", "mya"],
    ["Catalán", "ca", "cat"],
    ["Cebuano", "ceb", "ceb"],
    ["Checheno", "ce", "che"],
    ["Chino", "zh", "zho"],
    ["Chuvash", "cv", "chv"],
    ["Coreano", "ko", "kor"],
    ["Croata", "hr", "hrv"],
    ["Checo", "cs", "ces"],
    ["Danés", "da", "dan"],
    ["Holandés", "nl", "nld"],
    ["Inglés", "en", "eng"],
    ["Esperanto", "eo", "epo"],
    ["Estonio", "et", "est"],
    ["Ewe", "ee", "ewe"],
    ["Faroés", "fo", "fao"],
    ["Persa", "fa", "fas"],
    ["Finés", "fi", "fin"],
    ["Francés", "fr", "fra"],
    ["Gallego", "gl", "glg"],
    ["Georgiano", "ka", "kat"],
    ["Alemán", "de", "deu"],
    ["Griego", "el", "ell"],
    ["Guaraní", "gn", "grn"],
    ["Gujarati", "gu", "guj"],
    ["Haitiano", "ht", "hat"],
    ["Hausa", "ha", "hau"],
    ["Hebreo", "he", "heb"],
    ["Hindi", "hi", "hin"],
    ["Hmong", "hmn", "hmn"],
    ["Húngaro", "hu", "hun"],
    ["Islandés", "is", "isl"],
    ["Igbo", "ig", "ibo"],
    ["Indonesio", "id", "ind"],
    ["Irlandés", "ga", "gle"],
    ["Italiano", "it", "ita"],
    ["Japonés", "ja", "jpn"],
    ["Canarés", "kn", "kan"],
    ["Kazajo", "kk", "kaz"],
    ["Jemer", "km", "khm"],
    ["Kinyarwanda", "rw", "kin"],
    ["Kirguís", "ky", "kir"],
    ["Kurdo", "ku", "kur"],
    ["Lao", "lo", "lao"],
    ["Latín", "la", "lat"],
    ["Letón", "lv", "lav"],
    ["Lituano", "lt", "lit"],
    ["Luxemburgués", "lb", "ltz"],
    ["Macedonio", "mk", "mkd"],
    ["Malayalam", "ml", "mal"],
    ["Malayo", "ms", "msa"],
    ["Maltés", "mt", "mlt"],
    ["Maorí", "mi", "mri"],
    ["Maratí", "mr", "mar"],
    ["Mongol", "mn", "mon"],
    ["Nepalí", "ne", "nep"],
    ["Noruego", "no", "nor"],
    ["Panyabí", "pa", "pan"],
    ["Pastún", "ps", "pus"],
    ["Polaco", "pl", "pol"],
    ["Portugués", "pt", "por"],
    ["Quechua", "qu", "que"],
    ["Rumano", "ro", "ron"],
    ["Ruso", "ru", "rus"],
    ["Samoano", "sm", "smo"],
    ["Serbio", "sr", "srp"],
    ["Sesotho", "st", "sot"],
    ["Shona", "sn", "sna"],
    ["Eslovaco", "sk", "slk"],
    ["Esloveno", "sl", "slv"],
    ["Somalí", "so", "som"],
    ["Sotho del Sur", "st", "sot"],
    ["Español", "es", "spa"],
    ["Sundanés", "su", "sun"],
    ["Suajili", "sw", "swa"],
    ["Sueco", "sv", "swe"],
    ["Tágalo", "tl", "tgl"],
    ["Tayiko", "tg", "tgk"],
    ["Tamil", "ta", "tam"],
    ["Tártaro", "tt", "tat"],
    ["Telugu", "te", "tel"],
    ["Tailandés", "th", "tha"],
    ["Tibetano", "bo", "bod"],
    ["Tigriña", "ti", "tir"],
    ["Turco", "tr", "tur"],
    ["Turcomano", "tk", "tuk"],
    ["Ucraniano", "uk", "ukr"],
    ["Urdu", "ur", "urd"],
    ["Uzbeko", "uz", "uzb"],
    ["Vietnamita", "vi", "vie"],
    ["Galés", "cy", "cym"],
    ["Xhosa", "xh", "xho"],
    ["Yidis", "yi", "yid"],
    ["Yoruba", "yo", "yor"],
    ["Zulú", "zu", "zul"]
];

// Función para verificar si la tabla y las columnas existen y son correctas
function verificarYActualizarIdiomas($pdo)
{
    try {
        // Comprobamos si la tabla 'idiomas' existe
        $query = "SHOW TABLES LIKE 'idiomas'";
        $stmt = $pdo->prepare($query);
        $stmt->execute();

        // Si la tabla no existe, la creamos
        if ($stmt->rowCount() === 0) {
            $createTableQuery = "
                CREATE TABLE idiomas (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(255) NOT NULL,
                    codigo_2 CHAR(2) NOT NULL,
                    codigo_3 CHAR(3) NOT NULL
                )
            ";
            $pdo->exec($createTableQuery);
            echo "Tabla 'idiomas' creada exitosamente.<br>";
        }

        // Comprobamos si las columnas existen y son correctas
        $describeQuery = "DESCRIBE idiomas";
        $describeStmt = $pdo->prepare($describeQuery);
        $describeStmt->execute();
        $columns = $describeStmt->fetchAll(PDO::FETCH_ASSOC);

        $columnsArr = array_map(function ($column) {
            return $column['Field'];
        }, $columns);

        // Si alguna columna falta, la añadimos
        if (!in_array('nombre', $columnsArr)) {
            $pdo->exec("ALTER TABLE idiomas ADD nombre VARCHAR(255) NOT NULL");
        }
        if (!in_array('codigo_2', $columnsArr)) {
            $pdo->exec("ALTER TABLE idiomas ADD codigo_2 CHAR(2) NOT NULL");
        }
        if (!in_array('codigo_3', $columnsArr)) {
            $pdo->exec("ALTER TABLE idiomas ADD codigo_3 CHAR(3) NOT NULL");
        }

        echo "Tabla 'idiomas' verificada y actualizada si es necesario.<br>";
    } catch (PDOException $e) {
        echo "Error al verificar la tabla o columnas: " . $e->getMessage();
    }
}

// Llamada a la función para verificar y actualizar la tabla
verificarYActualizarIdiomas($pdo);

// Insertar idiomas si no existen en la base de datos
foreach ($idiomas as $idioma) {
    $query = "SELECT COUNT(*) FROM idiomas WHERE codigo_2 = :codigo_2";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':codigo_2', $idioma[1]);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    // Si no existe el idioma, lo insertamos
    if ($count == 0) {
        $insertQuery = "INSERT INTO idiomas (nombre, codigo_2, codigo_3) VALUES (:nombre, :codigo_2, :codigo_3)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->bindParam(':nombre', $idioma[0]);
        $insertStmt->bindParam(':codigo_2', $idioma[1]);
        $insertStmt->bindParam(':codigo_3', $idioma[2]);
        $insertStmt->execute();
        //echo "Idioma '{$idioma[0]}' insertado exitosamente.<br>";
    } else {
        // echo "El idioma '{$idioma[0]}' ya existe.<br>";
    }
}




// Consulta para obtener los datos requeridos de estudiantes, idiomas, universidades, ciudades y países
try {
    $stmt = $pdo->prepare("        SELECT 
            e.nombre_completo,
            e.meses_idioma, 
            i.nombre AS idioma,  
            u.nombre AS universidad, 
            c.nombre AS ciudad, 
            p.nombre AS pais
        FROM estudiantes e
        INNER JOIN idiomas i ON e.idioma_id = i.id
        INNER JOIN universidades u ON e.universidad_id = u.id
        INNER JOIN ciudades c ON e.ciudad_id = c.id
        INNER JOIN paises p ON e.pais_id = p.id
        ORDER BY e.nombre_completo ASC
    ");
    $stmt->execute();
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los estudiantes: " . $e->getMessage());
}

// Layout común
include_once("../componentes/sidebar.php");
?>

<main class="content" id="mainContent">
    <canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="bi bi-mortarboard-fill me-2"></i>Listado de idiomas</h3>
          
        </div>

        <div class="card shadow rounded-4">
            <div class="card-body">



                <div class="row mb-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filtrar por</label>
                        <select id="tipoFiltro" class="form-select" onchange="controlFiltroUI()">
                            <option value="" disabled selected>Seleccione filtro</option>
                            <option value="estudiante">Nombre del Estudiante</option>
                            <option value="idioma">Idioma</option>
                            <option value="universidad">Universidad</option>
                            <option value="pais">País</option>
                            <option value="orden_estudiante_az">Estudiante (A-Z)</option>
                            <option value="orden_meses_mayor">Mayor duración (Meses)</option>
                            <option value="orden_meses_menor">Menor duración (Meses)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Valor</label>
                        <input type="text" id="valorFiltro" class="form-control"
                            placeholder="Escribe el valor a filtrar">
                    </div>

                    <!-- BOTÓN FILTRAR -->
                    <div class="col-md-1 d-grid">
                        <button class="btn btn-primary d-flex btn-sm px-3 me-2" onclick="aplicarFiltro()">
                            <i class="bi bi-funnel-fill me-2"></i> Filtrar
                        </button>
                    </div>

                    <!-- BOTÓN LIMPIAR -->
                    <div class="col-md-1 d-grid">
                        <button class="btn btn-danger btn-sm px-2 ms-2" onclick="limpiarFiltros()">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>

                    <!-- BOTÓN IMPRIMIR -->
                    <div class="col-md-1 d-grid">
                        <button class="btn btn-success d-flex btn-sm px-3" onclick="imprimirFiltrado()">
                            <i class="bi bi-printer-fill me-2"></i> Imprimir
                        </button>
                    </div>
                </div>


                <div class="row mb-3">
                    <div class="col-md-6 mt-2">
                        <label for="busqueda" class="form-label fw-bold">
                            <i class="bi bi-search me-1"></i>Buscar idioma
                        </label>
                        <input type="text" class="form-control" id="busqueda" placeholder="Buscar por idioma...">
                    </div>
                </div>


                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-start">Estudiante</th>
                                <th>Idioma</th>
                                <th>Duración del Idioma (Meses)</th>
                                <th class="text-start">Universidad</th>
                                <th>Ciudad</th>
                                <th>País</th>
                            </tr>
                        </thead>
                        <tbody id="contenidoTabla"> <?php if (!empty($estudiantes)): ?>
                                <?php foreach ($estudiantes as $est): ?>
                                    <tr>
                                        <td class="text-start"><?= htmlspecialchars($est['nombre_completo']) ?></td>
                                        <td><?= htmlspecialchars($est['idioma']) ?></td>
                                        <td><?= htmlspecialchars($est['meses_idioma']) ?> meses</td>
                                        <td class="text-start"><?= htmlspecialchars($est['universidad']) ?></td>
                                        <td><?= htmlspecialchars($est['ciudad']) ?></td>
                                        <td><?= htmlspecialchars($est['pais']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr id="sin-resultados">
                                    <td colspan="6" class="text-center text-muted">No hay estudiantes registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // 1. Manejo de la interfaz
    function controlFiltroUI() {
        const filtro = document.getElementById('tipoFiltro').value;
        const inputValor = document.getElementById('valorFiltro');

        if (filtro.startsWith('orden_')) {
            inputValor.value = "";
            inputValor.disabled = true;
            inputValor.placeholder = "Ordenamiento activo...";
        } else {
            inputValor.disabled = false;
            inputValor.placeholder = "Escribe el valor a filtrar";
        }
    }

    // 2. Aplicar Filtros Principales
    function aplicarFiltro() {
        const tipo = document.getElementById('tipoFiltro').value;
        const valor = document.getElementById('valorFiltro').value.toLowerCase();
        const tabla = document.getElementById('contenidoTabla');
        const filas = Array.from(tabla.getElementsByTagName('tr'));

        let coincidencias = 0;

        if (!tipo.startsWith('orden_')) {
            filas.forEach(fila => {
                if (fila.id === 'sin-resultados') return;

                let textoCelda = "";
                // Índices: 0:Estudiante, 1:Idioma, 3:Universidad, 5:País
                if (tipo === "estudiante" || tipo === "") textoCelda = fila.cells[0].textContent.toLowerCase();
                else if (tipo === "idioma") textoCelda = fila.cells[1].textContent.toLowerCase();
                else if (tipo === "universidad") textoCelda = fila.cells[3].textContent.toLowerCase();
                else if (tipo === "pais") textoCelda = fila.cells[5].textContent.toLowerCase();

                if (textoCelda.includes(valor)) {
                    fila.style.display = "";
                    coincidencias++;
                } else {
                    fila.style.display = "none";
                }
            });
        } else {
            filas.forEach(f => {
                if (f.id !== 'sin-resultados') f.style.display = "";
            });
            coincidencias = filas.length;
            ordenarTabla(tipo);
        }
        manejarMensajeVacio(coincidencias);
    }

    // 3. Ordenamiento
    function ordenarTabla(metodo) {
        const tabla = document.getElementById('contenidoTabla');
        const filas = Array.from(tabla.querySelectorAll('tr:not(#sin-resultados)'));

        const sortedRows = filas.sort((a, b) => {
            const valA = a.cells[0].textContent.trim();
            const valB = b.cells[0].textContent.trim();
            // Extraemos solo el número de la celda de meses (ej: "12 meses" -> 12)
            const numA = parseInt(a.cells[2].textContent) || 0;
            const numB = parseInt(b.cells[2].textContent) || 0;

            switch (metodo) {
                case 'orden_estudiante_az':
                    return valA.localeCompare(valB);
                case 'orden_meses_mayor':
                    return numB - numA;
                case 'orden_meses_menor':
                    return numA - numB;
                default:
                    return 0;
            }
        });

        sortedRows.forEach(row => tabla.appendChild(row));
    }

    // 4. Mensaje de No Resultados
    function manejarMensajeVacio(count) {
        const tabla = document.getElementById('contenidoTabla');
        let filaVacia = document.getElementById('sin-resultados');

        if (count === 0) {
            if (!filaVacia) {
                filaVacia = document.createElement('tr');
                filaVacia.id = 'sin-resultados';
                filaVacia.innerHTML = `<td colspan="6" class="text-center text-muted py-4">No se encontraron coincidencias</td>`;
                tabla.appendChild(filaVacia);
            } else {
                filaVacia.style.display = "";
            }
        } else if (filaVacia) {
            filaVacia.style.display = "none";
        }
    }

    // 5. Limpiar
    function limpiarFiltros() {
        document.getElementById('tipoFiltro').value = "";
        document.getElementById('valorFiltro').value = "";
        document.getElementById('valorFiltro').disabled = false;
        document.getElementById('busqueda').value = "";

        const filas = document.querySelectorAll('#contenidoTabla tr');
        filas.forEach(f => {
            if (f.id === 'sin-resultados') f.style.display = "none";
            else f.style.display = "";
        });
    }

    // 6. Buscador Rápido (Input inferior)
    document.addEventListener('DOMContentLoaded', function() {
        const inputBusqueda = document.getElementById('busqueda');
        if (inputBusqueda) {
            inputBusqueda.addEventListener('keyup', function() {
                const valor = this.value.toLowerCase();
                const filas = document.querySelectorAll('#contenidoTabla tr:not(#sin-resultados)');
                let count = 0;

                filas.forEach(fila => {
                    const texto = fila.textContent.toLowerCase();
                    if (texto.includes(valor)) {
                        fila.style.display = "";
                        count++;
                    } else {
                        fila.style.display = "none";
                    }
                });
                manejarMensajeVacio(count);
            });
        }
    });

    function imprimirFiltrado() {
        const tipo = document.getElementById('tipoFiltro').value;
        const valor = document.getElementById('valorFiltro').value;
        const url = `../php/imprimir_idioma_estudiante.php?tipo=${tipo}&valor=${encodeURIComponent(valor)}`;
        window.open(url, '_blank');
    }
</script>








<?php include_once("../componentes/footer.php"); ?>