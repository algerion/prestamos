<?php
include_once('../compartidos/clases/conexion.php');
include_once('/protected/Comunes/Busquedas.php');

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
		$sindicatos = array_merge(array(array("cve_sindicato"=>"T", "sindicato"=>"Todos")), $result);
		$this->ddlSindicato->DataSource = $sindicatos;
		$this->ddlSindicato->dataBind();
	}
	
	public function btnBuscar_Click($sender, $param)
	{
		$sind = ($this->ddlSindicato->SelectedValue != "T" ? $this->ddlSindicato->SelectedValue : null);
		//$resultado = Busquedas::empleados($this->dbConexion, $this->ddlTipo->SelectedValue, $this->txtNombre->Text, $sind);
		$resultado = Conexion::Retorna_Registro($this->dbConexion, "sujetos", 
		for($i = 0; $i < count($resultado); $i++)
		{
			$campos = "'" . $this->Request["sufijo"] . "', '" . $resultado[$i]["numero"] . "', '" . 
					$resultado[$i]["nombre"] . " " . $resultado[$i]["paterno"] . " " . 
					$resultado[$i]["materno"] . "', '" . $resultado[$i]["sindicato"] . "', '" . 
					$resultado[$i]["antiguedad"] . "', '" . $resultado[$i]["tipo"] . "', '" .
					$resultado[$i]["importe"] . "', '" . $resultado[$i]["porcentaje"] . "'";
					
			$resultado[$i]["numero"] = "<a href='#' onclick=\"regresa(" . $campos . ")\">" . $resultado[$i]["numero"] . "</a>";
		}
		$this->dgEmpleados->DataSource = $resultado;
		$this->dgEmpleados->dataBind();
	}
}

?>