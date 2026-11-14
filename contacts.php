<?php
include 'includes/db.php'; // Подключаем с правами на запись

$uploadDir = '../uploads/messages/';

// Автоматически создаём папку, если её нет
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); // Создаёт рекурсивно /uploads/messages/
}

$fileName = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    if (!empty($_FILES['file']['name'])) {
        $tmpName = $_FILES['file']['tmp_name'];
        $originalName = basename($_FILES['file']['name']);
        $fileSize = $_FILES['file']['size'];

        if ($fileSize > 5 * 1024 * 1024) {
            echo "<p style='color:red;'>Ошибка: файл больше 5 МБ.</p>";
        } else {
            $fileExt = pathinfo($originalName, PATHINFO_EXTENSION);
            $fileName = uniqid('msg_', true) . '.' . strtolower($fileExt);
            $targetPath = $uploadDir . $fileName;

            if (!move_uploaded_file($tmpName, $targetPath)) {
                $error = error_get_last();
                echo "<p style='color:red;'>Ошибка: " . ($error['message'] ?? 'Неизвестная ошибка') . "</p>";
                $fileName = null;
            }
        }
    }

    if (!empty($name) && !empty($phone) && !empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name, phone, email, message, file_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $phone, $email, $message, $fileName]);

            header("Location: contacts.php?success=1");
            exit();
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Ошибка при отправке сообщения.</p>";
        }
    } else {
        echo "<p style='color:red;'>Пожалуйста, заполните все обязательные поля.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Контакты</title>
    <link rel="stylesheet" href="../assets/css/contacts.css">
</head>
<body>

<?php include 'templates/header.php'; ?>
<link rel="stylesheet" href="../assets/css/base.css">

<main class="contacts-main">
    <div class="contacts-container">
        <h1>Контакты</h1>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="form-success">Сообщение успешно отправлено!</div>
        <?php endif; ?>

        <div class="contacts-grid">
            <!-- Контактная информация -->
            <div class="contacts-info">
                <h2>Контактная информация</h2>
                <p><strong>Телефоны:</strong></p>
                <ul>
                    <li><strong>8 (800) 100-85-15</strong> (добавочный номер 1, звонок на территории РФ бесплатный)</li>
                    <li><strong>8 (913) 730-55-83</strong></li>
                </ul>
                <p><strong>График работы колл-центра:</strong> пн-пт с 9:00 до 19:00, сб-вс с 10:00 до 17:00.</p>
                <p><strong>E-mail:</strong> <a href="mailto:support@sibfood.ru">support@sibfood.ru</a></p>
                <p><strong>Telegram:</strong> <a href="https://t.me/spkmarket ">@spkmarket</a></p>
                <p><strong>Адрес:</strong> Новосибирск, ул. Дуси Ковальчук, 1в</p>
                <p><strong>Общество с ограниченной ответственностью</strong> "Сибирская Продовольственная Компания"</p>
                <p><strong>ИНН</strong> 5405200166 / <strong>КПП</strong> 540101001 / <strong>ОГРН</strong> 1025401012768 / © 1994–2025</p>
            </div>

            <!-- Карта -->
            <div class="map-container">
                <h2>Как нас найти</h2>
                <div class="map">
                    <script type="text/javascript" charset="utf-8" async 
                        src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A936a151db619e348134ba865741c66d8f7f865760e00e9ffec5906e968afb18d&amp ;width=600&amp;height=450&amp;lang=ru_RU&amp;scroll=true">
                    </script>
                </div>
            </div>
        </div>

        <!-- Форма обратной связи -->
        <div class="contact-form-section">
            <h2>Оставить сообщение</h2>
            <form id="contactForm" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Имя *</label>
                    <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Номер телефона *</label>
                    <input type="tel" id="phone" name="phone" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="email">Электронная почта</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="message">Текст сообщения *</label>
                    <textarea id="message" name="message" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="file">Файл</label>
                    <input type="file" id="file" name="file">
                </div>
                <button type="submit">Отправить сообщение</button>
                <p class="form-policy">Нажимая кнопку, вы даёте согласие на обработку персональных данных</p>
            </form>
        </div>
    </div>
</main>

<?php include 'templates/footer.php'; ?>
<script src="https://unpkg.com/imask "></script>
<script src="../assets/js/form-validation.js"></script>
</body>
</html>