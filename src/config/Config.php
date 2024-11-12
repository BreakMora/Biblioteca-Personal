<?php
    // Definicion de constantes para la conexion a la base de datos
    define('DB_SERVER', 'localhost:3306');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', 'parcial4');
    define('DB_NAME', 'biblioteca');

    // Establece la conexión con la base de datos
    $conn  = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME);

    // Comprueba si la conexión se realizo
    if($conn == false){
        die("ERROR: No se pudo conectar. " . mysqli_connect_error()); // Si no se pudo conectar, se muestra el mensaje de error y se detiene la ejecución
    }
    
?>