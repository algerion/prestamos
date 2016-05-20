<?php
include_once('../compartidos/clases/conexion.php');
include_once('../compartidos/clases/numaletras.php');

class RRedocumentacion extends TPage 
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
		
	
		$this->mostrarDatosGrid ();
	}
	public function mostrarDatosGrid() 
	{ 
		$consulta = "SELECT idContrato, fecha, titular, nombre, importePrestamo, MontoRedocumentacion FROM repAltasRedocumenta ";
		$comando = $this->dbConexion->createCommand($consulta);
		$resultado = $comando->query()->readAll();
		$this->dgredocumentacion->DataSource = $resultado;
		$this->dgredocumentacion->dataBind();
	}
	
}
;