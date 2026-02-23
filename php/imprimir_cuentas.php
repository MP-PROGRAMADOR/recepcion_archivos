<?php
require_once '../config/conexion.php';
require('../fpdf/fpdf.php');

/* ===============================
   FILTROS
=================================*/
$tipo  = $_GET['tipo'] ?? '';
$valor = trim($_GET['valor'] ?? '');

$sql = "SELECT 
        cb.id,
        est.nombre_completo AS titular,
        cb.banco,
        cb.numero_cuenta,
        cb.tipo_cuenta,
        cb.tarjeta_visa,
        cb.fecha_caducidad_tarjeta
        FROM cuentas_bancarias cb
        LEFT JOIN estudiantes est ON cb.estudiante_id = est.id
        WHERE 1=1";

$params = [];

/* ===== COLUMNAS FILTRABLES ===== */
$columnas = [
    'titular'          => 'est.nombre_completo',
    'banco'            => 'cb.banco',
    'tipo_cuenta'      => 'cb.tipo_cuenta',
    'fecha_caducidad'  => 'cb.fecha_caducidad_tarjeta'
];

if ($valor !== '' && isset($columnas[$tipo])) {

    if ($tipo === 'fecha_caducidad') {
        $sql .= " AND DATE(cb.fecha_caducidad_tarjeta) = :valor";
        $params[':valor'] = $valor;
    } else {
        $sql .= " AND {$columnas[$tipo]} LIKE :valor";
        $params[':valor'] = "%$valor%";
    }
}

/* ===== ORDENAMIENTO ===== */
if ($tipo === 'orden_az') {
    $sql .= " ORDER BY est.nombre_completo ASC";
} elseif ($tipo === 'orden_za') {
    $sql .= " ORDER BY est.nombre_completo DESC";
} else {
    $sql .= " ORDER BY cb.id DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* ===============================
   CLASE PDF INSTITUCIONAL
=================================*/
class PDF extends FPDF {

    function Header() {

        // LOGO
        $logo = '../config/img/logo1.png';
        if(file_exists($logo)){
            $this->Image($logo,10,8,20);
        }

        // NOMBRE INSTITUCIONAL
        $this->SetFont('Arial','B',14);
        $this->SetTextColor(0,51,102);
        $this->Cell(30);
        $this->Cell(0,6, mb_convert_encoding('REPUBLICA DE GUINEA ECUATORIAL','ISO-8859-1','UTF-8'),0,1);

        $this->SetFont('Arial','',11);
        $this->Cell(30);
        $this->Cell(0,6, mb_convert_encoding('TESORERIA Y PATRIMONIO DEL ESTADO','ISO-8859-1','UTF-8'),0,1);

        $this->Cell(30);
        $this->Cell(0,6, mb_convert_encoding('','ISO-8859-1','UTF-8'),0,1);

        // Líneas institucionales
        $this->Ln(2);
        $this->SetDrawColor(0,51,102);
        $this->SetLineWidth(0.8);
        $this->Line(10,32,287,32);
        $this->SetLineWidth(0.2);
        $this->Line(10,34,287,34);

        // TÍTULO
        $this->Ln(6);
        $this->SetFont('Arial','B',13);
        $this->SetTextColor(30,30,30);
        $this->Cell(0,8, mb_convert_encoding('REPORTE OFICIAL DE ESTUDIANTES BECADOS','ISO-8859-1','UTF-8'),0,1,'C');

        // Código del documento
        $this->SetFont('Arial','',9);
        $this->SetTextColor(90);
        $this->Cell(0,5, 'Codigo Documento: TGE-RPT-'.date('Ymd-His'),0,1,'C');

        $this->Ln(3);
    }

    function Footer() {
        $this->SetY(-18);
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(120);

        $this->Line(10,$this->GetY(),287,$this->GetY());
        $this->Ln(2);

        $this->Cell(0,5, mb_convert_encoding('Documento Oficial - Uso Interno Institucional','ISO-8859-1','UTF-8'),0,1,'L');
        $this->Cell(0,5,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
        $this->Cell(0,5,date('d/m/Y H:i'),0,0,'R');
    }
}


/* ===============================
   GENERAR PDF
=================================*/
$pdf = new PDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10,10,10);
$pdf->SetAutoPageBreak(true,20);


/* ===== CAJA INFORMATIVA ===== */
$pdf->SetFillColor(240,240,240);
$pdf->SetDrawColor(200,200,200);
$pdf->SetFont('Arial','',10);

$filtroTexto = $valor ? "Filtro aplicado: $valor" : "Sin filtros";

$pdf->Cell(140,8,"Fecha de emisión: ".date('d/m/Y'),1,0,'L',true);
$pdf->Cell(140,8,$filtroTexto,1,1,'L',true);

$pdf->Cell(140,8,'Total de registros: '.count($cuentas),1,0,'L',true);
$pdf->Cell(140,8,'Tipo de documento: Reporte Oficial',1,1,'L',true);

$pdf->Ln(6);


/* ===== CABECERA TABLA ===== */
$pdf->SetFillColor(30,70,140);
$pdf->SetTextColor(255);
$pdf->SetFont('Arial','B',9);

$w = [10, 70, 45, 50, 40, 30, 35];
$headers = ['N°','Titular','Banco','N° Cuenta','Tipo','Tarjeta','Caducidad'];

foreach($headers as $i=>$col){
    $pdf->Cell($w[$i],9,mb_convert_encoding($col,'ISO-8859-1','UTF-8'),1,0,'C',true);
}
$pdf->Ln();


/* ===== FILAS ===== */
$pdf->SetFont('Arial','',9);
$pdf->SetTextColor(40);
$pdf->SetFillColor(245,249,252);

$fill = false;
$contador = 1;

foreach($cuentas as $c){

    $pdf->Cell($w[0],8,$contador++,1,0,'C',$fill);
    $pdf->Cell($w[1],8,mb_convert_encoding($c['titular'],'ISO-8859-1','UTF-8'),1,0,'L',$fill);
    $pdf->Cell($w[2],8,$c['banco'],1,0,'L',$fill);
    $pdf->Cell($w[3],8,$c['numero_cuenta'],1,0,'C',$fill);
    $pdf->Cell($w[4],8,$c['tipo_cuenta'],1,0,'C',$fill);
    $pdf->Cell($w[5],8,$c['tarjeta_visa'] ?: 'No',1,0,'C',$fill);
    $pdf->Cell($w[6],8,
        $c['fecha_caducidad_tarjeta']
        ? date('d/m/Y', strtotime($c['fecha_caducidad_tarjeta']))
        : 'No definida',
        1,0,'C',$fill);

    $pdf->Ln();
    $fill = !$fill;
}


/* ===== FIRMA INSTITUCIONAL ===== */
$pdf->Ln(12);
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,'____________________________________',0,1,'R');
$pdf->Cell(0,6,mb_convert_encoding('Negociado de Misiones Diplomaticas','ISO-8859-1','UTF-8'),0,1,'R');
$pdf->Cell(0,6,'Tesoreria General y Patrimonio del  Estado',0,1,'R');

$pdf->Output('I','Reporte_Cuentas_Bancarias.pdf');
?>