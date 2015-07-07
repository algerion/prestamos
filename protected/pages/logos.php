<?php
class Logos extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
	}

	public function fulArchivo_Upload($sender, $param)
	{
		if($sender->HasFile)
		{
			if($sender->ID == "fulEncab")
				$archivo = "banner.png";
			else if($sender->ID == "fulPie")
				$archivo = "pie.png";
			else if($sender->ID == "fulEncabDoc")
				$archivo = "doc_encab.png";
			else if($sender->ID == "fulPieDoc")
				$archivo = "doc_pie.png";
			else if($sender->ID == "fulLogoIzq")
				$archivo = "logoizq.jpg";
			else if($sender->ID == "fulLogoDer")
				$archivo = "logoder.jpg";
			$ruta = "images/" . $archivo;
				if($sender->saveAs($ruta))
					$this->getClientScript()->registerBeginScript("exito",
							"alert('Archivo cargado.');\n");
				else
					$this->getClientScript()->registerBeginScript("error",
							"alert('El archivo no pudo cargarse.');\n");
		}
	}
}
?>
