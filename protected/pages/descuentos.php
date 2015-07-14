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
						"alert('No se pudo descargar el archivo " . $file . "del FTP');\n");
			}
			if ($download) 
			{
				$regs = UsaDBF::registros_dbf("temp/" . $file);
				echo strtolower($file);
				echo substr(strtoupper($file), 0, 3);
				echo strcmp(substr(strtoupper($file), 0, 3), "EMPLEA");
/*				foreach($regs as $r)
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
*/
/*					foreach($r as $key=>$value)
					{
						echo $key . ": " . Charset::CambiaCharset($value, 'CP850', 'UTF-8') . " ";
					}
					echo "<br />";*/
//				}
			}
			ftp_close($conn_id);
		}
		return $errorlevel;
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
	public function descarga_dbf
	if ($download)=Emplea 
	consulta = Emplea 
	
	
	
}
?>