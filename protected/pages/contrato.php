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
			$consultaCon="SELECT MAX(id_contrato)+1 AS contrato FROM contrato"; 
				$comando = $this->dbConexion->createCommand($consultaCon); 
				$result = $comando->query()->readAll();
				$this->txtFoliocontrato->Text = $result[0]["contrato"];
				
	
			
		}
	
	}

	public function btnBuscar_onclick($sender,$param)
	{
		
				
		$folio=$this->txtFoliosolicitud->Text;
		$this->carga_solicitud($folio);	
	}
	public function carga_solicitud($id_solicitud)
	{
		$consulta = "SELECT s.id_solicitud as solicitudes ,creada, estatus_p, s.estatus as estatus,
			t.numero as num_tit, t.nombre AS titular, st.cve_sindicato AS tit_cve_sind, st.sindicato AS tit_sind, TIMESTAMPDIFF(YEAR, t.fec_ingre, CURDATE()) AS tit_ant,
			s.aval1, a1.nombre AS aval1_n, sa1.cve_sindicato AS aval1_cve_sind, sa1.sindicato AS aval1_sind, TIMESTAMPDIFF(YEAR, a1.fec_ingre, CURDATE()) AS aval1_ant,
			s.aval2, a2.nombre AS aval2_n, sa2.cve_sindicato AS aval2_cve_sind, sa2.sindicato AS aval2_sind, TIMESTAMPDIFF(YEAR, a2.fec_ingre, CURDATE()) AS aval2_ant,
			firma, importe, plazo, tasa, saldo_anterior, descuento, s.id_contrato_ant as id_contrato_ant,s.saldo_anterior as saldo_anterior
			,(SELECT num_cheque FROM contrato WHERE id_solicitud = s.id_solicitud AND estatus <> 'C') AS num_cheque
			,(SELECT fec_ingre  FROM sujetos WHERE numero = t.numero) AS fechaIngresoTit
			,(SELECT fec_ingre  FROM sujetos WHERE numero = s.aval1) AS fechaIngresoAval1
			,(SELECT fec_ingre  FROM sujetos WHERE numero = s.aval2) AS fechaIngresoAval2
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
		$intereses = round($result[0]["importe"] * $result[0]["plazo"] * $result[0]["tasa"] / 100);
		$descuento = ($result[0]["plazo"] * 2); 
		$LItemp = ($result[0]["importe"]) / $descuento;
		$LItemp = $LItemp * 100;
		$importeDescuento = $LItemp / 100;	
		$importeADescontar = $descuento * $importeDescuento;
		$Redondeo = ($result[0]["importe"] - $importeADescontar);
		$seguro = 0.0;
		$cheque = $result[0]["importe"] - ($intereses +  $result[0]["saldo_anterior"] + $seguro);
		
		$creada = $result[0]["creada"];
		$fechaIngresoTit = $result[0]["fechaIngresoTit"];
		$fechaIngresoAval1 = $result[0]["fechaIngresoAval1"];
		$fechaIngresoAval2 = $result[0]["fechaIngresoAval2"];
	
			$this->txtBuscarTitularr->Text = $result[0]["num_tit"];
			$this->txtFoliosolicitudd->Text =$result[0]["solicitudes"];
			$this->txtFechaAutorizasioon->Text = $creada;
			$this->txtNoUnicoTit->Text = $result[0]["num_tit"];
			$this->txtNombreTit->Text = $result[0]["titular"];
			$this->txtSindicatoTit->Text = $result[0]["tit_sind"];
			$this->txtNoUnicoAval1->Text = $result[0]["aval1"];
			$this->txtNombreAval1->Text = $result[0]["aval1_n"];
			$this->txtSindicatoAval1->Text = $result[0]["aval1_sind"];
			$this->txtNoUnicoAval2->Text = $result[0]["aval2"];
			$this->txtNombreAval2->Text = $result[0]["aval2_n"];
			$this->txtSindicatoAval2->Text = $result[0]["aval2_sind"];
			$this->txtFechaFirmaAvales2->Text = $result[0]["firma"];
			$this->txtImporte->Text = $result[0]["importe"];
			$this->txtPlazo->Text = $result[0]["plazo"];
			$this->txtTasa->Text = $result[0]["tasa"];
			$this->txtInteres->Text = number_format($intereses,2);	
			$this->txtSaldoAnterior->Text = $result[0]["saldo_anterior"];
			$this->txtDescuentos->Text = $descuento;
			$this->txtImpDescuentos->Text = number_format($importeDescuento,2);
			$this->txtImpPrestamos->Text = number_format($importeADescontar,2);
			$this->txtSeguro->Text = $seguro;
			$this->txtDiferencia->Text = number_format($Redondeo,2);
			$this->txtImpCheque->Text = number_format($cheque,2); 
			$this->txtImpChequeCan->Text = $cheque;
			$this->lblNumContrato->Text = $result[0]["id_contrato_ant"];
			$this->lblSaldoAnterior->Text = $result[0]["saldo_anterior"];
			
			$status  = $result[0]["estatus"];
			switch ($status) {
                case "S":
                  $this->lblEstatus->Text = "SOLICITADO"; 
                    break;
                case "A":
                    $this->lblEstatus->Text =  'AUTORIZADO'; 
					$this->txtCheque->visible="false";
					$this->lblNumCheque->Text = $result[0]["num_cheque"];
					$this->btnGuardar->visible="false";
                    break;
                case "C":
                    $this->lblEstatus->Text =  'CANCELADA'; 
					$this->txtCheque->visible="false";
					$this->lblNumCheque->Text = $result[0]["num_cheque"];
					$this->btnGuardar->visible="false";
                    break;
            	}
			$fechaCreadaSolicitud = date_create($creada);
			$IngresoTit = date_create($fechaIngresoTit);
			$IngresoAval1 = date_create($fechaIngresoAval1);
			$IngresoAval2 = date_create($fechaIngresoAval2);
			
			$TitularFecha = date_diff($IngresoTit, $fechaCreadaSolicitud);
			$TitularFecha = $TitularFecha->format('%Y Año %m Meses %d Dias');			
			$AvalFecha1 = date_diff($IngresoAval1, $fechaCreadaSolicitud);
			$AvalFecha1 = $AvalFecha1->format('%Y Año %m Meses %d Dias');
			$AvalFecha2 = date_diff($IngresoAval2, $fechaCreadaSolicitud);
			$AvalFecha2 = $AvalFecha2->format('%Y Año %m Meses %d Dias');
			
			$this->txtAntiguedadTit->Text = $TitularFecha; 
			$this->txtAntiguedadAval1->text = $AvalFecha1;
			$this->txtAntiguedadAval2->text = $AvalFecha2;
			$comando->execute();			
		}
	}
	public function textChanged($sender,$param) // cheque
    {
		if ($this->txtCheque->Text == ""){
			$this->lblEstatus->text =  'SOLICITADO';
			$this->btnGuardar->visible="false";
		}ELSE{
		$this->lblEstatus->text =  'AUTORIZADO';
			if ($this->lblEstatus->text =  'AUTORIZADO')
				$this->btnGuardar->visible="true";
			else
				$this->btnGuardar->visible="false";
		}
	}		
	public function btnguardar_callback($sender, $param) 
	{
		$consulta="insert into contrato (id_contrato,id_solicitud,creado,entrega_cheque,num_cheque, observacion, estatus, id_usuario, entrega_real, autorizado, congelado, seguro )"
				  ." values (:id_contrato,:txtFoliosolicitud,:txtFechaAutorizasioon,:txtFechaEntrgaCheque,:txtNumeroDeCheque,:observacion,:estatus,:id_usuario,:txtfecha,:txtFechaContrato,:congelado, :seguro)";

		$comando = $this->dbConexion->createCommand($consulta);
		//$this->txtFoliocontrato->Text
		$comando->bindValue(":id_contrato",$this->txtFoliocontrato->Text);
		$comando->bindValue(":txtFoliosolicitud",$this->txtFoliosolicitudd->Text);
		$comando->bindValue(":txtFechaAutorizasioon",$this->txtFechaAutorizasioon->Text);
		$comando->bindValue(":txtFechaEntrgaCheque",$this->datFechaEntrgaCheque->Text);
		$comando->bindValue(":txtNumeroDeCheque",$this->txtCheque->Text);
		$comando->bindValue(":observacion",'');
		$comando->bindValue(":estatus",'A');
		$comando->bindValue(":id_usuario",0);
		$comando->bindValue(":txtfecha",$this->txtfecha->Text);
		$comando->bindValue(":txtFechaContrato",$this->dattxtFechaContrato->Text);
		$comando->bindValue(":congelado",0);
		$comando->bindValue(":seguro",0);
				
		if($comando->execute()){
		   $consulta="UPDATE solicitud SET estatus = :estatus WHERE id_solicitud = :id_solicitud";
			$comando = $this->dbConexion->createCommand($consulta);
			$comando->bindValue(":id_solicitud",$this->txtFoliosolicitudd->Text);
			$comando->bindValue(":estatus",'A');
			$comando->execute();
		
			$consulta="UPDATE solicitud SET estatus = 'C' WHERE titular = :Titular and id_solicitud < :id_solicitud";
			$comando = $this->dbConexion->createCommand($consulta);
			$comando->bindValue(":id_solicitud",$this->txtFoliosolicitudd->Text);
			$comando->bindValue(":Titular",$this->txtBuscarTitularr->Text);
			$comando->execute();
			
			$consulta="UPDATE contrato SET estatus = 'C' WHERE  id_solicitud IN (SELECT id_solicitud FROM solicitud WHERE titular=:Titular) AND id_solicitud < :id_solicitud ORDER BY id_solicitud DESC";
			$comando = $this->dbConexion->createCommand($consulta);
			$comando->bindValue(":id_solicitud",$this->txtFoliosolicitudd->Text);
			$comando->bindValue(":Titular",$this->txtBuscarTitularr->Text);
			$comando->execute();
			if ($this->lblNumContrato->Text >  0)
			{
				$consulta="INSERT INTO movimientos (id_contrato, creacion, id_tipo_movto, descripcion, cargo, abono, id_usuario, aplicacion, id_descuento, activo)"
						 ."VALUES (:id_contrato, :creacion, :id_tipo_movto, :descripcion,:cargo,:abono, :id_usuario, :aplicacion, :id_descuento, :activo)";
				$comando = $this->dbConexion->createCommand($consulta);
				$comando->bindValue(":id_contrato",$this->lblNumContrato->Text);
				$comando->bindValue(":creacion",$this->txtfecha->Text);
				$comando->bindValue(":id_tipo_movto",3);
				$comando->bindValue(":descripcion",'baja de prestamo');
				$comando->bindValue(":cargo",0.00);
				$comando->bindValue(":abono",$this->lblSaldoAnterior->Text);
				$comando->bindValue(":id_usuario",0);
				$comando->bindValue(":aplicacion",'');
				$comando->bindValue(":id_descuento",0);
				$comando->bindValue(":activo",1);
				$comando->execute();
					
				
			}else{
			
				$consulta="INSERT INTO movimientos (id_contrato, creacion, id_tipo_movto, descripcion, cargo, abono, id_usuario, aplicacion, id_descuento, activo)"
						 ."VALUES (:id_contrato, :creacion, :id_tipo_movto, :descripcion,:cargo,:abono, :id_usuario, :aplicacion, :id_descuento, :activo)";
				$comando = $this->dbConexion->createCommand($consulta);
				$comando->bindValue(":id_contrato",$this->txtFoliocontrato->Text);
				$comando->bindValue(":creacion",$this->txtfecha->Text);
				$comando->bindValue(":id_tipo_movto",1);
				$comando->bindValue(":descripcion",'importe de prestamo');
				$comando->bindValue(":cargo",$this->txtImpChequeCan->Text);
				$comando->bindValue(":abono",0.00);
				$comando->bindValue(":id_usuario",0);
				$comando->bindValue(":aplicacion",'');
				$comando->bindValue(":id_descuento",0);
				$comando->bindValue(":activo",1);
				$comando->execute();
		
			}
			$contratoSig = $this->txtFoliocontrato->Text;
			$this->Page->CallbackClient->callClientFunction("Mensaje", "alert('LOS DATOS SE GUARDARON CORRECTAMENTE')\n\n"."Num. contrato:".$contratoSig); 											   
		}
		else{
			$this->Page->CallbackClient->callClientFunction("Mensaje","alert('Error - NO SE PUDO GUARDAR LOS DATOS');");
		}
		
	}
	 public function btnImprimir_onclick($sender,$param)
	{
		if($this->txtCheque->Text > 0){
			$contratos=$this->txtFoliosolicitudd->Text;
			$this->ClientScript->RegisterBeginScript("Mensaje","alert('Se guardo correctamente');" .
				"open('index.php?page=reportes.contratopdf&id=$contratos', '_blank');\n" .
				"open('index.php?page=reportes.pagarepdf&id=$contratos', '_blank');");
				
				$this->Limpiar_Campos();
		}else {
			$titular=$this->txtNoUnicoTit->Text;		
			$this->ClientScript->RegisterBeginScript("Mensaje","alert('Ingrese el número de pagare');");	
			}
	}
	
	
	public function Limpiar_Campos($campos = null)
	{
			$this->txtBuscarTitularr->Text = '';
			$this->txtFoliosolicitudd->Text = '';
			$this->txtFechaAutorizasioon->Text = '';
			$this->txtNoUnicoTit->Text = '';
			$this->txtAntiguedadTit->Text = '';
			$this->txtNombreTit->Text = '';
			$this->txtSindicatoTit->Text = '';
			$this->txtNoUnicoAval1->Text = '';
			$this->txtAntiguedadAval1->Text = '';
			$this->txtNombreAval1->Text = '';
			$this->txtSindicatoAval1->Text = '';
			$this->txtNoUnicoAval2->Text = '';
			$this->txtAntiguedadAval2->Text = '';
			$this->txtNombreAval2->Text = '';
			$this->txtSindicatoAval2->Text = '';
			$this->txtFechaFirmaAvales2->Text = '';
			$this->txtImporte->Text = '';
			$this->txtPlazo->Text = '';
			$this->txtTasa->Text = '';
			$this->txtInteres->Text = '';
			$this->txtSaldoAnterior->Text = '';
			$this->txtDescuentos->Text = '';
			$this->txtImpDescuentos->Text = '';
			$this->txtImpPrestamos->Text = '';
			$this->txtSeguro->Text = '';
			$this->txtDiferencia->Text = '';
			$this->txtImpCheque->Text = '';
			$this->txtImpChequeCan->Text = '';
			$this->lblEstatus->Text = '';
			$this->txtCheque->Text = '';
	}
}

?>