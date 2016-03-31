<?php
include_once('../compartidos/clases/conexion.php');

class solicitudes extends TPage 
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);
		$idSolicitud = $_REQUEST['id'];
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();

		$consulta= "SELECT s.id_solicitud as solicitud,s.titular as titularr ,s.antiguedad as antiguedad,DATE_FORMAT(t.fec_ingre,'%Y/%m/%d') AS creada,t.numero AS num_tit, t.nombre AS titular, st.cve_sindicato AS tit_cve_sind, st.sindicato AS tit_sind, TIMESTAMPDIFF(YEAR, t.fec_ingre, CURDATE()) AS tit_ant,
					aval1, a1.nombre AS aval1_n, sa1.cve_sindicato AS aval1_cve_sind, sa1.sindicato AS aval1_sind, TIMESTAMPDIFF(YEAR, a1.fec_ingre, CURDATE()) AS aval1_ant,
					aval2, a2.nombre AS aval2_n, sa2.cve_sindicato AS aval2_cve_sind, sa2.sindicato AS aval2_sind, TIMESTAMPDIFF(YEAR, a2.fec_ingre, CURDATE()) AS aval2_ant,
					(SELECT representante FROM CATSINDICATOS WHERE cve_sindicato = tit_cve_sind) as SindicatoRpre
					,DATE_FORMAT(s.firma,'%d/%m/%Y') AS firma, importe, plazo, tasa, saldo_anterior, descuento
					,(SELECT CASE tipo_nomi
              WHEN tipo_nomi = 'S' THEN 'SEMANAL' 
              WHEN tipo_nomi = 'Q' THEN 'QUINCENAL'
              ELSE 'NO HAY TIPO DE NOMINA' END AS mesto_utovara
			FROM empleados WHERE numero = s.titular) as TipoNominaTit
					
		FROM Solicitud s 
		LEFT JOIN sujetos AS t ON t.numero = s.titular
		LEFT JOIN catsindicatos st ON st.cve_sindicato = s.cve_sindicato
		LEFT JOIN sujetos AS a1 ON a1.numero= s.aval1
		LEFT JOIN catsindicatos sa1 ON sa1.cve_sindicato = s.cve_sind_Aval1
		LEFT JOIN sujetos AS a2 ON a2.numero = s.aval2
		LEFT JOIN catsindicatos sa2 ON sa2.cve_sindicato = s.cve_sind_Aval2
		WHERE s.id_solicitud = (SELECT MAX(id_solicitud) AS id_solicitud FROM Solicitud WHERE  titular = :idSolicitud)";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":idSolicitud", $idSolicitud);
		$result = $comando->query()->readAll();

		foreach($result as $rows)
		{
			$VarSolicitante=$rows['solicitud'];
			$VarClaveSindicato=$rows['tit_cve_sind'];
			$VarRepreSindicato=$rows['SindicatoRpre'];
			$Solicitud=$rows['titular'];
			$VarSolicitud=$rows['titularr'];
			$VarFechadeIngreso=$rows['creada'];
			$VarAntiguedad=$rows['antiguedad'];
			$VarSindicato=$rows['tit_sind'];
			$VarFirmAvales=$rows['firma'];
			$Aval1=$rows['aval1'];
			$Aval2=$rows['aval2'];
			$VarAval1=$rows['aval1_n'];
			$VarAval2=$rows['aval2_n'];
			$VarPlazo=$rows['plazo'];
			$Importe=$rows['importe'];
			$creada=$rows['creada'];
			$TipoNomina=$rows['TipoNominaTit'];
		}
		$VarImporte = number_format($Importe,2);

		$mes = date("n");
		$meses = 11 - $mes;

		$consultaDirec="SELECT Nombre_completo FROM cat_director WHERE anio = YEAR(NOW())";
		$comando = $this->dbConexion->createCommand($consultaDirec);
		$result = $comando->query()->readAll();
		foreach($result as $rows)
		{
			$VarDirector=$rows['Nombre_completo'];
		}


		$fecha = date('Y/m/j');
		$i = strtotime($fecha);
		$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$i),date("d",$i), date("Y",$i)) , 0 ); 
		if ($dia >= 4 ){ 
			$nuevafecha = strtotime ( '+4 day' , strtotime ( $fecha ) ) ;
			$nuevafecha = date ( 'j/m/Y' , $nuevafecha );
		}else {
			$nuevafecha = strtotime ( '+1 day' , strtotime ( $fecha ) ) ;
			$nuevafecha = date ( 'j/m/Y' , $nuevafecha );
		}

		$datetime1 = date_create($creada);
		$datetime2 = date_create($fecha);
		$AntLetras = date_diff($datetime1, $datetime2);
		$AntLetra = $AntLetras->format('%Y Años %m Meses %d Dias');

		$hoy = date("Y-m-d H:i:s");  

		$this->lblNumSolicitante->Text = $VarSolicitante;
		$this->lblSolicitante->Text = $VarSolicitud . " - " . $Solicitud;
		$this->lblFechadeIngreso->Text = $VarFechadeIngreso;
		$this->lblAntiguedad->Text = $AntLetra;
		$this->lblTipoNomina->Text = $TipoNomina;
		$this->lblSindicato->Text = $VarSindicato;
		$this->lblAval1->Text = $Aval1. " - " .$VarAval1;
		$this->lblAval2->Text = $Aval2. " - " .$VarAval2;
		$this->lblfirmAvales->Text = $nuevafecha; 
		$this->lblmeses->Text = $meses; 
		$this->lblimporte->Text = $VarImporte; 
		$this->lbldirector->Text = $VarDirector;
	}
}