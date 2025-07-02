<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем и очищаем данные
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Хеширование пароля
    $salt = bin2hex(random_bytes(16));
    $password_hash = hash('sha256', $password . $salt);
    
    // Вставляем нового пользователя
    $sql = "INSERT INTO users (username, password_hash, salt) 
            VALUES ('$username', '$password_hash', '$salt')";
    
    if ($conn->query($sql) === TRUE) {
        // Получаем ID нового пользователя
        $user_id = $conn->insert_id;
        
        // Получаем ID роли 'user'
        $role_sql = "SELECT id FROM roles WHERE name = 'user'";
        $role_result = $conn->query($role_sql);
        
        if ($role_result && $role_result->num_rows > 0) {
            $role = $role_result->fetch_assoc();
            $role_id = $role['id'];
            
            // Вставляем запись в user_roles
            $roles_sql = "INSERT INTO user_roles (user_id, role_id, created_at) 
                         VALUES ($user_id, $role_id, NOW())";
            
            $conn->query($roles_sql);
        }
        
        // Перенаправляем на страницу входа
        header('Location: login.php');
        exit();
    } else {
        die("Ошибка регистрации: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Регистрация</h2>
        <form method="POST">
            <div class="form-group">
                <label for="username">Логин</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
            <a href="dashboard.php" class="btn btn-link">На главную</a>
            <!-- Добавить кнопку VK -->
<button onclick="location.href='/auth_system/vk/auth.php'">
    Зарегистрироваться через VK
</button>
        </form>
    </div>
</body>
</html>
