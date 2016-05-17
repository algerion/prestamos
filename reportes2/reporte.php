<?php
include("conexion/conexion.php");
require_once('tcpdf/tcpdf.php');
// get data from users table 
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
        // set bacground image
 
		//posición notarios
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
$pdf->SetAuthor('Municipio de Oaxaca de Juárez');
$pdf->SetTitle('');

//$pdf->SetHeaderData();
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(0, PDF_MARGIN_TOP, 0);   
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
//set image scale factor
//$pdf->setImageScale(1);
//set some language-dependent strings

//$pdf->setLanguageArray($l);

// ---------------------------------------------------------
// set font

// ---------------------------------------------------------
// add a page

$resolution= array(216, 355);

// ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
$consultaTram="SELECT idContrato,fecha,titular,nombre,saldoInicial,cargo,abono,saldoFinal,abonosHechos FROM repEdoCtaxperio2Desglosado";
$consulta = mysql_query($consultaTram);

    $pdf->AddPage('L', $resolution);
	$pdf->SetFont('helvetica', '', 10);
	$pdf->MultiCell(334, 5,iconv("ISO-8859-1","UTF-8", 'REPORTE'), 0, 'C', false, 0, 0, 15, true, 0, true, true, 0, 'M', false);
	//$pdf->setPageOrientation('L');
	$Cont=1;
	$i=56;
	$pdf->SetAutoPageBreak(false, 0);
	while($resultado = mysql_fetch_array($consulta)){	
		$pdf->MultiCell(7, 8.5, $Cont, 1, 'C', false, 1, 12, $i, true, 0, true, true, 0, 'M', false);
		//$pdf->MultiCell(57, 8.5, iconv('iso-8859-1','utf-8',''.$resultado['fctram_nombre'].' '.$resultado['fctram_ApePat'].' '.$resultado['fctram_ApeMat'].' '.$resultado['fctram_razsoc']), 1, 'C', false, 1, 19, $i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(25, 8.5,$resultado['idContrato'], 0, 'C', false, 1, 19,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(70, 8.5,$resultado['fecha'], 0, 'C', false, 1, 30,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(90, 8.5,$resultado['titular'], 0, 'C', false, 1, 50,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(150, 8.5,$resultado['nombre'], 0, 'C', false, 1, 70,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(230, 8.5,$resultado['saldoInicial'], 0, 'C', false, 1, 80,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(300, 8.5,$resultado['cargo'], 0, 'C', false, 1, 80,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(360, 8.5,$resultado['abono'], 0, 'C', false, 1, 80,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(420, 8.5,$resultado['saldoFinal'], 0, 'C', false, 1, 80,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(480, 8.5,$resultado['abonosHechos'], 0, 'C', false, 1, 80,$i, true, 0, true, true, 0, 'M', false);
		$i=$i+8.5;
		if(($Cont%15)==0){
			$i=56;
			$pdf->AddPage('L', $resolution);
		}
		$Cont=$Cont+1;
	}
// ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||

// $pdf->writeHTML($inlinecss, true, 0, true, 0); 

// reset pointer to the last page 
// $pdf->lastPage(); 

//Close and output PDF document 
$pdf->Output('example_006.pdf', 'I'); 

//============================================================+ 
// END OF FILE                                                  
//============================================================+ 
?>