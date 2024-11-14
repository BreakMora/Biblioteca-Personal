<?php
// Inicia una sesión de PHP
session_start();
require_once 'UsuarioController.php';
require_once '../config/Config.php';

// Información del cliente de Google (ID de cliente y secreto)
$client_id = '';
$client_secret = '';
$redirect_uri = 'http://localhost/Parcial4_Desarrollo/Biblioteca-Personal/src/controllers/Login_OAuth.php'; // Asegúrate de que coincide con el URI de redireccionamiento configurado en la consola de Google

// Verifica si hay un código de autorización en la URL
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // URL para el intercambio del código por un token
    $token_url = 'https://oauth2.googleapis.com/token';

    // Datos para la solicitud de token
    $token_data = [
        'code' => $code, // Código de autorización recibido.
        'client_id' => $client_id, // ID del cliente de Google.
        'client_secret' => $client_secret, // Secreto del cliente de Google.
        'redirect_uri' => $redirect_uri, // URL de redirección.
        'grant_type' => 'authorization_code' // Tipo de autorización.
    ];

    // Configuración de opciones de contexto HTTP para enviar la solicitud POST
    $opciones = [
        'http' => [
            'method' => 'POST', // Especifica la solicitud POST.
            'header' => 'Content-type: application/x-www-form-urlencoded', // El contenido para la solicitud.
            'content' => http_build_query($token_data) // Convierte los datos del token a formato URL.
        ]
    ];
    
    $contexto = stream_context_create($opciones); // Se crea el contexto para la solicitud HTTP.

    // Enviar la solicitud de token y recibir la respuesta
    $response = file_get_contents($token_url, false, $contexto);

    // Decodificar la respuesta JSON
    $token_response = json_decode($response, true);

    if (isset($token_response['access_token'])) {
        // Guardar el token de acceso en la sesión
        $_SESSION['access_token'] = $token_response['access_token'];

        // Solicitar información del usuario
        $user_info_url = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $_SESSION['access_token'];
        $user_info = file_get_contents($user_info_url);
        $user_data = json_decode($user_info, true);

        // Verifica si se obtuvo la información del usuario
        if (isset($user_data['email'])) {
            // Guardar información del usuario en la sesión
            $_SESSION['user'] = $user_data;

            // Se crea una instancia del controlador de usuario para guardar la información en la base de datos.
            $usuarioController = new UsuarioController($conn);
            $email = $user_data['email'];
            $nombre = $user_data['name'];
            $google_id = $user_data['id'];
            
            $usuarioController->guardar_Usuario($email, $nombre, $google_id); // Guarda los datos del usuario en la base de datos.

            header('Location: ../../public/index.php'); // Redirige al usuario a la página principal
            exit();

        } else {
            echo 'Error al obtener la información del usuario.';
        }
    } else {
        echo 'Error al obtener el token de acceso.';
    }
} else {
    echo 'No se recibió el código de autorización.';
}
?>


