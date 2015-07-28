<?php
include_once('../compartidos/PradoCookMenu/JsCookMenu.php');
//include_once('../compartidos/clases/acceso.php');
Prado::using('System.Web.UI.ActiveControls.*');

//session_start();
error_reporting(E_ALL | E_STRICT);

class MainLayout extends TTemplateControl
{
	public function onInit($param)
	{
		parent::onInit($param);
		
/*		if($this->User->IsGuest)
			$this->mnuPrincipal->Visible = false;
		else*/
			$this->mnuPrincipal->Visible = true;
/*
		echo $this->User->Name;
		echo "<br />";
		echo $this->Application->id;
*/		
		if($this->Request["popup"] != null)
		{
			$this->mnuPrincipal->Visible = false;
			$this->pnlEncabezado->Visible = false;
			if($this->Request["popup"] == "1")
				$this->pnlCerrar->Visible = true;
			else
				$this->pnlCerrar->Visible = false;
		}
		else
		{
			$this->pnlCerrar->Visible = false;
			$doc = new TXmlDocument();
			$doc->loadFromFile("protected/layouts/menu.xml");
			$x = $doc->getElements();
			$this->creaMenuXml($x, $this->mnuPrincipal);
		}
	}

	public function creaMenuXml($xml, $padre)
	{
		$asignados = array();

		foreach($xml as $ele)
		{
			if($ele->getTagName() == "MenuItem")
			{
				if($ele->getAttribute("IdPag") != null)
					$idpag = $ele->getAttribute("IdPag");
				else
					$idpag = "";

//				if(!$this->User->IsGuest)
				{
					if($idpag == "" || array_search($idpag, $this->User->Roles))
					{
						if(!in_array($ele->getAttribute("Title"), $asignados))
						{
							if($ele->getAttribute("Visible") == null || $ele->getAttribute("Visible") == "true")
								$asignados[] = $ele->getAttribute("Title");
							$item = new JsCookMenuItem;
							foreach($ele->getAttributes() as $key=>$value)
								if($key != "IdPag")
									$item->$key = $value;

/*							//Para evitar los recuadros con x del dibujo
							if($item->ImagePath == null)
								$item->ImagePath = "../compartidos/PradoCookMenu/JSCookMenu/ThemeOffice/blank.gif";
*/
							if($ele->getHasElement())
							{
								$contenido = $this->creaMenuXml($ele->getElements(), $item);
								if(count($contenido) == 0)
									$item->Visible = false;
							}

							$padre->Controls->Add($item);
						}
					}
				}
			}
		}

		return $asignados;
	}
}
?>
