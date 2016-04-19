<?php
Prado::using('System.Web.UI.ActiveControls.*');
require_once('../compartidos/clases/Comparte_Auth_Cookie.php');
include_once('../compartidos/clases/conexion.php');

class salir extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);

		$this->dbConexion = Conexion::getConexion($this->Application, "dbac");
		Conexion::createConfiguracion();
		
		
		Comparte_Auth_Cookie::BorraCookies($this, $this->Application->id);
/*		if($this->Request->Cookies["user_dbmunioax"] != null)
		{
			$cookie = new THttpCookie("user_dbmunioax", "");
			$cookie->Expire = time() - 1;
			$this->Response->Cookies->Add($cookie);
		}
		if($this->Request->Cookies["hash_dbmunioax"] != null)
		{
			$cookie = new THttpCookie("user_dbmunioax", "");
			$cookie->Expire = time() - 1;
			$this->Response->Cookies->Add($cookie);
		}*/
		// --------------------------------------------------------------------------------------------------------------------
	
		
		// --------------------------------------------------------------------------------------------------------------------
        $this->Application->getModule('auth')->logout();
		
        $url = $this->Service->constructUrl('Usuarios.Login');
        $this->Response->redirect($url);
	}
}
?>