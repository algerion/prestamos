<?php
class Busquedas
{
	public static function empleados($conexion, $tipo, $busca, $sindicato = null)
	{
		$camposempjub = "SELECT e.numero, e.nombre, cs.sindicato, fec_ingre, " .
				"TIMESTAMPDIFF(YEAR, fec_ingre, CURDATE()) AS anual, " . 
				"TIMESTAMPDIFF(MONTH, fec_ingre, CURDATE()) MOD 12 AS mensual, " . 
				"IFNULL(df.importe, 0) AS importe, IFNULL(df.porcentaje, 0) AS porcentaje";
		$joinsempjub = " e LEFT JOIN catsindicatos cs ON e.sindicato = cs.cve_sindicato " .
				"LEFT JOIN descuentos_fijos df ON e.numero = df.numero AND df.concepto = 61";
		$externos = "SELECT e.numero, nombre, 0 AS sindicato, fec_ingre, " . 
				"0 AS anual, 0 as mensual, 0 AS importe, 0 AS porcentaje, 'EXTERNO' AS tipo FROM externos e";
		$consulta = "";
		$where = " WHERE (e.numero LIKE :busca OR e.nombre LIKE :busca) ";
		$sind = "";
		
		if($sindicato != null)
			$sind .= "AND e.sindicato = :sindicato ";
		
		if($tipo == 0 || $tipo == 1)
			$consulta .= $camposempjub . 
					", CASE WHEN cs.cve_sindicato IN (0, 12) THEN 'CONFIANZA' ELSE 'SINDICALIZADO' END AS tipo FROM empleados" . 
					$joinsempjub . $where . $sind;
		if($tipo == 0)
			$consulta .= " UNION ";
		if($tipo == 0 || $tipo == 2)
			$consulta .= $camposempjub . ", 'JUBILADO' AS tipo FROM pensionados" . $joinsempjub . $where . $sind;
		if($tipo == 0)
			$consulta .= " UNION ";
		if($tipo == 0 || $tipo == 3)
			$consulta .= $externos . $where;
		
		$consulta .= " LIMIT 1000";
		$comando = $conexion->createCommand($consulta);
		$comando->bindValue("busca", "%" . $busca . "%");
		if($sindicato != null)
			$comando->bindValue("sindicato", $sindicato);

		return $comando->query()->readAll();
	}

	public static function obtenerPrestamoAnterior($conexion, $titular)
	{
		$consulta = "SELECT B.id_contrato, sum(C.Cargo - C.Abono) as saldo 
				From solicitud A Join contrato B on B.id_solicitud = A.id_solicitud 
				Join movimientos C On C.id_contrato = B.id_contrato And C.Activo=1 
				Where A.titular = :titular and A.estatus='A' and B.estatus='A' 
				Group By B.id_contrato Having saldo > 1";
		$comando = $conexion->createCommand($consulta);
		$comando->bindValue("titular", $titular);

		return $comando->query()->readAll();
		
	}
	
	public static function obtenerPrestamoAnteriorSinRedocumentado($conexion, $titular)
	{

		$consulta = "Select B.id_contrato, sum(C.Cargo - C.Abono) as saldo From solicitud A Join contrato B on B.id_solicitud = A.id_solicitud Join movimientos C On C.id_contrato = B.id_contrato  And C.Activo=1 Where A.titular = :titular and A.estatus='A' and B.estatus='A' Group By B.id_contrato Having saldo > 1 Order By B.id_contrato";
		$comando = $conexion->createCommand($consulta);
		$comando->bindValue("titular", $titular);

		return $comando->query()->readAll();		
	}
	
	public static function aval_disponible($conexion, $aval)
	{
		$consulta = "Select A.id_Solicitud, sum(C.Cargo - C.Abono) as saldo From solicitud A Join contrato
		B on B.id_Solicitud = A.id_Solicitud Join movimientos
		C On C.id_Contrato = B.id_Contrato where (A.aval1 = :aval  Or A.aval2 =  :aval) 
		group by B.id_Contrato, A.estatus Having (saldo > 1) or (A.estatus = 'S') ";
		$comando = $conexion->createCommand($consulta);
		$comando->bindValue("aval", $aval);
		return $comando->query()->readAll();	
	}
	
