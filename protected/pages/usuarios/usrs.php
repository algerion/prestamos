<?php
Prado::using('System.Web.UI.ActiveControls.*');
include_once('../compartidos/clases/listas.php');
include_once('../compartidos/clases/conexion.php');

class Usrs extends TPage
{
	var $dbConexion, $activar, $permisos, $asignados;

	public function onLoad($param)
	{
		parent::onLoad($param);

		$this->dbConexion = Conexion::getConexion($this->Application, "dbac");
		Conexion::createConfiguracion();

		if(!$this->isPostBack)
		{
			if(array_search("nusr", $this->User->Roles))
			{
				//Lee el archivo menu.xml para buscar los permisos de acceso a páginas que tenga activos para mostrar opciones
				$this->permisos = array();
				$this->asignados = array();
				$doc = new TXmlDocument();
				$doc->loadFromFile("protected/layouts/menu.xml");
				$x = $doc->getElements();
				$this->leeMenuXml($x);
				$this->cblPermisos->setDataSource($this->permisos);
				$this->cblPermisos->dataBind();
			}

			$this->Enlaza_Coord();
			$this->Enlaza_Usuario();

			if(!array_search("nusr", $this->User->Roles))
			{
				$this->addlUsuarios->SelectedValue = $this->User->Name;
				$this->addlUsuarios_Callback(null, null);
				$this->addlUsuarios->Visible = false;
				$this->alblUsuarios->Visible = false;
				$this->alblPermisos->Visible = false;
				$this->atxtUsuario->Enabled = false;
			}
		}
	}

	public function Enlaza_Usuario($sender = null, $param = null)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT '0' AS id_usuario, 'NUEVO USUARIO' AS usuario " .
				"UNION SELECT id_usuario, usuario FROM cat_aut_00_usuarios WHERE activo = 1", $this->addlUsuarios);
