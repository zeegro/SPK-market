<?php
session_start();

// Если уже залогинен — отправляем в админку
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: admin/index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    // Простая проверка логина/пароля (можно потом перенести в БД)
    if ($login === 'admin' && $password === '12345') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $login;

        header("Location: admin/index.php");
        exit();
    } else {
        $error = "Неверный логин или пароль.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в админку</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="login-body">

<div class="login-box">
    <h2>Вход в админку</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="login">Логин:</label>
            <input type="text" id="login" name="login" required>
        </div>
        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Войти</button>
    </form>
</div>

</body>
</html>