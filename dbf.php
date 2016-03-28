<?php
//	dbf("EMPLEARH.dbf");

//	dbf("PDFIJAPE.dbf");
//	"VRecordCount/vFirstRecord/vRecordLength"
	$struc = 
		array(
			array('nombre'=>'NOMBRE', 'tipo'=>'C', 'longitud'=>40, 'decimales'=>0),
			array('nombre'=>'EDAD', 'tipo'=>'N', 'longitud'=>2, 'decimales'=>0),
			array('nombre'=>'NACIMIENTO', 'tipo'=>'D', 'longitud'=>8, 'decimales'=>0),
			array('nombre'=>'SALARIO', 'tipo'=>'N', 'longitud'=>10, 'decimales'=>2),
		);
	$guarda = 
		array(
			array('Angel Bravo', '34', '19810507', '2000.40'),
			array('Berenice Robles', '30', '19850228', '3010.55'),
			array('Tony Robles', '18', '19960220', '0.22'),
		);
	
	esc("sueldos.dbf", $struc, $guarda);
//	dbf("tabla5.dbf");
//	$v = array(236, 125, 37, 0, 128, 158, 85, 4);
//	$v = array(4, 85, 158, 128, 0, 37, 125, 236);
/*	$m = 1;
	$f = 0;
	foreach($v as $n)
	{
		$f += $n * $m;
		$m *= 256;
	}
	echo $f . "<br />";
	echo date('Y-m-d', $f);
	
	*/
	function esc($dbfname, $estructura, $contenido)
	{
		$fdbf = fopen($dbfname,'w');
		$long_estruc = count($estructura);
		$primer_registro = ($long_estruc + 1) * 32 + 1;
		$longitud_total = array_sum(array_map(function($element) {return $element['longitud'];}, $estructura));
		$bin = pack("C4Vv2@32", 3, date("y"), date("m"), date("d"), count($contenido), $primer_registro, $longitud_total + 1);

		$ini = 1;
		foreach($estructura as $est)
		{
			$bin .= pack("a11A1VC2@32", $est["nombre"], $est["tipo"], $ini, $est["longitud"], $est["decimales"]);
			$ini += $est["longitud"];
		}
		
		$bin .= pack("C", 13);

		foreach($contenido as $cont)
		{
			$bin .= pack("C", 32);
			for($i = 0; $i < $long_estruc; $i++)
				$bin .= pack("A" . $estructura[$i]['longitud'], $cont[$i]);
		}

		$bin .= pack("C", 26);
		
//		echo "<br /><br />";
		print_r(unpack("C*",$bin));
		fwrite($fdbf, $bin);
		fclose($fdbf); 
	}
	
	
	function dbf($dbfname) {
		$fdbf = fopen($dbfname,'r');

		$cad = "";
		
		while (!feof($fdbf)) 
		{ 
			$buf = fread($fdbf, 32);
			if(!isset($header))
				$header = unpack("VRecordCount/vFirstRecord/vRecordLength", substr($buf, 4, 8));
				$cad .= $buf;
		}
		
		echo 'Header: ' . json_encode($header) . '<br/>';
		print_r(unpack("C*", $cad));
//		print_r(unpack("a241", substr($cad, 465, 241)));

/*
		$fields = array();
		$records = array();
		$buf = fread($fdbf,32);
		$header = unpack("VRecordCount/vFirstRecord/vRecordLength", substr($buf, 4, 8));
		echo 'Header: ' . json_encode($header) . '<br/>';
		$header = unpack("C*", $buf);
		echo 'Header: ' . json_encode($header) . '<br/>';
		

		$unpackString = '';

		// read fields:
		while (!feof($fdbf)) 
		{ 
			$buf = fread($fdbf, 32);
			if(substr($buf, 0, 1) != chr(13)) 
			{
				$field = unpack("a11fieldname/A1fieldtype/Voffset/Cfieldlen/Cfielddec", substr($buf, 0, 18));
				$unpackString .= "A$field[fieldlen]$field[fieldname]/";
				array_push($fields, $field);
				print_r(unpack("C*", substr($buf, 0, 18)));
				//print_r(unpack("C*", substr($buf, 18, 14)));
				//print_r(unpack("C*", $buf));
				echo "<br />";
			}
			else
				break;
		}

		// move back to the start of the first record (after the field definitions)
		fseek($fdbf, $header['FirstRecord'] + 1); 

		//raw record
		for ($i = 1; $i <= $header['RecordCount']; $i++) 
		{
			$buf = fread($fdbf, $header['RecordLength']);
			$record = unpack($unpackString, $buf);
			array_push($records, $record);
		} 
*/
		fclose($fdbf); 
	}

