<?php
// Inicia una sesión de PHP
session_start();

// Información del cliente de Google (ID de cliente y secreto)
$client_id = '';
$client_secret = '';
$redirect_uri = 'http://localhost/Parcial4_Desarrollo/Biblioteca-Personal/src/controllers/Login_OAuth.php'; // Asegúrate de que coincide con el URI de redireccionamiento configurado en la consola de Google

// URL base de autenticación de Google
$auth_url = 'https://accounts.google.com/o/oauth2/auth';

// Parámetros para la solicitud de autenticación
$params = [
    'response_type' => 'code',
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
    'access_type' => 'offline',
    'include_granted_scopes' => 'true',
    'state' => 'security_token=' . md5(uniqid(rand(), true))
];

// Redirige al usuario a la página de autenticación de Google
header('Location: ' . $auth_url . '?' . http_build_query($params));
exit();
?>
