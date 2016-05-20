<?php
include_once('../compartidos/clases/conexion.php');
include_once('../compartidos/clases/numaletras.php');

class RDesno extends TPage 
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
		$fechaInicial = $_REQUEST['id'];
		$fechafinal = $_REQUEST['id2'];
		$fecha_actual = date("Y-m-d H:i:s"); 
		
		$this->lblfechaActual->Text = $fecha_actual;
		$this->lblfechaInicial->Text = $fechaInicial;
		$this->lblfechafinal->Text = $fechafinal;		
		$consulta = "SELECT sum(diferencia) AS saldoFinalTotal_1  FROM repdetallemovtos";
		$comando = $this->dbConexion->createCommand($consulta);
		$resultado = $comando->query()->readAll();
		$this->lblsaldoFinalTotal->Text = $resultado[0]["saldoFinalTotal_1"];
		$this->mostrarDatosGriddetalle_movtos ();
		$consulta = " SELECT  sum(totalSemana) as totalSemana, sum(totalQuincena) as totalQuincena, sum(totalActivos) as totalActivos, sum(totalJubilados) as totalJubilados, sum(granTotal) as granTotal  FROM Desglose_abonos";
		$comando = $this->dbConexion->createCommand($consulta);
		$resultado = $comando->query()->readAll();
		$this->lblTotalSemanas->Text = $resultado[0]["totalSemana"];
		$this->lblTotalquicena->Text = $resultado[0]["totalQuincena"];
		$this->lblTotalactivos->Text = $resultado[0]["totalActivos"];
		$this->lblTotalJubilados->Text = $resultado[0]["totalJubilados"];
		$this->lblSubTotal->Text = $resultado[0]["granTotal"];
		$this->mostrarDatosGridNomina ();
		$this->mostrarDatosGridMovimiento8 ();
		//$this->mostrarDatosGriddetalle_desglose ();


	}
	public function mostrarDatosGriddetalle_desglose ( ) 
	{ 
		$consulta = "SELECT idContrato,fecha,titular,nombre,saldoInicial,cargo,abono,saldoFinal,abonosHechos FROM repEdoCtaxperio2Desglosado";
		$comando = $this->dbConexion->createCommand($consulta);
		$resultado = $comando->query()->readAll();
		$this->dgdesglosedet->DataSource = $resultado;
		$this->dgdesglosedet->dataBind();
	}
	public function mostrarDatosGriddetalle_movtos ( ) 
	{ 
		$consulta = " SELECT idDetMovtos, idTipoMovimiento, movimiento, cargo, abono, diferencia  FROM repdetallemovtos";
		$comando = $this->dbConexion->createCommand($consulta);
		$resultado = $comando->query()->readAll();
		$this->dgdesglose->DataSource = $resultado;
		$this->dgdesglose->dataBind();
	}
	public function mostrarDatosGridNomina ( ) 
	{ 
		$consulta = " SELECT idDescuento, fechaHora,  estatus, totalSemana, totalQuincena, totalActivos, totalJubilados, granTotal   FROM Desglose_abonos";
		$comando = $this->dbConexion->createCommand($consulta);
		$resultado = $comando->query()->readAll();
		$this->dgnomina->DataSource = $resultado;
		$this->dgnomina->dataBind();
	}
	
		public function mostrarDatosGridMovimiento8 ( ) 
	{ 
		$consulta = "SELECT idrepdetalle, fecha, idContrato, cargo, abono, descripcion  FROM repdetallemov8";
		$comando = $this->dbConexion->createCommand($consulta);
		$resultado = $comando->query()->readAll();
		$this->dgmovimiento8->DataSource = $resultado;
		$this->dgmovimiento8->dataBind();
	}
}
