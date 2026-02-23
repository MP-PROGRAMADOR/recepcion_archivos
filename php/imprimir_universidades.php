<?php
require_once '../config/conexion.php';
require('../fpdf/fpdf.php');

/* ===============================
    FILTROS Y CONSULTA RELACIONAL
=================================*/
$tipo = $_GET['tipo'] ?? '';
$valor = trim($_GET['valor'] ?? '');

// Consulta con JOINs para obtener Universidad, Ciudad, País y conteo de Estudiantes
$sql = "SELECT u.id, u.nombre AS universidad, 
               c.nombre AS ciudad, 
               p.nombre AS pais, 
               COUNT(e.id) AS total_estudiantes
        FROM universidades u
        INNER JOIN ciudades c ON u.ciudad_id = c.id
        INNER JOIN paises p ON c.pais_id = p.id
        INNER JOIN estudiantes e ON u.id = e.universidad_id
        WHERE 1=1";

$params = [];

if ($valor !== '') {
    if ($tipo === 'nombre') {
        $sql .= " AND u.nombre LIKE :valor";
        $params[':valor'] = "%$valor%";
    } elseif ($tipo === 'ciudad') {
        $sql .= " AND c.nombre LIKE :valor";
        $params[':valor'] = "%$valor%";
    } elseif ($tipo === 'pais') {
        $sql .= " AND p.nombre LIKE :valor";
        $params[':valor'] = "%$valor%";
    }
}

$sql .= " GROUP BY u.id";

// Lógica de ordenamiento
switch ($tipo) {
    case 'orden_az': $sql .= " ORDER BY u.nombre ASC"; break;
    case 'orden_za': $sql .= " ORDER BY u.nombre DESC"; break;
    case 'mayor_estudiantes': $sql .= " ORDER BY total_estudiantes DESC"; break;
    case 'menor_estudiantes': $sql .= " ORDER BY total_estudiantes ASC"; break;
    default: $sql .= " ORDER BY u.id DESC"; break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$universidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
    CLASE PDF MINISTERIAL (Estructura Original)
=================================*/
class PDF extends FPDF {

    function Header() {
        // LOGO
        $logo = '../config/img/logo1.png';
        if(file_exists($logo)){
            $this->Image($logo, 10, 8, 20);
        }

        // NOMBRE INSTITUCIONAL
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 51, 102);
        $this->Cell(30);
        $this->Cell(0, 6, mb_convert_encoding('REPÚBLICA DE GUINEA ECUATORIAL', 'ISO-8859-1', 'UTF-8'), 0, 1);

        $this->SetFont('Arial', '', 11);
        $this->Cell(30);
        $this->Cell(0, 6, mb_convert_encoding('TESORERÍA Y PATRIMONIO DEL ESTADO', 'ISO-8859-1', 'UTF-8'), 0, 1);

        $this->Ln(5); 

        // LÍNEAS INSTITUCIONALES
        $this->SetDrawColor(0, 51, 102);
        $this->SetLineWidth(0.8);
        $this->Line(10, 32, 287, 32); // Línea gruesa
        $this->SetLineWidth(0.2);
        $this->Line(10, 34, 287, 34); // Línea fina

        $this->SetY(38); 
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(30, 30, 30);
        $this->Cell(0, 10, mb_convert_encoding('REPORTE OFICIAL DE UNIVERSIDADES CON ESTUDIANTES', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(90);
        $this->Cell(0, 5, 'Codigo Documento: TGE-UNI-' . date('Ymd-His'), 0, 1, 'C');

        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-18);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120);

        $this->Line(10, $this->GetY(), 287, $this->GetY());
        $this->Ln(2);

        $this->Cell(0, 5, mb_convert_encoding('Documento Oficial - Uso Interno Institucional', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $this->Cell(0, 5, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->Cell(0, 5, date('d/m/Y H:i'), 0, 0, 'R');
    }
}

/* ===============================
    GENERAR PDF
=================================*/
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 20);

/* ===== CAJA INFORMATIVA ===== */
$pdf->SetFillColor(240, 240, 240);
$pdf->SetDrawColor(200, 200, 200);
$pdf->SetFont('Arial', '', 10);

$filtroTexto = $valor ? "Valor filtrado: $valor" : "Sin filtros aplicados";

$pdf->Cell(138, 8, mb_convert_encoding("Fecha de emisión: " . date('d/m/Y'), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
$pdf->Cell(139, 8, mb_convert_encoding($filtroTexto, 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);

$pdf->Cell(138, 8, 'Total de registros: ' . count($universidades), 1, 0, 'L', true);
$pdf->Cell(139, 8, mb_convert_encoding('Tipo: Reporte Académico Geográfico', 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);

$pdf->Ln(6);

/* ===== CABECERA TABLA (Diseño Original) ===== */
$pdf->SetFillColor(30, 70, 140);
$pdf->SetTextColor(255);
$pdf->SetFont('Arial', 'B', 10);

// Anchos ajustados para las 5 columnas (Total 277mm)
$w = [15, 92, 60, 60, 50]; 
$headers = ['ID', 'UNIVERSIDAD', 'CIUDAD', 'PAÍS', 'ESTUDIANTES'];

foreach($headers as $i => $col){
    $pdf->Cell($w[$i], 10, mb_convert_encoding($col, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
}
$pdf->Ln();

/* ===== FILAS ===== */
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(40);
$pdf->SetFillColor(245, 249, 252);

$fill = false;

if (!empty($universidades)) {
    foreach($universidades as $u){
        $pdf->Cell($w[0], 9, $u['id'], 1, 0, 'C', $fill);
        $pdf->Cell($w[1], 9, mb_convert_encoding($u['universidad'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);
        $pdf->Cell($w[2], 9, mb_convert_encoding($u['ciudad'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);
        $pdf->Cell($w[3], 9, mb_convert_encoding($u['pais'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);
        $pdf->Cell($w[4], 9, $u['total_estudiantes'], 1, 0, 'C', $fill);

        $pdf->Ln();
        $fill = !$fill;
    }
} else {
    $pdf->Cell(array_sum($w), 10, mb_convert_encoding('No se encontraron registros', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C');
}

/* ===== FIRMA INSTITUCIONAL (Original) ===== */
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, '____________________________________', 0, 1, 'R');
$pdf->Cell(0, 6, mb_convert_encoding('Negociado de Registro Académico', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');
$pdf->Cell(0, 6, mb_convert_encoding('Tesorería General y Patrimonio del Estado', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

$pdf->Output('I', 'Reporte_Universidades_Inscritas.pdf');
?>