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

		$this->MultiCell(10, 6, 'No', 0, 'C',false, 1, 12, 30, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(25, 6, 'Contrato', 0, 'C',false, 1, 21, 30, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(43, 6, 'Fecha Autorización', 0, 'C',false, 1, 45, 30, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(43, 6, 'Titular', 0, 'C',false, 1, 75, 30, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(50, 6, 'Nombre', 0, 'C',false, 1, 118, 30, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(250, 6,'Importe Prestamo', 0, 'C',false, 1, 80, 30, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(300, 6, 'Monto Redocumentación', 0, 'C',false, 1, 100, 30, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(420, 6, 'Sindicato', 0, 'C',false, 1, 80, 30, true, 0, true, true, 0, 'M', false);

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

$consultaTram="SELECT idContrato, fecha, titular, nombre, importePrestamo, MontoRedocumentacion,sindicato,idSindicato FROM repAltasRedocumenta ORDER BY idSindicato ASC";
$consulta = mysql_query($consultaTram);

    $pdf->AddPage('L', $resolution);
	$pdf->SetFont('helvetica', '', 10);
	$pdf->MultiCell(334, 20,'RESUMEN DE ALTAS Y REDOCUMENTACION DE PRESTAMOS', 0, 'C',false, 1, 12, 20, true, 0, true, true, 0, 'M', false);
	$pdf->MultiCell(334, 20,'CONSULTA DEL '.$fechadeInicio.' AL '.$fechadeFin, 0, 'C',false, 1, 12, 25, true, 0, true, true, 0, 'M', false);
			
	//$pdf->MultiCell(200, 5, 'FECHA INICIAL: '.$fechadeInicio, 0, 'C', false, 0, 0, 25, true, 0, true, true, 0, 'M', false);
	//$pdf->MultiCell(200, 5, 'FECHA FINAL: '.$fechadeFin, 0, 'C', false, 0, 155, 25, true, 0, true, true, 0, 'M', false);
	//$pdf->setPageOrientation('L');

	$Cont=1;
	$i=40;
	$pdf->SetAutoPageBreak(false, 0);
	while($resultado = mysql_fetch_array($consulta)){	
	//if ($resultado['idSindicato'])
		$pdf->MultiCell(10, 7, $Cont, 1, 'C', false, 1, 12, $i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(25, 7,$resultado['idContrato'], 0, 'C', false, 1, 19,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(70, 7,$resultado['fecha'], 0, 'C', false, 1, 30,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(90, 7,$resultado['titular'], 0, 'C', false, 1, 50,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(150, 7,$resultado['nombre'], 0, 'C', false, 1, 70,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(250, 7,'$'.number_format($resultado['importePrestamo'], 2,'.',','), 0, 'C', false, 1, 80,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(320, 7,'$'.number_format($resultado['MontoRedocumentacion'], 2,'.',','), 0, 'C', false, 1, 80,$i, true, 0, true, true, 0, 'M', false); 
		$pdf->MultiCell(420, 7,$resultado['sindicato'], 0, 'C', false, 1, 80,$i, true, 0, true, true, 0, 'M', false);
		$i=$i+7;
		if(($Cont%15)==0){
			$i=40;
			$pdf->AddPage('L', $resolution);
		}
	
		
		$Cont=$Cont+1;
	}
$pdf->MultiCell(150, 8.5,'_____________________________________', 0, 'C', false, 1, 70,$i, true, 0, true, true, 0, 'M', false);
$pdf->MultiCell(230, 8.5,'_____________________________', 0, 'C', false, 1, 80,$i, true, 0, true, true, 0, 'M', false);
$pdf->MultiCell(300, 8.5,'_____________________________', 0, 'C', false, 1, 80,$i, true, 0, true, true, 0, 'M', false); 

$consultaTtotal="SELECT SUM(importePrestamo) AS importePrestamo, SUM(MontoRedocumentacion) AS MontoRedocumentacion, COUNT(*) AS registros FROM repAltasRedocumenta ORDER BY idSindicato ASC";
$consultaTT = mysql_query($consultaTtotal);
$rowT=mysql_fetch_assoc($consultaTT);
$pdf->MultiCell(150, 8.5,'Total registros:'.$rowT['registros'], 0, 'C', false, 1, 70,$i+5, true, 0, true, true, 0, 'M', false);
$pdf->MultiCell(250, 8.5,'$'.number_format($rowT['importePrestamo'], 2,'.',','), 0, 'C', false, 1, 80,$i+5, true, 0, true, true, 0, 'M', false);
$pdf->MultiCell(320, 8.5,'$'.number_format($rowT['MontoRedocumentacion'], 2,'.',','), 0, 'C', false, 1, 80,$i+5, true, 0, true, true, 0, 'M', false); 

$pdf->AddPage('L', $resolution);





//$pdf->MultiCell(230, 8.5,'$'.number_format($rowT['importePrestamo'], 2,'.',','), 0, 'C', false, 1, 80,10, true, 0, true, true, 0, 'M', false);


$pdf->MultiCell(334, 20,'CONSULTA DEL:'.$fecha_actual, 0, 'C',false, 1, 12, 20, true, 0, true, true, 0, 'M', false);

$pdf->MultiCell(25, 20, 'Cve Sindicato ', 0, 'C', false, 0, 25,45, true, 0, true, true, 0, 'M', false);
$pdf->MultiCell(40, 50, 'Nombre Sindicato', 0, 'R', false, 0, 60,45, true, 0, true, true, 0, 'M', false);
$pdf->MultiCell(45, 20, 'Total Importe Prestamos', 0, 'R', false, 0, 150,45, true, 0, true, true, 0, 'M', false);
$pdf->MultiCell(45, 20, 'Total redocumentacion', 0, 'R', false, 0,250,45, true, 0, true, true, 0, 'M', false);

$consultaResumen="SELECT idSindicato, nombreSind, TotalImportePrestamo  ,TotalRedocumentacion FROM subRep_Altas_y_Redocumentados";
$consultaR = mysql_query($consultaResumen);

	$Cont=1;
	$i=56;
	$pdf->SetAutoPageBreak(false, 0);
	while($resultadoT = mysql_fetch_array($consultaR)){	
	
		$pdf->MultiCell(10, 8.5, $Cont, 1, 'C', false, 1, 12, $i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(25, 8.5,$resultadoT['idSindicato'], 0, 'C', false, 1, 19,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(60, 8.5,$resultadoT['nombreSind'], 0, 'R', false, 1, 30,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(140, 8.5,'$'.number_format($resultadoT['TotalImportePrestamo'], 2,'.',','), 0, 'R', false, 1, 50,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(220, 8.5,'$'.number_format($resultadoT['TotalRedocumentacion'], 2,'.',','), 0, 'R', false, 1, 70,$i, true, 0, true, true, 0, 'M', false);
		$i=$i+8.5;
		$Cont=$Cont+1;
	}
	$pdf->MultiCell(160, 8.5,'_____________________________', 0, 'R', false, 1, 30,$i, true, 0, true, true, 0, 'M', false);
	$pdf->MultiCell(260, 8.5,'_____________________________', 0, 'R', false, 1, 30,$i, true, 0, true, true, 0, 'M', false);
	
$consultaRtotal="SELECT idSindicato, nombreSind, sum(TotalImportePrestamo) as TotalImportePrestamo  , sum(TotalRedocumentacion) as TotalRedocumentacion FROM subRep_Altas_y_Redocumentados";
$consultaRt = mysql_query($consultaRtotal);
$row=mysql_fetch_assoc($consultaRt);
		
	$pdf->MultiCell(160, 8.5,'$'.number_format($row['TotalImportePrestamo'], 2,'.',','), 0, 'R', false, 1, 30,$i+5, true, 0, true, true, 0, 'M', false);
	$pdf->MultiCell(260, 8.5,'$'.number_format($row['TotalRedocumentacion'], 2,'.',','), 0, 'R', false, 1, 30,$i+5, true, 0, true, true, 0, 'M', false);

$pdf->Output('ResumenAltasRedocumentacion.pdf', 'I'); 

	/*
							<com:TBoundColumn HeaderText="Contrato" DataField="idContrato" />
					<com:TBoundColumn HeaderText="Fecha Autorización" DataField="fecha" />
					<com:TBoundColumn HeaderText="Titular" DataField="titular" />
					<com:TBoundColumn HeaderText="Nombre" DataField="nombre" />
					<com:TBoundColumn HeaderText="Importe Prestamos" DataField="importePrestamo" />
					<com:TBoundColumn HeaderText="Monto Redocumentación" DataField="MontoRedocumentacion" />
		*/

?>