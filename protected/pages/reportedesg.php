<?php
Prado::using('System.Web.UI.ActiveControls.*');
include_once('../compartidos/clases/conexion.php');

class reportedesg extends TPage
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
		$fechaInicial = $this->datFechainicio->Text;
		$fechafinal = $this->datFechaFirmafinal->Text;
		$reporte=$this->ddlReporte->Text ;
		
		//$this->dbConexion->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
		
		switch ($reporte) {
			case "4":
                $consulta = "CALL generaContratosAltaRedocumenta (:fechaInicial,:fechafinal)";
				$comando = $this->dbConexion->createCommand($consulta); 
				$comando->bindValue(":fechaInicial",$fechaInicial);
				$comando->bindValue(":fechafinal",$fechafinal);
				$result = $comando->query()->readAll();
				unset($comando);
				if(count($result) > 0)
				{
					$this->mostrarDatosGridRedocumentacion ();
				}						
            break;
			case "7":
				$consulta = "CALL obtenerListadoFirmas ()";
				$comando = $this->dbConexion->createCommand($consulta); 
				$result = $comando->query()->readAll();
				unset($comando);
				if(count($result) > 0)
				{
					$this->mostrarDatosGridFirmas ();
				}	
            break;
            case "9":
				$consulta = "CALL obtenerEdo_de_CtaXperio2Desglosado (:fechaInicial,:fechafinal)";
				$comando = $this->dbConexion->createCommand($consulta); 
				$comando->bindValue(":fechaInicial",$fechaInicial);
				$comando->bindValue(":fechafinal",$fechafinal);
				$result = $comando->query()->readAll();
				unset($comando);
				if(count($result) > 0)
				{
					$this->mostrarDatosGrid ();
				}	
            break;
		}
	}
	 public function btnImprimir_onclick($sender,$param)
	{
		$fechaInicial = $this->datFechainicio->Text;
		$fechafinal = $this->datFechaFirmafinal->Text;
		$reporte=$this->ddlReporte->Text ;
		switch ($reporte) 
		{
			case "4":
                 $this->ClientScript->RegisterBeginScript("Mensaje","alert('El reporte se esta generando');" .
				"open('http://127.0.0.1/prestamos/reportes2/ResumenAltasRedocumentacion.php?id=$fechaInicial&id2=$fechafinal', '_blank');");	
            break;
			case "7":
				$this->ClientScript->RegisterBeginScript("Mensaje","alert('El reporte se esta generando');" .
				"open('http://127.0.0.1/prestamos/reportes2/ListadoGeneralDeFirmas.php', '_blank');");	
			break;
            case "9":
				$this->ClientScript->RegisterBeginScript("Mensaje","alert('El reporte se esta generando');" .
				"open('index.php?page=reportes.RDesnopdf&id=$fechaInicial&id2=$fechafinal', '_blank');\n" .
				"open('http://127.0.0.1/prestamos/reportes2/reporte.php?id=$fechaInicial&id2=$fechafinal', '_blank');");	
			break;
        }
	}
	
	public function mostrarDatosGrid () 
	{ 
		$consulta = "SELECT idContrato,fecha,titular,nombre,saldoInicial,cargo,abono,saldoFinal,abonosHechos FROM repEdoCtaxperio2Desglosado";
		$comando = $this->dbConexion->createCommand($consulta);
		$resultado = $comando->query()->readAll();
		$this->dgdesglosedet->DataSource = $resultado;
		$this->dgdesglosedet->dataBind();
	}
	public function mostrarDatosGridRedocumentacion () 
	{ 
		$consulta = "SELECT idContrato, fecha, titular, nombre, importePrestamo, MontoRedocumentacion,sindicato,idSindicato FROM repAltasRedocumenta ORDER BY idSindicato ASC";
		$comando = $this->dbConexion->createCommand($consulta);
		$resultado = $comando->query()->readAll();
		$this->dgredocumentacion->DataSource = $resultado;
		$this->dgredocumentacion->dataBind();
	}
	public function mostrarDatosGridFirmas () 
	{ 
		$consulta = "SELECT cveEmpleado, nombre, Titular1, Titular2, Titular3  FROM replistadofirmas ORDER BY cveEmpleado";
		$comando = $this->dbConexion->createCommand($consulta);
		$resultado = $comando->query()->readAll();
		$this->dgfirmas->DataSource = $resultado;
		$this->dgfirmas->dataBind();
	}
}
?>