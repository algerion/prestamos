<?php
Prado::using('System.Web.UI.ActiveControls.*');
include_once('../compartidos/clases/conexion.php');
include_once('../compartidos/clases/usadbf.php');
include_once('../compartidos/clases/charset.php');

class descuentos extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
		if(!$this->IsPostBack)
		{
			$estatus = Conexion::Retorna_Registro($this->dbConexion, "estatus", array(), " where id_estatus > 0");
			$this->ddlEstatus->DataSource = $estatus;
			$this->ddlEstatus->dataBind();
		}
	}
	public function btnGenerar_Click($sender, $param)
	{	
			$this->Generar_desno($this->ddlTipo->SelectedValue == 'PE' ? "J" : "A");
	}
	public function Generar_desno($TipoEmpleado)
	{	
		$liberar =("TRUNCATE respMovimientos");
		$comando = $this->dbConexion->createCommand($liberar);
		$comando->execute();
		$consulta="INSERT INTO respMovimientos (id_contrato,  cargo,   abono,  activo , adeudo,movimientos)  
					SELECT id_contrato, SUM(cargo), SUM(abono), 1, SUM(Cargo - Abono) AS adeudo ,COUNT(id_contrato) AS movimientos FROM movimientos WHERE  activo = 1 
					GROUP BY id_contrato HAVING SUM(Cargo - Abono) > 1";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->execute();
		$registros = ("SELECT COUNT(*) AS registro FROM  contrato A
				INNER JOIN  solicitud B 	ON B.id_Solicitud = A.id_Solicitud
				INNER JOIN respMovimientos C	ON C.id_contrato  = A.id_contrato
				INNER JOIN sujetos 	T	ON T.numero = B.titular
				INNER JOIN sujetos 	AV1	ON AV1.numero = B.aval1
				INNER JOIN sujetos 	AV2	ON AV2.numero = B.aval2
				WHERE A.estatus = 'A' AND T.tipo = :TipoEmpleado AND A.congelado = 0 ");
		$comando = $this->dbConexion->createCommand($registros); 
		$comando->bindValue(":TipoEmpleado",$TipoEmpleado);
		$result =$comando->query()->readAll();
		$Total_registros = $result[0]["registro"];
		if(count($Total_registros) > 0)
		{
			$parametros = array("origen"=>"P", "creado"=>date("Y-m-d H:i:s"), "modificado"=>date("Y-m-d H:i:s"), "creador"=>0, "modificador"=>0, "id_estatus"=>1,
						"observaciones"=>"desno generado exitosamente", "tipo"=>($this->ddlTipo->SelectedValue == 'PE' ? "J" : "A"), 
						"pago"=>$this->ddlTipoNomina->SelectedValue, "periodo"=>(is_numeric($this->txtPeriodo->Text) ? $this->txtPeriodo->Text : 0));
				Conexion::Inserta_Registro($this->dbConexion, "descuento", $parametros);
				$id = Conexion::Ultimo_Id_Generado($this->dbConexion);

			$datos =("SELECT B.titular as NUMERO,  63 AS CLAVECON, B.descuento as IMPORTE, B.plazo as PERIODO,C.movimientos as PERIODOS 
					,A.id_contrato as CONTRATO,'0' AS APLICADO, '' AS TIPO,'' AS NOMINA 
					, B.aval1 as AVAL1, B.aval2 as AVAL2,'' AS NOTA, '' AS APAVAL
					,(SELECT tipo_nomi FROM EMPLEADOS WHERE numero = B.Titular) AS tipo_nomina
					, T.status AS EMPLEACT,  AV1.status AS AVAL1ACT, AV2.status AS AVAL2ACT
					FROM  contrato A
					INNER JOIN  solicitud B 	ON B.id_Solicitud = A.id_Solicitud
					INNER JOIN respMovimientos C	ON C.id_contrato  = A.id_contrato
					INNER JOIN sujetos 	T	ON T.numero = B.titular
					INNER JOIN sujetos 	AV1	ON AV1.numero = B.aval1
					INNER JOIN sujetos 	AV2	ON AV2.numero = B.aval2
					WHERE A.estatus = 'A' AND T.tipo = :TipoEmpleado AND A.congelado = 0 ");
			$comando = $this->dbConexion->createCommand($datos); 
			$comando->bindValue(":TipoEmpleado",$TipoEmpleado);
			$reg =$comando->query()->readAll();
			if(count($reg) > 0)
			{
				if ($TipoEmpleado == 'A'){
					$desno = "DESNOQ.txt";
					$f = fopen("C:\\www\\prestamos\\temp\\DESNOQ.txt","w");
				}ELSE if ($TipoEmpleado){
					$desno = "DESNOJ.txt";
					$f = fopen("C:\\www\\prestamos\\temp\\DESNOJ.txt","w");
				}
				$sep = "|";
				$salto = "\r\n";
				$this->lbldesceuntoId->Text = $id; 
				for($i = 0; $i < $Total_registros; $i++){
					$linea = $reg[$i]["NUMERO"] . $sep . $reg[$i]["CLAVECON"] . $sep . $reg[$i]["IMPORTE"] . $sep . $reg[$i]["PERIODO"] . $sep . $reg[$i]["PERIODOS"] . $sep . $reg[$i]["CONTRATO"] . $sep . $reg[$i]["APLICADO"] . $sep . $reg[$i]["tipo_nomina"] . $sep . $reg[$i]["NOMINA"] . $sep . $reg[$i]["AVAL1"] . $sep . $reg[$i]["AVAL2"] . $sep . $reg[$i]["NOTA"] . $sep . $reg[$i]["APAVAL"] . $sep . $reg[$i]["EMPLEACT"] . $sep . $reg[$i]["AVAL1ACT"] . $sep . $reg[$i]["AVAL2ACT"] . $salto;
					$this->detalle_descuento($id,$reg[$i]["NUMERO"],$reg[$i]["CLAVECON"],$reg[$i]["IMPORTE"],$reg[$i]["PERIODO"],$reg[$i]["PERIODOS"],$reg[$i]["CONTRATO"],$reg[$i]["tipo_nomina"],$reg[$i]["NOMINA"],$reg[$i]["APLICADO"],$reg[$i]["AVAL1"],$reg[$i]["AVAL2"],$reg[$i]["NOTA"],$reg[$i]["APAVAL"]);  
					fwrite($f,$linea);
				}
			fclose($f);
				$this-> subir_ftp($desno);
				$this->mostrarDatosGrid ();
				$this->datos($id);
				$this->mostrarDatosGridDesc ($id);
				$this->ClientScript->registerEndScript("exito",
				"alert('desno generado exitosamente');\n");
			}else{
				$this->ClientScript->registerEndScript("error",
				"alert('error al generar desno');\n");
			}
		}else{
			$liberar =("TRUNCATE respMovimientos");
			$comando = $this->dbConexion->createCommand($liberar);
			$comando->execute();
			$this->ClientScript->RegisterBeginScript("Mensaje","alert('No hay registro que generar');");
		}
	}
	public function detalle_descuento($id_descuento, $num_empleado, $clavecon, $importe, $periodo, $periodos, $contrato, $tipo_nomina, $nomina, $aplicado, $aval1, $aval2, $nota, $aplicaravales)
	{
		$consulta="INSERT INTO descuento_Detalle (id_descuento, num_empleado, clavecon, importe, periodo, periodos, contrato, tipo_nomina, nomina, aplicado,  aval1, aval2, nota, aplicaravales)  
					values(:id_descuento, :num_empleado, :clavecon, :importe, :periodo, :periodos, :contrato, :tipo_nomina, :nomina, :aplicado,  :aval1, :aval2, :nota, :aplicaravales)";
		$comando = $this->dbConexion->createCommand($consulta);	
		$comando->bindValue(":id_descuento",$id_descuento); 
		$comando->bindValue(":num_empleado",$num_empleado); 
		$comando->bindValue(":clavecon",$clavecon); 
		$comando->bindValue(":importe",$importe); 
		$comando->bindValue(":periodo",$periodo); 
		$comando->bindValue(":periodos",$periodos); 
		$comando->bindValue(":contrato",$contrato); 
		$comando->bindValue(":tipo_nomina",$tipo_nomina); 
		$comando->bindValue(":nomina",$nomina); 
		$comando->bindValue(":aplicado",$aplicado);  
		$comando->bindValue(":aval1",$aval1); 
		$comando->bindValue(":aval2",$aval2); 
		$comando->bindValue(":nota",$nota); 
		$comando->bindValue(":aplicaravales",$aplicaravales);
		$comando->execute();
	}
	public function btnRecibir_Click($sender, $param)
	{
		$exito = true;
		$archivoFTP = "DESNO". ($this->ddlTipo->SelectedValue == 'PE' ? "J" : $this->ddlTipoNomina->SelectedValue) . $this->txtPeriodo->Text.".txt"; 
		$regsdesno = $this->descarga_dbf($archivoFTP);	
		if($regsdesno = 1)
		{
			$archivo  = file("C:\\www\\prestamos\\temp\\DESNO". ($this->ddlTipo->SelectedValue == 'PE' ? "J" : $this->ddlTipoNomina->SelectedValue) . $this->txtPeriodo->Text.".txt"); 
			$parametros = array("origen"=>"N", "creado"=>date("Y-m-d H:i:s"), "modificado"=>date("Y-m-d H:i:s"), "creador"=>0, "modificador"=>0, "id_estatus"=>3
			,"observaciones"=>"desno recibido exitosamente", "tipo"=>($this->ddlTipo->SelectedValue == 'PE' ? "J" : "A"), "pago"=>$this->ddlTipoNomina->SelectedValue, "periodo"=>0);
			Conexion::Inserta_Registro($this->dbConexion, "descuento", $parametros);
			$idescuento = Conexion::Ultimo_Id_Generado($this->dbConexion);
			$this->lbldesceuntoId->Text = $idescuento;
			// -----------------------------------------
			foreach ($archivo as $linea_num => $linea)
			{
				$datos = explode("|",$linea);	  
				$consulta = "INSERT INTO descuento_detalle(id_descuento,num_empleado,clavecon,importe,periodo,periodos,contrato,aplicado,tipo_nomina,nomina,aval1,aval2,nota,aplicaravales )"
						."VALUES(:id_descuento,:num_empleado,:clavecon,:importe,:periodo,:periodos,:contrato,:aplicado,:tipo_nomina,:nomina,:aval1,:aval2,:nota,:aplicaravales)";
				$comando = $this->dbConexion->createCommand($consulta);	
				$comando->bindValue(":id_descuento",$idescuento); 
				$comando->bindValue(":num_empleado",trim($datos[0])); 
				$comando->bindValue(":clavecon",trim($datos[1]));    
				$comando->bindValue(":importe",trim($datos[2]));  
				$comando->bindValue(":periodo",trim($datos[3]));  
				$comando->bindValue(":periodos",trim($datos[4]));  
				$comando->bindValue(":contrato",trim($datos[5]));      
				$comando->bindValue(":aplicado",trim($datos[6]));  
				$comando->bindValue(":tipo_nomina",trim($datos[7]));  
				$comando->bindValue(":nomina",trim($datos[8]));    
				$comando->bindValue(":aval1",trim($datos[9]));   
				$comando->bindValue(":aval2",trim($datos[10]));    
				$comando->bindValue(":nota",trim($datos[11]));   
				$comando->bindValue(":aplicaravales",trim($datos[12]));
				if(!$comando->execute())
					$exito = false;
			
			}
			if($exito)
			{
				$this->renombrar_dbf($archivoFTP);
				//$this->lbldescuentoId->Text =$idescuento;
				$this->mostrarDatosGrid();
				$this->datos($idescuento);
				$this->mostrarDatosGridDesc ($idescuento);
				$this->Aplicar_desno($idescuento);
				$this->ClientScript->registerEndScript("bajada",
						"alert('carga desno completada');\n");
			}
			else
			{
				$this->ClientScript->registerEndScript("mensaje",
						"alert('error carga desno');\n");
			}	
		}
	}	
	public function Aplicar_desno($idescuento)
	{
		$consulta="INSERT INTO movimientos (id_contrato, creacion, id_tipo_movto, descripcion, cargo, abono, id_usuario, aplicacion, id_descuento, activo)
					SELECT contrato, NOW(),2,'abono via nomina',0,importe,'',NOW(),id_descuento,1 FROM descuento_detalle WHERE id_descuento = :idescuento ";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":idescuento",$idescuento); 
		$comando->execute();
	}
	public function btnActualizar_Click($sender, $param)
	{
		$archivoFTP = "EMPLEA".$this->ddlTipo->SelectedValue.".txt"; 
		$regsdesno = $this->descarga_dbf($archivoFTP);
				
		if($regsdesno == 1)
		{	
			echo $regsdesno;
			$archivo  = file("C:\\www\\prestamos\\temp\\EMPLEA". $this->ddlTipo->SelectedValue.".txt"); 	
			if ($archivoFTP == 'EMPLEARH.txt'){
				
				$this->actualizar_empleado($archivo,$archivoFTP);
				//echo $regsdesno;
			}
			if ($archivoFTP == 'EMPLEAPE.txt'){
				$this->actualizar_pensionados($archivo,$archivoFTP);
				//echo $regsdesno;
			}
			
		}
		$archivoFTPF = "PDFIJA" . $this->ddlTipo->SelectedValue . ".txt";
		$regsdfij = $this->descarga_dbf($archivoFTPF);
		if($regsdfij == 1)
		{
			$archivoF  = file("C:\\www\\prestamos\\temp\\PDFIJA". $this->ddlTipo->SelectedValue.".txt"); 
			$this->actualiza_descuentos_fijos($archivoF,$archivoFTPF);
		}
		
	}
	
	public function actualiza_descuentos_fijos($archivoFijo,$archivoFTPF)
	{
		$exito = true;
		$liberar =("TRUNCATE descuentos_fijos");
		$comando = $this->dbConexion->createCommand($liberar);
		$comando->execute();
		foreach ($archivoFijo as $linea_num => $linea)
		{
			$datos = explode("|",$linea);   
			$consulta = "REPLACE INTO descuentos_fijos VALUES (:numero, :concepto, :periodos, :pagados, :importe, :porcentaje )";
			$comando = $this->dbConexion->createCommand($consulta);	
			$comando->bindValue(":numero",trim($datos[0])); 
			$comando->bindValue(":concepto",trim($datos[1])); 
			$comando->bindValue(":periodos",trim($datos[2])); 
			$comando->bindValue(":pagados",trim($datos[3])); 
			$comando->bindValue(":importe",trim($datos[4])); 
			$comando->bindValue(":porcentaje",trim($datos[5])); 
			if(!$comando->execute())
				$exito = false;
		}
		if($exito)
			{
				$this->renombrar_dbf($archivoFTPF);
				$this->ClientScript->registerEndScript("bajada",
						"alert('carga desno completada');\n");
			}
			else
			{
				$this->ClientScript->registerEndScript("mensaje",
						"alert('error carga desno');\n");
			}	
	}
	public function actualizar_pensionados($archivo,$archivoftp)
	{
		$exito = true;
		foreach ($archivo as $linea_num => $linea)
		{
			$datos = explode("|",$linea);   
			$consulta = "REPLACE INTO pensionados VALUES (:numero, :num_empleado,  :nombre, :paterno, :materno, :sindicato,:fec_ingre, :sexo,:estatus,:tipo_nomi,:importe_pension)";
			$comando = $this->dbConexion->createCommand($consulta);	
			$comando->bindValue(":numero",trim($datos[0])); 
			$comando->bindValue(":num_empleado",trim($datos[0])); 
			$comando->bindValue(":nombre",trim($datos[1])); 
			$comando->bindValue(":paterno",trim($datos[2]));    
			$comando->bindValue(":materno",trim($datos[3]));  
			$comando->bindValue(":sindicato",trim($datos[13])); 
			$str = $datos[12];
			$date = DateTime::createFromFormat('d/m/Y', $str);
			$comando->bindValue(":fec_ingre",$date->format('Y-m-d'));  
			$comando->bindValue(":sexo",trim($datos[8]));      
			$comando->bindValue(":estatus",trim($datos[9]));  
			$comando->bindValue(":tipo_nomi",'Q'); 
			$comando->bindValue(":importe_pension",trim($datos[10]));  			
				if(!$comando->execute())
				$exito = false;
		}
		if($exito)
			{
				$this->renombrar_dbf($archivoftp);
				$this->ClientScript->registerEndScript("bajada",
						"alert('carga desno completada');\n");
			}
			else
			{
				$this->ClientScript->registerEndScript("mensaje",
						"alert('error carga desno');\n");
			}	
	}
	public function actualizar_empleado($archivo,$archivoftp)
	{
		
		foreach ($archivo as $linea_num => $linea)
		{
			$datos = explode("|",$linea);	  
			$consulta = "REPLACE INTO empleados VALUES (:numero, :nombre, :paterno, :materno, :sindicato, :fec_ingre, :sexo, :estatus,:tipo_nomi)";
			$comando = $this->dbConexion->createCommand($consulta);	
			$comando->bindValue(":numero",trim($datos[0])); 
			$comando->bindValue(":nombre",trim($datos[1])); 
			$comando->bindValue(":paterno",trim($datos[2]));    
			$comando->bindValue(":materno",trim($datos[3]));  
			$comando->bindValue(":sindicato",trim($datos[15])); 
			$str = $datos[17];
			$date = DateTime::createFromFormat('d/m/Y', $str);
			$comando->bindValue(":fec_ingre",$date->format('Y-m-d'));  
			$comando->bindValue(":sexo",trim($datos[18]));      
			$comando->bindValue(":estatus",trim($datos[19]));  
			$tiponomina = $datos[20];
			if($tiponomina == 1) $nomi = 'Q'; else $nomi ='S';
			$comando->bindValue(":tipo_nomi",$nomi);  
				if($comando->execute()){
					
					$this->renombrar_dbf($archivoftp);
					$this->ClientScript->registerEndScript("bajada",
					"alert('carga desno empleado completada');\n");
				}
				else{
				  $this->ClientScript->registerEndScript("mensaje",
					"alert('error carga desno completada');\n");
				} 
		}
	}
	public function btnftp_Click($sender, $param)
	{
		//$this->list_ftp();	
			$this->ClientScript->RegisterBeginScript("Mensaje","" .
					"open('index.php?page=FtpDesno', '_blank','width=400,height=300');");
	}
	public function subir_ftp($file)
	{
		// ftp_put($conn, '/www/site/file.html','c:/wamp/www/site/file.html',FTP_BINARY); 
		$archivo = "C:/www/prestamos/temp/".$file;
		$consulta = "SELECT * FROM parametros WHERE llave IN ('ftp_server', 'ftp_user', 'ftp_pass')";
		$comando = $this->dbConexion->createCommand($consulta);
		$param_ftp = $comando->query()->readAll();
		foreach($param_ftp as $r)
			$$r["llave"] = $r["valor"];
		try
		{
			$conn_id = ftp_connect($ftp_server); 
			$login_result = ftp_login($conn_id, $ftp_user, $ftp_pass); 
		}
		catch(Exception $e)
		{
			$this->ClientScript->registerEndScript("no_conectado",
					"alert('No se pudo conectar al FTP, archivo " . $file . "');\n");
		}
		// ----------------------------------------------------------------------------------------------------
		$remote_file = $file;
		ftp_put($conn_id, $remote_file, $archivo, FTP_ASCII);
	}
	public function list_ftp()
	{
		$consulta = "SELECT * FROM parametros WHERE llave IN ('ftp_server', 'ftp_user', 'ftp_pass')";
		$comando = $this->dbConexion->createCommand($consulta);
		$param_ftp = $comando->query()->readAll();
		foreach($param_ftp as $r)
			$$r["llave"] = $r["valor"];
		try
		{
			$conn_id = ftp_connect($ftp_server); 
			$login_result = ftp_login($conn_id, $ftp_user, $ftp_pass); 
			//$file = ftp_nlist($conn_id, ".");
		}
		catch(Exception $e)
		{
			$this->ClientScript->registerEndScript("no_conectado",
					"alert('No se pudo conectar al FTP, archivo " . $file . "');\n");
		}
		$archivo = 'DESNOQ';
		$list=ftp_nlist($conn_id, "$archivo*.txt");
		var_dump($list);
		
		$archivoe = 'EMPLEA';
		$liste=ftp_nlist($conn_id, "$archivoe*.txt");
		var_dump($liste);
	}
	public function renombrar_dbf($file)
	{
		$archivoFtp = $file.".tmp";
		$consulta = "SELECT * FROM parametros WHERE llave IN ('ftp_server', 'ftp_user', 'ftp_pass')";
		$comando = $this->dbConexion->createCommand($consulta);
		$param_ftp = $comando->query()->readAll();
		foreach($param_ftp as $r)
			$$r["llave"] = $r["valor"];
		try
		{
			$conn_id = ftp_connect($ftp_server); 
			$login_result = ftp_login($conn_id, $ftp_user, $ftp_pass); 
		}
		catch(Exception $e)
		{
			$this->ClientScript->registerEndScript("no_conectado",
					"alert('No se pudo conectar al FTP, archivo " . $file . "');\n");
		}
		ftp_rename($conn_id, $file, $archivoFtp);
	}
	
	public function actualiza_tabla_empleados($registros)
	{
		$parametros = array('nombre'=>'NOMBRE', 'paterno'=>'PATERNO', 'materno'=>'MATERNO', 'sindicato'=>'SINDICATO', 'fec_ingre'=>'FEC_INGRE', 
				'status'=>'STATUS', 'tipo_nomi'=>'TIPO_NOMI');
		$seleccion = array('numero'=>'NUMERO');
		Conexion::Inserta_Actualiza_Registros($this->dbConexion, "empleados", $registros, $parametros, $seleccion);
	}

	public function actualiza_tabla_pensionados($registros)
	{
		$parametros = array('nombre'=>'NOMBRE', 'paterno'=>'PATERNO', 'materno'=>'MATERNO', 'sindicato'=>'SIND', 'fec_ingre'=>'FECHALTA', 
				'status'=>'STATUS', 'importe_pension'=>'IMPORTE', 'tipo_nomi'=>':1');
		$seleccion = array('numero'=>'NUMERO');
		Conexion::Inserta_Actualiza_Registros($this->dbConexion, "pensionados", $registros, $parametros, $seleccion);
	}
	public function actualiza_desno($registros, $id_descuento)
	{
		$parametros = array('clavecon'=>'CLAVECON', 'importe'=>'IMPORTE', 'periodo'=>'PERIODO', 'periodos'=>'PERIODOS', 
				'contrato'=>'CONTRATO', 'aplicado'=>'APLICADO', 'tipo_nomina'=>'TIPO', 'nomina'=>'NOMINA', 'aval1'=>'AVAL1', 'aval2'=>'AVAL2', 
				'nota'=>'NOTA', 'aplicaravales'=>'APAVAL');
		$seleccion = array('id_descuento'=>":" . $id_descuento, 'num_empleado'=>'NUMERO');
		Conexion::Inserta_Actualiza_Registros($this->dbConexion, "descuento_detalle", $registros, $parametros, $seleccion);
	}

	public function entrada_bitacora($tabla, $file, $importe, $estatus, $observaciones)
	{
		$consulta = "insert into bitacora (fechahora, tabla, archivo, fechahora_archivo, longitud_archivo, importe, id_usuario, estatus , observaciones) 
				values (:fechahora, :tabla, :archivo, :fechahora_archivo, :longitud_archivo, :importe, :id_usuario, :estatus , :observaciones)";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":fechahora", date("Y-m-d H:i:s"));
		$comando->bindValue(":tabla", $tabla);
		$comando->bindValue(":archivo", $file);
		if(file_exists("temp/" . $file))
		{
			$comando->bindValue(":fechahora_archivo", date("Y-m-d H:i:s", filemtime("temp/" . $file)));
			$comando->bindValue(":longitud_archivo", filesize("temp/" . $file));
		}
		else
		{
			$comando->bindValue(":fechahora_archivo", date("Y-m-d H:i:s"));
			$comando->bindValue(":longitud_archivo", 0);
		}
		$comando->bindValue(":importe", $importe);
		$comando->bindValue(":id_usuario", "");
		$comando->bindValue(":estatus", $estatus);
		$comando->bindValue(":observaciones", $observaciones);
		$comando->execute();
	}

	public function ddlTipo_Change($sender, $param)
	{
		if($this->ddlTipo->SelectedValue == 'PE')
		{
			$this->ddlTipoNomina->Enabled = false;
			$this->ddlTipoNomina->SelectedValue = "Q";
		}
		else
			$this->ddlTipoNomina->Enabled = true;
	}
	 	
	public function mostrarDatosGridDesc ($id_Descuento) 
	{ 
		$consulta = "SELECT origen AS Origen,DATE_FORMAT(creado,'%d-%m-%Y') AS fecha2, DATE_FORMAT(creado, '%H:%i:%s') AS Hora2,creador AS Usuario2,id_estatus AS Estatus2,DATE_FORMAT(modificado,'%d-%m-%Y') AS Fecha3
					,DATE_FORMAT(modificado, '%H:%i:%s') AS Hora3,modificador AS Usuario3,pago AS Nomina3,observaciones AS Observaciones3 
					FROM descuento WHERE id_descuento = :id_Descuento";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_Descuento",$id_Descuento);
		$resultado = $comando->query()->readAll();
		$this->dgDescuentos->DataSource = $resultado;
		$this->dgDescuentos->dataBind(); 
	}
	// -------------------------------------------------------------------- datagrid---------------------------------------------------------------------------
	
	public function changePage($sender,$param)
    {
        $this->dgDescuentosDet->CurrentPageIndex=$param->NewPageIndex;
	   $this->mostrarDatosGrid ( );
    }
 
    public function pagerCreated($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,'Page: ');
    }
	 public function toggleColumnVisibility($sender,$param)
    {
        foreach($this->dgDescuentosDet->Columns as $index=>$column)
            $column->Visible=$sender->Items[$index]->Selected;
        $this->dgDescuentosDet->DataSource=$this->Data;
        $this->dgDescuentosDet->dataBind();
    }
    public function changePagerPosition($sender,$param)
    {
        $top=$sender->Items[0]->Selected;
        $bottom=$sender->Items[1]->Selected;
        if($top && $bottom)
            $position='TopAndBottom';
        else if($top)
            $position='Top';
        else if($bottom)
            $position='Bottom';
        else
            $position='';
        if($position==='')
            $this->dgDescuentosDet->PagerStyle->Visible=false;
        else
        {
            $this->dgDescuentosDet->PagerStyle->Position=$position;
            $this->dgDescuentosDet->PagerStyle->Visible=true;
        }
    }
 
    public function useNumericPager($sender,$param)
    {
        $this->dgDescuentosDet->PagerStyle->Mode='Numeric';
        $this->dgDescuentosDet->PagerStyle->NextPageText=$this->NextPageText->Text;
        $this->dgDescuentosDet->PagerStyle->PrevPageText=$this->PrevPageText->Text;
        $this->dgDescuentosDet->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;
        $this->dgDescuentosDet->dataBind();
    }
 
    public function useNextPrevPager($sender,$param)
    {
        $this->dgDescuentosDet->PagerStyle->Mode='NextPrev';
        $this->dgDescuentosDet->PagerStyle->NextPageText=$this->NextPageText->Text;
        $this->dgDescuentosDet->PagerStyle->PrevPageText=$this->PrevPageText->Text;
        $this->dgDescuentosDet->dataBind();
    }
 
    public function changePageSize($sender,$param)
    {
        $this->dgDescuentosDet->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
        $this->dgDescuentosDet->CurrentPageIndex=0;
        $this->dgDescuentosDet->dataBind();
    }	
	public function btnBuscar_Click($sender,$param)
	{
		
		$contrato=$this->ddlIdContrato->Text;
		$Cvempleado=$this->ddlCveEmpleado->Text;
		//$empleado=$this->ddlEmpleado->Text; 
		if ($contrato == '' and $Cvempleado == ''){
			$this->mostrarDatosGrid();
		}else{
			$this->mostrarDatosGridEmp($contrato,$Cvempleado);	
		}
			
	}
		public function mostrarDatosGridEmp ($contrato,$Cvempleado) 
	{ 
		$id_Descuento = $this->lbldesceuntoId->Text;	
		$consulta = "SELECT A.num_empleado 
					,TRIM(IF(B.numero IS NULL, IF(C.numero IS NULL, '', CONCAT(C.paterno, ' ', C.materno, ' ', C.nombre))
					,CONCAT(B.paterno, ' ', B.materno, ' ', B.nombre))) AS EmpleadoDesc 
					,A.clavecon as clavecon, A.aval1 as aval1, A.aval2 as aval2, IF(A.clavecon IN (0), 'Descuento de prestamo', IF(A.clavecon IN (63), 'Descuento de prestamo-Titular', 'Descuento de prestamo-Aval')) AS descripcion
					,A.importe as  importe, A.periodo as periodo, A.periodos as periodos , A.contrato as contrato, A.tipo_nomina as tipo_nomina, A.nomina as nomina, A.aplicado as aplicado
					,(SELECT s.status FROM sujetos s WHERE s.numero = A.num_empleado) AS titularActivo
					, (SELECT s.status FROM sujetos s WHERE s.numero = A.aval1) AS aval1Activo
					, (SELECT s.status FROM sujetos s WHERE s.numero = A.aval2) AS aval2Activo 
					FROM descuento_detalle A 
					LEFT JOIN empleados B ON B.numero = A.num_empleado 
					LEFT JOIN pensionados C ON C.numero = A.num_empleado  
					WHERE A.id_Descuento = :id_Descuento and (A.num_empleado = :id_clvempleado or A.contrato = :contrato')";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_Descuento",$id_Descuento);
		$comando->bindValue(":contrato",$contrato);
		$comando->bindValue(":id_clvempleado",$Cvempleado);
		$resultado = $comando->query()->readAll();
		$this->dgDescuentosDet->DataSource = $resultado;
		$this->dgDescuentosDet->dataBind();
	}
	public function mostrarDatosGrid ( ) 
	{ 
		$id_Descuento = $this->lbldesceuntoId->Text;	
		$consulta = "SELECT A.num_empleado 
					,TRIM(IF(B.numero IS NULL, IF(C.numero IS NULL, '', CONCAT(C.paterno, ' ', C.materno, ' ', C.nombre))
					,CONCAT(B.paterno, ' ', B.materno, ' ', B.nombre))) AS EmpleadoDesc 
					,A.clavecon as clavecon, A.aval1 as aval1, A.aval2 as aval2, IF(A.clavecon IN (0), 'Descuento de prestamo', IF(A.clavecon IN (63), 'Descuento de prestamo-Titular', 'Descuento de prestamo-Aval')) AS descripcion
					,A.importe as  importe, A.periodo as periodo, A.periodos as periodos , A.contrato as contrato, A.tipo_nomina as tipo_nomina, A.nomina as nomina, A.aplicado as aplicado
					,(SELECT s.status FROM sujetos s WHERE s.numero = A.num_empleado) AS titularActivo
					, (SELECT s.status FROM sujetos s WHERE s.numero = A.aval1) AS aval1Activo
					, (SELECT s.status FROM sujetos s WHERE s.numero = A.aval2) AS aval2Activo 
					FROM descuento_detalle A 
					LEFT JOIN empleados B ON B.numero = A.num_empleado 
					LEFT JOIN pensionados C ON C.numero = A.num_empleado  
					WHERE A.id_Descuento = :id_Descuento";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_Descuento",$id_Descuento);
		$resultado = $comando->query()->readAll();
		$this->dgDescuentosDet->DataSource = $resultado;
		$this->dgDescuentosDet->dataBind();
	}
	public function datos($id_Descuento)
	{
		$consulta = "SELECT 
					(SELECT SUM(d.importe) AS quincenal FROM descuento_detalle AS d WHERE d.id_descuento = det.id_descuento AND  d.tipo_nomina = 'Q') AS quincena
					,(SELECT SUM(d.importe) AS quincenal FROM descuento_detalle AS d WHERE d.id_descuento = det.id_descuento AND  d.tipo_nomina = 'S') AS semanal
					,(SELECT SUM(d.importe) AS quincenal FROM descuento_detalle AS d WHERE d.id_descuento = det.id_descuento ) AS total
					FROM descuento_detalle det WHERE det.id_descuento = :id_Descuento GROUP BY det.id_descuento";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_Descuento",$id_Descuento);
		$result = $comando->query()->readAll();
		if(count($result) > 0)
		{
		$this->ddlTotalSemana->Text = $result[0]["semanal"];
		$this->ddlTotalQuincena->Text = $result[0]["quincena"];
		$this->ddlTotal8->Text = $result[0]["total"];
		}
		$consulta = "SELECT COUNT(A.numero) AS activos
					FROM descuento_detalle D
					INNER JOIN sujetos A	ON	A.numero = D.num_empleado AND A.tipo = 'A'
					WHERE D.id_descuento = :id_Descuento ";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_Descuento",$id_Descuento);
		$result = $comando->query()->readAll();
		$this->ddlTotalActivos2->Text = $result[0]["activos"];
		
		$consulta = "SELECT COUNT(A.numero) AS jubilados
					FROM descuento_detalle D
					INNER JOIN sujetos A	ON	A.numero = D.num_empleado AND A.tipo = 'J'
					WHERE D.id_descuento = :id_Descuento ";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_Descuento",$id_Descuento);
		$result = $comando->query()->readAll();
		$this->ddlTotalJubilados8->Text = $result[0]["jubilados"];

	}
	public function descarga_dbf($file)
	{
		//$regs = 1;
		
		$consulta = "SELECT * FROM parametros WHERE llave IN ('ftp_server', 'ftp_user', 'ftp_pass')";
		$comando = $this->dbConexion->createCommand($consulta);
		$param_ftp = $comando->query()->readAll();
		foreach($param_ftp as $r)
			$$r["llave"] = $r["valor"];
		try
		{
			$conn_id = ftp_connect($ftp_server); 
			$login_result = ftp_login($conn_id, $ftp_user, $ftp_pass); 
		}
		catch(Exception $e)
		{
			$this->ClientScript->registerEndScript("no_conectado",
					"alert('No se pudo conectar al FTP, archivo" . $file . "');\n");
			$this->entrada_bitacora("", $file, 0, -1, "No se pudo conectar al FTP");
		}
		if (($conn_id) && ($login_result)) 
		{  
			try
			{
				$regs = 1;
				$download = ftp_get($conn_id, "temp/" . $file, $file, FTP_BINARY);
			}
			catch(Exception $e)
			{
				$download = false;
				$regs = 0;
				$this->ClientScript->registerEndScript("error_descarga",
						"alert('No se pudo descargar el archivo prueba" . $file . " del FTP".$regs."');\n");
				$this->entrada_bitacora("", $file, 0, -1, "No se pudo descargar el archivo del FTP");
			}
			if ($download) 
			$regs = 1;
			$this->entrada_bitacora("", $file, 0, 3, "Carga completada");
			ftp_close($conn_id);
		}

		return $regs;
	}
	
	// -----------------------------------------------------------------------------------------------------------------
	public function btnreporte_Click($sender,$param)
	{
	$idDescuentos = $this->lbldesceuntoId->Text;
	$this->ClientScript->RegisterBeginScript("Mensaje","alert('El reporte se está generando');" .
				"open('index.php?page=reportes.RDesnopdf&id=$idDescuentos', '_blank');");
	}
}
?>