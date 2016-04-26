<?php
include_once('../compartidos/clases/usadompdf.php');

class Reporte_Desnopdf extends TPage 
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		usadompdf::creapdf("http://" . $_SERVER["HTTP_HOST"] 
				. $_SERVER["PHP_SELF"] . "?page=reportes.Reporte_Desno&id=" . 
				$this->Request["id"], "letter", "portrait");				
	}
}
?>