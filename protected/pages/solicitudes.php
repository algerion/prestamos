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
	var $intereses;

	public function onLoad($param)
	{
		parent::onLoad($param);
	
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
		if(!$this->IsPostBack)
		{
			$this->txtFecha->Text = date("Y-m-d");
			$opcion=$this->request["opcion"];
			$this ->txtPrueba->text=$opcion;
			if ($opcion == "modificar") 
			{
				$this->btnBuscar->visible="true";
				$this->btnGuardar->visible="false";
				$this->txtFolio->BackColor="yellow";
			}
		}
	
	}
	public function btnguardar_callback($sender, $param)
{
	$consulta="insert into solicitud (creada,titular,antiguedad,tipo_empleado,cve_sindicato,aval1,antig_aval1,tipo_aval1,cve_sind_aval1,aval2,antig_aval2,tipo_aval2"
		.", cve_sind_aval2, importe,plazo,tasa,saldo_anterior,id_contrato_ant,descuento,importe_pa_tit, porcentaje_pa_tit,importe_pa_aval1, porcentaje_pa_aval1"
		.", importe_pa_aval2, porcentaje_pa_aval2, firma ,observacion, firma1,firma2, estatus, id_usuario,  seguro, AntLetraTi) values " 
		."(:txtFecha,:txtTitular,:txtAntiguedadTit,:txtTipoNumTit,:txtSindicatoNumTit,:txtNoUnicoAval1,:txtAntiguedadAval1,:txtTipoAval1,:txtSindicatoNumAval1,:txtNoUnicoAval2,"
		.":txtAntiguedadAval2,:txtTipoAval2,:txtSindicatoNumAval2,:txtImporte,:txtPlazo,:txtTasa,:txtSaldoAnterior,:msg18,:txtDescuentos,:msg20,:msg21,:msg22,:msg23,:msg24"
		.",:msg25,:datFechaFirmaAvales,:msg27,:txtNombreAval1,:txtNombreAval2,:estatus,:msg31,:msg32, :txtAntiguedad)";
		$descuento = ($this->txtPlazo->Text * 2);
		
		$comando = $this->dbConexion->createCommand($consulta);		
		$comando->bindValue(":txtFecha",$this->txtFecha->Text);
		$comando->bindValue(":txtTitular",$this->txtNoUnicoTit->Text);
		$comando->bindValue(":txtAntiguedadTit",$this->txtAntiguedadNumTit->Text);
		$comando->bindValue(":txtTipoNumTit",$this->txtTipoNumTit->Text);
		$comando->bindValue(":txtSindicatoNumTit",$this->txtSindicatoNumTit->Text);
		$comando->bindValue(":txtNoUnicoAval1",$this->txtNoUnicoAval1->Text);
		$comando->bindValue(":txtAntiguedadAval1",$this->txtAntiguedadNumAval1->Text);
		$comando->bindValue(":txtTipoAval1",$this->txtTipoAval1->Text);	
		$comando->bindValue(":txtSindicatoNumAval1",$this->txtSindicatoNumAval1->Text);
		$comando->bindValue(":txtNoUnicoAval2",$this->txtNoUnicoAval2->Text);
		$comando->bindValue(":txtAntiguedadAval2",$this->txtAntiguedadNumAval2->Text);
		$comando->bindValue(":txtTipoAval2",$this->txtTipoAval2->Text);
		$comando->bindValue(":txtSindicatoNumAval2",$this->txtSindicatoNumAval2->Text);
		$comando->bindValue(":txtImporte",$this->txtImporte->Text); 
		$comando->bindValue(":txtPlazo",$this->txtPlazo->Text);
		$comando->bindValue(":txtTasa",1.00);
		$comando->bindValue(":txtSaldoAnterior",$this->txtSaldoAnterior->Text);
		$comando->bindValue(":msg18",0);
		$comando->bindValue(":txtDescuentos",$descuento);
		$comando->bindValue(":msg20",0);
		$comando->bindValue(":msg21",0);
		$comando->bindValue(":msg22",0);
		$comando->bindValue(":msg23",0);
		$comando->bindValue(":msg24",0);
		$comando->bindValue(":msg25",0);
		$comando->bindValue(":datFechaFirmaAvales",$this->datFechaFirmaAvales->Text);
		$comando->bindValue(":msg27",'');
		$comando->bindValue(":txtNombreAval1",$this->txtNombreAval1->Text);
		$comando->bindValue(":txtNombreAval2",$this->txtNombreAval2->Text);
		$comando->bindValue(":estatus","S");
		$comando->bindValue(":msg31",4);
		$comando->bindValue(":msg32",0);
		$comando->bindValue(":txtAntiguedad",$this->txtAntiguedadTit->Text);
		$this->calculo();
		$MesesTrabajo = $this->txtMesTTit->Text;
		$comando->execute();
		/*switch ($MesesTrabajo) 
		{
                case ($MesesTrabajo  >= 7000.00  and $MesesTrabajo  <= 50000.00):
                  if($this->txtImporte->Text  >= 7000.00  and $this->txtImporte->Text  <= 50000.00  )
					{
						$this->lblNotaVal->visible="false";
						$comando->execute();
						$this->btnImprimir->visible="true";
					}else
					{
						$this->lblNotaVal->visible="true";
						$this->lblNotaVal->Text = 'No cumple con la Antigüedad para el préstamo de: $'.$this->txtImporte->Text;
					}
                    break;
                case ($MesesTrabajo >= 15.00):
                   if($this->txtImporte->Text  >= 7000.00  and $this->txtImporte->Text  <= 100000.00    )
						{
							$this->lblNotaVal->visible="false";
							$comando->execute();
							$this->btnImprimir->visible="true";
					}else
						{
							$this->lblNotaVal->visible="true";
							$this->lblNotaVal->Text = 'No cumple con la Antigüedad para el préstamo de: $'.$this->txtImporte->Text;
						}	 
                    break;
        }*/
}
	
	
	 public function btnImprimir_onclick($sender,$param)
		{
		if($this->txtSindicatoNumTit->Text > 0){
			$titular=$this->txtNoUnicoTit->Text;
			$this->ClientScript->RegisterBeginScript("Mensaje","alert('Se guardo correctamente');" .
				"open('index.php?page=reportes.solicitudSindicatopdf&id=$titular', '_blank');");
				$this->Limpiar_Campos();
		}else {
			$titular=$this->txtNoUnicoTit->Text;		
			$this->ClientScript->RegisterBeginScript("Mensaje","alert('Se guardo correctamente');" .
			"open('index.php?page=reportes.solicitudespdf&id=$titular', '_blank');");
				$this->Limpiar_Campos();
			}
		}
	public function calculo($valor = null)
		{
			$descuento = ($this->txtPlazo->Text * 2); //txtnumDescuentos = Val(txtPlazo) * 2
			$LItemp = ($this->txtImporte->Text) / $descuento;
			$LItemp = $LItemp * 100;
			$importeDescuento = round($LItemp / 100);									  
			$importeADescontar = $descuento * $importeDescuento;				   
			$Redondeo = ($this->txtImporte->Text) - $importeADescontar;
			
			$this->txtDescuentos->Text = $descuento;
			$this->txtInteres->Text =  number_format((($this->txtImporte->Text) * ($this->txtPlazo->Text) * (1.00 / 100)),2); 
			$this->txtImpDescuentos->Text = number_format($importeDescuento,2);
			$this->txtImpPrestamos->Text = number_format($importeADescontar,2);
			$this->txtDiferencia->Text = $Redondeo;
			
		}
	public function btnBuscar_onclick($sender,$param)
	{
		$folio=$this->txtFolio->Text;
		$this->carga_solicitud($folio);	
	}	
	public function btnModificar_onclick($sender,$param)
	{
		$consulta="UPDATE  solicitud SET creada = :txtFecha,titular =:txtTitular,antiguedad = :txtAntiguedadTit,tipo_empleado = :txtTipoNumTit,cve_sindicato = :txtSindicatoNumTit,aval1 = :txtNoUnicoAval1,"
		."antig_aval1 = :txtAntiguedadAval1,tipo_aval1 = :txtTipoAval1,cve_sind_aval1 = :txtSindicatoNumAval1,aval2 = :txtNoUnicoAval2,antig_aval2 = :txtAntiguedadAval2,tipo_aval2 = :txtTipoAval2"
		.", cve_sind_aval2 = :txtSindicatoNumAval2, importe = :txtImporte,plazo = :txtPlazo,tasa = :txtTasa,saldo_anterior = :txtSaldoAnterior,id_contrato_ant = :id_contrato_ant,descuento = :txtDescuentos"
		.",importe_pa_tit = :importe_pa_tit, porcentaje_pa_tit = :porcentaje_pa_tit,importe_pa_aval1 = :importe_pa_aval1, porcentaje_pa_aval1 = :porcentaje_pa_aval1, importe_pa_aval2 = :importe_pa_aval2 "
		.", porcentaje_pa_aval2 = :porcentaje_pa_aval2, firma = :datFechaFirmaAvales ,observacion = :observacion, firma1 = :txtNombreAval1,firma2 = :txtNombreAval2, estatus = :estatus, id_usuario = :id_usuario"
		.",  seguro = :seguro "
		."where id_solicitud = :id_solicitud";
		$descuento = ($this->txtPlazo->Text * 2);
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_solicitud",$this ->txtFolio->text);
		$comando->bindValue(":txtFecha",$this->txtFecha->Text);
		$comando->bindValue(":txtTitular",$this->txtNoUnicoTit->Text);
		$comando->bindValue(":txtAntiguedadTit",$this->txtAntiguedadNumTit->Text);
		$comando->bindValue(":txtTipoNumTit",$this->txtTipoNumTit->Text);
		$comando->bindValue(":txtSindicatoNumTit",$this->txtSindicatoNumTit->Text);
		$comando->bindValue(":txtNoUnicoAval1",$this->txtNoUnicoAval1->Text);
		$comando->bindValue(":txtAntiguedadAval1",$this->txtAntiguedadNumAval1->Text);
		$comando->bindValue(":txtTipoAval1",$this->txtTipoAval1->Text);	
		$comando->bindValue(":txtSindicatoNumAval1",$this->txtSindicatoNumAval1->Text);
		$comando->bindValue(":txtNoUnicoAval2",$this->txtNoUnicoAval2->Text);
		$comando->bindValue(":txtAntiguedadAval2",$this->txtAntiguedadNumAval2->Text);
		$comando->bindValue(":txtTipoAval2",$this->txtTipoAval2->Text);
		$comando->bindValue(":txtSindicatoNumAval2",$this->txtSindicatoNumAval2->Text);
		$comando->bindValue(":txtImporte",$this->txtImporte->Text);
		$comando->bindValue(":txtPlazo",$this->txtPlazo->Text);
		$comando->bindValue(":txtTasa",$this->txtTasa->Text);
		$comando->bindValue(":txtSaldoAnterior",$this->txtSaldoAnterior->Text);
		$comando->bindValue(":id_contrato_ant",0);
		$comando->bindValue(":txtDescuentos",$this->txtDescuentos->Text);
		$comando->bindValue(":importe_pa_tit",0);
		$comando->bindValue(":porcentaje_pa_tit",0);
		$comando->bindValue(":importe_pa_aval1",0);
		$comando->bindValue(":porcentaje_pa_aval1",0);
		$comando->bindValue(":importe_pa_aval2",0);
		$comando->bindValue(":porcentaje_pa_aval2",0);
		$comando->bindValue(":datFechaFirmaAvales",$this->datFechaFirmaAvales->Text);
		$comando->bindValue(":observacion",'');
		$comando->bindValue(":txtNombreAval1",$this->txtNombreAval1->Text);
		$comando->bindValue(":txtNombreAval2",$this->txtNombreAval2->Text);
		$comando->bindValue(":estatus","A");
		$comando->bindValue(":id_usuario",4);
		$comando->bindValue(":seguro",0);
	    $MesesTrabajo = $this->txtMesTTit->Text;
		
		switch ($MesesTrabajo) 
		{
                case ($MesesTrabajo  >= 0.61 and $MesesTrabajo  < 15.0):
                  if($this->txtImporte->Text  >= 7000.00  and $this->txtImporte->Text  <= 50000.00  )
					{
						$this->lblNotaVal->visible="false";
						$comando->execute();
						$this->btnImprimir->visible="true";
					}else
					{
						$this->lblNotaVal->visible="true";
						$this->lblNotaVal->Text = 'No cumple con la Antigüedad para el préstamo de: $'.$this->txtImporte->Text;
					}
                    break;
                case ($MesesTrabajo >= 15.00):
                   if($this->txtImporte->Text  >= 7000.00  and $this->txtImporte->Text  <= 100000.00    )
						{
							$this->lblNotaVal->visible="false";
							$comando->execute();
							$this->btnImprimir->visible="true";
					}else
						{
							$this->lblNotaVal->visible="true";
							$this->lblNotaVal->Text = 'No cumple con la Antigüedad para el préstamo de: $'.$this->txtImporte->Text;
						}	 
                    break;
        }
	}
	public function txtNoUnico_CallBack($sender, $param)
	{
		$this->Rellena_Datos($sender->Text, str_replace("txtNoUnico", "", $sender->ID));
	}
	
	public function Rellena_Datos($num_unico, $sufijo)
	{
		$result = Conexion::Retorna_Consulta($this->dbConexion, "sujetos", array("numero","nombre", "fec_ingre", "sindicato", "tipo"), array("numero"=>$num_unico));
		if(count($result) > 0)
		{
			$intervalo = date_diff(date_create($result[0]["fec_ingre"]), new DateTime("now"));
			$formatoD = '%d dias';
			$formatoM = '%m meses';
			$formatoDIAA = '%a'; 
			
			if($intervalo->format('%y') > 100){
				$formato = 'Desconocida';
				}
			elseif($intervalo->format('%y') > 0){
				$formato = '%y años ' . $formatoM ." ".$formatoD;
				$mesLine = '%y'.'.'.'%m'.'%d';
				$formatoANIO = '%y'; 
				$formatoDIAS = $formatoDIAA;
				}
			$ant = "txtAntiguedad" . $sufijo;
			$this->$ant->Text = $intervalo->format($formato);
			$mesesTras = (($intervalo->format($formatoDIAS))/ 365.25)*12; 
			$dia = "txtAntiguedadNum" . $sufijo;
			$this->$dia->Text =$intervalo->format($mesesTras);
			
			$mesLInea = "txtMesTTit"; 
			$this->$mesLInea->Text =$intervalo->format($mesLine);
			
			$MesTranscurrido = $intervalo->format($mesLine);
			if ($MesTranscurrido < 0.61) {
			$this->lblNotaVal->visible="true";
			$note = 'No cumple con la Antigüedad mínima 6 mese 1 día para el prestamos';
			}else {
				$note = '';
			}
			$Nota = "lblNotaVal";
			$this->$Nota->Text =$note;
			
			$nom = "txtNombre" . $sufijo;
			$this->$nom->Text = $result[0]["nombre"];
			$nomNum = "txtSindicatoNum" . $sufijo;
			$this->$nomNum->Text = $result[0]["sindicato"];
			
			$TipoNum = "txtTipoNum" . $sufijo;
			$this->$TipoNum->Text = $result[0]["tipo"];
		    
			$RespUnico = "txtNoUnicoResp". $sufijo;;
			$this->$RespUnico->Text =$result[0]["numero"];
			
			$tipo = Conexion::Retorna_Campo($this->dbConexion, "tipo_empleado", "texto", array("tipo_empleado"=>$result[0]["tipo"]));
			$tip = "txtTipo" . $sufijo;
			$this->$tip->Text = $tipo;
			$sindicato = Conexion::Retorna_Campo($this->dbConexion, "catsindicatos", "sindicato", array("cve_sindicato"=>$result[0]["sindicato"]));
			$sin = "txtSindicato" . $sufijo;
			$this->$sin->Text = $sindicato;
			
		}
		
		
		switch ($sufijo) {
                case ("txtNoUnico".$sufijo == "txtNoUnicoTit"):	
					$resultSTit = Conexion::Retorna_Campo($this->dbConexion, "solicitud", "count(titular)", array("titular"=>$num_unico), " AND (estatus = 'S')");
					if($resultSTit >= 1){
						$this->lblSolicitadasTit->visible="true";
						$this->lblSolicitadasTit->Text = "SOLICITADAS: "." ".$resultSTit;
						$this->btnGuardar->visible="false";	
					}else {
						$this->lblSolicitadasTit->visible="false";
						$this->btnGuardar->visible="true";	
					}
					$resultATit = Conexion::Retorna_Campo($this->dbConexion, "solicitud", "count(titular)", array("titular"=>$num_unico), " AND (estatus = 'A')");
					if($resultATit >= 1){
						$this->lblAutorizadasTit->visible="false";
						$this->lblAutorizadasTit->Text = " "."AUTORIZADAS: "." ".$resultATit;
					}else {
						$this->lblAutorizadasTit->visible="false";
					}
                    break;
                case ("txtNoUnico".$sufijo == "txtNoUnicoAval1"):
					$resultSAval1 = Conexion::Retorna_Campo($this->dbConexion, "solicitud", "count(aval1)", array("aval1"=>$num_unico), " AND (estatus = 'S')");
					if($resultSAval1 >= 3){
						$this->txtNoUnicoRespAval1->Text  = 1;
						$this->lblSolicitadasAval1->visible="true";
						$this->lblSolicitadasAval1->Text = "SOLICITADAS: "." ".$resultSAval1;
					}else {
						$this->txtNoUnicoRespAval1->Text  = 0;
						$this->lblSolicitadasAval1->visible="false";
					}
					$resultAAval1 = Conexion::Retorna_Campo($this->dbConexion, "solicitud", "count(aval1)", array("aval1"=>$num_unico), " AND (estatus = 'A')");
					if($resultAAval1 >= 3){
						$this->lblAutorizadasAval1->visible="true";
						$this->lblAutorizadasAval1->Text = " "."AUTORIZADAS: "." ".$resultAAval1;
					}else {
						$this->lblAutorizadasAval1->visible="false";
					}
                    break;
                case ("txtNoUnico".$sufijo == "txtNoUnicoAval2"):
				
					$resultSAval2 = Conexion::Retorna_Campo($this->dbConexion, "solicitud", "count(aval2)", array("aval2"=>$num_unico), " AND (estatus = 'S')");
					if($resultSAval2 >= 3){
						$this->lblSolicitadasAval2->visible="true";
						$this->lblSolicitadasAval2->Text = "SOLICITADAS: "." ".$resultSAval2;
					}else {
						$this->lblSolicitadasAval2->visible="false";
					}
					$resultAAval2 = Conexion::Retorna_Campo($this->dbConexion, "solicitud", "count(aval2)", array("aval2"=>$num_unico), " AND (estatus = 'A')");
					if($resultAAval2 >= 3){
						$this->lblAutorizadasAval2->visible="true";
						$this->lblAutorizadasAval2->Text = " "."AUTORIZADAS: "." ".$resultAAval2;
					}else {
						$this->lblAutorizadasAval2->visible="false";
					}
                    break;
            	}
				
	}

	
	public function carga_solicitud($id_solicitud)
	{
		$consulta = "SELECT creada, estatus_p, 
			t.numero as num_tit, t.nombre AS titular, st.cve_sindicato AS tit_cve_sind, st.sindicato AS tit_sind, TIMESTAMPDIFF(YEAR, t.fec_ingre, CURDATE()) AS tit_ant,
			s.aval1, a1.nombre AS aval1_n, sa1.cve_sindicato AS aval1_cve_sind, sa1.sindicato AS aval1_sind, TIMESTAMPDIFF(YEAR, a1.fec_ingre, CURDATE()) AS aval1_ant,
			s.aval2, a2.nombre AS aval2_n, sa2.cve_sindicato AS aval2_cve_sind, sa2.sindicato AS aval2_sind, TIMESTAMPDIFF(YEAR, a2.fec_ingre, CURDATE()) AS aval2_ant,
			firma, importe, plazo, tasa, saldo_anterior, descuento,s.estatus as estatus
			FROM Solicitud s
			LEFT JOIN estatus_prestamo ep ON s.estatus = ep.id_estatus_p
			LEFT JOIN sujetos AS t ON t.numero = s.titular
			LEFT JOIN catsindicatos st ON st.cve_sindicato = s.cve_sindicato
			LEFT JOIN sujetos AS a1 ON a1.numero= s.aval1
			LEFT JOIN catsindicatos sa1 ON sa1.cve_sindicato = s.cve_sind_Aval1
			LEFT JOIN sujetos AS a2 ON a2.numero = s.aval2
			LEFT JOIN catsindicatos sa2 ON sa2.cve_sindicato = s.cve_sind_Aval2
			WHERE s.id_solicitud = :id_solicitud";
			
		$comando = $this->dbConexion->createCommand($consulta); 
		$comando->bindValue(":id_solicitud",$id_solicitud);
		$result = $comando->query()->readAll();
		if(count($result) > 0)
		{
					
			//$this->txtTitular->Text = $result[0]["num_tit"];
			$this->txtFolio->Text =$id_solicitud;
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
			$status  = $result[0]["estatus"];
			$this->calculo();
			switch ($status) {
                case "S":
                  $this->lblIntereses->Text = "SOLICITADO"; 
                    break;
                case "A":
                    $this->lblIntereses->Text =  'AUTORIZADO'; 
                    break;
                case "C":
                    $this->lblIntereses->Text =  'CANCELADA'; 
                    break;
            	}
			
			$this->btnModificar->visible="true";
		}
	}
	
		public function Limpiar_Campos($campos = null)
	{
		//$this->txtTitular->Text = '';
			$this->txtFolio->Text ='';
			$this->txtFecha->Text = '';
			$this->txtNoUnicoTit->Text = '';
			$this->txtAntiguedadTit->Text = '';
			$this->txtNombreTit->Text = '';
			$this->txtTipoTit->Text = '';
			$this->txtSindicatoTit->Text = '';
			$this->txtNoUnicoAval1->Text = '';
			$this->txtAntiguedadAval1->Text = '';
			$this->txtNombreAval1->Text = '';
			$this->txtTipoAval1->Text = '';
			$this->txtSindicatoAval1->Text = '';
			$this->txtNoUnicoAval2->Text = '';
			$this->txtAntiguedadAval2->Text ='';
			$this->txtNombreAval2->Text = '';
			$this->txtTipoAval2->Text = '';
			$this->txtSindicatoAval2->Text = '';
			$this->datFechaFirmaAvales->Text = '';
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
	}
	
	
}

?>