<?php
Prado::using('System.Web.UI.ActiveControls.*');
Prado::using('System.Web.UI.WebControls.TDatePicker');
include_once('../compartidos/clases/listas.php');
include_once('../compartidos/clases/conexion.php');

class Pendientes extends TPage
{
	var $dbConexion, $permisos, $filapar = false;
	var $Consulta, $fte_consultante, $coord_consultante, $area_consultante;

	public function onLoad($param)
	{
		parent::onLoad($param);

		$this->dbConexion = Conexion::getConexion($this->Application, "dbac");
		Conexion::createConfiguracion();

		$comando = $this->dbConexion->createCommand("SET group_concat_max_len = 8000;");
		$comando->execute();

/*		$this->getClientScript()->registerEndScript("marcafila",
			"MarcaFila('" . $this->dgPendientes->ClientID . "');\n");*/

		if(!$this->IsPostBack)
		{
		}
		$this->BindGridConsulta();
	}

	public function AjusteDats()
	{
		$this->datInicial->getClientSide()->setOnDateChanged("datevalid('" . $this->datInicial->getClientID() . "');");
		$this->datFinal->getClientSide()->setOnDateChanged("datevalid('" . $this->datFinal->getClientID() . "');");

		$this->getClientScript()->registerEndScript("ajustedats",
			"datevalid('" . $this->datInicial->getClientID() . "');\n" .
			"datevalid('" . $this->datFinal->getClientID() . "');\n");
	}

