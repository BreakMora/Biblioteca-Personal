<?php

    define('DB_SERVER', 'localhost:3306');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', 'parcial4');
    define('DB_NAME', 'biblioteca');

    $mysqli  = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME);

    if($mysqli->connect_error){
        die("ERROR: " . $mysqli->connect_error);
    }

?>