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
		$sindicatos = array_merge(array(array("cve_sindicato"=>"T", "sindicato"=>"Todos")), $result);
		$this->ddlSindicato->DataSource = $sindicatos;
		$this->ddlSindicato->dataBind();
	}
	
	public function btnBuscar_Click($sender, $param)
	{
		$camposempjub = "SELECT e.numero, nombre, paterno, materno, cs.sindicato, fec_ingre, TIMESTAMPDIFF(YEAR, fec_ingre, CURDATE()) AS antiguedad, " . 
				"IFNULL(df.importe, 0) AS importe, IFNULL(df.porcentaje, 0) AS porcentaje";
		$joinsempjub = " e LEFT JOIN catsindicatos cs ON e.sindicato = cs.cve_sindicato LEFT JOIN descuentos_fijos df ON e.numero = df.numero AND df.concepto = 61";
		$externos = "SELECT e.numero, nombre, paterno, materno, 0 AS sindicato, fec_ingre, 0 AS antiguedad, 0 AS importe, 0 AS porcentaje, 'E' AS tipo FROM externos e";
		$consulta = "";
		$where = " WHERE (e.numero LIKE :busca OR e.nombre LIKE :busca OR e.paterno LIKE :busca OR e.materno LIKE :busca) ";
		$sind = "";
		
		if($this->ddlSindicato->SelectedValue != "T")
			$sind .= "AND e.sindicato = :sindicato ";
		
		if($this->ddlTipo->SelectedValue == 0 || $this->ddlTipo->SelectedValue == 1)
			$consulta .= $camposempjub . ", 'A' AS tipo FROM empleados" . $joinsempjub . $where . $sind;
		if($this->ddlTipo->SelectedValue == 0)
			$consulta .= " UNION ";
		if($this->ddlTipo->SelectedValue == 0 || $this->ddlTipo->SelectedValue == 2)
			$consulta .= $camposempjub . ", 'J' AS tipo FROM pensionados" . $joinsempjub . $where . $sind;
		if($this->ddlTipo->SelectedValue == 0)
			$consulta .= " UNION ";
		if($this->ddlTipo->SelectedValue == 0 || $this->ddlTipo->SelectedValue == 3)
			$consulta .= $externos . $where;
		$consulta .= " LIMIT 1000";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue("busca", "%" . $this->txtNombre->Text . "%");
		if($this->ddlSindicato->SelectedValue != "")
			$comando->bindValue("sindicato", $this->ddlSindicato->SelectedValue);
		$resultado = $comando->query()->readAll();
		for($i = 0; $i < count($resultado); $i++)
		{
			$campos = "'" . $this->Request["sufijo"] . "', '" . $resultado[$i]["numero"] . "', '" . $resultado[$i]["nombre"] . " " . $resultado[$i]["paterno"] . " " . 
					$resultado[$i]["materno"] . "', '" . $resultado[$i]["sindicato"] . "', '" . $resultado[$i]["antiguedad"] . "', '" . $resultado[$i]["tipo"] . "'";
					
			$resultado[$i]["numero"] = "<a href='#' onclick=\"regresa(" . $campos . ")\">" . $resultado[$i]["numero"] . "</a>";
		}
		$this->dgEmpleados->DataSource = $resultado;
		$this->dgEmpleados->dataBind();
	}
}

?>