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
				"0 AS antiguedad, 0 AS importe, 0 AS porcentaje, 'E' AS tipo FROM externos e";
		$consulta = "";
		$where = " WHERE (e.numero LIKE :busca OR e.nombre LIKE :busca OR e.paterno LIKE :busca OR e.materno LIKE :busca) ";
		$sind = "";
		
		if($sindicato != null)
			$sind .= "AND e.sindicato = :sindicato ";
		
		if($tipo == 0 || $tipo == 1)
			$consulta .= $camposempjub . 
					", CASE WHEN cs.cve_sindicato IN (0, 12) THEN 'C' ELSE 'S' END AS tipo FROM empleados" . 
					$joinsempjub . $where . $sind;
		if($tipo == 0)
			$consulta .= " UNION ";
		if($tipo == 0 || $tipo == 2)
			$consulta .= $camposempjub . ", 'J' AS tipo FROM pensionados" . $joinsempjub . $where . $sind;
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
}
?>