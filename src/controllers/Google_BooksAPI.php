<?php

// Aqui se verifica si no ha iniciado una sesion y la inicia si es necesario
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión para mantener datos del usuario
}

// Aqui se verifica si no existe un token de acceso en la sesión
if(!isset($_SESSION['access_token'])){ 
    die("Acceso no autorizado. Por favor inicie sesión."); // Cancela la ejecución del script si el usuario no está autenticado.
}

class GoogleBooksAPI {
    private $api_url = "https://www.googleapis.com/books/v1/volumes/"; // URL base de la API de Google Books

    // Método para buscar libros en la API de GoogleBooks
    public function buscar_Libros($query, $maxResultados = 30) {
        $access_token = $_SESSION['access_token']; // Toma el token de acceso de la sesión
        $url = $this->api_url . "?q=" . urlencode($query) . "&maxResults=" . $maxResultados;  // Concatena la URL para hacer la solicitud a la API con el parámetro de búsqueda y el máximo de resultados.

        // Define las opciones para la solicitud http
        $opciones = [
            'http' => [
                'header' => "Autorization: Bearer " . $access_token
            ]
        ];
        
        $contexto = stream_context_create($opciones); // Crea un contexto de transmisión para usar en la solicitud HTTP

        $respuesta = file_get_contents($url, false, $contexto); // Realiza la solicitud a la API y obtiene la respuesta

        if ($respuesta === false){  // Verifica si la solicitud falló
            return null;
        }

        $data = json_decode($respuesta, true); // Decodifica la respuesta JSON en un array asociativo

        if (isset($data['items'])){
            return $data['items']; // Retorna los libros encontrados
        } else {
            return null; // Retorna null si no hay libros en la respuesta
        }
    }
}

?>