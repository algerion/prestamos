<?php
Prado::using('System.Web.UI.ActiveControls.*');
Prado::using('System.Web.UI.WebControls.TDatePicker');
include_once('../compartidos/clases/conexion.php');

class Oficio_Can_Pendiente extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);

		$this->dbConexion = Conexion::getConexion($this->Application, "dbac");
		Conexion::createConfiguracion();
		$this->BindGridConsulta();
		$this->getClientScript()->registerEndScript("marcafila",
			"MarcaFila('" . $this->dgConsulta->ClientID . "');\n");
	}

	public function BindGridConsulta()
	{
		$consulta = "SELECT CONCAT('id=', s.id_solicitud, '&area=', a.id_area) AS url, CASE WHEN s.folio = '' THEN 0 ELSE s.folio END AS folio, " .
				"se.nombre, s.turnada, GROUP_CONCAT(a.asunto SEPARATOR '; ') AS asunto, CONCAT(f.fuente, CASE WHEN s.id_medio IS NULL THEN '' " .
				"ELSE CONCAT(' - ', m.medio) END) AS fuente, CONCAT(cc.nombre_coordinacion, ' - ', ca.nombre_area) AS area FROM dat_sol_04_solicitudes s " .
				"LEFT JOIN dat_sol_01_solicitantes se ON s.id_solicitante = se.id_solicitante " .
				"LEFT JOIN dat_sol_05_asuntos a ON s.id_solicitud = a.id_solicitud LEFT JOIN cat_serv_02_areas ca ON a.id_area = ca.id_area " .
				"LEFT JOIN cat_serv_01_coord cc ON ca.id_coordinacion = cc.id_coordinacion LEFT JOIN cat_fte_01_fuentes f ON s.id_fte = f.id_fte " .
				"LEFT JOIN cat_fte_02_medios m ON s.id_medio = m.id_medio WHERE a.id_status = 1 AND f.grupo = 'S' AND s.activo = 1 AND a.activo = 1 " .
				"GROUP BY s.id_solicitud, a.id_area ORDER BY s.id_solicitud DESC";
		$cmdConsulta = $this->dbConexion->createCommand($consulta);
		$drLector = $cmdConsulta->query();
		$arrConsulta = $drLector->readAll();
		/*
		$arrConsulta = array(
				array("folio"=>"00001", "nombre"=>"Nombre Apellido", "turnada"=>"12/10/2014", "asunto"=>"Solicitud de información", "fuente"=>"Oficio", "area"=>"Secretaría Municipal", "url"=>"about:blank"),
				array("folio"=>"00002", "nombre"=>"Nombre Apellido", "turnada"=>"12/10/2014", "asunto"=>"Solicitud de información", "fuente"=>"Oficio", "area"=>"Secretaría Técnica", "url"=>"about:blank"),
				array("folio"=>"00003", "nombre"=>"Nombre Apellido", "turnada"=>"13/10/2014", "asunto"=>"Solicitud de información", "fuente"=>"Verbal", "area"=>"Presidencia Municipal", "url"=>"about:blank"),
				array("folio"=>"00004", "nombre"=>"Nombre Apellido", "turnada"=>"13/10/2014", "asunto"=>"Solicitud de información", "fuente"=>"Oficio", "area"=>"Transparencia", "url"=>"about:blank")
			);
		*/
		$this->dgConsulta->dataSource = $arrConsulta;
		$this->dgConsulta->dataBind();
	}

	public function dgConsulta_PageIndexChanged($sender, $param)
	{
		$this->dgConsulta->setCurrentPageIndex($param->getNewPageIndex());
		$this->BindGridConsulta();
	}
}
?>
