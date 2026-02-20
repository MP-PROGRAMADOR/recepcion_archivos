<?php
require_once '../config/conexion.php';
require('../fpdf/fpdf.php');

/* ===============================
    FILTROS
=================================*/
$tipo = $_GET['tipo'] ?? '';
$valor = trim($_GET['valor'] ?? '');

// Consulta adaptada a Países con conteo de estudiantes
$sql = "SELECT p.id, p.nombre, COUNT(e.id) as estudiantes 
        FROM paises p
        LEFT JOIN estudiantes e ON p.id = e.pais_id
        GROUP BY p.id
        HAVING estudiantes > 0";

$params = [];

if ($valor !== '') {
    if ($tipo === 'nombre') {
        $sql .= " AND p.nombre LIKE :valor";
        $params[':valor'] = "%$valor%";
    } elseif ($tipo === 'id') {
        $sql .= " AND p.id = :valor";
        $params[':valor'] = $valor;
    }
}

$sql .= " ORDER BY p.nombre ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$paises = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

        // ESPACIO PARA QUE LAS LÍNEAS NO TOQUEN EL TEXTO
        $this->Ln(5); 

        // LÍNEAS INSTITUCIONALES (Ajustadas para no chocar)
        $this->SetDrawColor(0, 51, 102);
        $this->SetLineWidth(0.8);
        $this->Line(10, 32, 287, 32); // Línea gruesa
        $this->SetLineWidth(0.2);
        $this->Line(10, 34, 287, 34); // Línea fina

        // TÍTULO DEL REPORTE (Bajamos un poco para no pegar a la línea)
        $this->SetY(38); 
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(30, 30, 30);
        $this->Cell(0, 10, mb_convert_encoding('REPORTE OFICIAL DE PAÍSES CON ESTUDIANTES', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Código del documento
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(90);
        $this->Cell(0, 5, 'Codigo Documento: TGE-PAIS-' . date('Ymd-His'), 0, 1, 'C');

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

$pdf->Cell(138, 8, 'Total de registros: ' . count($paises), 1, 0, 'L', true);
$pdf->Cell(139, 8, mb_convert_encoding('Tipo: Estadístico Geográfico', 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);

$pdf->Ln(6);

/* ===== CABECERA TABLA (Centrada y Ajustada) ===== */
$pdf->SetFillColor(30, 70, 140);
$pdf->SetTextColor(255);
$pdf->SetFont('Arial', 'B', 10);

// Anchos para ocupar el ancho de la página horizontal (277mm aprox disponibles)
// ID (40) + NOMBRE (150) + ESTUDIANTES (87) = 277
$w = [40, 150, 87];
$headers = ['ID PAÍS', 'NOMBRE DEL PAÍS', 'CANTIDAD DE ESTUDIANTES'];

foreach($headers as $i => $col){
    $pdf->Cell($w[$i], 10, mb_convert_encoding($col, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
}
$pdf->Ln();

/* ===== FILAS ===== */
$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(40);
$pdf->SetFillColor(245, 249, 252);

$fill = false;

foreach($paises as $p){
    $pdf->Cell($w[0], 9, $p['id'], 1, 0, 'C', $fill);
    $pdf->Cell($w[1], 9, mb_convert_encoding($p['nombre'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);
    $pdf->Cell($w[2], 9, $p['estudiantes'], 1, 0, 'C', $fill);

    $pdf->Ln();
    $fill = !$fill;
}

/* ===== FIRMA INSTITUCIONAL ===== */
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, '____________________________________', 0, 1, 'R');
$pdf->Cell(0, 6, mb_convert_encoding('Negociado de Misiones Diplomáticas', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');
$pdf->Cell(0, 6, mb_convert_encoding('Tesorería General y Patrimonio del Estado', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

$pdf->Output('I', 'Reporte_Paises_Inscritos.pdf');
?>