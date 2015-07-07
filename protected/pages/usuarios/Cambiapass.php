<?php
include_once('../compartidos/clases/conexion.php');

class cambiapass extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);

		$this->dbConexion = Conexion::getConexion($this->Application, "dbac");
		Conexion::createConfiguracion();
	}

	public function btnAceptar_Clicked($sender, $param)
	{
		$parametros = array("acceso"=>md5(strtoupper($this->txtAcceso->getText())));
		$busqueda = array("id_usuario"=>$this->User->Name, "acceso"=>md5(strtoupper(
				$this->txtOldAcceso->getText())));
		if(Conexion::Actualiza_Registro($this->dbConexion, "cat_aut_00_usuarios", $parametros, $busqueda))
			$this->getClientScript()->registerBeginScript("exito",
				"alert('Se ha modificado el password del usuario.');\n");
		else
			$this->getClientScript()->registerBeginScript("error",
				"alert('El password actual proporcionado no es correcto. Reintente.');\n");
	}
}
?>