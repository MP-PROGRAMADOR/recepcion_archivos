<?php
require_once '../config/conexion.php';
require('../fpdf/fpdf.php');

/* ===============================
    FILTROS Y CONSULTA RELACIONAL
=================================*/
$tipo = $_GET['tipo'] ?? '';
$valor = trim($_GET['valor'] ?? '');

// Consulta base con todas las relaciones necesarias
$sql = "SELECT 
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
        WHERE 1=1";

$params = [];

// Aplicación de filtros según la selección del usuario
if ($valor !== '') {
    if ($tipo === 'estudiante') {
        $sql .= " AND e.nombre_completo LIKE :valor";
        $params[':valor'] = "%$valor%";
    } elseif ($tipo === 'idioma') {
        $sql .= " AND i.nombre LIKE :valor";
        $params[':valor'] = "%$valor%";
    } elseif ($tipo === 'universidad') {
        $sql .= " AND u.nombre LIKE :valor";
        $params[':valor'] = "%$valor%";
    } elseif ($tipo === 'pais') {
        $sql .= " AND p.nombre LIKE :valor";
        $params[':valor'] = "%$valor%";
    }
}

// Lógica de ordenamiento
switch ($tipo) {
    case 'orden_estudiante_az': 
        $sql .= " ORDER BY e.nombre_completo ASC"; 
        break;
    case 'orden_meses_mayor': 
        $sql .= " ORDER BY e.meses_idioma DESC"; 
        break;
    case 'orden_meses_menor': 
        $sql .= " ORDER BY e.meses_idioma ASC"; 
        break;
    default: 
        $sql .= " ORDER BY e.nombre_completo ASC"; 
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
    CLASE PDF MINISTERIAL
=================================*/
class PDF extends FPDF {

    function Header() {
        $logo = '../config/img/logo1.png';
        if(file_exists($logo)){
            $this->Image($logo, 10, 8, 20);
        }

        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 51, 102);
        $this->Cell(30);
        $this->Cell(0, 6, mb_convert_encoding('REPÚBLICA DE GUINEA ECUATORIAL', 'ISO-8859-1', 'UTF-8'), 0, 1);

        $this->SetFont('Arial', '', 11);
        $this->Cell(30);
        $this->Cell(0, 6, mb_convert_encoding('TESORERÍA Y PATRIMONIO DEL ESTADO', 'ISO-8859-1', 'UTF-8'), 0, 1);

        $this->Ln(5); 

        $this->SetDrawColor(0, 51, 102);
        $this->SetLineWidth(0.8);
        $this->Line(10, 32, 287, 32); 
        $this->SetLineWidth(0.2);
        $this->Line(10, 34, 287, 34); 

        $this->SetY(38); 
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(30, 30, 30);
        $this->Cell(0, 10, mb_convert_encoding('REPORTE OFICIAL DE ESTUDIANTES E IDIOMAS', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(90);
        $this->Cell(0, 5, 'Codigo Documento: TGE-IDI-' . date('Ymd-His'), 0, 1, 'C');

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

$filtroTexto = $valor ? "Filtro ($tipo): $valor" : "Sin filtros aplicados";

$pdf->Cell(138, 8, mb_convert_encoding("Fecha de emisión: " . date('d/m/Y'), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
$pdf->Cell(139, 8, mb_convert_encoding($filtroTexto, 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);

$pdf->Cell(138, 8, 'Total de estudiantes: ' . count($estudiantes), 1, 0, 'L', true);
$pdf->Cell(139, 8, mb_convert_encoding('Tipo: Reporte Académico de Idiomas', 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);

$pdf->Ln(6);

/* ===== CABECERA TABLA ===== */
$pdf->SetFillColor(30, 70, 140);
$pdf->SetTextColor(255);
$pdf->SetFont('Arial', 'B', 9);

// Anchos para 6 columnas: Estudiante, Idioma, Meses, Universidad, Ciudad, País
// Total 277mm
$w = [65, 35, 25, 67, 45, 40]; 
$headers = ['ESTUDIANTE', 'IDIOMA', 'MESES', 'UNIVERSIDAD', 'CIUDAD', 'PAÍS'];

foreach($headers as $i => $col){
    $pdf->Cell($w[$i], 10, mb_convert_encoding($col, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
}
$pdf->Ln();

/* ===== FILAS ===== */
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(40);
$pdf->SetFillColor(245, 249, 252);

$fill = false;

if (!empty($estudiantes)) {
    foreach($estudiantes as $est){
        // Usamos MultiCell o limitamos el texto para evitar que se desborde la celda
        $pdf->Cell($w[0], 8, mb_convert_encoding($est['nombre_completo'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);
        $pdf->Cell($w[1], 8, mb_convert_encoding($est['idioma'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
        $pdf->Cell($w[2], 8, $est['meses_idioma'], 1, 0, 'C', $fill);
        $pdf->Cell($w[3], 8, mb_convert_encoding($est['universidad'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);
        $pdf->Cell($w[4], 8, mb_convert_encoding($est['ciudad'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);
        $pdf->Cell($w[5], 8, mb_convert_encoding($est['pais'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);

        $pdf->Ln();
        $fill = !$fill;
    }
} else {
    $pdf->Cell(array_sum($w), 10, mb_convert_encoding('No se encontraron estudiantes con los criterios seleccionados', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C');
}

/* ===== FIRMA INSTITUCIONAL ===== */
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, '____________________________________', 0, 1, 'R');
$pdf->Cell(0, 6, mb_convert_encoding('Negociado de Registro Académico', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');
$pdf->Cell(0, 6, mb_convert_encoding('Tesorería General y Patrimonio del Estado', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

$pdf->Output('I', 'Reporte_Idiomas_Estudiantes.pdf');
?>