<?php
// Включаем вывод всех ошибок (по желанию, можно убрать в production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Подключение к БД
require_once '../includes/db-admin.php';
include '../includes/admin-auth.php';

$serverRequestMethod = $_SERVER['REQUEST_METHOD'] ?? 'CLI';

// === Обработка формы добавления товара ===
if ($serverRequestMethod === 'POST') {
    if (isset($_POST['add_product'])) {
        $category_id = $_POST['category_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $discount = $_POST['discount'] ?: null;
        $additional_info = $_POST['additional_info'];
        $additional_info2 = $_POST['additional_info2'];

        // Загрузка изображения
        $imagePath = null;

        if (!empty($_FILES['image']['name'])) {
            $uploadDir = '../assets/images/products/';
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imagePath = $fileName;
            }
        }

        // Добавление товара в БД
        try {
            $stmt = $pdo->prepare("INSERT INTO products 
                (category_id, name, description, price, discount, image, additional_info, additional_info2) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $category_id,
                $name,
                $description,
                $price,
                $discount,
                $imagePath,
                $additional_info,
                $additional_info2
            ]);

            $_SESSION['success_message'] = 'Товар успешно добавлен!';
        } catch (PDOException $e) {
            $_SESSION['success_message'] = 'Ошибка при добавлении товара: ' . $e->getMessage();
        }

        header("Location: products.php");
        exit;
    }

    // === Обработка сохранения изменений ===
    if (isset($_POST['update_product'])) {
        $id = $_POST['id'];
        $category_id = $_POST['category_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $discount = $_POST['discount'] ?: null;
        $additional_info = $_POST['additional_info'];
        $additional_info2 = $_POST['additional_info2'];

        $imagePath = $_POST['current_image'];

        if (!empty($_FILES['image']['name'])) {
            $uploadDir = '../assets/images/';
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                if ($imagePath && file_exists($uploadDir . $imagePath)) {
                    unlink($uploadDir . $imagePath); // Удаляем старое изображение
                }
                $imagePath = $fileName;
            }
        }

        $stmt = $pdo->prepare("UPDATE products SET 
            category_id = ?, 
            name = ?, 
            description = ?, 
            price = ?, 
            discount = ?, 
            image = ?, 
            additional_info = ?, 
            additional_info2 = ?
            WHERE id = ?");

        $stmt->execute([$category_id, $name, $description, $price, $discount, $imagePath, $additional_info, $additional_info2, $id]);

        $_SESSION['success_message'] = 'Товар успешно обновлён!';
        header("Location: products.php");
        exit;
    }
}

// === Обработка удаления товара ===
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Получаем информацию о товаре, чтобы удалить изображение
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product && !empty($product['image'])) {
        $imagePath = '../assets/images/products/' . $product['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // Удаляем файл изображения
        }
    }

    // Удаляем сам товар
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);

    $_SESSION['success_message'] = 'Товар и его изображение удалены.';
    header("Location: products.php");
    exit;
}

// === Получение товаров ===
$products = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id")->fetchAll(PDO::FETCH_ASSOC);

// === Получение категорий для формы ===
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../assets/css/admin.css">
<?php include '../templates/header.php'; ?>

<div class="admin-container">
    <a href="../admin/index.php" class="back-button">← Назад</a>
    <h2>Товары</h2>

    <!-- Сообщение об успехе -->
    <?php if (!empty($_SESSION['success_message'])): ?>
        <div style="color: green; margin-bottom: 20px;">
            <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <!-- Форма добавления -->
    <h3>Добавить товар</h3>
    <form method="post" enctype="multipart/form-data">
        <label>Категория:
            <select name="category_id" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Название: <input type="text" name="name" required></label><br><br>
        <label>Описание: <textarea name="description"></textarea></label><br><br>
        <label>Цена: <input type="number" step="0.01" name="price" required></label><br><br>
        <label>Скидка: <input type="number" step="0.01" name="discount"></label><br><br>
        <label>Изображение: <input type="file" name="image"></label><br><br>
        <label>Доп. информация: <textarea name="additional_info"></textarea></label><br><br>
        <label>Доп. информация 2: <textarea name="additional_info2"></textarea></label><br><br>

        <button type="submit" name="add_product">Добавить товар</button>
    </form>

    <hr>

    <!-- Список товаров -->
    <h3>Список товаров</h3>
    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Категория</th>
            <th>Название</th>
            <th>Цена</th>
            <th>Скидка</th>
            <th>Изображение</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['category_name']) ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= number_format($p['price'], 2) ?></td>
                <td><?= $p['discount'] ? number_format($p['discount'], 2) : '-' ?></td>
                <td><img src="../assets/images/products/<?= $p['image'] ?>" alt="" width="100"></td>
                <td>
                    <a href="?edit=<?= $p['id'] ?>">Редактировать</a> |
                    <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Удалить?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Редактирование товара -->
    <?php if (isset($_GET['edit'])):
        $id = $_GET['edit'];
        $product = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $product->execute([$id]);
        $p = $product->fetch(PDO::FETCH_ASSOC);
    ?>
        <hr>
        <h3>Редактировать товар</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <input type="hidden" name="current_image" value="<?= $p['image'] ?>">

            <label>Категория:
                <select name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $p['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label><br><br>

            <label>Название: <input type="text" name="name" value="<?= htmlspecialchars($p['name']) ?>" required></label><br><br>
            <label>Описание: <textarea name="description"><?= htmlspecialchars($p['description']) ?></textarea></label><br><br>
            <label>Цена: <input type="number" step="0.01" name="price" value="<?= $p['price'] ?>" required></label><br><br>
            <label>Скидка: <input type="number" step="0.01" name="discount" value="<?= $p['discount'] ?>"></label><br><br>
            <label>Текущее изображение:</label><br>
            <img src="../assets/images/products/<?= $p['image'] ?>" width="100"><br>
            <label>Заменить изображение: <input type="file" name="image"></label><br><br>
            <label>Доп. информация: <textarea name="additional_info"><?= htmlspecialchars($p['additional_info']) ?></textarea></label><br><br>
            <label>Доп. информация 2: <textarea name="additional_info2"><?= htmlspecialchars($p['additional_info2']) ?></textarea></label><br><br>

            <button type="submit" name="update_product">Сохранить изменения</button>
        </form>
    <?php endif; ?>
</div>

<?php include '../templates/footer.php'; ?>