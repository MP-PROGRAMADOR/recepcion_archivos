<?php
require_once '../config/conexion.php';
require('../fpdf/fpdf.php');

/* ===============================
    FILTROS
=================================*/
$tipo = $_GET['tipo'] ?? '';
$valor = trim($_GET['valor'] ?? '');

// Consulta adaptada a PASAPORTES y ESTUDIANTES
$sql = "SELECT p.*, e.nombre_completo 
        FROM pasaportes p
        INNER JOIN estudiantes e ON p.estudiante_id = e.id 
        WHERE 1=1";

$params = [];

if ($valor !== '') {
    if ($tipo === 'estudiante') {
        $sql .= " AND e.nombre_completo LIKE :valor";
        $params[':valor'] = "%$valor%";
    } elseif ($tipo === 'pasaporte') {
        $sql .= " AND p.numero_pasaporte LIKE :valor";
        $params[':valor'] = "%$valor%";
    }
}

// Lógica de ordenamiento
if ($tipo === 'orden_nombre_az') {
    $sql .= " ORDER BY e.nombre_completo ASC";
} elseif ($tipo === 'orden_fecha_expiracion') {
    $sql .= " ORDER BY p.fecha_expiracion ASC";
} elseif ($tipo === 'orden_fecha_reciente') {
    $sql .= " ORDER BY p.fecha_subida DESC";
} else {
    $sql .= " ORDER BY p.id DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        $this->Cell(0, 10, mb_convert_encoding('REPORTE OFICIAL DE PASAPORTES REGISTRADOS', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(90);
        $this->Cell(0, 5, 'Codigo Documento: TGE-PAS-' . date('Ymd-His'), 0, 1, 'C');
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

$filtroTexto = $valor ? "Valor buscado: $valor" : "Todos los registros";

$pdf->Cell(138, 8, mb_convert_encoding("Fecha de emision: " . date('d/m/Y'), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
$pdf->Cell(139, 8, mb_convert_encoding($filtroTexto, 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);
$pdf->Cell(138, 8, 'Total de pasaportes: ' . count($registros), 1, 0, 'L', true);
$pdf->Cell(139, 8, mb_convert_encoding('Tipo: Documentacion Estudiantil', 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);
$pdf->Ln(6);

/* ===== CABECERA TABLA ===== */
$pdf->SetFillColor(30, 70, 140);
$pdf->SetTextColor(255);
$pdf->SetFont('Arial', 'B', 9);

// Anchos para cubrir 277mm (Landscape A4)
// ID(12), NOMBRE(85), N.PAS(45), EMISION(35), EXPIRACION(35), SUBIDA(35), ESTADO(30)
$w = [12, 85, 45, 35, 35, 35, 30];
$headers = ['ID', 'NOMBRE DEL ESTUDIANTE', 'N. PASAPORTE', 'EMISION', 'EXPIRACION', 'SUBIDA', 'ESTADO'];

foreach($headers as $i => $col){
    $pdf->Cell($w[$i], 10, mb_convert_encoding($col, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
}
$pdf->Ln();

/* ===== FILAS ===== */
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(40);
$pdf->SetFillColor(245, 249, 252);
$fill = false;

foreach($registros as $row){
    // Determinar estado
    $fecha_exp = strtotime($row['fecha_expiracion']);
    $hoy = time();
    $estado = ($fecha_exp < $hoy) ? 'VENCIDO' : 'VIGENTE';

    $pdf->Cell($w[0], 9, $row['id'], 1, 0, 'C', $fill);
    $pdf->Cell($w[1], 9, mb_convert_encoding($row['nombre_completo'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);
    $pdf->Cell($w[2], 9, $row['numero_pasaporte'], 1, 0, 'C', $fill);
    $pdf->Cell($w[3], 9, date('d/m/Y', strtotime($row['fecha_emision'])), 1, 0, 'C', $fill);
    $pdf->Cell($w[4], 9, date('d/m/Y', strtotime($row['fecha_expiracion'])), 1, 0, 'C', $fill);
    $pdf->Cell($w[5], 9, date('d/m/Y', strtotime($row['fecha_subida'])), 1, 0, 'C', $fill);
    
    // Color rojo si está vencido
    if($estado === 'VENCIDO') $pdf->SetTextColor(200, 0, 0);
    $pdf->Cell($w[6], 9, $estado, 1, 0, 'C', $fill);
    $pdf->SetTextColor(40); // Reset color

    $pdf->Ln();
    $fill = !$fill;
}

/* ===== FIRMA ===== */
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, '____________________________________', 0, 1, 'R');
$pdf->Cell(0, 6, mb_convert_encoding('Negociado de Misiones Diplomáticas', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');
$pdf->Cell(0, 6, mb_convert_encoding('Tesorería General y Patrimonio del Estado', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

$pdf->Output('I', 'Reporte_Pasaportes_Inscritos.pdf');
?>