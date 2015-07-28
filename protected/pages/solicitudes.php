<?php
//Prado::using('System.Util.*'); //TVarDump
/*Prado::using('System.Web.UI.ActiveControls.*');
include_once('../compartidos/clases/listas.php');
include_once('../compartidos/clases/envia_mail.php');
include_once('../compartidos/clases/charset.php');
*/
include_once('../compartidos/clases/conexion.php');

class solicitudes extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
		if(!$this->IsPostBack)
		{
			$this->txtFecha->Text = date("d-m-Y");
		}

	}
	
	public function btnGuardar_Click($sender, $param)
	{
	}
}

?>