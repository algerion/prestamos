<?php
Prado::using('System.Web.UI.ActiveControls.*');
include_once('../compartidos/clases/acceso.php');
include_once('../compartidos/clases/conexion.php');
include_once('../compartidos/clases/scripts.php');
include_once('../compartidos/clases/tabla_dinamica.php');
include_once('../compartidos/clases/listas.php');
include_once('../compartidos/clases/charset.php');

class Detallado extends TPage
{
	var $dbConexion;
	var $nombre, $recibida, $asunto, $coordinacion, $area, $programa, $subprograma, $direccion, $telefono, $email, $agencia, $colonia, $organizacion, $cargo, $medio, $agencia2, $colonia2, $calle2, $referencia, $tipo_dom, $fuente, $encargado, $puesto;
	var $id_solicitud;

	public function onLoad($param)
	{
		parent::onLoad($param);
		if($this->Request["id"] != null)
		{
			$this->dbConexion = Conexion::getConexion($this->Application, "dbac");
			Conexion::createConfiguracion();
			$activar = new Acceso("dbac");

			$busqueda = array("id_asunto"=>$this->Request["id"]);
			$this->id_solicitud = Conexion::Retorna_Campo($this->dbConexion, "dat_sol_05_asuntos", "id_solicitud", $busqueda);
			$status = Conexion::Retorna_Campo($this->dbConexion, "dat_sol_05_asuntos", "id_status", $busqueda);

			if($this->Request["operacion"] == "resp" || $this->Request["operacion"] == "habl")
				$permiso = "w";
			else
				$permiso = "r";

			if($activar->Permiso_Consulta($this->User->Name, $this->Request["id"], $permiso))
			{
				if(!$this->IsPostBack)
				{
					//$this->AjusteDats();
					$this->Rellena_Datos();

					if($this->Request["operacion"] == "resp")
					{
						if($status == 1)
						{
							if(!array_search("resp", $this->User->Roles))
								$this->Response->redirect("index.php?page=Home");
							$this->datRespondido->setTimeStamp(time());
							Listas::EnlazaLista($this->dbConexion, "SELECT id_status, status FROM cat_status_01_status", $this->addlStatus);
							Listas::EnlazaLista($this->dbConexion, "SELECT id_motivo, motivo FROM cat_status_02_motivos WHERE id_status = 0 OR id_status = 3 ORDER BY id_status desc, id_motivo", $this->addlMotivo);
							$this->getClientScript()->registerEndScript("spell",
								"CreateGoogie('" . $this->txtRespuesta->ClientID . "');\n");
							Tabla_Dinamica::Filas_Visibles($this->tblPrincipal, "oculthab");
						}
						else
							$this->getClientScript()->registerBeginScript("err_status",
								"alert('El status de la solicitud debe ser activo.');\n" .
								"close();\n");
					}
					else if($this->Request["operacion"] == "habl")
					{
						if($status != 1)
						{
							if(!array_search("edit", $this->User->Roles))
								$this->Response->redirect("index.php?page=Home");
							Tabla_Dinamica::Filas_Visibles($this->tblPrincipal, "ocultresp");
						}
						else
							$this->getClientScript()->registerBeginScript("err_status",
								"alert('La solicitud ya se encuentra activa.');\n" .
								"close();\n");
					}
					else
						Tabla_Dinamica::Filas_Visibles($this->tblPrincipal, "ocult");
				}
			}
			else
				$this->getClientScript()->registerBeginScript("sin_permiso",
					"alert('No tiene permiso para realizar la operación.');\n" .
					"close();\n");
		}
		else
			$this->getClientScript()->registerBeginScript("sin_id",
				"alert('No se ha enviado el id del asunto a consultar.');\n" .
				"close();\n");
	}

/*	public function AjusteDats()
	{
		$this->datRespondido->getClientSide()->setOnDateChanged("datevalid('" . $this->datRespondido->getClientID() . "');");

		$this->getClientScript()->registerEndScript("ajustedats",
			"datevalid('" . $this->datRespondido->getClientID() . "');\n");
	}
*/
	public function Rellena_Datos()
	{
		$consulta = "SELECT * FROM dat_sol_05_asuntos a JOIN dat_sol_04_solicitudes s ON a.id_solicitud = s.id_solicitud WHERE a.id_asunto = :id_asunto AND s.activo = 1 AND a.activo = 1 ";
		$cmdConsulta = $this->dbConexion->createCommand($consulta);
		$cmdConsulta->bindValue(":id_asunto", $this->Request["id"]);
		$drLector = $cmdConsulta->query();
		if($row_asunto = $drLector->read())
		{
			$this->Datos_Solicitud($row_asunto);
			$this->Localizacion_Solicitud($row_asunto);

			$consulta = "SELECT CONCAT(revision, ': ', respuesta) AS respuesta FROM dat_sol_07_seguimiento " .
					"WHERE id_asunto = :id_asunto AND activo = 1 ORDER BY orden";
			$cmdConsulta = $this->dbConexion->createCommand($consulta);
			$cmdConsulta->bindValue(":id_asunto", $this->Request["id"]);
			$drLector = $cmdConsulta->query();
			if($row_ant = $drLector->readAll())
				$this->Respuestas_Anteriores($row_ant);
			if($row_asunto["id_solicitante"] != "")
			{
				if($row_sole = Conexion::Retorna_Registro($this->dbConexion, "dat_sol_01_solicitantes", array("id_solicitante"=>$row_asunto["id_solicitante"])))
					$this->Datos_Solicitante($row_sole);
			}
		}
	}

