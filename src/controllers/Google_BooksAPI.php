<?php

session_start();

if(!isset($_SESSION['access_token'])){
    die("Acceso no autorizado. Por favor inicie sesión.");
}

class GoogleBooksAPI {
    private $api_url = "https://www.googleapis.com/books/v1/volumes/";

    public function buscarLibro($query, $maxResultados = 10) {
        $access_token = $_SESSION['access_token'];
        $url = $this->api_url . "?q=" . urlencode($query) . "&maxResults=" . $maxResultados;

        $opciones = [
            'http' => [
                'header' => "Autorization: Bearer " . $access_token
            ]
        ];
        
        $contexto = stream_context_create($opciones);

        $respuesta = file_get_contents($url, false, $contexto);

        if ($respuesta === false){
            return null;
        }

        $data = json_decode($respuesta, true);

        if (isset($data['items'])){
            return $data['items'];
        } else {
            return null;
        }
    }
}

?>