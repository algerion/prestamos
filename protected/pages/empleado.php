<?php
include_once('../compartidos/clases/usadbf.php');
include_once('../compartidos/clases/charset.php');
include_once('../compartidos/clases/conexion.php');

class Empleado extends TPage
{
	var $dbConexion;
	
	public function onLoad($param)
	{
		parent::onLoad($param);
		
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();

		$regs = UsaDBF::registros_dbf("temp/EMPLEARH.dbf");
		foreach($regs as $r)
		{
			$consulta = "insert into empleados (numero, nombre, paterno, materno) values (:numero, :nombre, :paterno, :materno)";
			$comando = $this->dbConexion->createCommand($consulta);
			$comando->bindValue(":numero", $r["NUMERO"]);
			$comando->bindValue(":nombre", $r["NOMBRE"]);
			$comando->bindValue(":paterno", $r["PATERNO"]);
			$comando->bindValue(":materno", $r["MATERNO"]);
/*			$comando->bindValue(":ura", $r["URA"]);
			$comando->bindValue(":ur", $r["UR"]);
			$comando->bindValue(":pr", $r["PR"]);*/
			$comando->execute();


//echo $consulta . $r["NUMERO"] . "<br />";			
//var_dump($r);
/*			foreach($r as $key=>$value)
			{
				echo $key . ": " . Charset::CambiaCharset($value, 'CP850', 'UTF-8') . " ";
			echo "<br />";
			}
*/
		}
	}
}
?>
