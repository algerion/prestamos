<?php
include_once('../compartidos/clases/conexion.php');
class FtpDesno extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);

		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();

		if(!$this->IsPostBack)
		{
			$this->list_ftp();
		}
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
}
?>