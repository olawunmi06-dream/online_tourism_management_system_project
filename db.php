<?php
define('DB_SERVER', 'localhost');
define('DB_USER' ,'root');
define('DB_PASS', '');
define('DB_NAME', 'online_tourism_management_system_db');

$connect_db = mysqli_connect (DB_SERVER, DB_USER, DB_PASS, DB_NAME); 


if($connect_db == true){
    echo "database connected!";
}else{
    echo "database not connected!";
}