	public static function generaContratosAltaRedocumenta($conexion, $fechaInicio, $fechaFin)
	{
		$consulta = "SELECT c.id_contrato  ,
          mov.creacion     ,
          s.titular     ,
          tce.nombre    ,
          cya.SumaCargos,
          cya.SumaAbonos,
          cya.Saldo     ,
          s.id_solicitud ,
          s.id_contrato_ant,
          s.saldo_anterior,
          ((s.plazo * 2) * s.descuento) AS importePrestamo,
          CASE 
	       WHEN tce.tipo <> 'A' THEN 80
	       WHEN (s.cve_sindicato=2) THEN 211
               WHEN (s.cve_sindicato=11) THEN 211
               WHEN (s.cve_sindicato=1) THEN 113
               WHEN (s.cve_sindicato=13) THEN 113
               ELSE s.cve_sindicato
          END AS cve_sindicato,

          CASE 
               WHEN tce.tipo <> 'A' THEN 'JUBILADOS'
               WHEN (s.cve_sindicato=2) THEN '3 DE MARZO + (ADMIN)'
               WHEN (s.cve_sindicato=11) THEN '3 DE MARZO + (ADMIN)'
               WHEN (s.cve_sindicato=1) THEN 'CONFIANZA + (2)'
               WHEN (s.cve_sindicato=13) THEN 'CONFIANZA + (2)'
               ELSE cs.sindicato
          END AS sindicato
   FROM Contrato AS c
   INNER JOIN Movimientos     AS mov ON mov.id_contrato  = c.id_contrato AND mov.id_tipo_movto=1 
   AND DATE(mov.creacion) BETWEEN DATE  (:fechaInicio) AND IF (:fechaFin='',DATE (:fechaInicio),DATE (:fechaFin))
   LEFT JOIN   solicitud    AS s   ON   s.id_solicitud = c.id_solicitud
   LEFT JOIN 
(SELECT numero, CONCAT(nombre, ' ', paterno, ' ', materno) AS nombre, fec_ingre, 'A' AS tipo FROM empleados
UNION
SELECT numero, CONCAT(nombre, ' ', paterno, ' ', materno) AS nombre, fec_ingre, 'J' AS tipo FROM pensionados
UNION
SELECT numero, CONCAT(nombre, ' ', paterno, ' ', materno) AS nombre, fec_ingre, 'E' AS tipo FROM externos)   
   AS tce ON tce.numero = s.titular
   LEFT JOIN catsindicatos    AS cs  ON  cs.cve_sindicato = s.cve_sindicato
   LEFT JOIN 
(SELECT id_contrato,SUM(cargo) AS SumaCargos, SUM(Abono) AS SumaAbonos, SUM(cargo) - SUM(Abono) AS saldo FROM Movimientos
WHERE activo=1
GROUP BY id_contrato)   
    AS cya ON cya.id_contrato  = c.id_contrato
   ORDER BY s.cve_sindicato, c.id_contrato DESC;";
		$comando = $conexion->createCommand($consulta);
		$comando->bindValue("fechaInicio", $fechaInicio);
		$comando->bindValue("fechaFin", $fechaFin);
		return $comando->query()->readAll();
	}
    
	public static function subreporte_resumen_de_importes_por_sindicato($conexion, $fechaInicio, $fechaFin)
	{
		$consulta = " SELECT  s.cve_sindicato,sindicato,SUM(s.saldo_anterior)AS TotalRedocumentacion,
          SUM(((s.plazo * 2) * s.descuento))AS TotalImportePrestamo 
		  FROM Contrato AS c
   INNER JOIN Movimientos     AS mov ON mov.id_contrato  = c.id_contrato AND mov.id_tipo_movto=1 
   AND DATE(mov.creacion) BETWEEN DATE  (:fechaInicio) AND IF (:fechaFin='',DATE (:fechaInicio),DATE (:fechaFin))
   LEFT JOIN   solicitud    AS s   ON   s.id_solicitud = c.id_solicitud
   LEFT JOIN 
(SELECT numero, CONCAT(nombre, ' ', paterno, ' ', materno) AS nombre, fec_ingre, 'A' AS tipo FROM empleados
UNION
SELECT numero, CONCAT(nombre, ' ', paterno, ' ', materno) AS nombre, fec_ingre, 'J' AS tipo FROM pensionados
UNION
SELECT numero, CONCAT(nombre, ' ', paterno, ' ', materno) AS nombre, fec_ingre, 'E' AS tipo FROM externos)   
   AS tce ON tce.numero = s.titular
   LEFT JOIN catsindicatos    AS cs  ON  cs.cve_sindicato = s.cve_sindicato
   LEFT JOIN 
(SELECT id_contrato,SUM(cargo) AS SumaCargos, SUM(Abono) AS SumaAbonos, SUM(cargo) - SUM(Abono) AS saldo FROM Movimientos
WHERE activo=1
GROUP BY id_contrato)   
    AS cya ON cya.id_contrato  = c.id_contrato
   GROUP BY  s.cve_sindicato
   ORDER BY s.cve_sindicato, c.id_contrato DESC";
        $comando = $conexion->createCommand($consulta);
		$comando->bindValue("fechaInicio", $fechaInicio);
		$comando->bindValue("fechaFin", $fechaFin);
		return $comando->query()->readAll();
	}
}
	
?>
     