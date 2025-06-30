<?php
session_start();
require_once 'config.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Получаем информацию о пользователе
$user_id = $_SESSION['user_id'];
$sql = "SELECT u.*, GROUP_CONCAT(r.name SEPARATOR ', ') as roles 
        FROM users u
        LEFT JOIN user_roles ur ON u.id = ur.user_id
        LEFT JOIN roles r ON ur.role_id = r.id
        WHERE u.id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Безопасная проверка роли VK
$isVkUser = false;
if (!empty($user['roles'])) {
    $isVkUser = strpos($user['roles'], 'vk_user') !== false;
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель управления</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Добро пожаловать, <?= htmlspecialchars($user['username']) ?>!</h2>
        
        <div class="alert alert-info">
            <strong>Ваша роль:</strong> <?= htmlspecialchars($user['roles']) ?>
        </div>

                        <!-- Защищенная информация -->
            <div class="mt-4">
            <p>Это текст, доступный всем авторизованным пользователям.</p>
            
                <?php if ($isVkUser): ?>
                <img src="path_to_your_image.jpg" alt="Изображение для VK пользователей" class="img-fluid">
                <?php endif; ?>
            </div>
        </div>

        <nav class="navbar navbar-light bg-light">
            <a class="navbar-brand" href="#">Панель управления</a>
            <div class="navbar-nav">
                <a class="nav-item nav-link" href="logout.php">Выйти</a>
            </div>
        </nav>

        <div class="mt-4">
            <h3>Доступные действия:</h3>
            <ul class="list-group">
                <li class="list-group-item">Просмотр профиля</li>
                <li class="list-group-item">Управление настройками</li>
                <!-- Здесь можно добавить дополнительные функции -->
            </ul>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
