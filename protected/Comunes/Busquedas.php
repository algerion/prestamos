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
		$consulta = "SELECT B.idContrato, sum(C.Cargo - C.Abono) as saldo From solicitud A Join contrato B on B.idSolicitud = A.idSolicitud Join movimiento C On C.idContrato = B.idContrato And C.Activo=1 Where A.titular = :titular and A.estatus='A' and B.estatus='A' Group By B.idContrato Having saldo > 1";
		$comando = $conexion->createCommand($consulta);
		$comando->bindValue("busca", "%" . $busca . "%");
		if($sindicato != null)
			$comando->bindValue("sindicato", $sindicato);

		return $comando->query()->readAll();
		
	}
	public static function obtenerPrestamoAnteriorSinRedocumentado($conexion, $titular)
	{

		$consulta = "Select B.id_contrato, sum(C.Cargo - C.Abono) as saldo From solicitud A Join contrato B on B.id_solicitud = A.id_solicitud Join movimientos C On C.id_contrato = B.id_contrato  And C.Activo=1 Where A.titular = :titular and A.estatus='A' and B.estatus='A' Group By B.id_contrato Having saldo > 1 Order By B.id_contrato";
		$comando = $conexion->createCommand($consulta);
		$comando->bindValue("titular", $titular);

		return $comando->query()->readAll();		
	}
	public static function aval_disponible($conexion, $titular)
	{
		$consulta = "Select A.idSolicitud, sum(C.Cargo - C.Abono) as saldo From solicitud A Join contrato B on B.idSolicitud = A.idSolicitud Join movimiento C On C.idContrato = B.idContrato and A.aval1 = aval  Or A.aval2 =  aval 
    By B.idContrato, A.estatus 
   Having (saldo > 1) or (A.estatus = 'S') " & _
   "Order By B.idContrato "
		$comando = $conexion->createCommand($consulta);
		$comando->bindValue("aval_disponible", $aval_disponible);
		if($sindicato != null)
	}
}
?>