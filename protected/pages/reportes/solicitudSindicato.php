<?php
include_once('../compartidos/clases/conexion.php');

class solicitudSindicato extends TPage 
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);
		$idSolicitud = $_REQUEST['id'];
		//$idSolicitud = 41166; //$_REQUEST['id'];
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();

		$consulta= "SELECT s.id_solicitud as solicitud,s.titular as titularr ,s.antiguedad as antiguedad,DATE_FORMAT(t.fec_ingre,'%Y/%m/%d') AS creada,t.numero AS num_tit, t.nombre AS titular, st.cve_sindicato AS tit_cve_sind, st.sindicato AS tit_sind, TIMESTAMPDIFF(YEAR, t.fec_ingre, CURDATE()) AS tit_ant,
					aval1, a1.nombre AS aval1_n, sa1.cve_sindicato AS aval1_cve_sind, sa1.sindicato AS aval1_sind, TIMESTAMPDIFF(YEAR, a1.fec_ingre, CURDATE()) AS aval1_ant,
					aval2, a2.nombre AS aval2_n, sa2.cve_sindicato AS aval2_cve_sind, sa2.sindicato AS aval2_sind, TIMESTAMPDIFF(YEAR, a2.fec_ingre, CURDATE()) AS aval2_ant,
					(SELECT representante FROM CATSINDICATOS WHERE cve_sindicato = tit_cve_sind) as SindicatoRpre
					,DATE_FORMAT(s.firma,'%d/%m/%Y') AS firma, importe, plazo, tasa, saldo_anterior, descuento,AntLetraTi
		-- FROM contrato c
		FROM Solicitud s -- ON s.id_solicitud = c.id_solicitud
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
		$VarIimporteLetra = $this->numtoletras($VarImporte);

		$this->lblNumSolicitante->Text = $VarSolicitante;
		$this->lblSolicitante->Text = $VarSolicitud . " - " . $Solicitud;
		$this->lblFechadeIngreso->Text = $VarFechadeIngreso;
		$this->lblAntiguedad->Text = $AntLetra;
		$this->lblSindicato->Text = $VarSindicato;
		$this->lblAval1->Text = $Aval1. " - " .$VarAval1;
		$this->lblAval2->Text = $Aval2. " - " .$VarAval2;
		$this->lblfirmAvales->Text = $nuevafecha; 
		$this->lblmeses->Text = $meses; 
		$this->lblimporte->Text = $VarImporte; 
		$this->lbldirector->Text = $VarDirector;
		$this->lblsindicato->Text = $VarRepreSindicato;
	}

	public function numtoletras($xcifra) // convertir numero a letras
	{
		$xarray = array(0 => "Cero",
			1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
			"DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
			"VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
			100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
		);
	//
		$xcifra = trim($xcifra);
		$xlength = strlen($xcifra);
		$xpos_punto = strpos($xcifra, ".");
		$xaux_int = $xcifra;
		$xdecimales = "00";
		if (!($xpos_punto === false)) {
			if ($xpos_punto == 0) {
				$xcifra = "0" . $xcifra;
				$xpos_punto = strpos($xcifra, ".");
			}
			$xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
			$xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
		}
	 
		$XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
		$xcadena = "";
		for ($xz = 0; $xz < 3; $xz++) {
			$xaux = substr($XAUX, $xz * 6, 6);
			$xi = 0;
			$xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
			$xexit = true; // bandera para controlar el ciclo del While
			while ($xexit) {
				if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
					break; // termina el ciclo
				}
	 
				$x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
				$xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
				for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
					switch ($xy) {
						case 1: // checa las centenas
							if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
								 
							} else {
								$key = (int) substr($xaux, 0, 3);
								if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
									$xseek = $xarray[$key];
									$xsub = subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
									if (substr($xaux, 0, 3) == 100)
										$xcadena = " " . $xcadena . " CIEN " . $xsub;
									else
										$xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
									$xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
								}
								else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
									$key = (int) substr($xaux, 0, 1) * 100;
									$xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
									$xcadena = " " . $xcadena . " " . $xseek;
								} // ENDIF ($xseek)
							} // ENDIF (substr($xaux, 0, 3) < 100)
							break;
						case 2: // checa las decenas (con la misma lógica que las centenas)
							if (substr($xaux, 1, 2) < 10) {
								 
							} else {
								$key = (int) substr($xaux, 1, 2);
								if (TRUE === array_key_exists($key, $xarray)) {
									$xseek = $xarray[$key];
									$xsub = subfijo($xaux);
									if (substr($xaux, 1, 2) == 20)
										$xcadena = " " . $xcadena . " VEINTE " . $xsub;
									else
										$xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
									$xy = 3;
								}
								else {
									$key = (int) substr($xaux, 1, 1) * 10;
									$xseek = $xarray[$key];
									if (20 == substr($xaux, 1, 1) * 10)
										$xcadena = " " . $xcadena . " " . $xseek;
									else
										$xcadena = " " . $xcadena . " " . $xseek . " Y ";
								} // ENDIF ($xseek)
							} // ENDIF (substr($xaux, 1, 2) < 10)
							break;
						case 3: // checa las unidades
							if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada
								 
							} else {
								$key = (int) substr($xaux, 2, 1);
								$xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
								$xsub = subfijo($xaux);
								$xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
							} // ENDIF (substr($xaux, 2, 1) < 1)
							break;
					} // END SWITCH
				} // END FOR
				$xi = $xi + 3;
			} // ENDDO
	 
			if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
				$xcadena.= " DE";
	 
			if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
				$xcadena.= " DE";
	 
			// ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
			if (trim($xaux) != "") {
				switch ($xz) {
					case 0:
						if (trim(substr($XAUX, $xz * 6, 6)) == "1")
							$xcadena.= "UN BILLON ";
						else
							$xcadena.= " BILLONES ";
						break;
					case 1:
						if (trim(substr($XAUX, $xz * 6, 6)) == "1")
							$xcadena.= "UN MILLON ";
						else
							$xcadena.= " MILLONES ";
						break;
					case 2:
						if ($xcifra < 1) {
							$xcadena = "CERO PESOS $xdecimales/100 M.N.";
						}
						if ($xcifra >= 1 && $xcifra < 2) {
							$xcadena = "UN PESO $xdecimales/100 M.N. ";
						}
						if ($xcifra >= 2) {
							$xcadena.= " PESOS $xdecimales/100 M.N. "; //
						}
						break;
				} // endswitch ($xz)
			} // ENDIF (trim($xaux) != "")
			// ------------------      en este caso, para México se usa esta leyenda     ----------------
			$xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
			$xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
			$xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
			$xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
			$xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
			$xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
			$xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
		} // ENDFOR ($xz)
		return trim($xcadena);
	}

	function subfijo($xx)
	{ // esta función regresa un subfijo para la cifra
		$xx = trim($xx);
		$xstrlen = strlen($xx);
		if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
			$xsub = "";
		//
		if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
			$xsub = "MIL";
		//
		return $xsub;
	}
// Extend the TCPDF class to create custom Header and Footer  
}