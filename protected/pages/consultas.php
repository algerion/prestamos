<?php
include_once('../compartidos/clases/conexion.php');
include_once('/protected/Comunes/Busquedas.php');

class consultas extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);

		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();

		if(!$this->IsPostBack)
		{
			$resultado = Busquedas::obtenerPrestamoAnteriorSinRedocumentado($this->dbConexion, 6173);
			print_r($resultado);
		}
	}

	}
	

?>