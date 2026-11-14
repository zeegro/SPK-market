<?php 
include '../templates/header.php';
include '../includes/admin-auth.php';
?>

<link rel="stylesheet" href="../assets/css/admin.css">

<div class="admin-container">
    <h1>Админ-панель — СПК-Маркет</h1>

    <div class="admin-menu">
        <a href="../admin/products.php" class="menu-card">
            <span>Управление товарами</span>
        </a>
        <a href="../admin/categories.php" class="menu-card">
            <span>Управление категориями</span>
        </a>
        <a href="../admin/orders.php" class="menu-card">
            <span>Заказы</span>
        </a>
        <a href="../admin/reviews.php" class="menu-card">
            <span>Сообщения с формы</span>
        </a>
        <a href="../includes/admin-logout.php" class="logout-link">Выйти из аккаунта</a>
    </div>
</div>

<?php include '../templates/footer.php'; ?>