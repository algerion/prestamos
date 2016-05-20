<?php
include_once('../compartidos/clases/usadompdf.php');

class RRedocumentacionpdf extends TPage 
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		usadompdf::creapdf("http://" . $_SERVER["HTTP_HOST"] 
				. $_SERVER["PHP_SELF"] . "?page=reportes.RRedocumentacion&id=".$this->Request["id"]."&id2=".$this->Request["id2"], "letter", "portrait");	

				
	}
}
?>