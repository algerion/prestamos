<?php
$ftp_server = '192.168.201.250';
$ftp_user_name = 'nomina';
$ftp_user_pass = 'nomin4';
$local_file = 'temp/index.php';
$remote_file = 'a.txt';



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
$upload = ftp_put($conn_id, $remote_file, $local_file, FTP_BINARY);  

// comprobar el estado de la subida
if (!$upload) {  
    echo "¡La subida FTP ha fallado!";
} else {
    echo "Subida de $remote_file a $ftp_server como $local_file";
}
echo "<br />";

// cerrar la conexión ftp 
ftp_close($conn_id);
?>
