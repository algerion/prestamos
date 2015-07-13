<?php
$ftp_server = 'localhost';
$ftp_user_name = 'nomina';
$ftp_user_pass = 'nomin4';
$local_file = 'temp/EMPLEARH.DBF';
$remote_file = 'EMPLEARH.DBF';


// establecer una conexión básica
$conn_id = ftp_connect($ftp_server); 

// iniciar una sesión con nombre de usuario y contraseña
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

// verificar la conexión
if ((!$conn_id) || (!$login_result)) {  
    echo "¡La conexión FTP ha fallado!";
    echo "Se intentó conectar al $ftp_server por el usuario $ftp_user_name"; 
    exit; 
} else {
    echo "Conexión a $ftp_server realizada con éxito, por el usuario $ftp_user_name";
}
echo "<br />";

// bajar un archivo
$download = ftp_get($conn_id, $local_file, $remote_file, FTP_BINARY);  

// comprobar el estado de la bajada
if (!$download) {  
    echo "¡La bajada FTP ha fallado!";
} else {
    echo "Bajada de $remote_file a $ftp_server como $local_file";
}
echo "<br />";

// cerrar la conexión ftp 
ftp_close($conn_id);
?>
