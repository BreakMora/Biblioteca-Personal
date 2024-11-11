<?php

    define('DB_SERVER', 'localhost:3306');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', 'parcial4');
    define('DB_NAME', 'biblioteca');

    $conn = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME);

    if($conn === false){
        die("ERROR: no se establecio la conexion. " . mysqli_connect_error());
    }

?>