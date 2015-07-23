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
			$estatus = Conexion::Retorna_Registro($this->dbConexion, "estatus", array(), " id_estatus > 0");
			$this->ddlEstatus->DataSource = $estatus;
			$this->ddlEstatus->dataBind();
		}
	}
	
	public function btnGenerar_Click($sender, $param)
	{
		//$this->getPage()->getCallbackClient()->callClientFunction("reemplaza_desno", array());
		//SELECT id_descuento FROM descuento WHERE id_estatus = 1 ORDER BY creado LIMIT 1
		$id_descuento = Conexion::Retorna_Campo($this->dbConexion, "descuento", "id_descuento", array("id_estatus"=>1));
		if($id_descuento != "")
			$this->ClientScript->registerEndScript("reemplaza_desno_genera",
				"reemplaza_desno('Ya existe un desno generado. ¿Reemplazar?', 1);\n");
		else
			$this->ClientScript->registerEndScript("reemplaza_desno_genera",
				"reemplaza_desno('El archivo de descuentos de " . $this->ddlTipo->SelectedItem->Text . " sera generado, ¿Desea continuar?', 2);\n");
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

			$this->ClientScript->registerEndScript("exito",
				"alert('desno generado exitosamente');\n");
		}
	}
	
	public function btnRecibir_Click($sender, $param)
	{
		$archivo = "DESNO" . ($this->ddlTipo->SelectedValue == 'PE' ? "J" : $this->ddlTipoNomina->SelectedValue) . $this->txtPeriodo->Text . ".DBF";
		$regsdesno = $this->descarga_dbf($archivo);
		if($regsdesno)
		{
			$parametros = array("origen"=>"N", "creado"=>date("Ymd H:i:s"), "modificado"=>date("Ymd H:i:s"), "creador"=>0, "modificador"=>0, "id_estatus"=>3,
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
		
		if($regsempl)
		{
			if(strcmp(substr(strtoupper($archivo), 0, 8), "EMPLEARH") == 0)
				$this->actualiza_tabla_empleados($regsempl);
			elseif(strcmp(substr(strtoupper($archivo), 0, 8), "EMPLEAPE") == 0)
				$this->actualiza_tabla_pensionados($regsempl);

			$archivo = "PDFIJA" . $this->ddlTipo->SelectedValue . ".DBF";
			$regsdfij = $this->descarga_dbf($archivo);
		}

		if($regsdfij)
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
				'status'=>'STATUS', 'tipo_nomi'=>':1');
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