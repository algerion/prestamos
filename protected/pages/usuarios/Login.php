<?php
require_once('../compartidos/clases/conexion.php');
require_once('../compartidos/clases/Comparte_Auth_Cookie.php');
require_once('../compartidos/clases/HMAC.php');
//include_once('../compartidos/clases/acceso.php');

class Login extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);

		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
		
		if($this->Request["action"] == "Salir")
			$this->Salir();

		if(!isset($this->Session["aleat"]))
			$this->Session["aleat"] = mt_rand();
		$this->hidAleatorio->Value = $this->Session["aleat"];

		Comparte_Auth_Cookie::CreaUsuario($this, $this->Application->id, "usuario", "acceso", "usuarios");
		if(!$this->User->IsGuest)
			$this->RedireccionAcceso();
	}

	public function btnAceptar_Clicked($sender, $param)
	{
        $authManager=$this->Application->getModule('auth');

		if($this->hidHMAC->Value != "")
			$password = $this->hidHMAC->Value;
		else
		{
			$phphmac = new Crypt_HMAC($this->Session["aleat"]);
			$password = $phphmac->hash(md5(strtoupper($this->txtAcceso->getText())));
		}

        if($authManager->login($this->txtUsuario->Text, $password, 3600))
			$this->RedireccionAcceso();
		else
			$this->getClientScript()->registerBeginScript("error",
				"alert('Acceso incorrecto. Verifique su usuario / contraseña.');\n");
	}

	public function RedireccionAcceso()
	{
		Comparte_Auth_Cookie::CreaCookies($this, $this->Application->id, "usuario", "acceso", "usuarios");
/*		$url = $this->Application->getModule('auth')->ReturnUrl;
		if(empty($url))  // the user accesses the login page directly*/
			$url = $this->Service->DefaultPageUrl;
		$this->Response->redirect($url);
		//$this->Response->redirect("index.php?page=Home");
	}

	public function Salir()
	{
		Comparte_Auth_Cookie::BorraCookies($this, $this->Application->id);
        $this->Application->getModule('auth')->logout();
        $url = $this->Service->constructUrl('Usuarios.Login');
        $this->Response->redirect($url);
	}
}
?>