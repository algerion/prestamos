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
			$this->btnGuardar->visible="false";	
			$this->txtFecha->Text = date("Y-m-d");
			$opcion=$this->request["opcion"];
			$this ->txtPrueba->text=$opcion;
			if ($opcion == "modificar") 
			{
				$this->btnBuscar->visible="true";
				$this->btnGuardar->visible="false";
				$this->btnCancelar->visible="false";
				$this->txtFolio->BackColor="yellow";	
			}
		}	
	}
	public function textChangedImporte($sender,$param)
    {
		if ($this->txtImporte1->Text == ""){
			$this->txtPlazo1->Text = '';
			$this->txtTasa1->Text = '';
			$this->lblIntereses1->Text = '';
			$this->lblSaldoAnterior->Text = '';
			$this->lblDescuentos->Text = '';
			$this->lblImpDescuentos->Text = '';
			$this->lblImpPrestamos->Text = '';
			$this->lblSeguro->Text = '';
			$this->lblDiferencia->Text = '';
			$this->lblImpCheque->Text = '';
			$this->lblImpChequeResp->Text = '';
		}else{
			$this->calculo();
		}
	}
	public function textChanged($sender,$param)
    {
	if ($this->txtPlazo1->Text <> ''){
		if ($this->txtNoUnicoTit->Text  === "" or $this->txtNoUnicoAval1->Text  === "" or $this->txtNoUnicoAval2->Text  === ""){
			$this->btnGuardar->visible="false";	
			$this->lblNotaValCamposVacios->visible="true";
			$this->lblNotaValCamposVacios->Text = 'Ingresa  los datos correctamente.';	
		}else{
			
			$this->calculo();
		}
	}else{
			$this->txtImporte1->Text  = '';
			$this->txtTasa1->Text = '';
			$this->lblIntereses1->Text = '';
			$this->lblSaldoAnterior->Text = '';
			$this->lblDescuentos->Text = '';
			$this->lblImpDescuentos->Text = '';
			$this->lblImpPrestamos->Text = '';
			$this->lblSeguro->Text = '';
			$this->lblDiferencia->Text = '';
			$this->lblImpCheque->Text = '';
			$this->lblImpChequeResp->Text = '';
	}
    }
	public function calculo($campos = null)
	{
		$this->lblNotaValCamposVacios->visible="false";
			$intereses =  THttpUtility::htmlEncode(round ((($this->txtImporte1->Text) * ($this->txtPlazo1->Text) * (1.00 / 100))));
			$importe = $this->txtImporte1->Text;
			$saldoAnterior = $this->lblSaldoAnterior->text;
			$seguro =($this->lblSeguro->text = 0.0);
			$descuento = ($this->txtPlazo1->Text * 2); 
			$LItemp = ($importe) / $descuento;
			$LItemp = $LItemp * 100;
			$importeDescuento = $LItemp / 100;	
			$importeADescontar = $descuento * $importeDescuento;			
			$DescQuincena = $importe  / $descuento;
			$Redondeo = ($importe) - $importeADescontar;
			
			$this->lblIntereses1->Text = number_format($intereses,2);
			$this->lblDescuentos->text = number_format($descuento,2);
			$this->lblImpDescuentos->text = number_format($importeDescuento,2);
			$this->lblImpPrestamos->text =  number_format($importeADescontar,2);
			$this->lblDiferencia->text = number_format($Redondeo,2);
			
			$cheque = $importe - ($intereses + $saldoAnterior + $seguro);
			$this->lblImpCheque->text = number_format($cheque,2);
			$this->lblImpChequeResp->text = $cheque;
			
			if ($cheque  < 0 ){
			$this->lblValImpSanAnterior->Text = "El importe: $" .number_format($this->txtImporte1->Text,2).'<br/>'."es menor al saldo anterior: $".number_format($saldoAnterior,2); //.'\n\n'.
			}else{
				$this->lblValImpSanAnterior->visible="false";
			}
			if ($this->lblSolicitadasTit->Text > 0  or $this->lblSolicitadasAval1->Text > 0 or $this->lblAutorizadasAval1->Text > 0 or $this->lblSolicitadasAval2->Text > 0 or $this->lblAutorizadasAval2->Text > 0 )
					{
						$this->btnGuardar->visible="false";	
				}else{
						$this->btnGuardar->visible="true";	
					}
			if ($this->txtTipoTit->Text == "JUBILADO" ){
				if($this->txtImporte1->Text > 0){
						$this->lblNotaVal->visible="false";
						$this->btnImprimir->visible="true";
						$this->btnGuardar->visible="true";
				}else{
						$this->btnImprimir->visible="false";
						$this->lblNotaVal->visible="true";
						$this->lblNotaVal->Text = 'El importe tiene que ser mayor a:'.$this->txtImporte1->Text;
				}
			}else
			{
				$MesesTrabajo = $this->txtAntiguedadNumTit->Text;	
				switch ($MesesTrabajo) 
				{
					case ($MesesTrabajo  >= 0.61 and $MesesTrabajo  < 15.0):		
							if($this->txtImporte1->Text  >= 7000.00  and $this->txtImporte1->Text  <= 50000.00  )
							{
								$this->lblNotaVal->visible="false";
							}else{
								$this->btnImprimir->visible="false";
								$this->lblNotaVal->visible="true";
								$this->lblNotaVal->Text = 'No cumple con la Antigüedad para el préstamo de 50000: $'.$this->txtImporte1->Text;
							}
					break;
					case ($MesesTrabajo >= 15.00):
						   if($this->txtImporte1->Text  >= 7000.00  and $this->txtImporte1->Text  <= 100000.00    )
							{
								$this->lblNotaVal->visible="false";
								$this->btnImprimir->visible="true";
							}else{
								$this->btnImprimir->visible="false";
								$this->btnGuardar->visible="false";	
								$this->lblNotaVal->visible="true";
								$this->lblNotaVal->Text = 'No cumple con la Antigüedad para el préstamo de 100000: $'.$this->txtImporte1->Text;
							}
					break;
				}
			}
	}		
	public function btnguardar_callback($sender, $param)
	{
	$consulta="insert into solicitud (creada,titular,antiguedad,tipo_empleado,cve_sindicato,aval1,antig_aval1,tipo_aval1,cve_sind_aval1,aval2,antig_aval2,tipo_aval2"
		.", cve_sind_aval2, importe,plazo,tasa,saldo_anterior,id_contrato_ant,descuento,importeCheque,importe_pa_tit , porcentaje_pa_tit,importe_pa_aval1, porcentaje_pa_aval1"
		.", importe_pa_aval2, porcentaje_pa_aval2, firma ,observacion, firma1,firma2, estatus, id_usuario,  seguro)" 
		." values(:txtFecha,:txtTitular,:txtAntiguedadTit,:txtTipoNumTit,:txtSindicatoNumTit,:txtNoUnicoAval1,:txtAntiguedadAval1,:txtTipoAval1,:txtSindicatoNumAval1,:txtNoUnicoAval2,"
		.":txtAntiguedadAval2,:txtTipoAval2,:txtSindicatoNumAval2,:txtImporte,:txtPlazo,:txtTasa,:txtSaldoAnterior,:msg18,:txtDescuentos,:importeCheque,:msg20,:msg21,:msg22,:msg23,:msg24"
		.",:msg25,:datFechaFirmaAvales,:msg27,:txtNombreAval1,:txtNombreAval2,:estatus,:msg31,:msg32)";
		
		$comando = $this->dbConexion->createCommand($consulta);	
		$descuento = ($this->txtPlazo1->Text * 2); 
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
		$comando->bindValue(":txtImporte",$this->txtImporte1->Text); 
		$comando->bindValue(":txtPlazo",$this->txtPlazo1->Text);
		$comando->bindValue(":txtTasa",1.00);
		$comando->bindValue(":txtSaldoAnterior",$this->lblSaldoAnterior->Text);
		$comando->bindValue(":msg18",$this->lblContratoAnterior->text);
		$comando->bindValue(":txtDescuentos",$descuento);
		$comando->bindValue(":importeCheque",$this->lblImpChequeResp->Text);
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
		$titularTit = $this->txtNoUnicoTit->Text;
		if($comando->execute()){
		   $TitId= $this->solicitud($titularTit);	
		   //$this->Imprimir($titularTit);
		   $this->Page->CallbackClient->callClientFunction("Mensaje", "alert('LOS DATOS SE GUARDARON CORRECTAMENTE')\n\n"."Num. Solicitud: ".$TitId );
		}
		else{
		  $this->Page->CallbackClient->callClientFunction("Mensaje","alert('Error - NO SE PUDO GUARDAR LOS DATOS');");
		}	
	}
	public function solicitud($id_Titular)
	{
		$consulta = "SELECT MAX(id_solicitud) as solicitud FROM solicitud WHERE estatus = 'S' AND titular = :id_Titular";
			
		$comando = $this->dbConexion->createCommand($consulta); 
		$comando->bindValue(":id_Titular",$id_Titular);
		$result = $comando->query()->readAll();
		$titularId = $result[0]["solicitud"];
		return $titularId;
	}
	 public function btnImprimir_onclick($sender,$param)
	{
		if($this->txtSindicatoNumTit->Text > 0){
			$titular=$this->txtNoUnicoTit->Text;
			$this->ClientScript->RegisterBeginScript("Mensaje","alert('La solicitud se está imprimiendo');" .
				"open('index.php?page=reportes.solicitudSindicatopdf&id=$titular', '_blank');");
				$this->Limpiar_Campos();
		}else {
			$titular=$this->txtNoUnicoTit->Text;		
			$this->ClientScript->RegisterBeginScript("Mensaje","alert('La solicitud se está imprimiendo');" .
			"open('index.php?page=reportes.solicitudespdf&id=$titular', '_blank');");
				$this->Limpiar_Campos();
			}
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
		.",importeCheque = :importeCheque,importe_pa_tit = :importe_pa_tit, porcentaje_pa_tit = :porcentaje_pa_tit,importe_pa_aval1 = :importe_pa_aval1, porcentaje_pa_aval1 = :porcentaje_pa_aval1, importe_pa_aval2 = :importe_pa_aval2 "
		.", porcentaje_pa_aval2 = :porcentaje_pa_aval2, firma = :datFechaFirmaAvales ,observacion = :observacion, firma1 = :txtNombreAval1,firma2 = :txtNombreAval2, estatus = :estatus, id_usuario = :id_usuario"
		.",  seguro = :seguro "
		."where id_solicitud = :id_solicitud";
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
		$comando->bindValue(":txtImporte",$this->txtImporte1->Text);
		$comando->bindValue(":txtPlazo",$this->txtPlazo1->Text);
		$comando->bindValue(":txtTasa",$this->txtTasa1->Text);
		$comando->bindValue(":txtSaldoAnterior",$this->lblSaldoAnterior->Text); //$this->lblContratoAnterior->text
		$comando->bindValue(":id_contrato_ant",$this->lblContratoAnterior->text);
		$comando->bindValue(":txtDescuentos",$this->lblDescuentos->Text);
		$comando->bindValue(":importeCheque",$this->lblImpChequeResp->Text);
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
		
		if($comando->execute()){
		   $this->Page->CallbackClient->callClientFunction("Mensaje", "alert('LOS DATOS SE ACTUALIZADO CORRECTAMENTE')");
		}
		else{
		  $this->Page->CallbackClient->callClientFunction("Mensaje","alert('Error - NO SE PUDO ACTUALIZADO LOS DATOS');");
		}
	}
	public function btncancelar_callback($sender,$param)
	{
		$consulta="UPDATE  solicitud SET estatus = 'C' where id_solicitud = :id_solicitud";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_solicitud",$this ->txtFolio->text);
		if($comando->execute()){
			
			$this->lblEstatus->Text = "CANCELADA"; 
			$this->btnGuardar->visible="false";
		    $this->btnModificar->visible="false";	
			$this->btnCancelar->visible="false";
			
			$this->Limpiar_Campos();
			$this->Page->CallbackClient->callClientFunction("Mensaje", "alert('LA SOLICITUD HA SIDO CANCELADA')");
		}
		else{
		  $this->Page->CallbackClient->callClientFunction("Mensaje","alert('Error -LA SOLICITUD NO HA SIDO CANCELADA');");
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
		    
			$RespUnico = "txtNoUnicoResp". $sufijo;
			$this->$RespUnico->Text =$result[0]["numero"];
			
			$jubiladoTipo = $result[0]["tipo"];
			if ($jubiladoTipo == "J") {
				
				$tipoJU = Conexion::Retorna_Campo($this->dbConexion, "pensionados", "importe_pension", array("numero"=>$num_unico));
				$ImpJUb = ($tipoJU * 3);
				$Jubilado = $ImpJUb;
			}else{
				$Jubilado ='';
			}
			$TipoJubilado = "txtImporte1";
			$this->$TipoJubilado->Text =$Jubilado; 
			
			$tipo = Conexion::Retorna_Campo($this->dbConexion, "tipo_empleado", "texto", array("tipo_empleado"=>$result[0]["tipo"]));
			$tip = "txtTipo" . $sufijo;
			$this->$tip->Text = $tipo;
			
			$sindicato = Conexion::Retorna_Campo($this->dbConexion, "catsindicatos", "sindicato", array("cve_sindicato"=>$result[0]["sindicato"]));
			$sin = "txtSindicato" . $sufijo;
			$this->$sin->Text = $sindicato;
			
			$nominaEmp = Conexion::Retorna_Campo($this->dbConexion, "empleados", "tipo_nomi", array("numero"=>$num_unico));
			
			switch ($nominaEmp) {
				case ($nominaEmp == "Q"):
					$TipNom = 'Quincena';					
				break;
                case ($nominaEmp == "S"):
					$TipNom = 'Semanal';
				break;
			}
			$nomina = "txtNomina" . $sufijo;
			$this->$nomina->Text = $TipNom;
		}		
		switch ($sufijo) {
                case ("txtNoUnico".$sufijo == "txtNoUnicoTit"):	
					$resultSTit = Conexion::Retorna_Campo($this->dbConexion, "solicitud", "count(titular)", array("titular"=>$num_unico), " AND (estatus = 'S')");
					if($resultSTit >= 1){
						$this->lblSolicitadasTit->visible="true";
						$this->lblSolicitadasTit->Text = $resultSTit;
						$this->btnGuardar->visible="false";	
						
					}else {
						$this->lblSolicitadasTit->visible="false";
						$this->lblSolicitadasTit->Text = 0;
						if ($jubiladoTipo == "J") {
							$this->btnGuardar->visible="true";	
						}else{
							$this->btnGuardar->visible="false";
						}
					}
					
					$idContrato = Conexion::Retorna_Campo($this->dbConexion, "descuento_detalle", "contrato", array("num_empleado"=>$num_unico));
					$cargo =  Conexion::Retorna_Campo($this->dbConexion, "movimientos", "SUM(cargo)", array("id_contrato"=>$idContrato));
					$abono =  Conexion::Retorna_Campo($this->dbConexion, "movimientos", "SUM(abono)", array("id_contrato"=>$idContrato));
					$adeudo = $cargo - $abono;
					
					if ($adeudo == 0)
					{
						$this->lblSaldoAnterior->text = 0;	
						$this->lblContratoAnterior->text = "";
						$this->lblAutorizadasTit->Text = "";
					}else{
						$this->lblSaldoAnterior->text = $adeudo;	
						$this->lblContratoAnterior->text = $idContrato;
						$this->lblAutorizadasTit->Text = 1;
					}
                    break;
                case ("txtNoUnico".$sufijo == "txtNoUnicoAval1"):
					$resultSAval1 = Conexion::Retorna_Campo($this->dbConexion, "solicitud", "count(aval1)", array("aval1"=>$num_unico), " AND (estatus = 'S')");
					if($resultSAval1 >= 3){
						$this->btnGuardar->visible="false";	
						$this->lblSolicitadasAval1->visible="true";
						$this->lblSolicitadasAval1->Text = $resultSAval1; 
					}else {
						$this->lblSolicitadasAval1->visible="false";
						$this->lblSolicitadasAval1->Text = 0;
					}
					/*$consulta ="SELECT COUNT(id_contrato) AS pendiente   // 
					FROM contrato WHERE id_contrato IN (SELECT m.id_contrato 
					FROM solicitud AS s 
					INNER JOIN contrato AS c	ON	s.id_solicitud = c.id_solicitud 
					INNER JOIN movimientos AS m 	ON 	m.id_contrato = c.id_contrato 
					WHERE s.aval1 = :aval1 
					GROUP BY m.id_contrato HAVING (SUM(m.cargo) - SUM(m.abono)) > 0 )";
			
						$comando = $this->dbConexion->createCommand($consulta); 
						$comando->bindValue(":aval1",$num_unico);
						$result = $comando->query()->readAll();
						$resultAAval1B = $result[0]["pendiente"];

					$this->lblAutorizadasAval1->visible="true";
					$this->lblAutorizadasAval1->Text = $resultAAval1B;*/
					/*$resultAAval1 = Conexion::Retorna_Campo($this->dbConexion, "solicitud", "count(aval1)", array("aval1"=>$num_unico), " AND (estatus = 'A')");
					if($resultAAval1 >= 3){
						$this->btnGuardar->visible="false";	
						$this->lblAutorizadasAval1->visible="true";
						$this->lblAutorizadasAval1->Text = $resultAAval1;
					}else {
						$this->lblAutorizadasAval1->visible="false";
						$this->lblAutorizadasAval1->Text = 0;							
					}*/
                    break;
                case ("txtNoUnico".$sufijo == "txtNoUnicoAval2"):
				
					$resultSAval2 = Conexion::Retorna_Campo($this->dbConexion, "solicitud", "count(aval2)", array("aval2"=>$num_unico), " AND (estatus = 'S')");
					if($resultSAval2 >= 3){
						$this->btnGuardar->visible="false";	
						$this->lblSolicitadasAval2->visible="true";
						$this->lblSolicitadasAval2->Text = $resultSAval2;
					}else {
						$this->lblSolicitadasAval2->visible="false";
						$this->lblSolicitadasAval2->Text = 0;
					}
					$resultAAval2 = Conexion::Retorna_Campo($this->dbConexion, "solicitud", "count(aval2)", array("aval2"=>$num_unico), " AND (estatus = 'A')");
					if($resultAAval2 >= 3)
						{
							$this->btnGuardar->visible="false";	
							$this->lblAutorizadasAval2->visible="true";
							$this->lblAutorizadasAval2->Text = $resultAAval2;
						} else {
							$this->lblAutorizadasAval2->visible="false";
							$this->lblAutorizadasAval2->Text = 0;
						}
                    break;
            	}		
	}	
	public function carga_solicitud($id_solicitud)
	{
		$consulta = "SELECT creada, estatus_p, s.antiguedad as antiguedad,
			t.numero as num_tit, t.nombre AS titular, st.cve_sindicato AS tit_cve_sind, st.sindicato AS tit_sind, TIMESTAMPDIFF(YEAR, t.fec_ingre, CURDATE()) AS tit_ant,
			s.aval1, a1.nombre AS aval1_n, sa1.cve_sindicato AS aval1_cve_sind, sa1.sindicato AS aval1_sind, TIMESTAMPDIFF(YEAR, a1.fec_ingre, CURDATE()) AS aval1_ant,
			s.aval2, a2.nombre AS aval2_n, sa2.cve_sindicato AS aval2_cve_sind, sa2.sindicato AS aval2_sind, TIMESTAMPDIFF(YEAR, a2.fec_ingre, CURDATE()) AS aval2_ant,
			firma, importe, plazo, tasa, saldo_anterior, descuento,s.estatus as estatus
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
			WHERE s.id_solicitud = :id_solicitud";
			
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
		
			$this->txtFolio->Text =$id_solicitud;
			$this->txtFecha->Text = $result[0]["creada"];
			$this->txtNoUnicoTit->Text = $result[0]["num_tit"];
			$this->txtAntiguedadNumTit->Text; // pendiente
			$this->txtAntiguedadNumTit->Text = $result[0]["antiguedad"];
			$this->txtNombreTit->Text = $result[0]["titular"];
			$this->txtTipoTit->Text = "ACTIVO";
			$this->txtSindicatoTit->Text = $result[0]["tit_sind"];
			$this->txtNoUnicoAval1->Text = $result[0]["aval1"];
			$this->txtNombreAval1->Text = $result[0]["aval1_n"];
			$this->txtTipoAval1->Text = "ACTIVO";
			$this->txtSindicatoAval1->Text = $result[0]["aval1_sind"];
			$this->txtNoUnicoAval2->Text = $result[0]["aval2"];
			$this->txtNombreAval2->Text = $result[0]["aval2_n"];
			$this->txtTipoAval2->Text = "ACTIVO";
			$this->txtSindicatoAval2->Text = $result[0]["aval2_sind"];
			$this->datFechaFirmaAvales->Text = strtotime($result[0]["firma"]);
			$this->txtImporte1->Text = $result[0]["importe"];
			$this->txtPlazo1->Text = $result[0]["plazo"];
			$this->txtTasa1->Text = $result[0]["tasa"];
			$this->lblIntereses1->Text = number_format($intereses,2);	
			$this->lblSaldoAnterior->Text = $result[0]["saldo_anterior"];
			$this->lblDescuentos->Text = $descuento;
			$this->lblImpDescuentos->Text = number_format($importeDescuento,2);
			$this->lblImpPrestamos->Text = number_format($importeADescontar,2);
			$this->lblSeguro->Text = $seguro;
			$this->lblDiferencia->Text = number_format($Redondeo,2);
			$this->lblImpCheque->Text = number_format($cheque,2); 
			//$this->lblImpChequeResp->Text =$cheque;
			$status  = $result[0]["estatus"];
			switch ($status) {
                case "S":
                  $this->lblEstatus->Text = "SOLICITADO"; 
				  $this->btnGuardar->visible="false";
				  $this->btnModificar->visible="true";
				  $this->btnCancelar->visible="true";
                    break;
                case "A":
                    $this->lblEstatus->Text =  'AUTORIZADO';
					$this->btnGuardar->visible="false";	
					$this->btnModificar->visible="false";
					$this->btnCancelar->visible="false";					
                    break;
                case "C":
                    $this->lblEstatus->Text =  'CANCELADA'; 
					$this->btnGuardar->visible="false";
					$this->btnModificar->visible="false";	
					$this->btnCancelar->visible="false";
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
	public function Limpiar_Campos($campos = null)
	{
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
			$this->txtImporte1->Text = '';
			$this->txtPlazo1->Text = '';
			$this->txtTasa1->Text = '';
			$this->lblIntereses1->Text = '';
			$this->lblSaldoAnterior->Text = '';
			$this->lblDescuentos->Text = '';
			$this->lblImpDescuentos->Text = '';
			$this->lblImpPrestamos->Text = '';
			$this->lblSeguro->Text = '';
			$this->lblDiferencia->Text = '';
			$this->lblImpCheque->Text = '';
			$this->lblImpChequeResp->Text = '';
			$this->lblSolicitadasTit->Text = '';
			$this->lblAutorizadasTit->Text = '';
			$this->lblSolicitadasAval1->Text = '';
			$this->lblAutorizadasAval1->Text = '';
			$this->lblSolicitadasAval2->Text = '';
			$this->lblAutorizadasAval2->Text = '';
			$this->lblEstatus->Text = '';
			$this->lblSaldoAnterior->Text = '';

			$this->btnGuardar->visible="false";	
			$this->btnCancelar->visible="false";	
	}
	
	
}

?>