<?php
// Включаем вывод ошибок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once '../includes/db-admin.php';
include '../includes/admin-auth.php';

$serverRequestMethod = $_SERVER['REQUEST_METHOD'] ?? 'CLI';

// === Обработка формы добавления категории ===
if ($serverRequestMethod === 'POST') {
    if (isset($_POST['add_category'])) {
        $name = trim($_POST['name']);

        if (!empty($name)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
                $stmt->execute([$name]);
                $_SESSION['success_message'] = 'Категория успешно добавлена!';
            } catch (PDOException $e) {
                $_SESSION['success_message'] = 'Ошибка при добавлении: ' . $e->getMessage();
            }
        } else {
            $_SESSION['success_message'] = 'Название категории не может быть пустым.';
        }

        header("Location: categories.php");
        exit;
    }

    // === Обработка сохранения изменений ===
    if (isset($_POST['update_category'])) {
        $id = $_POST['id'];
        $name = trim($_POST['name']);

        if (!empty($name)) {
            $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $stmt->execute([$name, $id]);
            $_SESSION['success_message'] = 'Категория обновлена.';
        } else {
            $_SESSION['success_message'] = 'Название не может быть пустым.';
        }

        header("Location: categories.php");
        exit;
    }
}

// === Обработка удаления категории ===
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
    $_SESSION['success_message'] = 'Категория удалена.';
    header("Location: categories.php");
    exit;
}

// === Получение всех категорий ===
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../assets/css/admin.css">
<?php include '../templates/header.php'; ?>

<div class="admin-container">
    <a href="../admin/index.php" class="back-button">← Назад</a>
    <h2>Управление категориями</h2>

    <!-- Сообщение об успехе -->
    <?php if (!empty($_SESSION['success_message'])): ?>
        <div style="color: green; margin-bottom: 20px;">
            <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <!-- Форма добавления категории -->
    <h3>Добавить категорию</h3>
    <form method="post">
        <label>Название:
            <input type="text" name="name" required>
        </label><br><br>
        <button type="submit" name="add_category">Добавить</button>
    </form>

    <hr>

    <!-- Список категорий -->
    <h3>Список категорий</h3>
    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= $cat['id'] ?></td>
                <td><?= htmlspecialchars($cat['name']) ?></td>
                <td>
                    <a href="?edit=<?= $cat['id'] ?>">Редактировать</a> |
                    <a href="?delete=<?= $cat['id'] ?>" onclick="return confirm('Удалить?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Редактирование категории -->
    <?php if (isset($_GET['edit'])):
        $id = $_GET['edit'];
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $cat = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
        <hr>
        <h3>Редактировать категорию</h3>
        <form method="post">
            <input type="hidden" name="id" value="<?= $cat['id'] ?>">
            <label>Название:
                <input type="text" name="name" value="<?= htmlspecialchars($cat['name']) ?>" required>
            </label><br><br>
            <button type="submit" name="update_category">Сохранить изменения</button>
        </form>
    <?php endif; ?>
</div>

<?php include '../templates/footer.php'; ?>