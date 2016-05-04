<?php
include_once('../compartidos/clases/conexion.php');
include_once('../compartidos/clases/numaletras.php');

class estadodecuentaporcontrato extends TPage 
{
	var $dbConexion;
	var $NombreTitular;
	var $ImporteLetras;
	var $importeNeto,$FirmaAvales,$Contrato,$Titular,$Avall1,$Avall2;
	
	
	

	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
		$idcontrato = $_REQUEST['id'];

		$consulta = "SELECT c.id_contrato AS contrato, s.importe AS importe ,s.importeCheque AS ImporteDeCheque,t.nombre AS nombre ,c.entrega_cheque AS FechaDeCheque, s.descuento AS descuento
					,(SELECT sindicato FROM catsindicatos WHERE cve_sindicato = t.sindicato) AS Sindicato,plazo as plazo,saldo_anterior as saldo_anterior,entrega_real as entrega_real
					,(SELECT COUNT(*) AS movimientos FROM movimientos WHERE id_tipo_movto = 2 and id_contrato = c.id_contrato) AS AbonosRealizados
					,(SELECT COUNT(*) AS movimientos FROM movimientos WHERE id_contrato = c.id_contrato) AS MovimientosRealizados
					,(SELECT (SUM(cargo) - SUM(abono) ) AS saldo FROM movimientos WHERE  id_contrato = c.id_contrato) AS SaldoActual
					,s.descuento as descuento
					FROM contrato AS c
					LEFT JOIN solicitud AS s	ON c.id_solicitud = s.id_solicitud
					LEFT JOIN sujetos  AS t ON t.numero = s.titular	
					WHERE c.id_contrato = :idcontrato";	
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":idcontrato",$idcontrato);		
		$result = $comando->query()->readAll();
		if(count($result) > 0)
		{
			$plazo = $result[0]["plazo"];
			$seguro = 0.00;
			$saldoAnterior = $result[0]["saldo_anterior"];
			$this->lblContrato2->Text = $result[0]["contrato"];
			$this->lblNombre3->Text = $result[0]["nombre"];
			$this->lblSindicato4->Text = $result[0]["Sindicato"];
			$this->lblPrestamo2->Text = $result[0]["importe"];
			$intereses =  THttpUtility::htmlEncode(round ((($this->lblPrestamo2->Text) * ($plazo) * (1.00 / 100))));
			$this->lblImporteDeCheque3->Text = $result[0]["ImporteDeCheque"];
			$this->lblDescuentoQuincenal4->Text = $result[0]["descuento"];			
			$this->lblInteres2->Text =$intereses;		
			$cheque = $this->lblPrestamo2->Text - ($intereses + $saldoAnterior + $seguro);	
			$this->lblImporteDeCheque3->Text = $cheque;
			$this->lblFechaDeCheque3->Text = $result[0]["entrega_real"];
			$this->lblAbonosRealizados4->Text = $result[0]["AbonosRealizados"];
			$this->mostrarDatosGrid ();
		}
	}
	public function mostrarDatosGrid () 
	{ 
		$consulta = "SELECT id_movimiento, creacion, descripcion, cargo, abono FROM movimientos where id_contrato = :id_contrato";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_contrato",$this->lblContrato2->Text);
		$resultado = $comando->query()->readAll();
		$this->dgMovimientos->DataSource = $resultado;
		$this->dgMovimientos->dataBind();
		//$this->carga_solicitud($this->txtContrato2->Text);	
	}
}