<?php
include 'includes/db.php';

// Получаем выбранную категорию из GET-параметра
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

// Запрос для получения всех категорий (для меню)
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Запрос для получения товаров
if ($category_id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
} else {
    $stmt = $pdo->query("SELECT * FROM products");
}
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог товаров</title>
    <!-- Catalog styles -->
    <link rel="stylesheet" href="assets/css/catalog.css">
</head>
<body>
<?php include 'templates/header.php'; ?>

<!-- 🔥 Новый заголовок каталога -->
<div class="catalog-title">
    <h1>Каталог товаров</h1>
</div>

<aside class="catalog-nav">
    <!-- Текст категорий -->
    <ul class="category-list">
        <?php foreach ($categories as $cat): ?>
            <li class="<?= ($category_id === $cat['id'] ? 'active' : '') ?>">
                <span class="category-name"><?= htmlspecialchars($cat['name']) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
    <!-- Контейнер с точками справа -->
    <div class="dot-container">
        <?php foreach ($categories as $cat): ?>
            <a href="catalog.php?category_id=<?= $cat['id'] ?>" class="<?= ($category_id === $cat['id'] ? 'active' : '') ?>">
                <span class="category-dot"></span>
            </a>
        <?php endforeach; ?>
    </div>
</aside>

<main class="catalog-main">
    <div class="catalog-container">
        <?php if (!empty($products)): ?>
            <?php foreach ($categories as $cat):
                // Фильтруем товары по категории из уже загруженного массива
                $category_products = array_filter($products, fn($p) => $p['category_id'] === $cat['id']);
            ?>
                <!-- Название категории -->
                <section class="category-block">
                    <h2 class="category-title"><?= htmlspecialchars($cat['name']) ?></h2>
                    <!-- Товары -->
                    <div class="products-grid">
                        <?php if (!empty($category_products)): ?>
                            <?php foreach ($category_products as $product): ?>
                                <div class="product-card"
                                    data-product-id="<?= $product['id'] ?>"
                                    data-description="<?= htmlspecialchars($product['description']) ?>"
                                    data-additional-info="<?= htmlspecialchars($product['additional_info']) ?>"
                                    data-additional-info2="<?= htmlspecialchars($product['additional_info2']) ?>"
                                    data-price="<?= number_format($product['price'], 2) ?> ₽"
                                    data-image="../assets/images/products/<?= htmlspecialchars(basename($product['image'])) ?>">
                                    <!-- Путь к изображению исправлен -->
                                    <img src="../assets/images/products/<?= htmlspecialchars(basename($product['image'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>">

                                    <div class="product-info">
                                        <h4><?= htmlspecialchars($product['name']) ?></h4>
                                        <!-- additional_info -->
                                        <p><?= $product['additional_info'] ? nl2br(htmlspecialchars($product['additional_info'])) : '&nbsp;' ?></p>
                                        <a href="#" class="btn product-details">Подробнее</a>
                                        <!-- additional_info2 -->
                                        <p><?= $product['additional_info2'] ? nl2br(htmlspecialchars($product['additional_info2'])) : '&nbsp;' ?></p>
                                    </div>

                                    <!-- Цена теперь ВНЕ .product-info -->
                                    <div class="price-and-button">
                                        <p class="price"><?= number_format($product['price'], 2) ?> ₽</p>
                                        <button class="btn add-to-cart">В корзину</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Товаров пока нет.</p>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Товаров пока нет</p>
        <?php endif; ?>
    </div>
</main>

<!-- Модальное окно -->
<div class="modal-overlay" id="productModal">
    <div class="modal-content" id="modalContent">
        <button class="modal-close" id="modalClose">&times;</button>
        <div class="modal-body">
            <img src="" alt="Изображение товара" id="modalImage" class="modal-image">
            <h2 id="modalName"></h2>
            <p id="modalDescription"></p>
            <p id="modalInfo1"></p>
            <p id="modalInfo2"></p>
            <p class="price" id="modalPrice"></p>
            <button class="btn add-to-cart">В корзину</button>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
<script src="assets/js/script.js"></script>
</body>
</html>