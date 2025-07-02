<?php
// Настройки базы данных
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'authorised_27');

// Настройки VK API
define('VK_CLIENT_ID', '53825761');
define('VK_CLIENT_SECRET', 'UDEEmmIw8iloSxOuZY72');
define('VK_REDIRECT_URI', 'https://example.local/vk/callback');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
