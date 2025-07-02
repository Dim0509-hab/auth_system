<?php
session_start();
require_once '../config.php';

// Отладка: выводим все GET параметры
echo "<pre>";
print_r($_GET);
echo "</pre>";

// Проверяем наличие кода авторизации
if (!isset($_GET['code'])) {
    die('Ошибка авторизации: код отсутствует');
}

$code = $_GET['code'];

// Параметры для получения токена
$params = [
    'client_id' => '53825761',
    'client_secret' => 'UDEEmmIw8iloSxOuZY72',
    'redirect_uri' => 'https://example.local/vk/callback',
    'code' => $code
];

// Отладка: выводим параметры запроса
echo "Параметры запроса:<br>";
print_r($params);

// Получаем токен
$token_url = 'https://oauth.vk.com/access_token?' . http_build_query($params);
echo "URL для получения токена: " . $token_url . "<br>";

try {
    $response = file_get_contents($token_url);
    if ($response === false) {
        die('Ошибка при получении токена: ' . error_get_last()['message']);
    }
    
    $token_data = json_decode($response, true);
    
    // Отладка: выводим ответ сервера
    echo "Ответ сервера:<br>";
    print_r($token_data);
    
    // Проверяем успешность получения токена
    if (isset($token_data['error'])) {
        die('Ошибка получения токена: ' . $token_data['error_description']);
    }
    
    // Сохраняем токен
    $_SESSION['vk_token'] = $token_data['access_token'];
    
    // Получаем информацию о пользователе
    $user_url = 'https://api.vk.com/method/users.get?' . http_build_query([
        'access_token' => $token_data['access_token'],
        'v' => '5.131'
    ]);
    
    echo "URL для получения данных пользователя: " . $user_url . "<br>";
    
    $user_data = json_decode(file_get_contents($user_url), true);
    
    // Отладка: выводим данные пользователя
    echo "Данные пользователя:<br>";
    print_r($user_data);
    
    if (isset($user_data['response'][0])) {
        $user = $user_data['response'][0];
        echo "Авторизация прошла успешно! Ваш ID: " . $user['id'];
    } else {
        die('Ошибка получения данных пользователя');
    }
} catch (Exception $e) {
    die('Произошла ошибка: ' . $e->getMessage());
}
