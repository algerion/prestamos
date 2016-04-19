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
		//$this->getPage()->getCallbackClient()->callClientFunction("reemplaza_desno", array());
		//SELECT id_descuento FROM descuento WHERE id_estatus = 1 ORDER BY creado LIMIT 1
		$id_descuento = Conexion::Retorna_Campo($this->dbConexion, "descuento", "id_descuento", array("id_estatus"=>1));
		if($id_descuento != ""){
			
			// $this->ClientScript->registerEndScript("reemplaza_desno_genera",00	"reemplaza_desno('Ya existe un desno generado. ¿Reemplazar?', 1);\n");
				$this->ClientScript->registerEndScript("reemplaza_desno_genera",
				"reemplaza_desno('Ya existe un desno generado. ¿Reemplazar?', 1);\n");
			/*$Tiposujeto= $this->ddlTipo->SelectedValue;
			if ($Tiposujeto = 'RH'){
				$Sujeto = 'A';
			}else{
				$sujeto = 'J';
			}
			$this->Generar_desno($sujeto);	*/
			
			$this->Generar_desno($this->ddlTipo->SelectedValue == 'PE' ? "J" : "A");
		}else{
			$this->ClientScript->registerEndScript("reemplaza_desno_genera",
				"reemplaza_desno('El archivo de descuentos de " . $this->ddlTipo->SelectedItem->Text . " sera generado, ¿Desea continuar?', 2);\n");
		}
	}
	
	public function cbOperaciones_Callback($sender, $param)
	{
		if($param->CallbackParameter->valor)
		{
		
			
			if($param->CallbackParameter->tipo == 1)
			{
				$id_descuento = Conexion::Retorna_Campo($this->dbConexion, "descuento", "id_descuento", array("id_estatus"=>1));
				Elimina_Registro($this->dbConexion, "descuento_detalle", array("id_descuento"=>$id_descuento));
				Elimina_Registro($this->dbConexion, "descuento", array("id_descuento"=>$id_descuento));
			}
		
			$parametros = array("origen"=>"P", "creado"=>date("Ymd H:i:s"), "modificado"=>date("Ymd H:i:s"), "creador"=>0, "modificador"=>0, "id_estatus"=>1,
					"observaciones"=>"desno generado exitosamente", "tipo"=>($this->ddlTipo->SelectedValue == 'PE' ? "J" : "A"), 
					"pago"=>$this->ddlTipoNomina->SelectedValue, "periodo"=>(is_numeric($this->txtPeriodo->Text) ? $this->txtPeriodo->Text : 0));
			Conexion::Inserta_Registro($this->dbConexion, "descuento", $parametros);
			$id = Conexion::Ultimo_Id_Generado($this->dbConexion);
			$this->actualiza_desno($regsdesno, $id);
			//$this->Generar_desno($this->ddlTipo->SelectedValue == 'PE' ? "J" : "A");
			$this->ClientScript->registerEndScript("exito",
				"alert('desno generado exitosamente');\n");
		}
	}
	public function Generar_desno($TipoEmpleado)
	{		
		$consulta="INSERT INTO respMovimientos (id_contrato,  cargo,   abono,  activo , adeudo,movimientos)  
					SELECT id_contrato,cargo, abono,activo,SUM(Cargo - Abono) AS adeudo ,COUNT(id_contrato) AS movimientos FROM movimientos WHERE  activo = 1 GROUP BY id_contrato HAVING SUM(Cargo - Abono) > 1";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->execute();
		// ----------------------------------------------------------------------------------------------------------------------------------------------------------------
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
				$f = fopen("DESNOQ.txt","w");
			}ELSE if ($TipoEmpleado){
				$f = fopen("DESNOJ.txt","w");
			}
			$sep = "|";
			$salto = "\r\n";
		
			for($i = 0; $i < $Total_registros; $i++){
				$linea = $reg[$i]["NUMERO"] . $sep . $reg[$i]["CLAVECON"] . $sep . $reg[$i]["IMPORTE"] . $sep . $reg[$i]["PERIODO"] . $sep . $reg[$i]["PERIODOS"] . $sep . $reg[$i]["CONTRATO"] . $sep . $reg[$i]["APLICADO"] . $sep . $reg[$i]["TIPO"] . $sep . $reg[$i]["NOMINA"] . $sep . $reg[$i]["AVAL1"] . $sep . $reg[$i]["AVAL2"] . $sep . $reg[$i]["NOTA"] . $sep . $reg[$i]["APAVAL"] . $sep . $reg[$i]["EMPLEACT"] . $sep . $reg[$i]["AVAL1ACT"] . $sep . $reg[$i]["AVAL2ACT"] . $salto;
				fwrite($f,$linea);
			}
		fclose($f);
			$liberar =("TRUNCATE respMovimientos");
			$comando = $this->dbConexion->createCommand($liberar);
			$comando->execute();
			//$this->ClientScript->RegisterBeginScript("Mensaje","alert('Se ah generado el archivo');");
		}else{
			//$this->ClientScript->RegisterBeginScript("Mensaje","alert('No hay registros que procesar');");
		}
		}
	}
	public function btnRecibir_Click($sender, $param)
	{
		$archivo = "DESNO" . ($this->ddlTipo->SelectedValue == 'PE' ? "J" : $this->ddlTipoNomina->SelectedValue) . $this->txtPeriodo->Text . ".DBF";
		$regsdesno = $this->descarga_dbf($archivo);
		if($regsdesno)
		{
			$parametros = array("origen"=>"N", "creado"=>date("Y-m-d H:i:s"), "modificado"=>date("Y-m-d H:i:s"), "creador"=>0, "modificador"=>0, "id_estatus"=>3,
					"observaciones"=>"desno recibido exitosamente", "tipo"=>($this->ddlTipo->SelectedValue == 'PE' ? "J" : "A"), 
					"pago"=>$this->ddlTipoNomina->SelectedValue, "periodo"=>$this->txtPeriodo->Text);
			Conexion::Inserta_Registro($this->dbConexion, "descuento", $parametros);
			$id = Conexion::Ultimo_Id_Generado($this->dbConexion);
			$this->actualiza_desno($regsdesno, $id);

			$this->ClientScript->registerEndScript("bajada",
				"alert('carga desno completada');\n");
		}
	}

	public function btnActualizar_Click($sender, $param)
	{
		$regsdfij = 0;
		
		$archivo = "EMPLEA" . $this->ddlTipo->SelectedValue . ".DBF";
		$regsempl = $this->descarga_dbf($archivo);
		
		if(is_array($regsempl))
		{
			if(strcmp(substr(strtoupper($archivo), 0, 8), "EMPLEARH") == 0)
				$this->actualiza_tabla_empleados($regsempl);
			elseif(strcmp(substr(strtoupper($archivo), 0, 8), "EMPLEAPE") == 0)
				$this->actualiza_tabla_pensionados($regsempl);

			$archivo = "PDFIJA" . $this->ddlTipo->SelectedValue . ".DBF";
			$regsdfij = $this->descarga_dbf($archivo);
		}

		if(is_array($regsdfij))
		{
			$this->actualiza_descuentos_fijos($regsdfij);
			$this->ClientScript->registerEndScript("bajada",
				"alert('carga completada');\n");
		}
	}

	public function descarga_dbf($file)
	{
		$regs = 0;
		
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
			$this->entrada_bitacora("", $file, 0, -1, "No se pudo conectar al FTP");
		}
		if (($conn_id) && ($login_result)) 
		{  
			try
			{
				$download = ftp_get($conn_id, "temp/" . $file, $file, FTP_BINARY);
			}
			catch(Exception $e)
			{
				$download = false;
				$this->ClientScript->registerEndScript("error_descarga",
						"alert('No se pudo descargar el archivo " . $file . " del FTP');\n");
				$this->entrada_bitacora("", $file, 0, -1, "No se pudo descargar el archivo del FTP");
			}
			if ($download) 
				$regs = UsaDBF::registros_dbf("temp/" . $file);

			$this->entrada_bitacora("", $file, 0, 3, "Carga completada");
			ftp_close($conn_id);
		}

		return $regs;
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

	public function actualiza_descuentos_fijos($registros)
	{
		$parametros = array('concepto'=>'CONCEPTO', 'periodos'=>'PERIODOS', 'pagados'=>'PAGADOS', 'importe'=>'IMPORTE', 
				'porcentaje'=>'PORCENTAJE');
		$seleccion = array('numero'=>'NUMERO');
		Conexion::Inserta_Actualiza_Registros($this->dbConexion, "descuentos_fijos", $registros, $parametros, $seleccion);
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
}
?>