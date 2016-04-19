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
					,(SELECT sindicato FROM catsindicatos WHERE cve_sindicato = t.sindicato) AS Sindicato
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
			$this->txtPrestamo2->Text = $result[0]["importe"];
			$this->txtImporteDeCheque3->Text = $result[0]["ImporteDeCheque"];
			$this->txtNombre3->Text = $result[0]["nombre"];
			$this->txtFechaDeCheque3->Text = $result[0]["FechaDeCheque"];
			$this->txtSindicato4->Text = $result[0]["Sindicato"];
			$this->txtAbonosRealizados4->Text = $result[0]["AbonosRealizados"];
			$this->txtDescuentoQuincenal4->Text = $result[0]["descuento"];
			$this->txtTotalDeMovimiento2->Text = $result[0]["MovimientosRealizados"];
			$this->txtSaldo4->Text = $result[0]["SaldoActual"];
			/*if ( $result[0]["SaldoActual"] > 0.00){
				$this->txtSaldo4->Text = $result[0]["ImporteDeCheque"];
				
			}else{
				$this->txtSaldo4->Text = $result[0]["SaldoActual"];
			}*/
			$this->txtdescuento->Text = $result[0]["descuento"];
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
		$this->pnlMovimientos->DataSource = $resultado;
		$this->pnlMovimientos->dataBind();
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
	public function Limpiar_Campos($campos = null)
	{
		$this->txtCantidad->Text ='';
		$this->txtAbonos->Text ='';
		$this->txtjustificacion->Text ='';
	}
}

?>