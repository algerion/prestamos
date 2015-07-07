<?php
Prado::using('System.Web.UI.ActiveControls.*');
//Prado::using('System.Util.*'); //TVarDump
include_once('../compartidos/clases/listas.php');
include_once('../compartidos/clases/conexion.php');
include_once('../compartidos/clases/envia_mail.php');
include_once('../compartidos/clases/charset.php');

class solicitudes extends TPage
{
	var $dbConexion;
	var $anexo;

	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->dbConexion = Conexion::getConexion($this->Application, "dbac");
		Conexion::createConfiguracion();
		if(!$this->IsPostBack)
		{
			$this->Ddls();
			if($this->Request["id"] != null)
				$this->rellena_datos();
		}
	}

	public function cbRegargaGrid_Callback($sender, $param)
	{
		$this->dgSolicitudes_DataBind($this);
		$this->apnlSolicitudes->render($param->getNewWriter());
	}

	public function Ddls()
	{
		$this->Enlaza_Agencia();
		$this->Enlaza_Coord();
	}

	public function Enlaza_Agencia($sender = null, $param = null)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_ag, nombre_ag FROM agencias ORDER BY id_ag", $this->addlAgencia);
		$this->addlAgencia->raiseEvent("OnSelectedIndexChanged", $this->addlAgencia, new TCallbackEventParameter(null, null));
	}
	
	public function Enlaza_Coord($sender = null, $param = null)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_coordinacion, coordinacion FROM coordinaciones WHERE activo = 1", $this->addlCoord);
		$this->addlCoord->raiseEvent("OnSelectedIndexChanged", $this->addlCoord, new TCallbackEventParameter(null, null));
	}

	public function addlAgencia_Changed($sender, $param)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_col, nombre_col FROM colonias WHERE id_ag = '" . $this->addlAgencia->getSelectedValue() . "'", $this->addlColonia);
	}

	public function addlCoord_Changed($sender, $param)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_area, area FROM areas WHERE activo = 1 AND id_coordinacion = " . $this->addlCoord->getSelectedValue(), $this->addlArea);
		$this->addlArea->raiseEvent("OnSelectedIndexChanged", $this->addlArea, new TCallbackEventParameter(null, null));
	}

	public function fulArchivo_Upload($sender, $param)
	{
		if($sender->HasFile)
		{
			$this->anexo = date('Ymd_his ');
			$ruta = "upload/" . $this->anexo . Charset::CambiaCharset($sender->FileName);
			$this->anexo .= $sender->FileName;
			if(!$sender->saveAs($ruta))
				$this->getClientScript()->registerBeginScript("error",
						"alert('El anexo no pudo cargarse.');\n");
		}
	}
	
	public function registro(){
		return array(
				"solicitante"=>$this->txtNombre->Text, 
				"email"=>$this->txtEMail->Text, 
				"id_colonia"=>$this->addlColonia->SelectedValue, 
				"direccion"=>$this->txtDireccion->Text, 
				"telefono"=>$this->txtTelefono->Text, 
				"asunto"=>$this->txtAsunto->Text, 
				"id_area"=>$this->addlArea->SelectedValue, 
				"anexo"=>$this->anexo,
				"recepcion"=>date("Y-m-d H:i:s", time()),
				"id_usuario"=>$this->User->Name, 
				"activo"=>1);
	}

	public function btnGuardar_Click($sender, $param)
	{
		$parametros = $this->registro();
		
		if($this->Request["id"] == null)
		{
			if(Conexion::Inserta_Registro_Historial($this->dbConexion, "solicitudes", $parametros, $this->User->Name))
			{
				//Envia_Mail::getConexion($this->dbConexion, $id_sold);

				$this->getClientScript()->registerBeginScript("exito",
					"alert('La solicitud se registró correctamente.');\n" .
					"document.location.replace(document.location.href);\n"); //Se usa replace para usar los mismos parámetros de la URL
			}
			else
				$this->getClientScript()->registerBeginScript("fallo",
					"alert('No se pudo completar el registro de la solicitud.');\n");
		}
		else
		{
			$busqueda = array("id_solicitud"=>$this->Request["id"]);
			Conexion::Actualiza_Registro_Historial($this->dbConexion, "solicitudes", $parametros, $busqueda, $this->User->Name);
			$this->getClientScript()->registerBeginScript("exito",
				"alert('La solicitud se actualizó correctamente.');\n" .
				"opener.recarga_grid();\n" .
				"close();\n");
		}
	}

	public function rellena_datos()
	{
		if(!array_search("edit", $this->User->Roles))
			$this->Response->redirect("index.php?page=Home");

		$consulta = "SELECT s.solicitante, s.email, cc.id_ag, s.id_colonia, s.direccion, s.telefono, s.asunto, a.id_coordinacion, s.id_area, s.anexo " .
				"FROM solicitudes s LEFT JOIN colonias cc ON s.id_colonia = cc.id_col LEFT JOIN areas a ON s.id_area = a.id_area WHERE s.id_solicitud = :id AND s.activo = 1";
		$cmdConsulta = $this->dbConexion->createCommand($consulta);
		$cmdConsulta->bindValue(":id", $this->Request["id"]);
		$drLector = $cmdConsulta->query();
		if($row = $drLector->read())
		{
			$this->txtNombre->Text = $row["solicitante"];
			$this->txtEMail->Text = $row["email"];
			Listas::setValorSelected($this->addlAgencia, $row["id_ag"]);
			Listas::EnlazaLista($this->dbConexion, "SELECT id_col, nombre_col FROM colonias WHERE id_ag = '" . $row["id_ag"] . "'", $this->addlColonia);
			Listas::setValorSelected($this->addlColonia, $row["id_colonia"]);
			$this->txtDireccion->Text = $row["direccion"];
			$this->txtTelefono->Text = $row["telefono"];
			$this->txtAsunto->Text = $row["asunto"];
			Listas::setValorSelected($this->addlCoord, $row["id_coordinacion"]);
			$this->addlCoord->raiseEvent("OnSelectedIndexChanged", $this->addlCoord, null);
			Listas::setValorSelected($this->addlArea, $row["id_area"]);
			if($row["anexo"] != null)
				$this->hlAnexo->NavigateUrl = "upload/" . $row["anexo"];
			$this->btnGuardar->setText("Modificar");
		}
		else
			$this->getClientScript()->registerBeginScript("inexistente",
				"alert('No se encuentra la solicitud en la base de datos.');\n" .
					"document.location.replace(document.location.href);\n");
	}
}
?>