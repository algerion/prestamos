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

class movimientosEmpleado extends TPage
{
	var $dbConexion;

	private $_data=null;
 
    protected function getData()
    {
        if($this->_data===null)
            $this->loadData();
        return $this->_data;
    }
	    protected function loadData()
    {
		$idTitular= $_REQUEST['id'];
		
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
		
		$consulta= "SELECT s.titular,s.id_solicitud
					,(SELECT CONCAT(nombre,' ', paterno,' ', materno) AS nombre FROM empleados WHERE numero = titular) AS nombre
					,(SELECT (SELECT sindicato FROM catsindicatos WHERE cve_sindicato = e.sindicato) AS Tipo_sindicato FROM empleados e WHERE e.numero = titular) AS sindicato
					,(SELECT (SELECT CASE cat.status
							WHEN '0' THEN 'PERMISO TEMPORAL' 
							WHEN '1' THEN 'ACTIVO' 
							WHEN '2' THEN 'BAJAS' END
					FROM empleados cat WHERE cat.numero = c.numero) AS TipoEstatus  FROM empleados c WHERE  c.numero = titular) AS estatus
					,(SELECT CASE tipo_nomi
								  WHEN 'S' THEN 'SEMANAL' 
								  WHEN 'Q' THEN 'QUINCENAL'
								  ELSE 'NO HAY TIPO DE NOMINA' END
					FROM empleados cat WHERE cat.numero = titular) AS TipoNomina
					,(SELECT fec_ingre FROM empleados WHERE numero = titular) AS fechaIngreso
					,(SELECT entrega_cheque FROM contrato WHERE id_solicitud = s.id_solicitud) AS FirmadeCheque
					FROM solicitud s WHERE titular = :idTitular AND estatus IN ('A','S')";

		$comando = $this->dbConexion->createCommand($consulta); 
		$comando->bindValue(":idTitular", $idTitular);
		$result = $comando->query()->readAll();
		if(count($result) > 0)
		{
		$this->txtNoUnicoTit->Text = $result[0]["titular"];
		$this->txtNombreTit->Text = $result[0]["nombre"];
		$this->txtSindicatoTit->Text = $result[0]["sindicato"];
		$this->txtTipoTit->Text = $result[0]["estatus"];
		$this->txtNominaTit->Text = $result[0]["TipoNomina"];
		$this->txtFirmaCheque->Text = $result[0]["FirmadeCheque"];
	    $fechaIngresoTit = $result[0]["fechaIngreso"];
		$fecha = date('Y/m/j');
		$datetime2 = date_create($fecha);
		$IngresoTit = date_create($fechaIngresoTit);
		$TitularFecha = date_diff($IngresoTit, $datetime2);
		$TitularFecha = $TitularFecha->format('%Y Año %m Meses %d Dias');
		$this->txtAntiguedadTit->Text = $TitularFecha; 
		
		$sql_query= "SELECT titular,id_solicitud
					,(SELECT CONCAT(nombre,' ', paterno,' ', materno) AS nombre FROM empleados WHERE numero = titular) AS nombre
					,aval1,aval2,importe, plazo,tasa,importe_pa_aval1, porcentaje_pa_aval1,importe_pa_aval2, porcentaje_pa_aval2, observacion,estatus
					FROM solicitud WHERE titular = :idTitular AND estatus IN ('A','S') ORDER BY id_solicitud DESC";
		$comando = $this->dbConexion->createCommand($sql_query); 
		$comando->bindValue(":idTitular", $idTitular);
		$db_records = $comando->query();
		
		$i = 0;
		
			foreach($db_records as $key){
			$consulta = "SELECT id_solicitud FROM solicitud where id_solicitud='" . $key["id_solicitud"] . "'";
			$comando = $this->dbConexion->createCommand($consulta);
			$type_name = $comando->queryScalar();			
			$array[$i] = array(
			//'ID'=>$i+1,
			'id_solicitud'=>$type_name
			,'numero'=>$key["titular"]
			,'aval1'=>$key["aval1"]
			,'aval2'=>$key["aval2"]
			,'importe'=>$key["importe"]
			,'plazo'=>$key["plazo"]
			,'tasa'=>$key["tasa"]
			,'importe_pa_aval1'=>$key["importe_pa_aval1"]
			,'porcentaje_pa_aval1'=>$key["porcentaje_pa_aval1"]
			,'importe_pa_aval2'=>$key["importe_pa_aval2"] 
			,'porcentaje_pa_aval2'=>$key["porcentaje_pa_aval2"]
			,'observacion'=>$key["observacion"]
			,'estatus'=>$key["estatus"]
			);
		$i++;
		} 
		$this->_data = $array;
		
		$this->saveData(); 
		}else{
		$this->ClientScript->RegisterBeginScript("Mensaje","alert('El titular no tiene ninguna solicitud pendiente.');");
		}
		
	}
 
