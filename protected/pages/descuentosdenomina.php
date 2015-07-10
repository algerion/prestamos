<?php
//Prado::using('System.Util.*'); //TVarDump
/*Prado::using('System.Web.UI.ActiveControls.*');
include_once('../compartidos/clases/listas.php');
*/
include_once('../compartidos/clases/conexion.php');
/*
include_once('../compartidos/clases/envia_mail.php');
include_once('../compartidos/clases/charset.php');
*/

class descuentosdenomina extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);
/*
		$this->dbConexion = $this->Application->Modules["dbpr"]->Database;
		$this->dbConexion->Active = true;
*/
/*
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
		$consulta = "SELECT num_movto, fecha, movimiento, justificacion, cargo, abono FROM movimientos";
		$comando = $this->dbConexion->createCommand($consulta);
		$resultado = $comando->query()->readAll();
		$this->dgDescuentos->DataSource = $resultado;
		$this->dgDescuentos->dataBind();
		*/
/*		if(!$this->IsPostBack)
		{
		}
*/
	}
}

?>