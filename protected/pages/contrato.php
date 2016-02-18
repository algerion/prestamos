<?php
//Prado::using('System.Util.*'); //TVarDump
/*Prado::using('System.Web.UI.ActiveControls.*');
include_once('../compartidos/clases/listas.php');
include_once('../compartidos/clases/conexion.php');
include_once('../compartidos/clases/envia_mail.php');
include_once('../compartidos/clases/charset.php');
*/
Prado::using('System.Web.UI.ActiveControls.*');
include_once('../compartidos/clases/conexion.php');
class contrato extends TPage
{
	var $dbConexion;
	

	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
		if(!$this->IsPostBack)
		{
			$this->txtfecha->Text = date("Y-m-d");
			//$this->carga_solicitud();
			
		}
	
	}
	public function btnBuscar_onclick($sender,$param)
	{
		
				
		$folio=$this->txtFoliosolicitud->Text;
		$this->carga_solicitud($folio);	
	}
	public function carga_solicitud($id_solicitud)
	{
		$consulta = "SELECT s.id_solicitud as solicitudes ,creada, estatus_p, 
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
			WHERE s.id_solicitud = (SELECT MAX(id_solicitud) AS id_solicitud FROM Solicitud WHERE titular = :id_solicitud OR id_solicitud = :id_solicitud)";
			
			
		$comando = $this->dbConexion->createCommand($consulta); 
		$comando->bindValue(":id_solicitud",$id_solicitud);
		$result = $comando->query()->readAll();
		if(count($result) > 0)
		{	
			$this->txtBuscarTitularr->Text = $result[0]["num_tit"];
			$this->txtFoliosolicitudd->Text =$result[0]["solicitudes"];
			$this->txtFechaAutorizasioon->Text = $result[0]["creada"];
			$this->txtNoUnicoTit->Text = $result[0]["num_tit"];
			$this->txtAntiguedadTit->Text = $result[0]["tit_ant"];
			$this->txtNombreTit->Text = $result[0]["titular"];
			$this->txtSindicatoTit->Text = $result[0]["tit_sind"];
			$this->txtNoUnicoAval1->Text = $result[0]["aval1"];
			$this->txtAntiguedadAval1->Text = $result[0]["aval1_ant"];
			$this->txtNombreAval1->Text = $result[0]["aval1_n"];
			$this->txtSindicatoAval1->Text = $result[0]["aval1_sind"];
			$this->txtNoUnicoAval2->Text = $result[0]["aval2"];
			$this->txtAntiguedadAval2->Text = $result[0]["num_tit"];
			$this->txtNombreAval2->Text = $result[0]["aval2_n"];
			$this->txtSindicatoAval2->Text = $result[0]["aval2_sind"];
			$this->txtFechaFirmaAvales2->Text = $result[0]["firma"];
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
			$comando->execute();
			
		}
	}
	public function btnGuardar_Click($sender, $param) 
	{
		$consulta="insert into contrato (id_solicitud,creado,entrega_cheque,num_cheque, observacion, estatus, id_usuario, entrega_real, autorizado, congelado, seguro )"
				  ." values (:txtFoliosolicitud,:txtFechaAutorizasioon,:txtFechaEntrgaCheque,:txtNumeroDeCheque,:observacion,:estatus,:id_usuario,:txtfecha,:txtFechaContrato,:congelado, :seguro)";

		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":txtFoliosolicitud",$this->txtFoliosolicitudd->Text);
		$comando->bindValue(":txtFechaAutorizasioon",$this->txtFechaAutorizasioon->Text);
		$comando->bindValue(":txtFechaEntrgaCheque",$this->datFechaEntrgaCheque->Text);
		$comando->bindValue(":txtNumeroDeCheque",$this->txtNumeroDeCheque->Text);
		$comando->bindValue(":observacion",'');
		$comando->bindValue(":estatus",'A');
		$comando->bindValue(":id_usuario",0);
		$comando->bindValue(":txtfecha",$this->txtfecha->Text);
		$comando->bindValue(":txtFechaContrato",$this->dattxtFechaContrato->Text);
		$comando->bindValue(":congelado",0);
		$comando->bindValue(":seguro",0);
		$comando->execute();
		
		$contratos=$this->txtFoliosolicitudd->Text;
				$this->ClientScript->RegisterBeginScript("Mensaje","alert('Se guardo correctamente');" .
		  		"document.location.href='index.php?page=reportes.contratopdf&id=$contratos','_blank'; ");
	}
}

?>
