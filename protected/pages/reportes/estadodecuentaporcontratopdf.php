<?php
include_once('../compartidos/clases/usadompdf.php');

class estadodecuentaporcontratopdf extends TPage 
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		usadompdf::creapdf("http://" . $_SERVER["HTTP_HOST"] 
				. $_SERVER["PHP_SELF"] . "?page=reportes.pagare&id=" . 
				$this->Request["id"], "letter", "portrait");
				
				$consulta = "SELECT c.id_contrato AS contrato, s.importe AS importe ,s.importeCheque AS ImporteDeCheque,t.nombre AS nombre ,c.entrega_cheque AS FechaDeCheque, s.descuento AS descuento
					,(SELECT sindicato FROM catsindicatos WHERE cve_sindicato = t.sindicato) AS Sindicato
					,(SELECT COUNT(*) AS movimientos FROM movimientos WHERE id_tipo_movto = 2 and id_contrato = c.id_contrato) AS AbonosRealizados
					,(SELECT COUNT(*) AS movimientos FROM movimientos WHERE id_contrato = c.id_contrato) AS MovimientosRealizados
					,(SELECT (SUM(cargo) - SUM(abono) ) AS saldo FROM movimientos WHERE  id_contrato = c.id_contrato) AS SaldoActual
					,s.descuento as descuento
					FROM contrato AS c
					LEFT JOIN solicitud AS s	ON c.id_solicitud = s.id_solicitud
					LEFT JOIN sujetos  AS t ON t.numero = s.titular	
					WHERE c.id_contrato = 14659";	
		$comando = $this->dbConexion->createCommand($consulta); 
		$comando->bindValue(":id_contrato",$id_contrato);
		$result = $comando->query()->readAll();
		if(count($result) > 0)
		{
			$this->lblPrestamo2->Text = $result[0]["importe"];
			$this->lblImporteDeCheque3->Text = $result[0]["ImporteDeCheque"];
			$this->lblNombre3->Text = $result[0]["nombre"];
			$this->lblFechaDeCheque3->Text = $result[0]["FechaDeCheque"];
			$this->lblSindicato4->Text = $result[0]["Sindicato"];
			$this->lblAbonosRealizados4->Text = $result[0]["AbonosRealizados"];
			$this->lblDescuentoQuincenal4->Text = $result[0]["descuento"];
			$this->lblTotalDeMovimiento2->Text = $result[0]["MovimientosRealizados"];
			$this->lblSaldo4->Text = $result[0]["SaldoActual"];
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
			$this->lblTotalDeMovimiento2->Text = $result[0]["cargo"];
			$this->lblTotalDeMovimiento3->Text = $result[0]["abono"];
		}	
	}
}
?>