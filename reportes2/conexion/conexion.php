<?php
// conecion con el servidor
$conexion = mysql_connect("localhost","root","vertrigo");
//seleccion de la base de datos
mysql_select_db("prestamos",$conexion);
mysql_set_charset('utf8');
?>