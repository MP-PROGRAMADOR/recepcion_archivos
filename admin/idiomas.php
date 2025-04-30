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
function verificarYActualizarIdiomas($pdo) {
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

        $columnsArr = array_map(function($column) {
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
            e.nombre_completo AS estudiante, 
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
            <a href="registrar_idioma.php" class="btn btn-primary rounded-3">
                <i class="bi bi-person-plus-fill me-1"></i> Nuevo Idioma
            </a>
        </div>

        <div class="card shadow rounded-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Estudiante</th>
                                <th>Idioma</th>
                                <th>Duración del Idioma (Meses)</th>
                                <th>Universidad</th>
                                <th>Ciudad</th>
                                <th>País</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($estudiantes)): ?>
                                <?php foreach ($estudiantes as $est): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($est['estudiante']) ?></td>
                                        <td><?= htmlspecialchars($est['idioma']) ?></td>
                                        <td><?= htmlspecialchars($est['meses']) ?> meses</td>
                                        <td><?= htmlspecialchars($est['universidad']) ?></td>
                                        <td><?= htmlspecialchars($est['ciudad']) ?></td>
                                        <td><?= htmlspecialchars($est['pais']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
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

<?php include_once("../componentes/footer.php"); ?>
