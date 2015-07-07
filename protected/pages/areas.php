<?php
Prado::using('System.Web.UI.ActiveControls.*');
include_once('../compartidos/clases/listas.php');
include_once('../compartidos/clases/conexion.php');

class Areas extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);

		$this->dbConexion = Conexion::getConexion($this->Application, "dbac");
		Conexion::createConfiguracion();
		if(!$this->IsPostBack)
		{
			$this->Enlaza_Coordinacion();
		}
	}

	public function Enlaza_Coordinacion()
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_coordinacion, nombre_coordinacion FROM cat_serv_01_coord WHERE activo = 1 ORDER BY id_coordinacion",
				$this->alstCoordinacion);
		$this->alstCoordinacion->raiseEvent("OnSelectedIndexChanged", $this->alstCoordinacion, new TCallbackEventParameter(null, null));
	}

	public function alstCoordinacion_Changed($sender, $param)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_area, nombre_area FROM cat_serv_02_areas WHERE id_coordinacion = '" .
				$this->alstCoordinacion->getSelectedValue() . "' AND activo = 1 ORDER BY id_coordinacion, id_area", $this->alstArea);
		$this->alstArea->raiseEvent("OnSelectedIndexChanged", $this->alstArea, new TCallbackEventParameter(null, null));
	}

	public function abtnCoordinacion_Nueva_Callback($sender, $param)
	{
		$this->getPage()->getCallbackClient()->callClientFunction("pregunta", array("coordinacion", "nueva", "", ""));
	}

	public function abtnCoordinacion_Modificar_Callback($sender, $param)
	{
		$this->getPage()->getCallbackClient()->callClientFunction("pregunta", array("coordinacion", "modificada",
				$this->alstCoordinacion->getSelectedValue(), $this->alstCoordinacion->SelectedItem->getText()));
	}

	public function abtnCoordinacion_Quitar_Callback($sender, $param)
	{
		$this->getPage()->getCallbackClient()->callClientFunction("pregunta", array("coordinacion", "quitar", $this->alstCoordinacion->getSelectedValue(), ""));
	}

	public function abtnArea_Nueva_Callback($sender, $param)
	{
		$this->getPage()->getCallbackClient()->callClientFunction("pregunta", array("area", "nueva", "", ""));
	}

	public function abtnArea_Modificar_Callback($sender, $param)
	{
		$this->getPage()->getCallbackClient()->callClientFunction("pregunta", array("area", "modificada",
				$this->alstArea->getSelectedValue(), $this->alstArea->SelectedItem->getText()));
	}

	public function abtnArea_Quitar_Callback($sender, $param)
	{
		$this->getPage()->getCallbackClient()->callClientFunction("pregunta", array("area", "quitar", $this->alstArea->getSelectedValue(), ""));
	}

	public function cbOperaciones_Callback($sender, $param)
	{
		if($param->CallbackParameter->tabla == "coordinacion")
		{
			$tabla = "cat_serv_01_coord";
			$campo = "nombre_coordinacion";
			$id_tabla = "id_coordinacion";
			$parametros = array($campo=>$param->CallbackParameter->valor);
		}
		else if($param->CallbackParameter->tabla == "area")
		{
			$tabla = "cat_serv_02_areas";
			$campo = "nombre_area";
			$id_tabla = "id_area";
			$parametros = array("id_coordinacion"=>$this->alstCoordinacion->getSelectedValue(),
					$campo=>$param->CallbackParameter->valor);
		}
		else
			$this->getPage()->getCallbackClient()->callClientFunction("msg", array("Error de nombre de tabla."));

		if($param->CallbackParameter->oper == "quitar")
		{
			$campo = "activo";
			$param->CallbackParameter->valor = "0";
		}

		if($tabla != null)
		{
			if($param->CallbackParameter->tabla == "coordinacion" &&
					Conexion::Retorna_Campo($this->dbConexion, $tabla, $id_tabla,
					array($campo=>$param->CallbackParameter->valor)) != "" && 
					$param->CallbackParameter->oper != "quitar")
				$this->getPage()->getCallbackClient()->callClientFunction("msg", array("El nombre de coordinación ya existe."));
			else if($param->CallbackParameter->tabla == "area" &&
					Conexion::Retorna_Campo($this->dbConexion, $tabla, $id_tabla,
					array($campo=>$param->CallbackParameter->valor, "id_area"=>$this->alstArea->getSelectedValue())) != "" && $param->CallbackParameter->oper != "quitar")
				$this->getPage()->getCallbackClient()->callClientFunction("msg", array("El nombre del área ya existe."));
			else
			{
				if($param->CallbackParameter->oper == "nueva")
					Conexion::Inserta_Registro_Historial($this->dbConexion, $tabla, array_merge($parametros, array("activo"=>"1")), $this->User->Name);
				else 
					Conexion::Actualiza_Registro_Historial($this->dbConexion, $tabla, array($campo=>$param->CallbackParameter->valor),
							array($id_tabla=>$param->CallbackParameter->valor_id), $this->User->Name);
				$this->getPage()->getCallbackClient()->callClientFunction("msg", array("Se completó la operación."));
			}
		}
		else
			$this->getPage()->getCallbackClient()->callClientFunction("msg", array("Esa tabla."));

		$idx_coord = $this->alstCoordinacion->getSelectedValue();
		$idx_area = $this->alstArea->getSelectedValue();
		$this->Enlaza_Coordinacion();
		/*Listas::EnlazaLista($this->dbConexion, "SELECT id_coordinacion, nombre_coordinacion FROM cat_serv_01_coord WHERE activo = 1 ORDER BY id_coordinacion",
				$this->alstCoordinacion);*/
		try
		{
			$this->alstCoordinacion->setSelectedValue($idx_coord);
		}
		catch(Exception $e)
		{
		}
		$this->alstCoordinacion->raiseEvent("OnSelectedIndexChanged", $this->alstCoordinacion, new TCallbackEventParameter(null, null));
		try
		{
			$this->alstArea->setSelectedValue($idx_area);
		}
		catch(Exception $e)
		{
		}
	}
}
?>