	public function Datos_Solicitud($row_asunto)
	{
		$labelini = array("HorizontalAlign"=>"Right", "Width"=>"20%");
		$labels = array("HorizontalAlign"=>"Right");

		$folio = $row_asunto["folio"];
		$recibida = $row_asunto["recibida"];
		$asunto = $row_asunto["asunto"];
		$anexo = $row_asunto["anexo"];

		$busqueda = array("id_area"=>$row_asunto["id_area"]);
		$area = Conexion::Retorna_Campo($this->dbConexion, "cat_serv_02_areas", "nombre_area", $busqueda);
		$encargado = Conexion::Retorna_Campo($this->dbConexion, "cat_serv_02_areas", "CONCAT(tratamiento, ' ', titular)", $busqueda);
		$puesto = Conexion::Retorna_Campo($this->dbConexion, "cat_serv_02_areas", "puesto_titular", $busqueda);
		$id_coordinacion = Conexion::Retorna_Campo($this->dbConexion, "cat_serv_02_areas", "id_coordinacion", $busqueda);
		$coordinacion = $id_coordinacion != "" ? Conexion::Retorna_Campo($this->dbConexion, "cat_serv_01_coord", "nombre_coordinacion", array("id_coordinacion"=>$id_coordinacion)) : "";

		$busqueda = array("id_programa"=>$row_asunto["id_subprograma"]);
		$subprograma = Conexion::Retorna_Campo($this->dbConexion, "cat_serv_05_programa", "nombre_programa", $busqueda);
		$id_programa = Conexion::Retorna_Campo($this->dbConexion, "cat_serv_05_programa", "depende_de", $busqueda);
		$programa = $id_programa != "" ? Conexion::Retorna_Campo($this->dbConexion, "cat_serv_05_programa", "nombre_programa", array("id_programa"=>$id_programa)) : "";

		$fuente = Conexion::Retorna_Campo($this->dbConexion, "cat_fte_01_fuentes", "fuente", array("id_fte"=>$row_asunto["id_fte"]));
		$medio = Conexion::Retorna_Campo($this->dbConexion, "cat_fte_02_medios", "medio", array("id_medio"=>$row_asunto["id_medio"]));
		if($medio != "")
			$fuente .= " - " . $medio;

		$this->Ordena_Datos($this->tblSolicitud, "Folio: ", $folio, $labelini);
		$this->Ordena_Datos($this->tblSolicitud, "Fecha de recepción: ", $recibida, $labels);
		$this->Ordena_Datos($this->tblSolicitud, "Solicitud: ", $asunto, $labels);
		$this->Ordena_Datos($this->tblSolicitud, "Coordinación: ", $coordinacion, $labels);
		$this->Ordena_Datos($this->tblSolicitud, "Área: ", $area, $labels);
		$this->Ordena_Datos($this->tblSolicitud, "Encargado: ", $encargado, $labels);
		$this->Ordena_Datos($this->tblSolicitud, "Puesto: ", $puesto, $labels);
		$this->Ordena_Datos($this->tblSolicitud, "Programa: ", $programa, $labels);
		$this->Ordena_Datos($this->tblSolicitud, "Subprograma: ", $subprograma, $labels);
		$this->Ordena_Datos($this->tblSolicitud, "Obtenida vía: ", $fuente, $labels);
		if($anexo != null)
			$this->hlAnexo->NavigateUrl = "upload/" . $anexo;
	}

