<?php
// Inicia una sesión de PHP
session_start();

// Información del cliente de Google (ID de cliente y secreto)
$client_id = '';
$client_secret = '';
$redirect_uri = 'http://localhost/Parcial4_Desarrollo/Biblioteca-Personal/src/controllers/Login_OAuth.php'; // Asegúrate de que coincide con el URI de redireccionamiento configurado en la consola de Google

// Verifica si hay un código de autorización en la URL
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // URL para el intercambio del código por un token
    $token_url = 'https://oauth2.googleapis.com/token';

    // Configuración de la solicitud de token
    $token_data = [
        'code' => $code,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code'
    ];

    // Enviar la solicitud de token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

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

            // Validación exitosa
            echo 'Inicio de sesión exitoso. Bienvenido, ' . htmlspecialchars($user_data['name']) . '!';
            echo '<br><a href="../../public/Libros.php">Buscar Libros</a>';
            echo '<br><a href="../../public/Logout.php">Cerrar sesión</a>';
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


