<?php
require_once('../compartidos/clases/conexion.php');
require_once('../compartidos/clases/smail.php');

class Recup_Pass extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);

		$this->dbConexion = Conexion::getConexion($this->Application, "dbac");
		Conexion::createConfiguracion();
	}

	public function btnEnviar_Clicked($sender, $param)
	{
		$pass = "";

		$usuario = Conexion::Retorna_Campo($this->dbConexion, "cat_aut_00_usuarios", "id_usuario",
				array("usuario"=>$this->txtUsuario->Text));
		$direccion = Conexion::Retorna_Campo($this->dbConexion, "gencatusuariodetalle", "email",
				array("idUsuario"=>$usuario));

		if($direccion != "")
		{
			for($i = 0; $i < 10; $i++)
			{
				$rand = mt_rand(1, 3);
				$rand = ($rand == 1 ? mt_rand(48, 57) : ($rand == 2 ? mt_rand(65, 90) : mt_rand(97, 122)));
				$pass .= chr($rand);
			}

			$parametros = array("password"=>md5(strtoupper($pass)));
			$busqueda = array("idUsuario"=>$usuario);

			Conexion::Actualiza_Registro($this->dbConexion, "gencatusuario", $parametros, $busqueda);

			$master_mail = Conexion::Retorna_Campo($this->dbConexion, "gencatvariables", "valor",
					array("variable"=>"solmail"));
			SMail::Envia_Correo($direccion, "Nueva contraseña del sistema de solicitudes de " .
					"transparencia municipal", "Estimado usuario:\n\nSu nueva contraseña de acceso al " .
					"sistema de solicitudes de transparencia es: " . $pass . "\n\n", $master_mail);
			$this->getClientScript()->registerBeginScript("enviado",
				"alert('Se ha enviado un correo a su cuenta registrada con su nueva contraseña.');\n" .
				"document.location.href = 'index.php?page=usuarios.login';\n");
		}
		else
			$this->getClientScript()->registerBeginScript("no_usuario",
				"alert('Usuario no válido.');\n");
	}

	public function btnCancelar_Clicked($sender, $param)
	{
		$this->Response->redirect("index.php?page=usuarios.login");
	}
}
?>