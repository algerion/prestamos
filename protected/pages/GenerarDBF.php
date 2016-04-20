<?php
include_once('../compartidos/clases/conexion.php');
include_once('../compartidos/clases/usadbf.php');
Prado::using('System.Web.UI.ActiveControls.*');
include_once('../compartidos/clases/charset.php');


class GenerarDBF extends TPage
{
	var $dbConexion, $Consulta;
	
	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
	
	}
	public function btnRecibir_Click($sender, $param)
	{
		$archivo = file("C:\\www\\prestamos\\DESNO\\recibidos\\DESNO". $this->ddlTipoNomina->SelectedValue .".txt");
		$parametros = array("origen"=>"N", "creado"=>date("Y-m-d H:i:s"), "modificado"=>date("Y-m-d H:i:s"), "creador"=>0, "modificador"=>0, "id_estatus"=>3
		,"observaciones"=>"desno recibido exitosamente", "tipo"=>($this->ddlTipo->SelectedValue == 'PE' ? "J" : "A"), "pago"=>$this->ddlTipoNomina->SelectedValue, "periodo"=>0);
		Conexion::Inserta_Registro($this->dbConexion, "descuento", $parametros);
		$idescuento = Conexion::Ultimo_Id_Generado($this->dbConexion);

		foreach ($archivo as $linea_num => $linea)
		{
		  $datos = explode("|",$linea);	  

		$consulta = "INSERT INTO descuento_detalle(id_descuento,num_empleado,clavecon,importe,periodo,periodos,contrato,aplicado,tipo_nomina,nomina,aval1,aval2,nota,aplicaravales )"
				."VALUES(:id_descuento,:num_empleado,:clavecon,:importe,:periodo,:periodos,:contrato,:aplicado,:tipo_nomina,:nomina,:aval1,:aval2,:nota,:aplicaravales)";
		$comando = $this->dbConexion->createCommand($consulta);	
		    $comando->bindValue(":id_descuento",$idescuento); 
		   $comando->bindValue(":num_empleado",trim($datos[0])); 
		   $comando->bindValue(":clavecon",trim($datos[1]));    
		   $comando->bindValue(":importe",trim($datos[2]));  
		   $comando->bindValue(":periodo",trim($datos[3]));  
		   $comando->bindValue(":periodos",trim($datos[4]));  
		   $comando->bindValue(":contrato",trim($datos[5]));      
		   $comando->bindValue(":aplicado",trim($datos[6]));  
		   $comando->bindValue(":tipo_nomina",trim($datos[7]));  
		   $comando->bindValue(":nomina",trim($datos[8]));    
		   $comando->bindValue(":aval1",trim($datos[9]));   
		   $comando->bindValue(":aval2",trim($datos[10]));    
		   $comando->bindValue(":nota",trim($datos[11]));   
		   $comando->bindValue(":aplicaravales",trim($datos[12]));
			if($comando->execute()){
			  $this->ClientScript->registerEndScript("bajada",
				"alert('carga desno completada');\n");
			}
			else{
			  $this->ClientScript->registerEndScript("mensaje",
				"alert('error carga desno completada');\n");
			}	
		
		}
	}	
	
	public function btnActualizar_Click($sender, $param)
	{
		$archivo = file("C:\\www\\prestamos\\DESNO\\recibidos\\DESNO.txt");
	}
	public function btnGenerar_Click($sender, $param)
	{	
		$consulta="INSERT INTO respMovimientos (id_contrato,  cargo,   abono,  activo , adeudo,movimientos)  
					SELECT id_contrato,cargo, abono,activo,SUM(Cargo - Abono) AS adeudo ,COUNT(id_contrato) AS movimientos FROM movimientos WHERE  activo = 1 GROUP BY id_contrato HAVING SUM(Cargo - Abono) > 1";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->execute();
		// ----------------------------------------------------------------------------------------------------------------------------------------------------------------
		$registros = ("SELECT COUNT(*) AS registro FROM  contrato A
				INNER JOIN  solicitud B 	ON B.id_Solicitud = A.id_Solicitud
				INNER JOIN respMovimientos C	ON C.id_contrato  = A.id_contrato
				INNER JOIN sujetos 	T	ON T.numero = B.titular
				INNER JOIN sujetos 	AV1	ON AV1.numero = B.aval1
				INNER JOIN sujetos 	AV2	ON AV2.numero = B.aval2
				WHERE A.estatus = 'A' AND T.tipo = :TipoEmpleado AND A.congelado = 0 ");
		$comando = $this->dbConexion->createCommand($registros); 
		$comando->bindValue(":TipoEmpleado",$this->ddlEmpleado->Text);
		$result =$comando->query()->readAll();
		$Total_registros = $result[0]["registro"];
		if(count($Total_registros) > 0)
		{
		$datos =("SELECT B.titular as NUMERO,  63 AS CLAVECON, B.descuento as IMPORTE, B.plazo as PERIODO,C.movimientos as PERIODOS 
				,A.id_contrato as CONTRATO,'0' AS APLICADO, '' AS TIPO,'' AS NOMINA 
				, B.aval1 as AVAL1, B.aval2 as AVAL2,'' AS NOTA, '' AS APAVAL
				,(SELECT tipo_nomi FROM EMPLEADOS WHERE numero = B.Titular) AS tipo_nomina
				, T.status AS EMPLEACT,  AV1.status AS AVAL1ACT, AV2.status AS AVAL2ACT
				FROM  contrato A
				INNER JOIN  solicitud B 	ON B.id_Solicitud = A.id_Solicitud
				INNER JOIN respMovimientos C	ON C.id_contrato  = A.id_contrato
				INNER JOIN sujetos 	T	ON T.numero = B.titular
				INNER JOIN sujetos 	AV1	ON AV1.numero = B.aval1
				INNER JOIN sujetos 	AV2	ON AV2.numero = B.aval2
				WHERE A.estatus = 'A' AND T.tipo = :TipoEmpleado AND A.congelado = 0 ");
		$comando = $this->dbConexion->createCommand($datos); 
		$comando->bindValue(":TipoEmpleado",$this->ddlEmpleado->Text);
		$reg =$comando->query()->readAll();
		if(count($reg) > 0)
		{
			if ($this->ddlEmpleado->Text == 'A' and $this->ddlNomina->Text == 'Q'){
				$f = fopen("DESNOQ.txt","w");
			}ELSE if ($this->ddlEmpleado->Text == 'J' and $this->ddlNomina->Text == 'Q'){
				$f = fopen("DESNOJ.txt","w");
			}
			$sep = "|";
			$salto = "\r\n";
		
			for($i = 0; $i < $Total_registros; $i++){
				$linea = $reg[$i]["NUMERO"] . $sep . $reg[$i]["CLAVECON"] . $sep . $reg[$i]["IMPORTE"] . $sep . $reg[$i]["PERIODO"] . $sep . $reg[$i]["PERIODOS"] . $sep . $reg[$i]["CONTRATO"] . $sep . $reg[$i]["APLICADO"] . $sep . $reg[$i]["TIPO"] . $sep . $reg[$i]["NOMINA"] . $sep . $reg[$i]["AVAL1"] . $sep . $reg[$i]["AVAL2"] . $sep . $reg[$i]["NOTA"] . $sep . $reg[$i]["APAVAL"] . $sep . $reg[$i]["EMPLEACT"] . $sep . $reg[$i]["AVAL1ACT"] . $sep . $reg[$i]["AVAL2ACT"] . $salto;
				fwrite($f,$linea);
			}
		fclose($f);
			$liberar =("TRUNCATE respMovimientos");
			$comando = $this->dbConexion->createCommand($liberar);
			$comando->execute();
			//$this->ClientScript->RegisterBeginScript("Mensaje","alert('Se ah generado el archivo');");
		}else{
			//$this->ClientScript->RegisterBeginScript("Mensaje","alert('No hay registros que procesar');");
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
	public function ddlTipo_Change($sender, $param)
	{
		if($this->ddlTipo->SelectedValue == 'PE')
		{
			$this->ddlTipoNomina->Enabled = false;
			$this->ddlTipoNomina->SelectedValue = "Q";
		}
		else
			$this->ddlTipoNomina->Enabled = true;
	}
}
?>