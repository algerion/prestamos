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

class movimientosAct extends TPage
{
	var $dbConexion;

	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->dbConexion = Conexion::getConexion($this->Application, "dbpr");
		Conexion::createConfiguracion();
		if(!$this->IsPostBack)
        {
			//$this->mostrarDatosGrid();

		}		
	}	
	public function changePage($sender,$param)
    {
        $this->DataGrid->CurrentPageIndex=$param->NewPageIndex;
	   $this->mostrarDatosGrid ();
    }
 
    public function pagerCreated($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,'Page: ');
    }
	 public function toggleColumnVisibility($sender,$param)
    {
        foreach($this->DataGrid->Columns as $index=>$column)
            $column->Visible=$sender->Items[$index]->Selected;
        $this->DataGrid->DataSource=$this->Data;
        $this->DataGrid->dataBind();
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
        $this->DataGrid->dataBind();
    }
      public function mostrarDatosGrid () {
		
		$consulta = "SELECT c.numero AS num_Unico, CONCAT(c.nombre,' ', c.paterno,' ', c.materno) AS nombre
					,CASE tipo_nomi
								  WHEN 'S' THEN 'SEMANAL' 
								  WHEN 'Q' THEN 'QUINCENAL'
								  ELSE 'NO HAY TIPO DE NOMINA' END
					AS TipoNomina
					,CASE c.status
								  WHEN '0' THEN 'PERMISO TEMPORAL' 
								  WHEN '1' THEN 'ACTIVO' 
								  WHEN '2' THEN 'BAJAS' END
					AS TipoEstatus
					,COUNT(a.id_solicitud) AS Autorizadas
					,COUNT(s.id_solicitud) AS Solicitadas
					FROM empleados c JOIN solicitud a ON a.titular = c.numero JOIN solicitud s ON s.titular = c.numero WHERE c.status = :estatu
					AND a.estatus = 'A' AND s.estatus = 'S' 
					GROUP BY c.numero, CONCAT(c.nombre,' ', c.paterno,' ', c.materno), tipo_nomi, c.status
					ORDER BY c.numero ASC ";
		$comando = $this->dbConexion->createCommand($consulta);
		$comando->bindValue(":estatu",$this ->ddlEstadostado->SelectedValue);
		$resultado = $comando->query()->readAll();
		$this->DataGrid->DataSource = $resultado;
		$this->DataGrid->dataBind();
            
		 }
	public function btnBuscar_onclick($sender,$param)
	{
		$this->mostrarDatosGrid ();

	}
}

?>