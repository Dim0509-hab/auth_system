<?php
session_start();

// Генерация CSRF токена
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$auth_url = 'https://oauth.vk.com/authorize?' . http_build_query([
    'client_id' => VK_CLIENT_ID,
    'redirect_uri' => VK_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'email',
    'state' => $_SESSION['csrf_token']
]);

header('Location: ' . $auth_url);
