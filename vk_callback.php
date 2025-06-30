<?php
session_start();

// Проверка CSRF токена
if ($_GET['state'] !== $_SESSION['csrf_token']) {
    die('CSRF ошибка');
}

// Обмен code на access_token
$token_url = 'https://oauth.vk.com/access_token?' . http_build_query([
    'client_id' => VK_CLIENT_ID,
    'client_secret' => VK_CLIENT_SECRET,
    'redirect_uri' => VK_REDIRECT_URI,
    'code' => $_GET['code']
]);

$response = json_decode(file_get_contents($token_url), true);

// Получение информации о пользователе
$user_url = 'https://api.vk.com/method/users.get?' . http_build_query([
    'access_token' => $response['access_token'],
    'v' => '5.131',
    'fields' => 'email'
]);

$user_data = json_decode(file_get_contents($user_url), true);

// Обработка пользователя
handleVkUser($user_data['response'][0]);

function handleVkUser($vk_data) {
    global $conn;
    
    $email = $vk_data['email'];
    $username = $vk_data['first_name'] . ' ' . $vk_data['last_name'];
    
    // Проверка существования пользователя
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 0) {
        // Создание нового пользователя
        $sql = "INSERT INTO users (username, email, vk_id) VALUES 
                ('$username', '$email', {$vk_data['id']})";
        $conn->query($sql);
        
        // Получение ID новой записи
        $user_id = $conn->insert_id;
        
        // Назначение роли VK пользователя
        $sql = "INSERT INTO user_roles (user_id, role_id) 
                SELECT $user_id, id 
                FROM roles 
                WHERE name = 'vk_user'";
        $conn->query($sql);
    } else {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
    }
    
    // Авторизация пользователя
    $_SESSION['user_id'] = $user_id;
    $_SESSION['auth_type'] = 'vk';
    
    header('Location: dashboard.php');
    exit();
}
