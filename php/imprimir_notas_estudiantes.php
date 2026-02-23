<?php
require_once '../config/conexion.php';
require('../fpdf/fpdf.php');

/* ===============================
    FILTROS Y CONSULTA RELACIONAL
=================================*/
$tipo = $_GET['tipo'] ?? '';
$valor = trim($_GET['valor'] ?? '');

// Consulta corregida con JOINs para evitar el Fatal Error de n.nombre_completo
$sql = "SELECT n.id, 
               e.nombre_completo, 
               a.nombre AS anio_academico, 
               n.observaciones, 
               n.fecha_subida, 
               n.archivo_url
        FROM notas n
        INNER JOIN estudiantes e ON n.estudiante_id = e.id
        INNER JOIN anios_academicos a ON n.anio_academico_id = a.id
        WHERE 1=1";

$params = [];

if ($valor !== '') {
    if ($tipo === 'estudiante') {
        $sql .= " AND e.nombre_completo LIKE :valor";
        $params[':valor'] = "%$valor%";
    } elseif ($tipo === 'anio') {
        $sql .= " AND a.nombre LIKE :valor";
        $params[':valor'] = "%$valor%";
    } elseif ($tipo === 'observaciones') {
        $sql .= " AND n.observaciones LIKE :valor";
        $params[':valor'] = "%$valor%";
    }
}

// Lógica de ordenamiento
switch ($tipo) {
    case 'orden_nombre_az': $sql .= " ORDER BY e.nombre_completo ASC"; break;
    case 'orden_fecha_reciente': $sql .= " ORDER BY n.fecha_subida DESC"; break;
    default: $sql .= " ORDER BY n.id DESC"; break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$notas = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        $this->Line(10, 32, 287, 32); 
        $this->SetLineWidth(0.2);
        $this->Line(10, 34, 287, 34); 

        $this->SetY(38); 
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(30, 30, 30);
        $this->Cell(0, 10, mb_convert_encoding('REPORTE OFICIAL DE NOTAS ACADÉMICAS', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(90);
        $this->Cell(0, 5, 'Codigo Documento: TGE-NOT-' . date('Ymd-His'), 0, 1, 'C');

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

$pdf->Cell(138, 8, 'Total de registros: ' . count($notas), 1, 0, 'L', true);
$pdf->Cell(139, 8, mb_convert_encoding('Tipo: Reporte de Notas Estudiantiles', 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);

$pdf->Ln(6);

/* ===== CABECERA TABLA (Diseño Original) ===== */
$pdf->SetFillColor(30, 70, 140);
$pdf->SetTextColor(255);
$pdf->SetFont('Arial', 'B', 10);

// Anchos para 6 columnas ajustadas al ancho A4 horizontal (Total 277mm)
$w = [12, 65, 35, 95, 40, 30]; 
$headers = ['ID', 'NOMBRE COMPLETO', 'AÑO ACAD.', 'OBSERVACIONES', 'FECHA SUBIDA', 'ADJUNTO'];

foreach($headers as $i => $col){
    $pdf->Cell($w[$i], 10, mb_convert_encoding($col, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
}
$pdf->Ln();

/* ===== FILAS ===== */
$pdf->SetFont('Arial', '', 9); // Un poco más pequeña para que quepa la observación
$pdf->SetTextColor(40);
$pdf->SetFillColor(245, 249, 252);

$fill = false;

if (!empty($notas)) {
    foreach($notas as $n){
        $ext = strtoupper(pathinfo($n['archivo_url'], PATHINFO_EXTENSION));
        
        $pdf->Cell($w[0], 9, $n['id'], 1, 0, 'C', $fill);
        $pdf->Cell($w[1], 9, mb_convert_encoding($n['nombre_completo'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);
        $pdf->Cell($w[2], 9, mb_convert_encoding($n['anio_academico'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
        
        // Truncar observaciones para que no rompan la línea de la tabla
        $obs = mb_strimwidth($n['observaciones'], 0, 55, '...');
        $pdf->Cell($w[3], 9, mb_convert_encoding($obs, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);
        
        $pdf->Cell($w[4], 9, date('d/m/Y H:i', strtotime($n['fecha_subida'])), 1, 0, 'C', $fill);
        $pdf->Cell($w[5], 9, $ext, 1, 0, 'C', $fill);

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
$pdf->Cell(0, 6, mb_convert_encoding('Departamento de Registro y Calificaciones', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');
$pdf->Cell(0, 6, mb_convert_encoding('Tesorería General y Patrimonio del Estado', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

$pdf->Output('I', 'Reporte_Notas_Estudiantes.pdf');
?>