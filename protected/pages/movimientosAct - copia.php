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

class movimientosAct extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
		$consulta = "SELECT c.cveEmpleado AS num_Unico, 
					CONCAT(c.nombre,' ', c.apPaterno,' ', c.apMaterno) AS nombre
					,(SELECT CASE tipoNomina
								  WHEN 'S' THEN 'SEMANAL' 
								  WHEN 'Q' THEN 'QUINCENAL'
								  ELSE 'NO HAY TIPO DE NOMINA' END
					FROM catempleado cat WHERE cat.cveEmpleado = c.cveEmpleado) AS TipoNomina
					,(SELECT CASE estatus
								  WHEN '0' THEN 'PERMISO TEMPORAL' 
								  WHEN '1' THEN 'ACTIVO' 
								  WHEN '2' THEN 'BAJAS' END
					FROM catempleado cat WHERE cat.cveEmpleado = c.cveEmpleado) AS TipoEstatus
					FROM catempleado c ORDER BY c.cveEmpleado ASC";
		$comando = $this->dbConexion->createCommand($consulta);
		$resultado = $comando->query()->readAll();
		$this->pnlMovimientos->DataSource = $resultado;
		$this->pnlMovimientos->dataBind();
		

	}
}

?>