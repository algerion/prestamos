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
		$camposempjub = "SELECT e.numero, nombre, paterno, materno, cs.sindicato, fec_ingre, df.importe, df.porcentaje FROM ";
		$joinsempjub = " e JOIN catsindicatos cs ON e.sindicato = cs.cve_sindicato JOIN descuentos_fijos df ON e.numero = df.numero WHERE df.concepto = 61";
		$externos = "SELECT e.numero, nombre, paterno, materno, 0 AS sindicato, fec_ingre, 0 AS importe, 0 AS porcentaje FROM externos";
		$consulta = "";
		
		if($this->ddlTipo->SelectedValue == 0 || $this->ddlTipo->SelectedValue == 1)
			$consulta .= $camposempjub . "empledos" . $joinsempjub;
		if($this->ddlTipo->SelectedValue == 0)
			$consulta .= " UNION ";
		if($this->ddlTipo->SelectedValue == 0 || $this->ddlTipo->SelectedValue == 2)
			$consulta .= $camposempjub . "pensionados" . $joinsempjub;
		if($this->ddlTipo->SelectedValue == 0)
			$consulta .= " UNION ";
		if($this->ddlTipo->SelectedValue == 0 || $this->ddlTipo->SelectedValue == 3)
			$consulta .= $externos;
		
		$comando = $this->dbConexion->createCommand($consulta);
		$resultado = $comando->query()->readAll();
		$this->dgDescuentos->DataSource = $resultado;
		$this->dgDescuentos->dataBind();
	}
}

?>