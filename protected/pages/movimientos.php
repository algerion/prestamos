<?php
Prado::using('System.Web.UI.ActiveControls.*');
include_once('../compartidos/clases/conexion.php');

class movimientos extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);

		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
	}
	public function btnBuscar_onclick($sender,$param)
	{			
		$folio=$this->txtContrato2->Text;
		$this->carga_solicitud($folio);	
		$this->mostrarDatosGrid ();
	}
	public function carga_solicitud($id_contrato)
	{
		$consulta = "SELECT c.id_contrato AS contrato, s.importe AS importe ,s.importeCheque AS ImporteDeCheque,t.nombre AS nombre ,c.entrega_cheque AS FechaDeCheque, s.descuento AS descuento
					,(SELECT sindicato FROM catsindicatos WHERE cve_sindicato = t.sindicato) AS Sindicato,plazo as plazo,saldo_anterior as saldo_anterior,entrega_real as entrega_real
					,(SELECT COUNT(*) AS movimientos FROM movimientos WHERE id_tipo_movto = 2 and id_contrato = c.id_contrato) AS AbonosRealizados
					,(SELECT COUNT(*) AS movimientos FROM movimientos WHERE id_contrato = c.id_contrato) AS MovimientosRealizados
					,(SELECT (SUM(cargo) - SUM(abono) ) AS saldo FROM movimientos WHERE  id_contrato = c.id_contrato) AS SaldoActual
					,s.descuento as descuento
					FROM contrato AS c
					LEFT JOIN solicitud AS s	ON c.id_solicitud = s.id_solicitud
					LEFT JOIN sujetos  AS t ON t.numero = s.titular	
					WHERE c.id_contrato = :id_contrato ";	
		$comando = $this->dbConexion->createCommand($consulta); 
		$comando->bindValue(":id_contrato",$id_contrato);
		$result = $comando->query()->readAll();
		if(count($result) > 0)
		{
			// ----------------------------,(SELECT (SUM(cargo) - SUM(abono) ) AS saldo FROM movimientos WHERE  id_contrato = c.id_contrato) AS SaldoActual-----------------------------------------------------------------------------------------------------------
			
			$plazo = $result[0]["plazo"];
			$seguro = 0.00;
			$saldoAnterior = $result[0]["saldo_anterior"];
			$this->txtContrato2->Text = $result[0]["contrato"];
			$this->txtNombre3->Text = $result[0]["nombre"];
			$this->txtSindicato4->Text = $result[0]["Sindicato"];
			$this->txtPrestamo2->Text = $result[0]["importe"];
			$intereses =  THttpUtility::htmlEncode(round ((($this->txtPrestamo2->Text) * ($plazo) * (1.00 / 100))));
			$this->txtImporteDeCheque3->Text = $result[0]["ImporteDeCheque"];
			$this->txtDescuentoQuincenal4->Text = $result[0]["descuento"];			
			$this->txtInteres2->Text =$intereses;		
			$cheque = $this->txtPrestamo2->Text - ($intereses + $saldoAnterior + $seguro);	
			$this->txtImporteDeCheque3->Text = $cheque;
			$this->txtFechaDeCheque3->Text = $result[0]["entrega_real"];
			$this->txtAbonosRealizados4->Text = $result[0]["AbonosRealizados"];
			$this->txtSaldo4->Text = $result[0]["SaldoActual"];
		}
		$consulta = "SELECT SUM(cargo) AS cargo,  SUM(abono) AS abono  FROM movimientos WHERE id_contrato = :id_contrato";	
		$comando = $this->dbConexion->createCommand($consulta); 
		$comando->bindValue(":id_contrato",$id_contrato);
		$result = $comando->query()->readAll();
		if(count($result) > 0)
		{
			$this->txtTotalDeMovimiento2->Text = $result[0]["cargo"];
			$this->txtTotalDeMovimiento3->Text = $result[0]["abono"];
		}	
	}

	 public function mostrarDatosGrid () 
	 { 
		$consulta = "SELECT id_movimiento, creacion, descripcion, cargo, abono FROM movimientos where id_contrato = :id_contrato";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_contrato",$this->txtContrato2->Text);
		$resultado = $comando->query()->readAll();
		$this->dgMovimientos->DataSource = $resultado;
		$this->dgMovimientos->dataBind();
		$this->carga_solicitud($this->txtContrato2->Text);	
	}
	
	public function textChangedAbonos($sender,$param)
    {
		
		$ImporteSB = $this->txtdescuento->Text * $this->txtAbonos->Text;
		$this->txtCantidad->Text = $ImporteSB; 
	}
	
	public function btnAceptar_Click($sender,$param)
	{
		$fecha = date('Y-m-j');
		$consulta = "SELECT idMovimiento,  movimiento, idTMovto FROM catMovimiento WHERE idMovimiento = :id_Movimiento";	
		$comando = $this->dbConexion->createCommand($consulta); 
		$comando->bindValue(":id_Movimiento",$this->ddlMovimiento->SelectedValue);
		$result = $comando->query()->readAll();
		$idMovimiento = $result[0]["idMovimiento"];
		$movimiento = $result[0]["movimiento"];
		$idTMovto = $result[0]["idTMovto"];
		
		if($this->txtAbonos->Text =="" )
		{
			$this->lblalerta->visible="true";
			$this->lblalerta->Text = 'Ingrese el número de abono para generar la cantidad';	
		
		  }else{
			  $this->lblalerta->visible="false";
			$consulta="insert into movimientos (id_contrato,creacion,id_tipo_movto,descripcion,cargo,abono,id_usuario,aplicacion,id_descuento,activo)" 
			." values(:id_contrato,:creacion,:id_tipo_movto,:descripcion,:cargo,:abono,:id_usuario,:aplicacion,:id_descuento,:activo )";
			$comando = $this->dbConexion->createCommand($consulta);	
			$comando->bindValue(":id_contrato",$this->txtContrato2->Text);
			$comando->bindValue(":creacion",$fecha);
			$comando->bindValue(":id_tipo_movto",$idMovimiento);
			$comando->bindValue(":descripcion",$movimiento );
			switch ($this->ddlCargoAbono->SelectedValue) 
			{
				case 1:
				$comando->bindValue(":cargo",$this->txtCantidad->Text);
				$comando->bindValue(":abono",0);
				break;
				case 2:
				$comando->bindValue(":cargo",0);
				$comando->bindValue(":abono",$this->txtCantidad->Text);					
				 break;  
			 }
			$comando->bindValue(":id_usuario",0);
			$comando->bindValue(":aplicacion",'');
			$comando->bindValue(":id_descuento",0);
			$comando->bindValue(":activo",1);
			
			if($comando->execute()){
			   $this->ClientScript->RegisterBeginScript("Mensaje","alert('El movimiento fue insertado correctamente');");
			   $this->mostrarDatosGrid ();
			   $this->Limpiar_Campos();
			}
			else{
			  $this->ClientScript->RegisterBeginScript("Mensaje","alert('El movimiento NO fue insertado correctamente');");
			}
		}		
	}
	
	 public function btnImprimir_onclick($sender,$param)
	{
		$contrato= $this->txtContrato2->Text;
		if($contrato <> ''){
			$this->ClientScript->RegisterBeginScript("Mensaje","alert('El documento se está imprimiendo');" .
				"open('index.php?page=reportes.estadodecuentaporcontratopdf&id=$contrato', '_blank');");
		}
	}
	public function Limpiar_Campos($campos = null)
	{
		$this->txtCantidad->Text ='';
		$this->txtAbonos->Text ='';
		$this->txtjustificacion->Text ='';
	}
}

?>