//		$this->addlUsuarios->raiseEvent("OnSelectedIndexChanged", $this->addlUsuarios, null);
	}

	public function Enlaza_Coord($sender = null, $param = null)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_coordinacion, nombre_coordinacion FROM cat_serv_01_coord WHERE activo = 1", 
				$this->addlCoord);
		$this->addlCoord->raiseEvent("OnSelectedIndexChanged", $this->addlCoord, new TCallbackEventParameter(null, null));
	}

	public function addlUsuarios_Callback($sender, $param)
	{
		if($this->addlUsuarios->getSelectedValue() == 0)
		{
			$this->atxtUsuario->setText("");
			$this->atxtTratamiento->setText("");
			$this->atxtNombre->setText("");
			$this->atxtCorreo->setText("");

			Listas::setValorSelected($this->addlCoord, 1);
			$this->addlCoord->raiseEvent("OnSelectedIndexChanged", $this->addlCoord, null);
			Listas::setValorSelected($this->addlArea, 1);

			foreach($this->cblPermisos->Items as $permisos)
			{
				$permisos->setSelected(false);
				$permisos->Enabled = true;
			}
		}
		else
		{
			$campos = array("id_area", "tratamiento", "nombre", "correo");
			$busqueda = array("id_usuario"=>$this->addlUsuarios->getSelectedValue());

			$drLector = Conexion::Retorna_Consulta($this->dbConexion, "cat_aut_00_usuarios", $campos, $busqueda);
			if($drLector)
			{
				$row = $drLector[0];
				$this->atxtUsuario->setText($this->addlUsuarios->getSelectedItem()->getText());
				$this->atxtTratamiento->setText($row["tratamiento"]);
				$this->atxtNombre->setText($row["nombre"]);
				$this->atxtCorreo->setText($row["correo"]);

				$id_coordinacion = Conexion::Retorna_Campo($this->dbConexion, "cat_serv_02_areas", "id_coordinacion", array("id_area"=>$row["id_area"]));
				Listas::setValorSelected($this->addlCoord, $id_coordinacion);
				$this->addlCoord->raiseEvent("OnSelectedIndexChanged", $this->addlCoord, null);
				Listas::setValorSelected($this->addlArea, $row["id_area"]);

				$consulta = "SELECT GROUP_CONCAT(permiso) FROM cat_aut_00_lista_permisos lp JOIN cat_aut_02_permisos p " .
						"ON lp.id_permiso = p.id_permiso WHERE id_usuario = :id_usuario";
				$comando = $this->dbConexion->createCommand($consulta);
				$comando->bindValue(":id_usuario", $this->addlUsuarios->SelectedValue);
				$roles = explode(",", $comando->queryScalar());
				$roles = array_merge(array(""), $roles);

				foreach($this->cblPermisos->Items as $permisos)
				{
					if(array_search($permisos->Value, $roles))
						$permisos->setSelected(true);
					else 
						$permisos->setSelected(false);

					//Desactiva la casilla de verificación para quitar el permiso de acceso a esta página para el usuario actual. De lo contrario, puede perderse totalmente el acceso a esta página.
					if($permisos->Value == "nusr" && $this->addlUsuarios->SelectedValue == $this->User->Name)
						$permisos->Enabled = false;
					else
						$permisos->Enabled = true;
				}
			}
		}
/*		if($param != null)
			$this->apnlPermisos->render($param->getNewWriter());*/
	}

	public function addlCoord_Changed($sender, $param)
	{
		Listas::EnlazaLista($this->dbConexion, "SELECT id_area, nombre_area FROM cat_serv_02_areas WHERE activo = 1 AND " .
				"id_coordinacion = " . $this->addlCoord->getSelectedValue(), $this->addlArea);
	}

	public function leeMenuXml($xml)
	{
		foreach($xml as $ele)
		{
			if($ele->getTagName() == "MenuItem")
			{
				if($ele->getAttribute("IdPag") != null)
				{
					if(!in_array($ele->getAttribute("IdPag"), $this->asignados))
					{
						$this->asignados[] = $ele->getAttribute("IdPag");
						$this->permisos[] = array("Title" => $ele->getAttribute("Title"), "IdPag" => $ele->getAttribute("IdPag"));
					}
				}

				if($ele->getHasElement())
					$this->leeMenuXml($ele->getElements());
			}
		}
	}

	public function Guarda_Permisos($id_usuario)
	{
		$consulta = "DELETE FROM cat_aut_02_permisos WHERE id_usuario = :id_usuario";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_usuario", $id_usuario);
		$comando->execute();

		foreach($this->cblPermisos->SelectedValues as $x)
		{
			$busqueda = array("permiso"=>$x);
			$id_permiso = Conexion::Retorna_Campo($this->dbConexion, "cat_aut_00_lista_permisos", "id_permiso", $busqueda);
			$parametros = array("id_usuario"=>$id_usuario, "id_permiso"=>$id_permiso);
			Conexion::Inserta_Registro_Historial($this->dbConexion, "cat_aut_02_permisos", $parametros, $this->User->Name);
		}
	}

	public function btnAceptar_Clicked($sender, $param)
	{
		$exito = true;
		$distinto = true;

		if($this->addlUsuarios->getSelectedItem()->getText() != $this->atxtUsuario->getText())// && $this->atxtUsuario->getText() != $this->User->Name)
		{
			$cmdConsulta = $this->dbConexion->createCommand("SELECT usuario FROM cat_aut_00_usuarios WHERE activo = 1 AND usuario = :usuario UNION SELECT nu AS usuario FROM cat_aut_00_empleados WHERE nu = :usuario");
			$cmdConsulta->bindValue(":usuario", $this->atxtUsuario->getText());
			$drLector = $cmdConsulta->query();
			$distinto = !$drLector->read();
		}

		if($distinto)
		{
			if($this->addlUsuarios->getSelectedValue() == 0)
			{
				$parametros = array("ftes_w"=>"", "coords_w"=>"", "areas_w"=>"/" . $this->addlArea->getSelectedValue() . "/", "ftes_r"=>"",
						"coords_r"=>"", "areas_r"=>"/" . $this->addlArea->getSelectedValue() . "/", "id_area"=>$this->addlArea->getSelectedValue(),
						"usuario"=>strtoupper($this->atxtUsuario->getText()), "acceso"=>md5(strtoupper($this->atxtUsuario->getText())), 
						"correo"=>$this->atxtCorreo->getText(), "tratamiento"=>strtoupper($this->atxtTratamiento->getText()), 
						"nombre"=>strtoupper($this->atxtNombre->getText()), "activo"=>1);
				$username = Conexion::Inserta_Registro_Historial($this->dbConexion, "cat_aut_00_usuarios", $parametros, $this->User->Name);
				$this->Guarda_Permisos($username);
				if($username)
					$this->getClientScript()->registerBeginScript("exito",
						"alert('Se ha creado el nuevo usuario.');\n" .
						"document.location.replace(document.location.href);\n");
				else
					$this->getClientScript()->registerBeginScript("error",
						"alert('No pudo crearse el nuevo usuario.');\n");
			}
			else
			{
				$parametros = array("id_area"=>$this->addlArea->getSelectedValue(), "correo"=>$this->atxtCorreo->getText(), "tratamiento"=>strtoupper($this->atxtTratamiento->getText()), "nombre"=>strtoupper($this->atxtNombre->getText()));
				if(array_search("nusr", $this->User->Roles))
				{
					$this->Guarda_Permisos($this->addlUsuarios->SelectedValue);
					$parametros = array_merge($parametros, array("usuario"=>strtoupper($this->atxtUsuario->getText())));
				}

				$busqueda = array("id_usuario"=>$this->addlUsuarios->getSelectedValue());
				Conexion::Actualiza_Registro_Historial($this->dbConexion, "cat_aut_00_usuarios", $parametros, $busqueda, $this->User->Name);

				$this->getClientScript()->registerBeginScript("exito",
					"alert('Se han modificado los datos del usuario.');\n" .
					"document.location.replace(document.location.href);\n");
			}
		}
		else
			$this->getClientScript()->registerBeginScript("existe",
				"alert('El nombre de usuario ya existe. Seleccione un nuevo nombre de usuario.');\n");
	}
}
?>