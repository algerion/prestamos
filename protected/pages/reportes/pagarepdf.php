<?php
include_once('../compartidos/clases/usadompdf.php');

class pagarepdf extends TPage 
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		usadompdf::creapdf("http://" . $_SERVER["HTTP_HOST"] 
				. $_SERVER["PHP_SELF"] . "?page=reportes.pagare&id=" . 
				$this->Request["id"], "letter", "portrait");
	}
}
?>