    protected function saveData()
    {
        $this->setViewState('Data',$this->_data);
    }
	protected function updateBook($id_solicitud,$importe_pa_aval1,$porcentaje_pa_aval1,$importe_pa_aval2, $porcentaje_pa_aval2,$observacion)
	{
        if($this->_data===null)
            $this->loadData();
        $updateRow=null;
        foreach($this->_data as $index=>$row)
		{
           if($row['id_solicitud']===$id_solicitud)
                $updateRow=&$this->_data[$index];
		}

		if($updateRow!==null){

			$consulta = "update solicitud set importe_pa_aval1=:importe_pa_aval1,  porcentaje_pa_aval1=:porcentaje_pa_aval1,  importe_pa_aval2=:importe_pa_aval2 , porcentaje_pa_aval2=:porcentaje_pa_aval2, observacion =:observacion where id_solicitud=:id_solicitud";
			$comando = $this->dbConexion->createCommand($consulta);
			$comando->bindValue(":id_solicitud", $id_solicitud);
			$comando->bindValue(":importe_pa_aval1", $importe_pa_aval1);
			$comando->bindValue(":porcentaje_pa_aval1", $porcentaje_pa_aval1);
			$comando->bindValue(":importe_pa_aval2", $importe_pa_aval2);
			$comando->bindValue(":porcentaje_pa_aval2", $porcentaje_pa_aval2);
			$comando->bindValue(":observacion", $observacion);
			$comando->execute();
			$this->loadData();
            $this->DataGrid->DataSource=$this->Data;
            $this->DataGrid->dataBind();

		}
    }

	public function saveItem($sender,$param)
    {
        $item=$param->Item;
        $this->updateBook(
            $this->DataGrid->DataKeys[$item->ItemIndex],    
            $item->importe_pa_aval1->TextBox->Text,            
            $item->porcentaje_pa_aval1->TextBox->Text,
			$item->importe_pa_aval2->TextBox->Text,            
            $item->porcentaje_pa_aval2->TextBox->Text,
			$item->observacion->TextBox->Text
            );	
        $this->DataGrid->EditItemIndex=-1;
        $this->DataGrid->DataSource=$this->Data;
        $this->DataGrid->dataBind();
    }
	public function onLoad($param)
    {
        parent::onLoad($param);
        if(!$this->IsPostBack)
        {
            $this->DataGrid->DataSource=$this->Data;
            $this->DataGrid->dataBind();
        }
    }
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='EditItem')
        {
			$item->id_solicitud->TextBox->Columns=5;
			$item->numero->TextBox->Columns=5;
			$item->aval1->TextBox->Columns=4;
			$item->aval2->TextBox->Columns=4;
            $item->importe->TextBox->Columns=5;
			$item->plazo->TextBox->Columns=3;
			$item->tasa->TextBox->Columns=3;
			$item->importe_pa_aval2->TextBox->Columns=8;
			$item->porcentaje_pa_aval1->TextBox->Columns=5;
            $item->importe_pa_aval1->TextBox->Columns=8;
			$item->porcentaje_pa_aval2->TextBox->Columns=5;
            $item->observacion->TextBox->Columns=10;
			$item->estatus->TextBox->Columns=2;
        }
    }
 
    public function editItem($sender,$param)
    {
        $this->DataGrid->EditItemIndex=$param->Item->ItemIndex;
        $this->DataGrid->DataSource=$this->Data;
        $this->DataGrid->dataBind();
    }
	public function changePage($sender,$param)
    {
        $this->DataGrid->CurrentPageIndex=$param->NewPageIndex;
		$this->DataGrid->DataSource=$this->Data;
    }
 
    public function pagerCreated($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,'Page: ');
    }
 
    public function changePagerPosition($sender,$param)
    {
        $top=$sender->Items[0]->Selected;
        $bottom=$sender->Items[1]->Selected;
        if($top && $bottom)
            $position='TopAndBottom';
        else if($top)
            $position='Top';
        else if($bottom)
            $position='Bottom';
        else
            $position='';
        if($position==='')
            $this->DataGrid->PagerStyle->Visible=false;
        else
        {
            $this->DataGrid->PagerStyle->Position=$position;
            $this->DataGrid->PagerStyle->Visible=true;
			$this->DataGrid->DataSource=$this->Data;
        }
    }
 
    public function useNumericPager($sender,$param)
    {
        $this->DataGrid->PagerStyle->Mode='Numeric';
        $this->DataGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
        $this->DataGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
        $this->DataGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;
        $this->DataGrid->dataBind();
    }
 
    public function useNextPrevPager($sender,$param)
    {
        $this->DataGrid->PagerStyle->Mode='NextPrev';
        $this->DataGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
        $this->DataGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
        $this->DataGrid->dataBind();
    }
 
    public function changePageSize($sender,$param)
    {
        $this->DataGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
        $this->DataGrid->CurrentPageIndex=0;
		$this->DataGrid->DataSource=$this->Data;
        $this->DataGrid->dataBind();
    }
}
?>