	public function Enlaza_Org($sender = null, $param = null)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_org, CASE WHEN LENGTH(nombre_org) <= 80 THEN nombre_org " .
				"ELSE CONCAT(SUBSTRING(nombre_org, 1, 80), '...') END as nombre_org FROM " .
				"dat_sol_03_organizacion ORDER BY nombre_org", $this->addlOrg);
	}

	public function Enlaza_Modulo($sender = null, $param = null)
	{
		if($this->fte_consultante == "")
			$cond = "";
		else
			$cond = " JOIN cat_fte_01_fuentes f ON m.grupo = f.grupo WHERE '" . $this->fte_consultante . "' LIKE CONCAT('%/', f.id_fte, '/%')";
		Listas::EnlazaLista($this->dbConexion, "SELECT m.grupo, m.nombre FROM cat_fte_00_modulos m" . $cond, $this->addlModulo);
	}

	public function Enlaza_Fuente($sender = null, $param = null)
	{
		if($this->fte_consultante == "")
			$cond = "";
		else
			$cond = " WHERE '" . $this->fte_consultante . "' LIKE CONCAT('%/', f.id_fte, '/%')";
		$consulta = "SELECT id_fte, fuente FROM cat_fte_01_fuentes f " . $cond;
		Listas::EnlazaLista($this->dbConexion, $consulta, $this->addlFuente);
		$this->addlFuente->raiseEvent("OnSelectedIndexChanged", $this->addlFuente, new TCallbackEventParameter(null, null));
	}

	public function addlFuente_Changed($sender, $param)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_medio, medio FROM cat_fte_02_medios WHERE id_fte = " . $this->addlFuente->getSelectedValue(), $this->addlMedio);
		if($this->addlMedio->getItemCount() <= 0)
			$this->addlMedio->getItems()->add("SIN MEDIOS DE COMUNICACIÓN ENLAZADOS");
	}

	public function Enlaza_Rubro($sender = null, $param = null)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_rubro, rubro FROM cat_form_00_rubros", $this->addlRubro);
	}

	public function Enlaza_Coord($sender = null, $param = null)
	{
		if($this->coord_consultante == "")
		{
//			$this->area_consultante = Conexion::Retorna_Campo($this->dbConexion, "cat_aut_00_usuarios", "areas_" . $permiso, array("id_usuario"=>$this->User->Name));
			$cond = " JOIN cat_serv_02_areas a ON c.id_coordinacion = a.id_coordinacion WHERE '" . $this->area_consultante . "' LIKE CONCAT('%/', a.id_area, '/%')";
		}
		else if($this->coord_consultante != "*")
			$cond = " JOIN cat_serv_02_areas a ON c.id_coordinacion = a.id_coordinacion WHERE '" . $this->area_consultante . "' LIKE CONCAT('%/', a.id_area, '/%') OR '" . $this->coord_consultante . "' LIKE CONCAT('%/', c.id_coordinacion, '/%')";
		else
			$cond = "";
		Listas::EnlazaLista($this->dbConexion, "SELECT DISTINCT c.id_coordinacion, c.nombre_coordinacion FROM cat_serv_01_coord c " . $cond, $this->addlCoord);
		$this->addlCoord->raiseEvent("OnSelectedIndexChanged", $this->addlCoord, new TCallbackEventParameter(null, null));
	}

	public function Enlaza_Status($sender = null, $param = null)
	{
		$consulta = "SELECT id_status, status FROM cat_status_01_status";
		Listas::EnlazaLista($this->dbConexion, $consulta, $this->addlStatus);
	}

	public function addlCoord_Changed($sender, $param)
	{
		if($this->coord_consultante != "*" && $this->area_consultante != "")
			$cond = " AND '" . $this->area_consultante . "' LIKE CONCAT('%/', id_area, '/%')";
		else
			$cond = "";
		Listas::EnlazaLista($this->dbConexion, "SELECT id_area, nombre_area FROM cat_serv_02_areas WHERE id_coordinacion = " . $this->addlCoord->getSelectedValue() . $cond, $this->addlArea);
		Listas::EnlazaLista($this->dbConexion, "SELECT cp.id_programa, cp.nombre_programa FROM cat_serv_05_programa cp JOIN cat_serv_04_coord_prog ccp ON cp.id_programa = ccp.id_programa WHERE cp.depende_de = 0 AND ccp.id_coordinacion = " . $this->addlCoord->getSelectedValue(), $this->addlPrograma);
		$this->addlPrograma->raiseEvent("OnSelectedIndexChanged", $this->addlPrograma, null);
	}

	public function addlPrograma_Changed($sender, $param)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_programa, nombre_programa FROM cat_serv_05_programa WHERE depende_de = " . $this->addlPrograma->getSelectedValue(), $this->addlSubprograma);
	}

	public function ParamsConsulta()
	{
		$this->Consulta->Limpia_Params();

		if($this->achkAsunto->Checked)
			$this->Consulta->asunto = $this->atxtAsunto->getText();
		if($this->achkSolicitud->Checked)
			$this->Consulta->solicitud = $this->atxtSolicitud->getText();
		if($this->achkRecepcion->Checked)
			$this->Consulta->num_oficio = $this->atxtRecepcion->getText();
		if($this->achkInterno->Checked)
			$this->Consulta->num_registro = $this->atxtInterno->getText();
		if($this->achkDetAsunto->Checked)
			$this->Consulta->detasunto = $this->atxtDetAsunto->getText();
		if($this->achkNombre->Checked)
			$this->Consulta->nombre = $this->atxtNombreDem->getText();
		if($this->achkDireccion->Checked)
			$this->Consulta->direccion = $this->atxtDireccionDem->getText();
		if($this->achkOrg->Checked)
			$this->Consulta->org = $this->addlOrg->getSelectedValue();
		if($this->achkInicial->Checked)
			$this->Consulta->ini = date("Y-m-d", $this->datInicial->getTimeStamp());
		if($this->achkFinal->Checked)
			$this->Consulta->fin = date("Y-m-d", $this->datFinal->getTimeStamp());
		if($this->achkModulo->Checked)
			$this->Consulta->modulo = $this->addlModulo->getSelectedValue();
		if($this->txtFolio->getText() != "")
			$this->Consulta->folio = $this->txtFolio->getText();
		if($this->achkFuente->Checked)
			$this->Consulta->fuente = $this->addlFuente->getSelectedValue();
		if($this->achkMedio->Checked)
			$this->Consulta->medio = $this->addlMedio->getSelectedValue();
		if($this->achkRubro->Checked)
			$this->Consulta->rubro = $this->addlRubro->getSelectedValue();
		if($this->achkCoord->Checked)
			$this->Consulta->coord = $this->addlCoord->getSelectedValue();
		if($this->achkArea->Checked)
			$this->Consulta->area = $this->addlArea->getSelectedValue();
		if($this->achkPrograma->Checked)
			$this->Consulta->programa = $this->addlPrograma->getSelectedValue();
		if($this->achkSubprograma->Checked)
			$this->Consulta->subprograma = $this->addlSubprograma->getSelectedValue();
		if($this->achkStatus->Checked)
			$this->Consulta->status = $this->addlStatus->getSelectedValue();
		if($this->achkAtencion->Checked)
			$this->Consulta->semaforo = $this->addlAtencion->getSelectedValue();
	}

	public function BindGridConsulta()
	{
		$datos = array(
				array("folio"=>"00001", "nombre"=>"Nombre Apellido", "asunto"=>"Solicitud de información", "area"=>"Secretaría Municipal", "turnada"=>"12/10/2014", "plazo"=>"15 días hábiles", "respuestas"=>"", "url"=>"about:blank"),
				array("folio"=>"00002", "nombre"=>"Nombre Apellido", "asunto"=>"Solicitud de información", "area"=>"Secretaría Técnica", "turnada"=>"12/10/2014", "plazo"=>"15 días hábiles", "respuestas"=>"", "url"=>"about:blank"),
				array("folio"=>"00003", "nombre"=>"Nombre Apellido", "asunto"=>"Solicitud de información", "area"=>"Presidencia Municipal", "turnada"=>"13/10/2014", "plazo"=>"15 días hábiles", "respuestas"=>"", "url"=>"about:blank"),
				array("folio"=>"00004", "nombre"=>"Nombre Apellido", "asunto"=>"Solicitud de información", "area"=>"Transparencia", "turnada"=>"13/10/2014", "plazo"=>"15 días hábiles", "respuestas"=>"", "url"=>"about:blank")
			);
		$this->dgPendientes->dataSource = $datos;
		$this->dgPendientes->dataBind();
	}
	
	public function dgPendientes_DataBound($sender, $param)
	{
		$item = $param->Item;
		
	}

	public function dgPendientes_PageIndexChanged($sender, $param)
	{
		$this->dgPendientes->setCurrentPageIndex($param->getNewPageIndex());
		$this->BindGridConsulta();
	}

	public function achkHabilitar($sender, $param)
	{
		if($param->getCallbackParameter() != null)
		{
			$this->{$param->getCallbackParameter()}->Enabled = $sender->Checked;
			if(strtolower(substr($param->getCallbackParameter(), 0, 4)) == "apnl")
				$this->{$param->getCallbackParameter()}->render($param->getNewWriter());
		}
		$this->Borra_Mensaje($sender, $param);
	}

	public function btnBuscar_Click($sender, $param)
	{
		$this->dgPendientes->setCurrentPageIndex(0);
		$this->BindGridConsulta();
//		$this->apnlPendientes->render($param->getNewWriter());
	}

	public function cbRecargaGrid_Callback($sender, $param)
	{
		$this->BindGridConsulta();
		$this->apnlPendientes->render($param->getNewWriter());
	}

	public function Borra_Mensaje($sender, $param)
	{
		$this->dgPendientes->reset();
		$this->apnlPendientes->render($param->getNewWriter());
	}
}
?>