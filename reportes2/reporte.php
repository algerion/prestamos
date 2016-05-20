<?php
include("conexion/conexion.php");
require_once('tcpdf/tcpdf.php');
// get data from users table 

$fechaIni=$_GET['id'];
$fechaFin=$_GET['id2'];

//$fechaIni='20160501';
//$fechaFin='20160511';
$fecha_actual = date("Y-m-d H:i:s"); 
//echo $fecha_actual;
$fechadeInicio = date("Y-m-d",strtotime($fechaIni));
$fechadeFin =  date("Y-m-d",strtotime($fechaFin));

    //Page header
class MYPDF extends TCPDF {
    //Page header
 public function Header() {
	 	
		global $fechaIni;
		global $fechaFin;
		global $nombre_tramite;
		global $trasladoTotal;
		global $trasladoParcial;
		global $fusionPredios;
		global $subdivisionPredios;
		
        // get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);

	
		$this->MultiCell(334, 1, iconv("ISO-8859-1","UTF-8", 'DIRECCION DE PENSIONES'), 0, 'C',false, 1, 12,5, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(334, 20,iconv("ISO-8859-1","UTF-8", 'DEL MUNICIPIO DE OAXACA DE JUAREZ OAX.'), 0, 'C',false, 1, 12, 12, true, 0, true, true, 0, 'M', false);

		$this->MultiCell(8, 7, 'No', 0, 'C',false, 1, 12, 45, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(20, 7, 'Contrato', 0, 'C',false, 1, 21, 45, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(30, 7, 'Autorizacion', 0, 'C',false, 1, 45, 45, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(30, 7, 'Numero', 0, 'C',false, 1, 81, 45, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(40, 7, 'Nombre', 0, 'C',false, 1, 122, 45, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(40, 7, 'Saldo Inicial', 0, 'C',false, 1, 178, 45, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(40, 7, 'Debe', 0, 'C',false, 1, 210, 45, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(40, 7, 'Haber', 0, 'C',false, 1, 240, 45, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(40, 7, 'Saldo Final', 0, 'C',false, 1, 272, 45, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(40, 7, 'Abonos', 0, 'C',false, 1, 300, 45, true, 0, true, true, 0, 'M', false);
		
		//$this->Image($img_file, 25, 220, 155, 91, '', '', '', false,300, '', false, false, 0);
        // restore auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();
    }
	
    public function Footer() {
        //$image_file = "img/pie.jpg";
        //$this->Image($image_file, 7, 250, 198, 23, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
    }
}

// create new PDF document
$pdf = new MYPDF();



//set document information
$pdf->SetAuthor('DIRECCION DE PENSIONES');
$pdf->SetTitle('DEL MUNICIPIO DE OAXACA DE JUÁREZ OAX');

$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(0, PDF_MARGIN_TOP, 0);   
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$resolution= array(216, 355);

$consultaTram="SELECT idContrato,fecha,titular,nombre,saldoInicial,cargo,abono,saldoFinal,abonosHechos FROM repEdoCtaxperio2Desglosado";
$consulta = mysql_query($consultaTram);

    $pdf->AddPage('L', $resolution);
	$pdf->SetFont('helvetica', '', 10);
	$pdf->MultiCell(334, 20,'FECHA DE IMPRESION:'.$fecha_actual, 0, 'C',false, 1, 12, 20, true, 0, true, true, 0, 'M', false);
			
	$pdf->MultiCell(200, 5, 'FECHA INICIAL: '.$fechadeInicio, 0, 'C', false, 0, 0, 25, true, 0, true, true, 0, 'M', false);
	$pdf->MultiCell(200, 5, 'FECHA FINAL: '.$fechadeFin, 0, 'C', false, 0, 155, 25, true, 0, true, true, 0, 'M', false);
	//$pdf->setPageOrientation('L');

	$Cont=1;
	$i=56;
	$pdf->SetAutoPageBreak(false, 0);
	while($resultado = mysql_fetch_array($consulta)){	
		$pdf->MultiCell(10, 8.5, $Cont, 1, 'C', false, 1, 12, $i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(25, 8.5,$resultado['idContrato'], 0, 'C', false, 1, 19,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(70, 8.5,$resultado['fecha'], 0, 'C', false, 1, 30,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(90, 8.5,$resultado['titular'], 0, 'C', false, 1, 50,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(150, 8.5,$resultado['nombre'], 0, 'C', false, 1, 70,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(230, 8.5,'$'.number_format($resultado['saldoInicial'], 2,'.',','), 0, 'C', false, 1, 80,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(300, 8.5,'$'.number_format($resultado['cargo'], 2,'.',','), 0, 'C', false, 1, 80,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(360, 8.5,'$'.number_format($resultado['abono'], 2,'.',','), 0, 'C', false, 1, 80,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(420, 8.5,'$'.number_format($resultado['saldoFinal'], 2,'.',','), 0, 'C', false, 1, 80,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(480, 8.5,$resultado['abonosHechos'], 0, 'C', false, 1, 80,$i, true, 0, true, true, 0, 'M', false);
		$i=$i+8.5;
		if(($Cont%15)==0){
			$i=56;
			$pdf->AddPage('L', $resolution);
		}
		$Cont=$Cont+1;
	}
	$consultaTramTotal="SELECT SUM(saldoInicial) AS saldoInicial,SUM(cargo) AS cargo,SUM(abono) AS abono,SUM(saldoFinal) AS saldoFinal,SUM(abonosHechos) AS abonosHechos , COUNT(*) AS registros FROM repEdoCtaxperio2Desglosado";
	$consultaTotal = mysql_query($consultaTramTotal);
	while($resultadoT = mysql_fetch_array($consultaTotal)){	
		$pdf->MultiCell(150, 5,'Total de Registro: '.$resultadoT['registros'],  0, 'C', false, 0, 80, 38, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(230, 5,'$'.number_format($resultadoT['saldoInicial'], 2,'.',','),  0, 'C', false, 0, 80, 38, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(300, 5,'$'.number_format($resultadoT['cargo'], 2,'.',','),  0, 'C', false, 0, 80, 38, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(360, 5,'$'.number_format($resultadoT['abono'], 2,'.',','),  0, 'C', false, 0, 80, 38, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(420, 5,'$'.number_format($resultadoT['saldoFinal'], 2,'.',','),  0, 'C', false, 0, 80, 38, true, 0, true, true, 0, 'M', false);
	}	


$pdf->Output('EstadoDeCuentaDesglose.pdf', 'I'); 


?>