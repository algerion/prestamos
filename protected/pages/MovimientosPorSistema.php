<?php
include_once('../compartidos/clases/conexion.php');
include_once('../compartidos/clases/usadbf.php');
class MovimientosPorSistema extends TPage
{
	var $dbConexion, $Consulta;
	
	public function onLoad($param)
	{
		parent::onLoad($param);
		$id_Contrato = $_REQUEST['id'];
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
		
	
	}
		public function carga_solicitud($sender,$param)
	{
		$consulta = "SELECT idMovimiento,  movimiento, idTMovto FROM catMovimiento WHERE idMovimiento = :id_Movimiento";
			
		$comando = $this->dbConexion->createCommand($consulta); 
		$comando->bindValue(":id_Movimiento",$this->ddlMovimiento->SelectedValue));
		$result = $comando->query()->readAll();
		$idMovimiento = $result[0]["idMovimiento"];
		$movimiento = $result[0]["movimiento"];
		$idTMovto = $result[0]["idTMovto"];
		
		$consulta="insert into movimientos (id_contrato,creacion,id_tipo_movto,descripcion,cargo,abono,id_usuario,aplicacion,id_descuento,activo)" 
		." values(:id_movimiento,:id_contrato,:creacion,:id_tipo_movto,:descripcion,:cargo,:abono,:id_usuario,:aplicacion,:id_descuento,:activo )";
		
		$comando = $this->dbConexion->createCommand($consulta);	
		$descuento = ($this->txtPlazo1->Text * 2); 
		//$comando->bindValue(":id_movimiento",$this->txtFecha->Text);
		$comando->bindValue(":id_contrato",$this->txtNoUnicoTit->Text);
		$comando->bindValue(":creacion",$this->txtAntiguedadNumTit->Text);
		$comando->bindValue(":id_tipo_movto",$this->txtTipoNumTit->Text);
		$comando->bindValue(":descripcion",$this->txtSindicatoNumTit->Text);
		$comando->bindValue(":cargo",$this->txtNoUnicoAval1->Text);
		$comando->bindValue(":abono",$this->txtTipoNumTit->Text);
		$comando->bindValue(":id_usuario",$this->txtTipoNumTit->Text);
		$comando->bindValue(":aplicacion",'');
		$comando->bindValue(":id_descuento",0);
		$comando->bindValue(":activo"1);
		
		if($comando->execute()){
		   $this->ClientScript->RegisterBeginScript("Mensaje","alert('El movimiento fue insertado correctamente');");
		}
		else{
		  $this->ClientScript->RegisterBeginScript("Mensaje","alert('El movimiento NO fue insertado correctamente');");
		}	
		
	}
	
}
?>