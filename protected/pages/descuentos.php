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
	
	public function btnActualizar_Click($sender, $param)
	{
		$archivo = "EMPLEA" . $this->ddlOrigen->SelectedValue . ".DBF";
		$error = $this->descarga_dbf($archivo);
		if($error == 0)
			$error = $this->descarga_dbf("desno.dbf");
		if($error == 0)
		{
			$archivo = "BITACOAP.DBF";
			$error = $this->descarga_dbf($archivo);
		}

		if($error == 0)
			$this->ClientScript->registerEndScript("bajada",
				"alert('carga completada');\n");
	}

	public function descarga_dbf($file)
	{
		$errorlevel = 0;
		
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
			$errorlevel = -1;
			$this->ClientScript->registerEndScript("no_conectado",
					"alert('No se pudo conectar al FTP, archivo " . $file . "');\n");
		}
		if (($conn_id) && ($login_result)) 
		{  
			try
			{
				$download = ftp_get($conn_id, "temp/" . $file, $file, FTP_BINARY);
			}
			catch(Exception $e)
			{
				$errorlevel = -2;
				$download = false;
				$this->ClientScript->registerEndScript("error_descarga",
						"alert('No se pudo descargar el archivo " . $file . " del FTP');\n");
			}
			if ($download) 
			{
				$regs = UsaDBF::registros_dbf("temp/" . $file);
				if(strcmp(substr(strtoupper($file), 0, 8), "EMPLEARH") == 0)
					$this->actualiza_tabla_empleados($regs);
				elseif(strcmp(substr(strtoupper($file), 0, 8), "EMPLEAPE") == 0)
					$this->actualiza_tabla_pensionados($regs);
			}
			ftp_close($conn_id);
		}
		return $errorlevel;
	}
	
	public function actualiza_tabla_empleados($registros)
	{
		foreach($registros as $r)
		{
			$consulta = "insert into empleados (numero, nombre, paterno, materno, sindicato, fec_ingre, status, tipo_nomi) 
					values (:numero, :nombre, :paterno, :materno, :sindicato, :fec_ingre, :status, :tipo_nomi)";
			try
			{
				$this->consulta_empleados($consulta, $r);
			}
			catch(Exception $e)
			{
				$consulta = "update empleados SET nombre = :nombre, paterno = :paterno, materno = :materno, 
					sindicato = :sindicato, fec_ingre = :fec_ingre, status = :status, tipo_nomi = :tipo_nomi where numero = :numero";
				$this->consulta_empleados($consulta, $r);
			}
		}
	}
	
	public function consulta_empleados($consulta, $r)
	{
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":numero", $r["NUMERO"]);
		$comando->bindValue(":nombre", Charset::CambiaCharset($r["NOMBRE"], 'CP850', 'UTF-8'));
		$comando->bindValue(":paterno", Charset::CambiaCharset($r["PATERNO"], 'CP850', 'UTF-8'));
		$comando->bindValue(":materno", Charset::CambiaCharset($r["MATERNO"], 'CP850', 'UTF-8'));
		$comando->bindValue(":sindicato", $r["SINDICATO"]);
		$comando->bindValue(":fec_ingre", $r["FEC_INGRE"]);
		$comando->bindValue(":status", $r["STATUS"]);
		$comando->bindValue(":tipo_nomi", $r["TIPO_NOMI"]);
		$comando->execute();
		
	}
	
	public function actualiza_tabla_pensionados($registros)
	{
		foreach($registros as $r)
		{
			$consulta = "insert into pensionados (numero, nombre, paterno, materno, sindicato, fec_ingre, status, tipo_nomi) 
					values (:numero, :nombre, :paterno, :materno, :sindicato, :fec_ingre, :status, :tipo_nomi)";
			try
			{
				$this->consulta_pensionados($consulta, $r);
			}
			catch(Exception $e)
			{
				$consulta = "update pensionados SET nombre = :nombre, paterno = :paterno, materno = :materno, 
					sindicato = :sindicato, fec_ingre = :fec_ingre, status = :status, tipo_nomi = :tipo_nomi where numero = :numero";
				$this->consulta_pensionados($consulta, $r);
			}
		}
	}
	public function consulta_pensionados($consulta, $r)
	{
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":numero", $r["NUMERO"]);
		$comando->bindValue(":nombre", Charset::CambiaCharset($r["NOMBRE"], 'CP850', 'UTF-8'));
		$comando->bindValue(":paterno", Charset::CambiaCharset($r["PATERNO"], 'CP850', 'UTF-8'));
		$comando->bindValue(":materno", Charset::CambiaCharset($r["MATERNO"], 'CP850', 'UTF-8'));
		$comando->bindValue(":sindicato", $r["SIND"]);
		$comando->bindValue(":fec_ingre", $r["FECHALTA"]);
		$comando->bindValue(":status", $r["STATUS"]);
		$comando->bindValue(":tipo_nomi", '1');
		$comando->execute();
		
	}
	
		public function descuentos_fijos($registros)
	{
		foreach($registros as $r)
		{
			$consulta = "insert into descuentos_fijos (numero, conceptos, periodo, pagados, importe, porcentaje) 
					values (:numero, :conceptos, :periodo,:pagados, :importe, :porcentaje)";
			try
			{
				$this->descuentos_fijos($consulta, $r);
			}
			catch(Exception $e)
			{
				$consulta = "update descuentos_fijos SET numero = :numero, conceptos = :conceptos, periodo = :periodo, 
					pagados = :pagados, importe = :importe, porcentaje = :porcentaje ";
				$this->descuentos_fijos($consulta, $r);
			}
		}
	}
	public function descuentos_fijos($consulta, $r)
	{
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":numero", $r["NUMERO"]);
		$comando->bindValue(":conceptos", Charset::CambiaCharset($r["conceptos"], 'CP850', 'UTF-8'));
		$comando->bindValue(":periodo", Charset::CambiaCharset($r["periodo"], 'CP850', 'UTF-8'));
		$comando->bindValue(":pagados", Charset::CambiaCharset($r["pagados"], 'CP850', 'UTF-8'));
		$comando->bindValue(":importe", $r["impor"]);
		$comando->bindValue(":porcentaje", $r["porcentaje"]);
		$comando->execute();
		
	}
			
			
			
			public function bitacora($registros)
	{
		foreach($registros as $r)
		{
			$consulta = "insert into bitacora (id_registro, fechahora, tabla, archivo, fechahora_archivo, longitud_archivo, importe, id_usuario, estatus , observaciones ) 
					values (:id_registro, :fechahora, :tabla, :archivo, :fechahora_archivo, :longitud_archivo, :importe, :id_usuario, :estatus , :observaciones
 )";
			try
			{
				$this->bitacora($consulta, $r);
			}
			catch(Exception $e)
			{
				$consulta = "id_registro = :id_registro, fechahora = :fechahora, tabla = :tabla, archivo = :archivo, fechahora_archivo = :fechahora_archivo, = longitud_archivo :longitud_archivo, importe = :importe, id_usuario = :id_usuario, estatus = :estatus , observaciones = :observaciones";
				$this->bitacora($consulta, $r);
			}
		}
	}
	public function bitacora($consulta, $r)
	{
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_registro", $r["id_registro"]);
		$comando->bindValue(":fechahora", Charset::CambiaCharset($r["fechahora"], 'CP850', 'UTF-8'));
		$comando->bindValue(":tabla", Charset::CambiaCharset($r["tabla"], 'CP850', 'UTF-8'));
		$comando->bindValue(":archivo", Charset::CambiaCharset($r["archivo"], 'CP850', 'UTF-8'));
		$comando->bindValue(":fechahora_archivo", $r["fechahora_archivo"]);
		$comando->bindValue(":longitud_archivo", $r["longitud_archivo"]);
		$comando->bindValue(":importe", $r["importe"]);
		$comando->bindValue(":id_usuario", $r["id_usuario"]);
		$comando->bindValue(":estatus", $r["estatus"]);
		$comando->bindValue(":observaciones", $r["observaciones"]);
		$comando->execute();
		
	}
	
	
}
?>