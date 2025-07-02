<?php
session_start();
require_once 'config.php';

// Генерируем CSRF-токен
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Инициализация переменных для сообщений
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Получаем IP-адрес пользователя
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Проверяем существование пользователя
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Проверяем пароль
        $password_hash = hash('sha256', $password . $user['salt']);
        
        if ($password_hash === $user['password_hash']) {
            // Успешная авторизация
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            
            // Обнуляем неудачные попытки
            $conn->query("UPDATE users SET failed_attempts = 0, last_failed_attempt = NULL WHERE id = {$user['id']}");
            
            header('Location: dashboard.php');
            exit();
        } else {
            // Неверный пароль
            $error = 'Неверный пароль';
            
            // Увеличиваем счетчик неудачных попыток
            $failed_attempts = $user['failed_attempts'] + 1;
            $conn->query("UPDATE users SET failed_attempts = $failed_attempts, last_failed_attempt = NOW() WHERE id = {$user['id']}");
            
            // Записываем неудачную попытку в БД
            $sql_log = "INSERT INTO auth_logs (username, ip_address, attempt_time, status, message) 
                        VALUES ('$username', '$ip_address', NOW(), 'failed', 'Неверный пароль')";
            $conn->query($sql_log);
            
            // Записываем неудачную попытку в файл log.txt
            $log_message = "
            [".date('Y-m-d H:i:s')."] 
            Username: $username
            IP: $ip_address
            Status: failed
            Message: Неверный пароль
            -----------------------------------
            ";
            
            file_put_contents('log.txt', $log_message, FILE_APPEND);
        }
        } else {
            // Пользователь не найден
            $error = 'Пользователь не найден';
        
            // Записываем неудачную попытку в БД
            $sql_log = "INSERT INTO auth_logs (username, ip_address, attempt_time, status, message) 
                    VALUES ('$username', '$ip_address', NOW(), 'failed', 'Пользователь не найден')";
            $conn->query($sql_log);
        
             // Записываем неудачную попытку в файл log.txt
              $log_message = "
              [".date('Y-m-d H:i:s')."] 
              Username: $username
              IP: $ip_address
              Status: failed
               Message: Пользователь не найден
             -----------------------------------
             ";
        
            file_put_contents('log.txt', $log_message, FILE_APPEND);
        }
}


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в систему</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Вход в систему</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Логин</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Войти</button>
            
            <a href="register.php" class="btn btn-link">Зарегистрироваться</a>
             <!-- Кнопка авторизации через VK -->
              <a href="/vk/auth.php">
            <img src="vk-button.png" alt="Войти через VK">
            </a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
