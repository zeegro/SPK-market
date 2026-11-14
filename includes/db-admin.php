<?php
$host = 'localhost';
$dbname = 'spk_market';
$user = 'admin_user'; // Пользователь только для админки
$password = 'z3-g40'; // Пароль, который указал выше

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}
?>