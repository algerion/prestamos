<?php
class Busquedas
{
	public static function empleados($conexion, $tipo, $busca, $sindicato = null)
	{
		$camposempjub = "SELECT e.numero, nombre, paterno, materno, cs.sindicato, fec_ingre, " .
				"TIMESTAMPDIFF(YEAR, fec_ingre, CURDATE()) AS antiguedad, " . 
				"IFNULL(df.importe, 0) AS importe, IFNULL(df.porcentaje, 0) AS porcentaje";
		$joinsempjub = " e LEFT JOIN catsindicatos cs ON e.sindicato = cs.cve_sindicato " .
				"LEFT JOIN descuentos_fijos df ON e.numero = df.numero AND df.concepto = 61";
		$externos = "SELECT e.numero, nombre, paterno, materno, 0 AS sindicato, fec_ingre, " . 
				"0 AS antiguedad, 0 AS importe, 0 AS porcentaje, 'EXTERNO' AS tipo FROM externos e";
		$consulta = "";
		$where = " WHERE (e.numero LIKE :busca OR e.nombre LIKE :busca OR e.paterno LIKE :busca OR e.materno LIKE :busca) ";
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
		$consulta = "SELECT e.numero";
		$comando = $conexion->createCommand($consulta);
		$comando->bindValue("busca", "%" . $busca . "%");
		if($sindicato != null)
			$comando->bindValue("sindicato", $sindicato);

		return $comando->query()->readAll();
	}
	public static function obtenerPrestamoAnteriorSinRedocumentado($conexion, $titular)
	{

		$consulta = "SELECT e.numero Select B.idContrato, sum(C.Cargo - C.Abono) as saldo " & _"From solicitud A " & _ "Inner Join contrato B on B.idSolicitud = A.idSolicitud " & _ "Inner Join movimiento C On C.idContrato = B.idContrato and C.Activo=1 " &  "Where A.titular = " & titular & " and A.estatus='A' and B.estatus='A' " & _"Group By B.idContrato " & _ "Having saldo > 1 " & _ "Order By B.idContrato "";
		$comando = $conexion->createCommand($consulta);
		$comando->bindValue("busca", "%" . $busca . "%");
		if($sindicato != null)
			$comando->bindValue("sindicato", $sindicato);

		return $comando->query()->readAll();	
	}
}
?>