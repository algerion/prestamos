<?php
include_once('../compartidos/clases/conexion.php');
include_once('../compartidos/clases/numaletras.php');

class pagare extends TPage 
{
	var $dbConexion;
	var $NombreTitular;
	var $ImporteLetras;
	var $importeNeto,$FirmaAvales,$Contrato,$Titular,$Avall1,$Avall2;
	
	
	

	public function onLoad($param)
	{
		parent::onLoad($param);
		$nal = new NumALetras();
		/*$lienzo = imagecreatetruecolor(200, 200);
		$negro = imagecolorallocate($lienzo, 132, 135, 28);
		$dibujo = imagerectangle($lienzo, 45, 60, 120, 100, $negro);*/
		
		$id_Contrato = $_REQUEST['id'];
		
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
		$consulta="SELECT c.id_contrato as contrato ,s.id_solicitud AS solicitud,s.titular AS titularr ,s.antiguedad AS antiguedad,s.creada AS creada,t.numero AS num_tit, t.nombre AS titular, st.cve_sindicato AS tit_cve_sind, st.sindicato AS tit_sind, TIMESTAMPDIFF(YEAR, t.fec_ingre, CURDATE()) AS tit_ant,
			s.aval1, a1.nombre AS aval1_n, sa1.cve_sindicato AS aval1_cve_sind, sa1.sindicato AS aval1_sind, TIMESTAMPDIFF(YEAR, a1.fec_ingre, CURDATE()) AS aval1_ant,
			s.aval2, a2.nombre AS aval2_n, sa2.cve_sindicato AS aval2_cve_sind, sa2.sindicato AS aval2_sind, TIMESTAMPDIFF(YEAR, a2.fec_ingre, CURDATE()) AS aval2_ant,
			DATE_FORMAT(s.firma,'%d/%m/%Y') AS firma, importe, plazo, tasa, saldo_anterior, descuento
			,(SELECT fec_ingre FROM empleados WHERE numero = s.aval1) AS AntiAval1
			,(SELECT fec_ingre FROM empleados WHERE numero = s.aval2) AS AntiAval2
			FROM contrato c
			LEFT JOIN  Solicitud s  ON s.id_solicitud = c.id_solicitud
			LEFT JOIN sujetos AS t ON t.numero = s.titular
			LEFT JOIN catsindicatos st ON st.cve_sindicato = s.cve_sindicato
			LEFT JOIN sujetos AS a1 ON a1.numero= s.aval1
			LEFT JOIN catsindicatos sa1 ON sa1.cve_sindicato = s.cve_sind_Aval1
			LEFT JOIN sujetos AS a2 ON a2.numero = s.aval2
			LEFT JOIN catsindicatos sa2 ON sa2.cve_sindicato = s.cve_sind_Aval2
			WHERE c.id_contrato = (SELECT MAX(id_contrato) AS contrato FROM contrato WHERE id_solicitud = :id_Contrato )";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_Contrato", $id_Contrato);
		$result = $comando->query()->readAll();

		foreach($result as $rows)
		{
			$VarContrato=$rows['contrato'];
			$VarTitular=$rows['num_tit'];
			$VarNombreTitular=$rows['titular']; //$Solicitud=$rows['titular'];
			$VarAntiguedadTit=$rows['antiguedad'];
			$SindicatoTit=$rows['tit_sind'];
			$Importe=$rows['importe'];	
			$Varaval1=$rows['aval1'];
			$Varaval2=$rows['aval2'];
			$VarnombreAval1=$rows['aval1_n'];
			$VarnombreAval2=$rows['aval2_n'];
			$AntiAval1=$rows['AntiAval1'];
			$AntiAval2=$rows['AntiAval2'];
			$VarsindicatoAval1=$rows['aval1_sind'];
			$VarsindicatoAval2=$rows['aval2_sind'];
			$VarFirmAvales=$rows['firma'];	
			$inter=$rows["importe"] * $rows["plazo"] * $rows["tasa"] / 100;
			$Varsubtotals = $Importe - $inter;
			$Varsaldoanterior = 0;
			$VarImportecheque = 0;
			$quincena = $rows["plazo"] * 2;
			$VardescQuincenas =$Importe / $quincena; 
		}
		$consultaDirec="SELECT Nombre_completo FROM cat_director WHERE anio = YEAR(NOW())";
		$comando = $this->dbConexion->createCommand($consultaDirec);
		$result = $comando->query()->readAll();
		foreach($result as $rows)
		{
			$VarDirector=$rows['Nombre_completo'];
		}
			$VarImporte = number_format($Importe,2); 
			$intereses = number_format($inter,2);
			$VardescQuincena = number_format($VardescQuincenas,2);
			$Varsubtotal = number_format($Varsubtotals,2);
			
			$this->Titular	= $VarTitular. " - " . $VarNombreTitular; 		
			$this->Contrato= $VarContrato;
			$this->importeNeto= $VarImporte; //
			$this->ImporteLetras = $nal->ValorEnLetras($Importe, "pesos", " M.N."); //
			$this->Avall1 = $Varaval1. " - " . $VarnombreAval1; //
			$this->Avall2 = $Varaval2. " - " . $VarnombreAval2; //
			$this->FirmaAvales = $VarFirmAvales;
			
			//$this->lienzo = imagecreatetruecolor(200, 200);
			
			
	}
				
}