	public function Localizacion_Solicitud($row_asunto)
	{
		$labels = array("HorizontalAlign"=>"Right");

		$calle = Conexion::Retorna_Campo($this->dbConexion, "cat_dom_03_calles", "nom_calle", array("id_calle"=>$row_asunto["id_calle"]));
		$id_col = Conexion::Retorna_Campo($this->dbConexion, "cat_dom_03_calles", "id_col", array("id_calle"=>$row_asunto["id_calle"]));
		$colonia = $id_col != "" ? Conexion::Retorna_Campo($this->dbConexion, "cat_dom_02_colonia", "nombre_col", array("id_col"=>$id_col)) : "";
		$id_ag = Conexion::Retorna_Campo($this->dbConexion, "cat_dom_02_colonia", "id_ag", array("id_col"=>$id_col));
		$agencia = $id_ag != "" ? Conexion::Retorna_Campo($this->dbConexion, "cat_dom_01_agencia", "nombre_ag", array("id_ag"=>$id_ag)) : "";
		$numero = $row_asunto["numero"];
		$referencia = $row_asunto["referencia"];
		$tipo_dom = Conexion::Retorna_Campo($this->dbConexion, "cat_dom_04_tipo_dom", "tipo_domicilio", array("id_tipo_dom"=>$row_asunto["id_tipo_dom"]));

		$this->Ordena_Datos($this->tblSolicitud, "Agencia: ", $agencia, $labels);
		$this->Ordena_Datos($this->tblSolicitud, "Colonia: ", $colonia, $labels);
		$this->Ordena_Datos($this->tblSolicitud, "Calle: ", $calle, $labels);
		$this->Ordena_Datos($this->tblSolicitud, "Número: ", $numero, $labels);
		$this->Ordena_Datos($this->tblSolicitud, "Referencia: ", $referencia, $labels);
		$this->Ordena_Datos($this->tblSolicitud, "Tipo de domicilio: ", $tipo_dom, $labels);
	}

	public function Respuestas_Anteriores($row_ant)
	{
		$labels = array("HorizontalAlign"=>"Right");

		$campo = "";
		foreach($row_ant as $respuestas)
			foreach($respuestas as $x)
				$campo .= Charset::CambiaCharset($x, "ISO-8859-1", "UTF-8") . "<br />";

		$this->Ordena_Datos($this->tblSolicitud, "Respuestas anteriores: ", $campo, $labels);
	}

	public function Datos_Solicitante($row_sole)
	{
		$labelini = array("HorizontalAlign"=>"Right", "Width"=>"20%");
		$labels = array("HorizontalAlign"=>"Right");

		$nombre = $row_sole[0]["nombre"];
		$direccion = $row_sole[0]["direccion"];
		$telefono = $row_sole[0]["telefono"];
		$email = $row_sole[0]["email"];
		$id_ag = Conexion::Retorna_Campo($this->dbConexion, "cat_dom_02_colonia", "id_ag", array("id_col"=>$row_sole[0]["id_colonia"]));
		$agencia = $id_ag != "" ? Conexion::Retorna_Campo($this->dbConexion, "cat_dom_01_agencia", "nombre_ag", array("id_ag"=>$id_ag)) : "";
		$colonia = Conexion::Retorna_Campo($this->dbConexion, "cat_dom_02_colonia", "nombre_col", array("id_col"=>$row_sole[0]["id_colonia"]));
		$org = Conexion::Retorna_Campo($this->dbConexion, "dat_sol_03_organizacion", "nombre_org", array("id_org"=>$row_sole[0]["id_org"]));
		$cargo = Conexion::Retorna_Campo($this->dbConexion, "dat_sol_02_cargos", "cargo", array("id_cargo"=>$row_sole[0]["id_cargo"]));

		$this->Ordena_Datos($this->tblSolicitante, "Nombre: ", $nombre, $labelini);
		$this->Ordena_Datos($this->tblSolicitante, "Dirección: ", $direccion, $labels);
		$this->Ordena_Datos($this->tblSolicitante, "Teléfono: ", $telefono, $labels);
		$this->Ordena_Datos($this->tblSolicitante, "Correo electrónico: ", $email, $labels);
		$this->Ordena_Datos($this->tblSolicitante, "Agencia: ", $agencia, $labels);
		$this->Ordena_Datos($this->tblSolicitante, "Colonia: ", $colonia, $labels);
		$this->Ordena_Datos($this->tblSolicitante, "Organización /Comité: ", $org, $labels);
		$this->Ordena_Datos($this->tblSolicitante, "Cargo o puesto: ", $cargo, $labels);
	}

	public function Ordena_Datos($tabla, $texto, $valor, $labels)
	{
		if($valor != "")
			Tabla_Dinamica::Agrega_Fila($tabla, array(array_merge(array("Text"=>$texto), $labels), array("Text"=>$valor)));
	}

	public function addlStatus_Callback($sender, $param)
	{
		if($sender->getSelectedValue() == 3)
			$this->addlMotivo->setEnabled(true);
		else
			$this->addlMotivo->setEnabled(false);
	}

