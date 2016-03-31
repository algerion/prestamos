<?php
include_once('../compartidos/clases/conexion.php');
include_once('../compartidos/clases/usadbf.php');
class GenerarDBF extends TPage
{
	var $dbConexion, $Consulta;
	
	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
	
	}
	
	public function btnGenerar_Click($sender, $param)
	{
		 
		$def = array(
			   array("numero",    "N",10,0),  
			   array("nombre" ,   "C",50  ),     
			   array("paterno" ,  "C",50  ),
			   array("fec_ingre", "D",    ),
			   array("sexo",      "C",50  )  
		); 
		$created = "no"; 
		$created = dbase_create('permiso.dbf', $def); 
		if ($created == FALSE) { 
		  echo "ERROR.<br>NO SE PUDO CREAR: <b>.test.dbf</b>\n"; 
		  exit; 
		} else { 
		$infocreate = "dBase: test.dbf<br>was created<br><br>"; 
		}

		$db=dbase_open('permiso.dbf', 2); 
		if($db){
			$sql_query= "SELECT e.numero as numero, e.nombre as nombre, e.paterno as paterno, e.fec_ingre as fec_ingre, e.sexo as sexo FROM empleados e WHERE e.status = :idStatus";
			$comando = $this->dbConexion->createCommand($sql_query); 
			$comando->bindValue(":idStatus",$this->ddlTipo->Text);
			$db_records = $comando->query();
			$i = 0;
			foreach($db_records as $key)
			{
				$consulta = "SELECT numero FROM empleados  WHERE numero= '" . $key["numero"] . "'";
				$comando = $this->dbConexion->createCommand($consulta);	
				$type_name = $comando->queryScalar();
				
				dbase_add_record($db, array(
				$type_name,
				$key['nombre'],
				$key['paterno'],
				$key['fec_ingre'],
				$key['sexo']));
			} 	
		}
	}
	
	public function btnGenerar_Angel_Click($sender, $param)
	{
		$def = 
			array(
				array('nombre'=>'numero', 'tipo'=>'N', 'longitud'=>10, 'decimales'=>0),
				array('nombre'=>'nombre', 'tipo'=>'C', 'longitud'=>50, 'decimales'=>0),
				array('nombre'=>'paterno', 'tipo'=>'C', 'longitud'=>50, 'decimales'=>0),
				array('nombre'=>'fec_ingre', 'tipo'=>'D', 'longitud'=>8, 'decimales'=>0),
				array('nombre'=>'sexo', 'tipo'=>'C', 'longitud'=>50, 'decimales'=>0),
			);
		$sql_query= "SELECT e.numero as numero, e.nombre as nombre, e.paterno as paterno, date_format(e.fec_ingre, '%Y%m%d') as fec_ingre, e.sexo as sexo FROM empleados e WHERE e.status = :idStatus";
		$comando = $this->dbConexion->createCommand($sql_query); 
		$comando->bindValue(":idStatus",$this->ddlTipo->Text);
		$db_records = $comando->query()->readAll();
		UsaDBF::esc("cosarara.dbf", $def, $db_records);
		//var_dump($db_records);
	}
}
?>