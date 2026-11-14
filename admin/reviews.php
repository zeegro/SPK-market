<?php
include '../includes/db-admin.php';
include '../includes/admin-auth.php';

// Удаление сообщения
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Получаем имя файла
    $stmt = $pdo->prepare("SELECT file_path FROM messages WHERE id = ?");
    $stmt->execute([$id]);
    $message = $stmt->fetch();

    if ($message && $message['file_path']) {
        $filePath = '../uploads/messages/' . $message['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath); // Удаляем файл
        }
    }

    // Удаляем сообщение из БД
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: reviews.php");
    exit();
}

// Отметка как прочитанное
if (isset($_GET['mark_read'])) {
    $id = intval($_GET['mark_read']);
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: reviews.php");
    exit();
}

// Фильтры
$sort = $_GET['sort'] ?? 'desc';
$readFilter = $_GET['read'] ?? '';

$whereClauses = [];
if ($readFilter === 'read') {
    $whereClauses[] = "is_read = 1";
} elseif ($readFilter === 'unread') {
    $whereClauses[] = "is_read = 0 OR is_read IS NULL";
}

$query = "SELECT * FROM messages";
if (!empty($whereClauses)) {
    $query .= " WHERE " . implode(" AND ", $whereClauses);
}
$query .= " ORDER BY created_at " . ($sort === 'asc' ? 'ASC' : 'DESC');

$stmt = $pdo->query($query);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Сообщения пользователей</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<?php include '../templates/header.php'; ?>
<main class="admin-main">
    <div class="admin-container">
        <a href="../admin/index.php" class="back-button">← Назад</a>
        <h1>Сообщения пользователей</h1>

        <!-- Фильтры -->
        <div class="filters">
            <form method="get" class="filter-form">
                <label for="sort">Сортировать по дате:</label>
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>Новые → Старые</option>
                    <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>Старые → Новые</option>
                </select>

                <label for="read">Фильтр:</label>
                <select name="read" id="read" onchange="this.form.submit()">
                    <option value="">Все</option>
                    <option value="read" <?= $readFilter === 'read' ? 'selected' : '' ?>>Прочитанные</option>
                    <option value="unread" <?= $readFilter === 'unread' ? 'selected' : '' ?>>Непрочитанные</option>
                </select>
            </form>
        </div>

        <!-- Таблица -->
        <table class="messages-table">
            <thead>
                <tr>
                    <th>Имя</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Сообщение</th>
                    <th>Файл</th>
                    <th>Дата</th>
                    <th>Прочитано</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($messages): ?>
                    <?php foreach ($messages as $msg): ?>
                        <tr class="<?= empty($msg['is_read']) ? 'unread' : '' ?>">
                            <td><?= htmlspecialchars($msg['name']) ?></td>
                            <td><?= htmlspecialchars($msg['phone']) ?></td>
                            <td><?= htmlspecialchars($msg['email']) ?: '-' ?></td>
                            <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                            <td>
                                <?php if ($msg['file_path']): ?>
                                    <a href="../uploads/messages/<?= htmlspecialchars($msg['file_path']) ?>" download="<?= htmlspecialchars($msg['file_path']) ?>">Скачать</a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td><?= date('d.m.Y H:i', strtotime($msg['created_at'])) ?></td>
                            <td><?= $msg['is_read'] ? 'Да' : 'Нет' ?></td>
                            <td>
                                <a href="?mark_read=<?= $msg['id'] ?>" class="btn mark-read-btn">Прочитано</a>
                                <a href="?delete=<?= $msg['id'] ?>" class="btn delete-btn" onclick="return confirm('Вы уверены?')">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">Сообщений пока нет.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../templates/footer.php'; ?>
</body>
</html>