	public function addlMotivo_Callback($sender, $param)
	{
		if($sender->getSelectedItem()->getText() == "Otra")
			$this->atxtEspecifique->setEnabled(true);
		else
			$this->atxtEspecifique->setEnabled(false);
	}

	public function btnSi_Click($sender, $param)
	{
		$parametros = array("id_status"=>1);
		$busqueda = array("id_asunto"=>$this->Request["id"]);
		Conexion::Actualiza_Registro_Historial($this->dbConexion, "dat_sol_05_asuntos", $parametros, $busqueda, $this->User->Name);

		$this->getClientScript()->registerBeginScript("exito",
			"alert('Se ha reactivado la solicitud.');\n" .
			"opener.recarga_grid();\n" .
			"close();\n");
	}

	public function btnNo_Click($sender, $param)
	{
		$this->getClientScript()->registerBeginScript("cerrar",
			"close();\n");
	}

	public function btnGuardar_Click($sender, $param)
	{
		$exito = true;
		$consulta = "SELECT MAX(orden) AS orden FROM dat_sol_07_seguimiento WHERE id_asunto = :id_asunto";
		$cmdConsulta = $this->dbConexion->createCommand($consulta);
		$cmdConsulta->bindValue(":id_asunto", $this->Request["id"]);
		$drLector = $cmdConsulta->query();
		if($row = $drLector->read())
			$orden = $row["orden"] + 1;
		else
			$orden = 1;

		//Estatus = 3 es rechazado
		if($this->addlStatus->getSelectedValue() == 3)
		{
			if($this->addlMotivo->getSelectedItem()->getText() == "Otra")
			{
				$consulta = "SELECT id_motivo FROM cat_status_02_motivos WHERE motivo = TRIM(:especifique)";
				$cmdConsulta = $this->dbConexion->createCommand($consulta);
				$cmdConsulta->bindValue(":especifique", $this->atxtEspecifique->getText());
				$drLector = $cmdConsulta->query();
				if($row = $drLector->read())
					$motivo = $row["id_motivo"];
				else
				{
					$parametros = array("motivo"=>$this->atxtEspecifique->getText(), "id_status"=>3, "activo"=>1);
					if(($motivo = Conexion::Inserta_Registro_Historial($this->dbConexion, "cat_status_02_motivos", $parametros, $this->User->Name)) == 0)
						$motivo = 0;
				}
			}
			else
				$motivo = $this->addlMotivo->getSelectedValue();
		}
		else
			$motivo = 0;

		$parametros = array("id_asunto"=>$this->Request["id"], "orden"=>$orden, "respuesta"=>$this->txtRespuesta->getText(), "id_motivo"=>$motivo, "revision"=>date("Y-m-d", $this->datRespondido->getTimeStamp()), "activo"=>1);
		if((Conexion::Inserta_Registro_Historial($this->dbConexion, "dat_sol_07_seguimiento", $parametros, $this->User->Name)) == 0)
			$exito = false;

		$parametros = array("id_status"=>$this->addlStatus->getSelectedValue(), "revision"=>date("Y-m-d", $this->datRespondido->getTimeStamp()));
		$busqueda = array("id_asunto"=>$this->Request["id"]);

		//cancelada la asignación automática de área para evitar que la marque en miércoles ciudadano
		/*if(Conexion::Retorna_Campo($this->dbConexion, "dat_sol_05_asuntos", "id_area", $busqueda) == null)
		{
			$area_respuesta = Conexion::Retorna_Campo($this->dbConexion, "cat_aut_00_usuarios", "id_area", array("id_usuario"=>$this->User->Name));
			$parametros = array_merge($parametros, array("id_area"=>$area_respuesta));
		}*/

		Conexion::Actualiza_Registro_Historial($this->dbConexion, "dat_sol_05_asuntos", $parametros, $busqueda, $this->User->Name);

		if(Conexion::Retorna_Campo($this->dbConexion, "dat_sol_04_solicitudes", "turnada", array("id_solicitud"=>$this->id_solicitud, "activo"=>1)) == null)
			Conexion::Actualiza_Registro_Historial($this->dbConexion, "dat_sol_04_solicitudes", array("turnada"=>date("Y-m-d H:i:s")), array("id_solicitud"=>$this->id_solicitud), $this->User->Name);

		if($exito)
			$this->getClientScript()->registerBeginScript("exito",
				"alert('La respuesta se registró correctamente.');\n" .
				"opener.recarga_grid();\n" .
				"document.location.href = 'index.php?page=detallado&operacion=cons&popup=2&id=" . $this->Request["id"] . "';\n");
//				"document.location.href = 'index.php?page=pendientes';\n");
		else
			$this->getClientScript()->registerBeginScript("fallo",
				"alert('No se pudo registrar la respuesta.');\n");
	}
}
?>