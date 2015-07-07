<?php
Prado::using('System.Web.UI.ActiveControls.*');
include_once('../compartidos/clases/listas.php');
include_once('../compartidos/clases/conexion.php');

class Oficio_Can extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->dbConexion = Conexion::getConexion($this->Application, "dbac");
		Conexion::createConfiguracion();
		$this->dgConsulta->setEmptyTemplate(new TTemplate('<com:TLiteral Text="No se encontraron solicitudes" />', null));

		$this->BindGridConsulta();
		if(!$this->IsPostBack)
		{
			$this->EnlazaCoord();
			Listas::EnlazaLista($this->dbConexion, "SELECT id_razon, razon FROM cat_form_00_razon", $this->ddlRazon);
			$this->Redaccion();
			$this->getClientScript()->registerEndScript("spell",
				"CreateGoogie('" . $this->txtRedaccion->ClientID . "');\n");
		}
	}

	public function BindGridConsulta()
	{
		$consulta = "SELECT t.id_area_ccp, CONCAT(ca.tratamiento, ' ', ca.titular, ', ', ca.puesto_titular) AS director, cr.razon FROM dat_txt_02_ccp t JOIN cat_serv_02_areas ca ON t.id_area_ccp = ca.id_area JOIN cat_form_00_razon cr ON t.id_razon = cr.id_razon WHERE ca.activo = 1 AND t.id_solicitud = :id_solicitud AND t.id_area = :id_area";
		$cmdConsulta = $this->dbConexion->createCommand($consulta);
		$cmdConsulta->bindValue(":id_solicitud", $this->Request["id"]);
		$cmdConsulta->bindValue(":id_area", $this->Request["area"]);
		$drLector = $cmdConsulta->query();
		$arrConsulta = $drLector->readAll();
		$this->dgConsulta->dataSource = $arrConsulta;
		$this->dgConsulta->dataBind();
	}

	public function EnlazaCoord()
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_coordinacion, nombre_coordinacion FROM cat_serv_01_coord WHERE activo = 1", $this->ddlCoord);
		$this->ddlCoord->raiseEvent("OnSelectedIndexChanged", $this->ddlCoord, new TCallbackEventParameter(null, null));
	}

	public function Redaccion()
	{
		$consulta = "SET lc_time_names = 'es_MX'";
		$cmdConsulta = $this->dbConexion->createCommand($consulta);
		$cmdConsulta->execute();

		$consulta = "SELECT num_oficio, texto FROM dat_txt_01_oficios WHERE id_solicitud = :id_solicitud AND id_area = :id_area";
		$cmdConsulta = $this->dbConexion->createCommand($consulta);
		$cmdConsulta->bindValue(":id_solicitud", $this->Request["id"]);
		$cmdConsulta->bindValue(":id_area", $this->Request["area"]);
		$drLector = $cmdConsulta->query();
		if($row = $drLector->read())
		{
			$this->txtNumOficio->Text = $row["num_oficio"];
			$this->txtRedaccion->Text = $row["texto"];
			$consulta = "SELECT CONCAT(ca.tratamiento, ' ', ca.titular, ' - ', ca.puesto_titular) AS dirigido, ca.id_area FROM cat_serv_02_areas ca WHERE ca.id_area = :id_area";
		}
		else
		{
			$consulta = "SELECT DATE_FORMAT(s.turnada, '%e de %M') AS turnada, YEAR(s.turnada) AS yturnada, se.nombre, cg.cargo, co.nombre_org, GROUP_CONCAT(a.asunto SEPARATOR '; ') AS asunto, CONCAT(ca.tratamiento, ' ', ca.titular, ' - ', ca.puesto_titular) AS dirigido, a.id_area FROM dat_sol_04_solicitudes s JOIN dat_sol_01_solicitantes se ON s.id_solicitante = se.id_solicitante JOIN dat_sol_05_asuntos a ON s.id_solicitud = a.id_solicitud JOIN dat_sol_02_cargos cg ON se.id_cargo = cg.id_cargo JOIN dat_sol_03_organizacion co ON se.id_org = co.id_org JOIN cat_serv_02_areas ca ON a.id_area = ca.id_area WHERE s.id_solicitud = :id AND a.id_area = :id_area AND s.activo = 1 AND a.activo = 1 GROUP BY s.id_solicitud, a.id_area";
		}
		$cmdConsulta = $this->dbConexion->createCommand($consulta);
		if($this->txtRedaccion->Text == "")
			$cmdConsulta->bindValue(":id", $this->Request["id"]);
		$cmdConsulta->bindValue(":id_area", $this->Request["area"]);
		$drLector = $cmdConsulta->query();

		if($row = $drLector->read())
		{
			$this->lblDirigido->Text = "Dirigido a: " . $row["dirigido"];
			if($this->txtRedaccion->Text == "")
			{
				$arrTextos = $this->Textos();
				$this->txtRedaccion->Text = $arrTextos[0] . " " . $row["turnada"] . ($row["yturnada"] == date("Y", time()) ? " del año en curso" : " de " . $row["yturnada"]) . " " . $arrTextos[1] . " " . $row["nombre"] . ($row["cargo"] != null ? ", " . $row["cargo"] : "") . (($row["nombre_org"] != null && $row["nombre_org"] != "Libre") ? " de " . $row["nombre_org"] : "") . " " . $arrTextos[2] . " " . $row["asunto"] . ". " . $arrTextos[3] . "\n\n" . $arrTextos[4];
			}
		}
	}

	public function Textos()
	{
		$arrTextos = array(
			"Por este conducto, me permito enviar a usted escrito de fecha",
			"suscrito por",
			"mediante el cual solicita",
			"De igual manera, le agradecería envíe por escrito la respuesta a su solicitud, marcando copia de la misma " .
				"a esta área a mi cargo.",
			"Sin otro asunto en particular, y no dudando de la atención que sirva brindar al presente quedo de usted " .
				"no sin antes enviarle un cordial saludo."
		);
		$consulta = "SELECT id_parte, texto FROM cat_txt_00_oficio";
		$cmdConsulta = $this->dbConexion->createCommand($consulta);
		$drLector = $cmdConsulta->query();
		while($row = $drLector->read())
			$arrTextos[$row["id_parte"] - 1] = $row["texto"];

		return $arrTextos;
	}

	public function albtnCoord_Callback($sender, $param)
	{
		$this->EnlazaCoord();
	}

	public function ddlCoord_Changed($sender, $param)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_area, nombre_area FROM cat_serv_02_areas WHERE activo = 1 AND id_coordinacion = " . $this->ddlCoord->getSelectedValue(), $this->ddlArea);
		$this->ddlArea->raiseEvent("OnSelectedIndexChanged", $this->ddlArea, new TCallbackEventParameter(null, null));
	}

	public function ddlArea_Changed($sender, $param)
	{
		$cmdConsulta = $this->dbConexion->createCommand("SELECT CONCAT(tratamiento, ' ', titular) AS titular, puesto_titular FROM cat_serv_02_areas WHERE id_area = " . $this->ddlArea->getSelectedValue());
		$drLector = $cmdConsulta->query();
		foreach($drLector as $x)
		{
			$this->lblEncargado->setText("Titular: " . $x['titular'] . ".");
			$this->lblPuestoEnc->setText($x['puesto_titular']);
		}
	}

	public function abtnAgregar_Callback($sender, $param)
	{
		if($this->Request["area"] != $this->ddlArea->getSelectedValue())
		{
			$consulta = "SELECT id_solicitud FROM dat_txt_02_ccp WHERE id_solicitud = :id_solicitud AND id_area = :id_area AND id_area_ccp = :id_area_ccp";

			$cmdConsulta = $this->dbConexion->createCommand($consulta);
			$cmdConsulta->bindValue(":id_solicitud", $this->Request["id"]);
			$cmdConsulta->bindValue(":id_area", $this->Request["area"]);
			$cmdConsulta->bindValue(":id_area_ccp", $this->ddlArea->getSelectedValue());
			$drLector = $cmdConsulta->query();
			if(!$drLector->read())
			{
				$parametros = array("id_solicitud"=>$this->Request["id"], "id_area"=>$this->Request["area"], "id_area_ccp"=>$this->ddlArea->getSelectedValue(), "id_razon"=>$this->ddlRazon->getSelectedValue());
				Conexion::Inserta_Registro_Historial($this->dbConexion, "dat_txt_02_ccp", $parametros, $this->User->Name);
			}
			else
				$this->getPage()->getCallbackClient()->callClientFunction("mensaje", array("Se ha intentado dirigir una segunda copia de la solicitud a un área a la que previamente se había dirigido."));
		}
		else
			$this->getPage()->getCallbackClient()->callClientFunction("mensaje", array("Se ha intentado dirigir una copia de la solicitud al área a la que se turnó originalmente."));

		$this->BindGridConsulta();
		$this->apnlConsulta->render($param->getNewWriter());
	}

	public function btnBorraDest_Callback($sender, $param)
	{
		$this->getPage()->getCallbackClient()->callClientFunction("desea_borrar", array($sender->Parent->Parent->Data['id_area_ccp']));
	}

	public function cbBorraDest_Callback($sender, $param)
	{
		$busqueda = array("id_solicitud"=>$this->Request["id"], "id_area"=>$this->Request["area"], "id_area_ccp"=>$param->CallbackParameter->borrar);
		Conexion::Elimina_Registro_Historial($this->dbConexion, "dat_txt_02_ccp", $busqueda, $this->User->Name);
		$this->BindGridConsulta();
		$this->apnlConsulta->render($param->getNewWriter());
	}

	public function btnGenerar_Click($sender, $param)
	{
		$exito = true;

		$consulta = "SELECT id_solicitud FROM dat_txt_01_oficios WHERE id_solicitud = :id_solicitud AND id_area = :id_area";
		$cmdConsulta = $this->dbConexion->createCommand($consulta);
		$cmdConsulta->bindValue(":id_solicitud", $this->Request["id"]);
		$cmdConsulta->bindValue(":id_area", $this->Request["area"]);
		$drLector = $cmdConsulta->query();
		if($row = $drLector->read())
		{
			$parametros = array("num_oficio"=>$this->txtNumOficio->Text, "texto"=>$this->txtRedaccion->getText(), "fecha"=>date("Y-m-d", time()), "activo"=>1);
			$busqueda = array("id_solicitud"=>$this->Request["id"], "id_area"=>$this->Request["area"]);
			Conexion::Actualiza_Registro_Historial($this->dbConexion, "dat_txt_01_oficios", $parametros, $busqueda, $this->User->Name);
		}
		else
		{
			$parametros = array("id_solicitud"=>$this->Request["id"], "id_area"=>$this->Request["area"], "num_oficio"=>$this->txtNumOficio->Text, "texto"=>$this->txtRedaccion->getText(), "fecha"=>date("Y-m-d", time()), "activo"=>1);
			if(Conexion::Inserta_Registro_Historial($this->dbConexion, "dat_txt_01_oficios", $parametros, $this->User->Name) == 0)
				$exito = false;
		}

		$this->getClientScript()->registerBeginScript("nuevaventana",
			"open('index.php?pdfs=oficio_can&id=" . $this->Request["id"] . "&area=" . $this->Request["area"] . "', 'oficio');\n");
	}
}
?>
