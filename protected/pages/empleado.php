<?php
class Empleado extends TPage
{
	var $dbConexion;
	
	public function onLoad($param)
	{
		parent::onLoad($param);
		
		//$dsn = "DRIVER={Microsoft dBase Driver (*.dbf)};SourceType=DBF;SourceDB=C:\\db\\article.dbf;DefaultDir=$excelDir;Exclusive=NO;collate=Machine;NULL=NO;DELETED=NO;BACKGROUNDFETCH=NO;";
		$dsn = "Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=temp/EMPLEARH.dbf;Exclusive=NO;Collate=Machine;NULL=NO;DELETED=NO;BACKGROUNDFETCH=NO;OLE DB Services=0";
		$this->dbConexion = odbc_connect($dsn,"","");
		$result = odbc_exec($this->conn, "SELECT * FROM emplearh");
		if ($valor = odbc_fetch_array($result))
		{
			foreach($valor as $v)
				echo $v;
		}
	}
}
?>
