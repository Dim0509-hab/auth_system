<?php
session_start();
require_once '../config.php'; // Подключаем настройки

// Отладка: проверка загруженных настроек
echo "<pre>";
echo "Загруженные настройки:\n";
print_r($_SESSION);
echo "\n";

// Генерируем CSRF-токен
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Отладка: проверка сгенерированного токена
echo "Сгенерированный CSRF-токен: " . $_SESSION['csrf_token'] . "\n";

// Параметры VK API
$client_id = '53825761';
$redirect_uri = 'https://example.local/vk/callback';

// Формируем параметры для авторизации
$params = [
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'response_type' => 'code',
    'scope' => 'email' // Упрощаем scope до одного разрешения
];

// Формируем полный URL
$auth_url = 'https://oauth.vk.com/authorize?' . http_build_query($params);

// Отладка: вывод сформированного URL
echo "Сформированный URL для авторизации:\n";
echo $auth_url . "\n";

// Проверка параметров
if (empty($client_id) || empty($redirect_uri)) {
    die('Ошибка: не все параметры API настроены');
}

// Перенаправляем
header('Location: ' . $auth_url);
exit();
?>
