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
		$id_Descuento = $_REQUEST['id'];
		
		$consulta = "SELECT 
				(SELECT SUM(d.importe) AS quincenal FROM descuento_detalle AS d WHERE d.id_descuento = det.id_descuento AND  d.tipo_nomina = 'Q') AS quincena
				,(SELECT SUM(d.importe) AS semanla FROM descuento_detalle AS d WHERE d.id_descuento = det.id_descuento AND  d.tipo_nomina = 'S') AS semanal
				,(SELECT SUM(d.importe) AS total FROM descuento_detalle AS d WHERE d.id_descuento = det.id_descuento ) AS total
				,(SELECT count(d.tipo_nomina) AS tquincenal FROM descuento_detalle AS d WHERE d.id_descuento = det.id_descuento AND  d.tipo_nomina = 'Q') AS totalquincena
				,(SELECT count(d.tipo_nomina) AS tsemanla FROM descuento_detalle AS d WHERE d.id_descuento = det.id_descuento AND  d.tipo_nomina = 'S') AS totalsemanal
				,(SELECT count(d.id_descuento) AS total FROM descuento_detalle AS d WHERE d.id_descuento = det.id_descuento ) AS total_resgistros
				FROM descuento_detalle det WHERE det.id_descuento = :id_Descuento GROUP BY det.id_descuento";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_Descuento",$id_Descuento);
		$result = $comando->query()->readAll();
		if(count($result) > 0)
		{
		$this->lblQuincenalI->Text = $result[0]["quincena"];
		$this->lblSemanalI->Text = $result[0]["semanal"];
		$this->lblTotalI->Text = $result[0]["total"];
		
		$this->lblQuincenal->Text = $result[0]["totalquincena"];
		$this->lblSemanal->Text = $result[0]["totalsemanal"];
		$this->lblTotal->Text = $result[0]["total_resgistros"];
		}
		$comando->execute();
		$consulta = "SELECT COUNT(A.numero) AS activos, SUM(importe) AS importeA
					FROM descuento_detalle D
					INNER JOIN sujetos A	ON	A.numero = D.num_empleado AND A.tipo = 'A'
					WHERE D.id_descuento = :id_Descuento ";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_Descuento",$id_Descuento);
		$result = $comando->query()->readAll();
		$this->lblActivos->Text = $result[0]["activos"];
		$this->lblActivosI->Text = $result[0]["importeA"];
		$comando->execute();
		$consulta = "SELECT COUNT(A.numero) AS jubilados, SUM(importe) AS importeJ
					FROM descuento_detalle D
					INNER JOIN sujetos A	ON	A.numero = D.num_empleado AND A.tipo = 'J'
					WHERE D.id_descuento = :id_Descuento ";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_Descuento",$id_Descuento);
		$result = $comando->query()->readAll();
		$this->lblJubilados->Text = $result[0]["jubilados"];
		$this->lblJubiladosI->Text = $result[0]["importeJ"];
		$comando->execute();
	
		//	$this->mostrarDatosGrid ($id_Descuento);
		
		$consulta = "SELECT dd.contrato as contrato, dd.num_empleado as num_empleado,s.nombre as nombre,
					 IF (dd.clavecon=63, 'Descuento de nomina',IF (dd.clavecon=64,'Descuento a avales','')) AS descripcion,
					  dd.periodo,dd.periodos,dd.importe,(CASE s.tipo 
						WHEN 'A' THEN 'EMPLEADOS ACTIVOS'
						WHEN 'J' THEN 'EMPLEADOS JUBILADOS'
						WHEN 'E' THEN 'EMPLEADOS EXTERNOS'
					   END) AS tipoEmpleado,
						IF(dd.tipo_nomina = 'Q', 'QUINCENAL', 'SEMANAL') AS tipoNomina,IF(de.periodo IS NULL,0,de.periodo) AS perdesno
					FROM Descuento_Detalle AS dd
					LEFT JOIN sujetos AS s ON s.numero=dd.num_empleado
					LEFT JOIN Descuento AS de ON dd.id_descuento=de.id_descuento
					WHERE dd.id_descuento = :idDescuento  ORDER BY dd.num_empleado";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":idDescuento",$id_Descuento);
		$resultado = $comando->query()->readAll();
		$this->Repeater->DataSource=$resultado;
         $this->Repeater->dataBind();
	
		
	}
