<?php
include_once('../compartidos/clases/conexion.php');
class Home extends TPage
{
	var $dbConexion, $Consulta;
	
	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
	}
}
?>
