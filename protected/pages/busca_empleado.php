<?php
include_once('../compartidos/clases/conexion.php');

class busca_empleado extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);

		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();

		if(!$this->IsPostBack)
		{
			$this->rellena_sindicatos();
		}
	}
	
	public function rellena_sindicatos()
	{
		$result = Conexion::Retorna_Registro($this->dbConexion, "catsindicatos", array("1"=>"1"));
		$this->ddlSindicato->DataSource = $result;
		$this->ddlSindicato->dataBind();
	}
	
	public function btnBuscar_Click($sender, $param)
	{
		$consulta = "SELECT numero, nombre, paterno, materno, cs.sindicato FROM ";
		$comando = $this->dbConexion->createCommand($consulta);
		$resultado = $comando->query()->readAll();
		$this->dgDescuentos->DataSource = $resultado;
		$this->dgDescuentos->dataBind();
	}
}

?>