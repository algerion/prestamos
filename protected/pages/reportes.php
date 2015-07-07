<?php
Prado::using('System.Web.UI.ActiveControls.*');
Prado::using('System.Web.UI.WebControls.TDatePicker');
include_once('../compartidos/clases/listas.php');
include_once('../compartidos/clases/conexion.php');
include_once('protected/clases/consultas.php');
include_once('protected/clases/subconsultas.php');

class Reportes extends TPage
{
	var $dbConexion, $Consulta, $permisos, $filapar = false;
	var $fte_consultante, $coord_consultante, $area_consultante;

	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->dbConexion = Conexion::getConexion($this->Application, "dbac");
		Conexion::createConfiguracion();
		//Verifica la coordinación y el área correspondiente a la persona que realiza la consulta
		$this->Consulta = new Consultas("dbac");
		$this->fte_consultante = Conexion::Retorna_Campo($this->dbConexion, "cat_aut_00_usuarios", "ftes_r", array("id_usuario"=>$this->User->Name));
		$this->coord_consultante = Subconsultas::Coords_Todas($this->dbConexion, $this->User->Name);
		$this->area_consultante = Subconsultas::Areas_Todas($this->dbConexion, $this->User->Name);
		if($this->coord_consultante == "" && $this->area_consultante == "")
			$this->Response->Redirect("index.php?page=Home");

