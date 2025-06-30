<?php
// Настройки базы данных
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'authorised_27');

// Настройки VK API
define('VK_CLIENT_ID', 'ВАШ_CLIENT_ID');
define('VK_CLIENT_SECRET', 'ВАШ_CLIENT_SECRET');
define('VK_REDIRECT_URI', 'http://ваш_сайт/vk_callback.php');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
