<?php
Prado::using('System.Web.UI.ActiveControls.*');
include_once('../compartidos/clases/listas.php');
include_once('../compartidos/clases/conexion.php');

class Jefes extends TPage
{
	var $dbConexion;
	
	public function onLoad($param)
	{
		parent::onLoad($param);
		
		$this->dbConexion = Conexion::getConexion($this->Application, "dbac");
		Conexion::createConfiguracion();
		if(!$this->IsPostBack)
		{
			Listas::EnlazaLista($this->dbConexion, 
					"SELECT id_coordinacion, nombre_coordinacion FROM cat_serv_01_coord WHERE activo = 1", $this->addlCoord);
			$this->addlCoord->raiseEvent("OnSelectedIndexChanged", $this->addlCoord, null);
		}
	}
	
	public function addlCoord_Changed($sender, $param)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_area, nombre_area FROM cat_serv_02_areas WHERE activo = 1 and id_coordinacion = " . 
				$this->addlCoord->getSelectedValue(), $this->addlArea);
		$this->addlArea->raiseEvent("OnSelectedIndexChanged", $this->addlArea, null);
	}
	
	public function addlArea_Changed($sender, $param)
	{
		$consulta = "SELECT tratamiento, titular, puesto_titular FROM cat_serv_02_areas WHERE id_area = :id_area";
		$cmdConsulta = $this->dbConexion->createCommand($consulta);
		$cmdConsulta->bindValue(":id_area", $this->addlArea->getSelectedValue());
		$drLector = $cmdConsulta->query();
		if($row = $drLector->read())
		{
			$this->atxtTratamiento->setText($row["tratamiento"]);
			$this->atxtTitular->setText($row["titular"]);
			$this->atxtPuesto->setText($row["puesto_titular"]);
		}
		Listas::EnlazaLista($this->dbConexion, "SELECT id_oficina, oficina FROM cat_serv_03_oficinas WHERE activo = 1", $this->addlOficina);
		$id_oficina = Conexion::Retorna_Campo($this->dbConexion, "cat_serv_02_areas", "id_oficina", 
				array("id_area"=>$this->addlArea->SelectedValue));
		$this->addlOficina->SelectedValue = $id_oficina;		
		$this->addlOficina->raiseEvent("OnSelectedIndexChanged", $this->addlOficina, null);
	}
	
	public function addlOficina_Changed($sender, $param)
	{
		$consulta = "SELECT oficina, direccion FROM cat_serv_03_oficinas WHERE id_oficina = :id_oficina";
		$cmdConsulta = $this->dbConexion->createCommand($consulta);
		$cmdConsulta->bindValue(":id_oficina", $this->addlOficina->SelectedValue);
		$drLector = $cmdConsulta->query();
		if($row = $drLector->read())
		{
			$this->atxtOficina->Text = $row["oficina"];
			$this->atxtDireccion->Text = $row["direccion"];
		}
		else
		{
			$this->atxtOficina->Text = "";
			$this->atxtDireccion->Text = "";
		}
	}
	
	public function atxtOficina_Changed($sender, $param)
	{
		$parametros = array("oficina"=>$this->atxtOficina->getText());
		$busqueda = array("id_oficina"=>$this->addlOficina->getSelectedValue());
		Conexion::Actualiza_Registro_Historial($this->dbConexion, "cat_serv_03_oficinas", $parametros, $busqueda, $this->User->Name);
	}
	
	public function atxtDireccion_Changed($sender, $param)
	{
		$parametros = array("direccion"=>$this->atxtDireccion->getText());
		$busqueda = array("id_oficina"=>$this->addlOficina->getSelectedValue());
		Conexion::Actualiza_Registro_Historial($this->dbConexion, "cat_serv_03_oficinas", $parametros, $busqueda, $this->User->Name);
	}
	
	public function btnGuardar_Click($sender, $param)
	{
		$parametros = array("tratamiento"=>$this->atxtTratamiento->getText(), "titular"=>$this->atxtTitular->getText(), 
				"puesto_titular"=>$this->atxtPuesto->getText(), "id_oficina"=>$this->addlOficina->SelectedValue);
		$busqueda = array("id_area"=>$this->addlArea->getSelectedValue());
		Conexion::Actualiza_Registro_Historial($this->dbConexion, "cat_serv_02_areas", $parametros, $busqueda, $this->User->Name);

		$this->getClientScript()->registerBeginScript("exito", 
			"alert('Los datos del titular de área se actualizaron correctamente.');\n" .
			"document.location.replace(document.location.href);\n");
	}
}
?>
