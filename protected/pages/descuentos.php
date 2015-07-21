<?php
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
/*		if(!$this->IsPostBack)
		{
		}
*/
	}
	
	public function btnRecibir_Click($sender, $param)
	{
	}
	
	public function btnActualizar_Click($sender, $param)
	{
		$regsdfij = 0;
		
		$archivo = "EMPLEA" . $this->ddlOrigen->SelectedValue . ".DBF";
		$regsempl = $this->descarga_dbf($archivo);
		
		if($regsempl)
		{
			if(strcmp(substr(strtoupper($archivo), 0, 8), "EMPLEARH") == 0)
				$this->actualiza_tabla_empleados($regsempl);
			elseif(strcmp(substr(strtoupper($archivo), 0, 8), "EMPLEAPE") == 0)
				$this->actualiza_tabla_pensionados($regsempl);

			$archivo = "PDFIJA" . $this->ddlOrigen->SelectedValue . ".DBF";
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

	public function entrada_bitacora($tabla, $file, $importe, $estatus, $observaciones)
	{
		$consulta = "insert into bitacora (fechahora, tabla, archivo, fechahora_archivo, longitud_archivo, importe, id_usuario, estatus , observaciones) 
				values (:fechahora, :tabla, :archivo, :fechahora_archivo, :longitud_archivo, :importe, :id_usuario, :estatus , :observaciones)";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":fechahora", date("Y-m-d H:i:s"));
		$comando->bindValue(":tabla", $tabla);
		$comando->bindValue(":archivo", $file);
		$comando->bindValue(":fechahora_archivo", date("Y-m-d H:i:s", filemtime("temp/" . $file)));
		$comando->bindValue(":longitud_archivo", filesize("temp/" . $file));
		$comando->bindValue(":importe", $importe);
		$comando->bindValue(":id_usuario", "");
		$comando->bindValue(":estatus", $estatus);
		$comando->bindValue(":observaciones", $observaciones);
		$comando->execute();
	}
}
?>