		if(!$this->IsPostBack)
		{
			$this->AjusteDats();
			$this->Enlaza_Agencia();
			$this->Enlaza_Org();
			$this->Enlaza_Modulo();
			$this->Enlaza_Fuente();
			$this->Enlaza_Rubro();
			$this->Enlaza_Coord();
			$this->Enlaza_Status();
			/*$this->datInicial->setTimeStamp(time());
			$this->datFinal->setTimeStamp(time());*/
		}
		//$this->BindGridConsulta();
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
		Listas::EnlazaLista($this->dbConexion, "SELECT id_org, CASE WHEN LENGTH(nombre_org) <= 80 THEN nombre_org ELSE CONCAT(SUBSTRING(nombre_org, 1, 80), '...') END as nombre_org FROM dat_sol_03_organizacion", $this->addlOrg);
	}

	public function Enlaza_Agencia($sender = null, $param = null)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_ag, nombre_ag FROM cat_dom_01_agencia", $this->addlAgencia);
	}

	public function Enlaza_Modulo($sender = null, $param = null)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT grupo, nombre FROM cat_fte_00_modulos", $this->addlModulo);
	}

	public function Enlaza_Fuente($sender = null, $param = null)
	{
		if($this->fte_consultante == "")
			$cond = "";
		else
			$cond = " WHERE '" . $this->fte_consultante . "' LIKE CONCAT('%/', f.id_fte, '/%')";
		$activar = new Acceso("dbac");
		$consulta = "SELECT id_fte, fuente FROM cat_fte_01_fuentes f " . $cond;
		if(!array_search("sold", $this->User->Roles) && array_search("empl", $this->User->Roles)))
			$consulta .= " WHERE grupo = 'E'";
		Listas::EnlazaLista($this->dbConexion, $consulta, $this->addlFuente);
		$this->addlFuente->raiseEvent("OnSelectedIndexChanged", $this->addlFuente, new TCallbackEventParameter(null, null));
	}

	public function addlFuente_Changed($sender, $param)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_medio, medio FROM cat_fte_02_medios WHERE id_fte = " . $this->addlFuente->getSelectedValue(), $this->addlMedio);
		if($this->addlMedio->getItemCount() <= 0)
		{
//			$this->addlMedio->getItems()->clear();
			$this->addlMedio->getItems()->add("SIN MEDIOS DE COMUNICACIÓN ENLAZADOS");
		}
	}

	public function Enlaza_Rubro($sender = null, $param = null)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_rubro, rubro FROM cat_form_00_rubros", $this->addlRubro);
	}

	public function Enlaza_Coord($sender = null, $param = null)
	{
		if($this->coord_consultante == "")
		{
//			$this->area_consultante = Conexion::Retorna_Campo($this->dbConexion, "cat_aut_00_usuarios", "areas", array("id_usuario"=>$this->User->Name));
			$cond = " JOIN cat_serv_02_areas a ON c.id_coordinacion = a.id_coordinacion WHERE '" . $this->area_consultante . "' LIKE CONCAT('%/', a.id_area, '/%') AND ";
		}
		else if($this->coord_consultante != "*" )
			$cond = " WHERE '" . $this->coord_consultante . "' LIKE CONCAT('%/', c.id_coordinacion, '/%') AND ";
		else
			$cond = "WHERE ";
			
		$cond .= " activo = 1";
		Listas::EnlazaLista($this->dbConexion, "SELECT c.id_coordinacion, c.nombre_coordinacion FROM cat_serv_01_coord c " . $cond, $this->addlCoord);
		$this->addlCoord->raiseEvent("OnSelectedIndexChanged", $this->addlCoord, new TCallbackEventParameter(null, null));
	}

	public function addlCoord_Changed($sender, $param)
	{
		if($this->coord_consultante != "*" && $this->area_consultante != "")
			$cond = " AND '" . $this->area_consultante . "' LIKE CONCAT('%/', id_area, '/%')";
		else
			$cond = "";
		$cond .= " AND activo = 1";
		Listas::EnlazaLista($this->dbConexion, "SELECT id_area, nombre_area FROM cat_serv_02_areas WHERE id_coordinacion = " . $this->addlCoord->getSelectedValue() . $cond, 
				$this->addlArea);
		Listas::EnlazaLista($this->dbConexion, "SELECT cp.id_programa, cp.nombre_programa FROM cat_serv_05_programa cp JOIN cat_serv_04_coord_prog ccp ON cp.id_programa = ccp.id_programa WHERE cp.depende_de = 0 AND ccp.id_coordinacion = " . $this->addlCoord->getSelectedValue(), $this->addlPrograma);
		$this->addlPrograma->raiseEvent("OnSelectedIndexChanged", $this->addlPrograma, null);
	}

	public function addlPrograma_Changed($sender, $param)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_programa, nombre_programa FROM cat_serv_05_programa WHERE depende_de = " . $this->addlPrograma->getSelectedValue(), $this->addlSubprograma);
	}

	public function Enlaza_Status($sender = null, $param = null)
	{
		$consulta = "SELECT id_status, status FROM cat_status_01_status";
		Listas::EnlazaLista($this->dbConexion, $consulta, $this->addlStatus);
	}

	public function ParamsConsulta()
	{
		$this->Consulta->Limpia_Params();

		if($this->achkAsunto->Checked)
			$this->Consulta->asunto = $this->atxtAsunto->getText();
		if($this->achkSolicitud->Checked)
			$this->Consulta->solicitud = $this->atxtSolicitud->getText();
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
		if($this->achkAgencia->Checked)
			$this->Consulta->agencia = $this->addlAgencia->getSelectedValue();
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
		$this->ParamsConsulta();
		$this->dgPendientes->dataSource = $this->Consulta->DatosSemaforo();
		$this->dgPendientes->dataBind();
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

	public function Borra_Mensaje($sender, $param)
	{
		$this->dgPendientes->reset();
		$this->apnlPendientes->render($param->getNewWriter());
	}

	public function btnPrevisualizar_Click($sender, $param)
	{
		$this->BindGridConsulta();
//		$this->apnlPendientes->render($param->getNewWriter());
	}

	public function btnGrafica_Click($sender, $param)
	{
		$this->ParamsConsulta();
		$this->getClientScript()->registerBeginScript("nuevaventana",
			"open('index.php?pdfs=graficas" . $this->Consulta->Params_Url() . "', 'reporte');\n");
	}

	public function btnReporte_Click($sender, $param)
	{
		$this->ParamsConsulta();
		$this->getClientScript()->registerBeginScript("nuevaventana",
			"open('index.php?pdfs=semaforo" . $this->Consulta->Params_Url() . "', 'reporte');\n");
	}

	public function btnExcel_Click($sender, $param)
	{
		$this->ParamsConsulta();
/*		$this->getClientScript()->registerBeginScript("nuevaventana",
			"open('index.php?page=reporte_excel" . $this->Consulta->Params_Url() . "');\n");*/
		$this->Response->redirect("index.php?page=reporte_excel" . $this->Consulta->Params_Url());
	}
}
?>