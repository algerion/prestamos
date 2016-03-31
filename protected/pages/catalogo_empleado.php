<?php
//Prado::using('System.Util.*'); //TVarDump
/*Prado::using('System.Web.UI.ActiveControls.*');
include_once('../compartidos/clases/listas.php');
*/
include_once('../compartidos/clases/conexion.php');
/*
include_once('../compartidos/clases/envia_mail.php');
include_once('../compartidos/clases/charset.php');
*/

class catalogo_empleado extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);
		
			parent::onLoad($param);
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
		if(!$this->IsPostBack)
		{
			//$estatus = Conexion::Retorna_Registro($this->dbConexion, "estatus", array(), " id_estatus > 0");
			$ClaveEmpleado = Conexion::Retorna_Campo($this->dbConexion, "sujetos", "(MAX(numero)+1)", array(""));
			$this->txtclaveempleado->text = $ClaveEmpleado;
			$this->txtEstatus->text = 'ACTIVO';
		}

	}
	public function btnaceptar_onclick($sender,$param){
		$consulta="insert into externos (numero,nombre,paterno,materno,direccion, curp, fec_ingre, sexo,status) values " .
				"(:numero,:nombre,:paterno,:materno,:direccion, :curp, :fec_ingre, :sexo,:status)";
		$comando = $this->dbConexion->createCommand($consulta);	
		$comando->bindValue(":numero",$this->txtclaveempleado->text);
		$comando->bindValue(":nombre",$this->txtNombre->Text);
		$comando->bindValue(":paterno",$this->txtApellidoPaterno->Text);
		$comando->bindValue(":materno",$this->txtApellidoMaterno->Text);
		$comando->bindValue(":direccion",$this->txtdireccion->Text);
		$comando->bindValue(":curp",$this->txtCurp->Text);
		$comando->bindValue(":fec_ingre",$this->datFechadeingreso->Text);
		$comando->bindValue(":sexo",$this->txtGenero->Text);
		$comando->bindValue(":status",1);
		if($comando->execute()){
		   $this->Page->CallbackClient->callClientFunction("Mensaje", "alert('LOS DATOS SE GUARDARON CORRECTAMENTE')");
		   
		      $this->ClientScript->RegisterBeginScript("Mensaje","alert('LOS DATOS SE GUARDARON CORRECTAMENTE');" .
		  		"document.location.href='index.php?page=catalogo_empleado'; ");
		}
		else{
			$this->ClientScript->RegisterBeginScript("Mensaje","alert(' NO SE PUDO GUARDAR LOS DATOS');" .
		  		"document.location.href='index.php?page=catalogo_empleado'; ");
		}	
	}
}

?>