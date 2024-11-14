<?php
// Inicia una sesión de PHP
session_start();

// Información del cliente de Google (ID de cliente y secreto)
$client_id = '';
$client_secret = '';
$redirect_uri = 'http://localhost/Parcial4_Desarrollo/Biblioteca-Personal/src/controllers/Login_OAuth.php'; // Redireccionamiento configurado en Google

// URL base de autenticación de Google
$auth_url = 'https://accounts.google.com/o/oauth2/auth';

// Parámetros para la solicitud de autenticación
$params = [
    'response_type' => 'code', 
    'client_id' => $client_id, // ID del cliente
    'redirect_uri' => $redirect_uri, // URL de redireccionamiento
    'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile', // Permisos solicitados
    'access_type' => 'offline', // Permite acceso offline
    'include_granted_scopes' => 'true', // Incluye permisos concedidos anteriormente
    'state' => 'security_token=' . md5(uniqid(rand(), true)) // Token de seguridad para proteger la solicitud
];

// Dirige al usuario a la página de autenticación de Google
header('Location: ' . $auth_url . '?' . http_build_query($params));
exit();
?>
