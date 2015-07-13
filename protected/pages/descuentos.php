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
		$file = "EMPLEA" . $this->ddlOrigen->SelectedValue . ".DBF";
		echo "<br />";
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
			"alert('No se pudo conectar al FTP');\n");
		}
		if (($conn_id) && ($login_result)) 
		{  
			$download = ftp_get($conn_id, "temp/" . $file, $file, FTP_BINARY);  
			if ($download) 
			{  
				$regs = UsaDBF::registros_dbf("temp/EMPLEARH.dbf");
				foreach($regs as $r)
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
/*					foreach($r as $key=>$value)
					{
						echo $key . ": " . Charset::CambiaCharset($value, 'CP850', 'UTF-8') . " ";
					}
					echo "<br />";*/
				}
				$this->ClientScript->registerEndScript("bajada",
	//					"alert('Bajada de $file a $ftp_server como $file');\n");
				"alert('carga completada');\n");
			}
			ftp_close($conn_id);
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
}
?>