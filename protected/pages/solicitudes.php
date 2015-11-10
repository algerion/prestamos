<?php
//Prado::using('System.Util.*'); //TVarDump
Prado::using('System.Web.UI.ActiveControls.*');
/*
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
			//$this->carga_solicitud();
			
		}
	
	}
	
	public function btnGuardar_Click($sender, $param)
	{
	}
	
	public function txtNoUnico_CallBack($sender, $param)
	{
		$this->Rellena_Datos($sender->Text, str_replace("txtNoUnico", "", $sender->ID));
	}
	
	public function Rellena_Datos($num_unico, $sufijo)
	{
		$result = Conexion::Retorna_Consulta($this->dbConexion, "sujetos", array("nombre", "fec_ingre", "sindicato", "tipo"), array("numero"=>$num_unico));
		if(count($result) > 0)
		{
			$intervalo = date_diff(date_create($result[0]["fec_ingre"]), new DateTime("now"));
			$formato = '%m meses';
			if($intervalo->format('%y') > 100)
				$formato = 'Desconocida';
			elseif($intervalo->format('%y') > 0)
				$formato = '%y años ' . $formato;
			$ant = "txtAntiguedad" . $sufijo;
			$this->$ant->Text = $intervalo->format($formato);
			$nom = "txtNombre" . $sufijo;
			$this->$nom->Text = $result[0]["nombre"];
			$tipo = Conexion::Retorna_Campo($this->dbConexion, "tipo_empleado", "texto", array("tipo_empleado"=>$result[0]["tipo"]));
			$tip = "txtTipo" . $sufijo;
			$this->$tip->Text = $tipo;
			$sindicato = Conexion::Retorna_Campo($this->dbConexion, "catsindicatos", "sindicato", array("cve_sindicato"=>$result[0]["sindicato"]));
			$sin = "txtSindicato" . $sufijo;
			$this->$sin->Text = $sindicato;
		}
	}
	
	public function carga_solicitud($id_solicitud = null)
	{
		$consulta = "SELECT creada, estatus_p, 
			t.numero as num_tit, t.nombre AS titular, st.cve_sindicato AS tit_cve_sind, st.sindicato AS tit_sind, TIMESTAMPDIFF(YEAR, t.fec_ingre, CURDATE()) AS tit_ant,
			s.aval1, a1.nombre AS aval1_n, sa1.cve_sindicato AS aval1_cve_sind, sa1.sindicato AS aval1_sind, TIMESTAMPDIFF(YEAR, a1.fec_ingre, CURDATE()) AS aval1_ant,
			s.aval2, a2.nombre AS aval2_n, sa2.cve_sindicato AS aval2_cve_sind, sa2.sindicato AS aval2_sind, TIMESTAMPDIFF(YEAR, a2.fec_ingre, CURDATE()) AS aval2_ant,
			firma, importe, plazo, tasa, saldo_anterior, descuento
			FROM Solicitud s
			LEFT JOIN estatus_prestamo ep ON s.estatus = ep.id_estatus_p
			LEFT JOIN sujetos AS t ON t.numero = s.titular
			LEFT JOIN catsindicatos st ON st.cve_sindicato = s.cve_sindicato
			LEFT JOIN sujetos AS a1 ON a1.numero= s.aval1
			LEFT JOIN catsindicatos sa1 ON sa1.cve_sindicato = s.cve_sind_Aval1
			LEFT JOIN sujetos AS a2 ON a2.numero = s.aval2
			LEFT JOIN catsindicatos sa2 ON sa2.cve_sindicato = s.cve_sind_Aval2
			WHERE s.id_solicitud = 8000
			ORDER BY s.id_solicitud";

		$comando = $this->dbConexion->createCommand($consulta);
		$result = $comando->query()->readAll();
		
		if(count($result) > 0)
		{
			$this->txtTitular->Text = $result[0]["num_tit"];
			$this->txtFolio->Text = 8000;
			$this->txtFecha->Text = $result[0]["creada"];
			$this->txtNoUnicoTit->Text = $result[0]["num_tit"];
			$this->txtAntiguedadTit->Text = $result[0]["tit_ant"];
			$this->txtNombreTit->Text = $result[0]["titular"];
			$this->txtTipoTit->Text = "ACTIVO";
			$this->txtSindicatoTit->Text = $result[0]["tit_sind"];
			$this->txtNoUnicoAval1->Text = $result[0]["aval1"];
			$this->txtAntiguedadAval1->Text = $result[0]["aval1_ant"];
			$this->txtNombreAval1->Text = $result[0]["aval1_n"];
			$this->txtTipoAval1->Text = "ACTIVO";
			$this->txtSindicatoAval1->Text = $result[0]["aval1_sind"];
			$this->txtNoUnicoAval2->Text = $result[0]["aval2"];
			$this->txtAntiguedadAval2->Text = $result[0]["num_tit"];
			$this->txtNombreAval2->Text = $result[0]["aval2_n"];
			$this->txtTipoAval2->Text = "ACTIVO";
			$this->txtSindicatoAval2->Text = $result[0]["aval2_sind"];
			$this->datFechaFirmaAvales->Text = strtotime($result[0]["firma"]);
			$this->txtImporte->Text = $result[0]["importe"];
			$this->txtPlazo->Text = $result[0]["plazo"];
			$this->txtTasa->Text = $result[0]["tasa"];
			$this->txtInteres->Text = $result[0]["importe"] * $result[0]["plazo"] * $result[0]["tasa"] / 100;
			$this->txtSaldoAnterior->Text = $result[0]["saldo_anterior"];
			$this->txtDescuentos->Text = $result[0]["descuento"];
			$this->txtImpDescuentos->Text = 0;
			$this->txtImpPrestamos->Text = 0;
			$this->txtSeguro->Text = 0;
			$this->txtDiferencia->Text = 0;
			$this->txtImpCheque->Text = $result[0]["importe"];
		}
	}
}

?>