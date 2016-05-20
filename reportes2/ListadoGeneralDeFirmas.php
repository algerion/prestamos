<?php
include("conexion/conexion.php");
require_once('tcpdf/tcpdf.php');
// get data from users table 

//$fechaIni=$_GET['id'];
//$fechaFin=$_GET['id2'];

$fechaIni='20160501';
$fechaFin='20160511';
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

	
		$this->MultiCell(178, 1, iconv("ISO-8859-1","UTF-8", 'DIRECCION DE PENSIONES'), 0, 'C',false, 1, 12,5, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(178, 20,iconv("ISO-8859-1","UTF-8", 'DEL MUNICIPIO DE OAXACA DE JUAREZ OAX.'), 0, 'C',false, 1, 12, 12, true, 0, true, true, 0, 'M', false);

		$this->MultiCell(10, 6, 'No', 0, 'C',false, 1, 12, 30, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(25, 6, 'Num Unico', 0, 'C',false, 1, 21, 30, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(45, 6, 'Nombre', 0, 'C',false, 1, 45, 30, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(68, 6, 'Titular', 0, 'C',false, 1, 75, 30, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(130, 6, 'Aval 1', 0, 'C',false, 1, 75, 30, true, 0, true, true, 0, 'M', false);
		$this->MultiCell(200, 6,'Aval 2', 0, 'C',false, 1, 75, 30, true, 0, true, true, 0, 'M', false);
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

$consultaTram="SELECT cveEmpleado, nombre, Titular1, Titular2, Titular3  FROM replistadofirmas ORDER BY cveEmpleado";
$consulta = mysql_query($consultaTram);

    $pdf->AddPage();
	$pdf->SetFont('helvetica', '', 9);
	$pdf->MultiCell(178, 20,'LISTADO GENERAL DE FIRMAS', 0, 'C',false, 1, 12, 20, true, 0, true, true, 0, 'M', false);
	$pdf->MultiCell(178, 20,'FECHA', 0, 'C',false, 1, 12, 25, true, 0, true, true, 0, 'M', false);

	$Cont=1;
	$i=40;
	$pdf->SetAutoPageBreak(false, 0);
	while($resultado = mysql_fetch_array($consulta)){	
	//if ($resultado['idSindicato'])
		$pdf->MultiCell(10, 8.5, $Cont, 1, 'C', false, 1, 12, $i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(25, 8.5,$resultado['cveEmpleado'], 0, 'C', false, 1, 20,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(50, 8.5,$resultado['nombre'], 0, 'C', false, 1, 40,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(120, 8.5,$resultado['Titular1'], 0, 'C', false, 1, 50,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(180, 8.5,$resultado['Titular2'], 0, 'C', false, 1, 50,$i, true, 0, true, true, 0, 'M', false);
		$pdf->MultiCell(250, 8.5,$resultado['Titular3'], 0, 'C', false, 1, 50,$i, true, 0, true, true, 0, 'M', false);
		$i=$i+8.5;
		if(($Cont%28)==0){
			$i=40;
			$pdf->AddPage();
		}
	
		
		$Cont=$Cont+1;
	}

$pdf->Output('ResumenAltasRedocumentacion.pdf', 'I'); 

?>