/*	public function mostrarDatosGrid ($id_Descuento) 
	{
	$consulta = "SELECT dd.contrato as contrato, dd.num_empleado as num_empleado,s.nombre as nombre,
					 IF (dd.clavecon=63, 'Descuento de nomina',IF (dd.clavecon=64,'Descuento a avales','')) AS descripcion,
					  dd.periodo,dd.periodos,dd.importe,(CASE s.tipo 
						WHEN 'A' THEN 'EMPLEADOS ACTIVOS'
						WHEN 'J' THEN 'EMPLEADOS JUBILADOS'
						WHEN 'E' THEN 'EMPLEADOS EXTERNOS'
					   END) AS tipoEmpleado,
						IF(dd.tipo_nomina = 'Q', 'QUINCENAL', 'SEMANAL') AS tipoNomina,IF(de.periodo IS NULL,0,de.periodo) AS perdesno
					FROM Descuento_Detalle AS dd
					LEFT JOIN sujetos AS s ON s.numero=dd.num_empleado
					LEFT JOIN Descuento AS de ON dd.id_descuento=de.id_descuento
					WHERE dd.id_descuento = :idDescuento  ORDER BY dd.num_empleado LIMIT 0,100";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":idDescuento",$id_Descuento);
		$resultado = $comando->query()->readAll();
		$this->DataList->DataSource = $resultado;
		$this->DataList->dataBind(); 		
	}*/
	/*public function mostrarDatosGrid100 ($id_Descuento) 
	{ 
		$consulta = "SELECT dd.contrato, dd.num_empleado,s.nombre,
					 IF (dd.clavecon=63, 'Descuento de nomina',IF (dd.clavecon=64,'Descuento a avales','')) AS descripcion,
					  dd.periodo,dd.periodos,dd.importe,(CASE s.tipo 
						WHEN 'A' THEN 'EMPLEADOS ACTIVOS'
						WHEN 'J' THEN 'EMPLEADOS JUBILADOS'
						WHEN 'E' THEN 'EMPLEADOS EXTERNOS'
					   END) AS tipoEmpleado,
						IF(dd.tipo_nomina = 'Q', 'QUINCENAL', 'SEMANAL') AS tipoNomina,IF(de.periodo IS NULL,0,de.periodo) AS perdesno
					FROM Descuento_Detalle AS dd
					LEFT JOIN sujetos AS s ON s.numero=dd.num_empleado
					LEFT JOIN Descuento AS de ON dd.id_descuento=de.id_descuento
					WHERE dd.id_descuento = :id_Descuento
					ORDER BY dd.num_empleado LIMIT 100,100";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_Descuento",$id_Descuento);
		$resultado = $comando->query()->readAll();
		$this->dgDescuentos2->DataSource = $resultado;
		$this->dgDescuentos2->dataBind(); 
	}	
	public function mostrarDatosGrid200 ($id_Descuento) 
	{ 
		$consulta = "SELECT dd.contrato, dd.num_empleado,s.nombre,
					 IF (dd.clavecon=63, 'Descuento de nomina',IF (dd.clavecon=64,'Descuento a avales','')) AS descripcion,
					  dd.periodo,dd.periodos,dd.importe,(CASE s.tipo 
						WHEN 'A' THEN 'EMPLEADOS ACTIVOS'
						WHEN 'J' THEN 'EMPLEADOS JUBILADOS'
						WHEN 'E' THEN 'EMPLEADOS EXTERNOS'
					   END) AS tipoEmpleado,
						IF(dd.tipo_nomina = 'Q', 'QUINCENAL', 'SEMANAL') AS tipoNomina,IF(de.periodo IS NULL,0,de.periodo) AS perdesno
					FROM Descuento_Detalle AS dd
					LEFT JOIN sujetos AS s ON s.numero=dd.num_empleado
					LEFT JOIN Descuento AS de ON dd.id_descuento=de.id_descuento
					WHERE dd.id_descuento = :id_Descuento
					ORDER BY dd.num_empleado LIMIT 200,100";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":id_Descuento",$id_Descuento);
		$resultado = $comando->query()->readAll();
		$this->dgDescuentos3->DataSource = $resultado;
		$this->dgDescuentos3->dataBind(); 
	}	*/
}