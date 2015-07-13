<?php
$ftp_server = 'localhost';
$ftp_user_name = 'nomina';
$ftp_user_pass = 'nomin4';
$local_file = 'temp/EMPLEAPE.DBF';
$remote_file = 'EMPLEAPE.DBF';


// establecer una conexi�n b�sica
$conn_id = ftp_connect($ftp_server); 

// iniciar una sesi�n con nombre de usuario y contrase�a
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

// verificar la conexi�n
if ((!$conn_id) || (!$login_result)) {  
    echo "�La conexi�n FTP ha fallado!";
    echo "Se intent� conectar al $ftp_server por el usuario $ftp_user_name"; 
    exit; 
} else {
    echo "Conexi�n a $ftp_server realizada con �xito, por el usuario $ftp_user_name";
}
echo "<br />";

// bajar un archivo
$upload = ftp_put($conn_id, $remote_file, $local_file, FTP_BINARY);  

// comprobar el estado de la subida
if (!$upload) {  
    echo "�La subida FTP ha fallado!";
} else {
    echo "Subida de $remote_file a $ftp_server como $local_file";
}
echo "<br />";

// cerrar la conexi�n ftp 
ftp_close($conn_id);
?>
