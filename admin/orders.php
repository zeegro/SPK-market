<?php
// Подключение к базе данных и аутентификация
include '../includes/admin-auth.php';
include '../includes/db-admin.php';

// Обработка AJAX-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из POST-запроса
    $action = $_POST['action'] ?? null;

    if ($action === 'update_status') {
        $orderId = $_POST['orderId'];
        $status = $_POST['status'];

        // Обновляем статус заказа
        $query = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([$status, $orderId]);

        echo json_encode(['success' => $result]);
        exit;
    }

    if ($action === 'delete_order') {
        $orderId = $_POST['orderId'];

        // Удаляем заказ
        $query = "DELETE FROM orders WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([$orderId]);

        echo json_encode(['success' => $result]);
        exit;
    }
}

// Получение фильтра по статусу из GET-параметра
$statusFilter = isset($_GET['status']) ? $_GET['status'] : null;

// SQL-запрос для получения заказов
if ($statusFilter && $statusFilter !== 'все') {
    // Если выбран конкретный статус
    $query = "SELECT * FROM orders WHERE status = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$statusFilter]);
} else {
    // Если фильтр не выбран или выбран "Все статусы"
    $query = "SELECT * FROM orders";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
}
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Функция для получения названий и цен товаров по их ID
function getProductDetailsByIds($pdo, $productIds) {
    if (empty($productIds)) {
        return [];
    }

    // Создаем строку для IN-запроса
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $query = "SELECT id, name, price FROM products WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($query);
    $stmt->execute($productIds);

    // Сопоставляем ID с данными (названиями и ценами)
    $products = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $products[$row['id']] = [
            'name' => $row['name'],
            'price' => $row['price']
        ];
    }
    return $products;
}

// Формирование опций для фильтрации по статусу
$statusOptions = [
    'все' => 'Все статусы',
    'новый' => 'Новый',
    'в обработке' => 'В обработке',
    'выполнен' => 'Выполнен',
    'отменён' => 'Отменён'
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказы пользователей</title>
    <!-- Подключение CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include '../templates/header.php'; ?>

    <div class="container">
        <h1 style="color: #9D0A0F;">ЗАКАЗЫ ПОЛЬЗОВАТЕЛЕЙ</h1>
        <a href="../admin/index.php" class="back-button">← Назад</a>

        <!-- Фильтр по статусу -->
        <div class="filter-container">
            <form action="orders.php" method="get" class="filter-form">
                <label for="statusFilter">Фильтр по статусу:</label>
                <select name="status" id="statusFilter" onchange="this.form.submit()">
                    <?php foreach ($statusOptions as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo ($statusFilter === $value) ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <!-- Таблица заказов -->
        <div class="table-container">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Телефон</th>
                        <th>Email</th>
                        <th>Адрес</th>
                        <th>Товары</th>
                        <th>Сумма</th>
                        <th>Дата</th>
                        <th>Статус и действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="<?php echo ($order['status'] === 'новый') ? 'unread' : ''; ?>">
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['phone']); ?></td>
                                <td><?php echo htmlspecialchars($order['email']); ?></td>
                                <td><?php echo htmlspecialchars($order['address']); ?></td>
                                <td>
                                    <?php
                                    // Получаем массив ID товаров
                                    $productIds = json_decode($order['product_ids'], true);
                                    if (!is_array($productIds)) {
                                        $productIds = [];
                                    }

                                    // Подсчитываем количество каждого товара
                                    $productCounts = array_count_values($productIds);

                                    // Получаем названия и цены товаров
                                    $products = getProductDetailsByIds($pdo, array_keys($productCounts));

                                    // Выводим названия товаров с указанием количества и цены
                                    foreach ($productCounts as $productId => $count) {
                                        $productName = htmlspecialchars($products[$productId]['name'] ?? 'Неизвестный товар');
                                        $productPrice = htmlspecialchars(number_format($products[$productId]['price'] ?? 0, 2, '.', '')) . ' ₽';
                                        if ($count > 1) {
                                            echo $productName . " (х" . htmlspecialchars($count) . ") - " . $productPrice . "<br>";
                                        } else {
                                            echo $productName . " - " . $productPrice . "<br>";
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?php echo number_format($order['total_price'], 2, '.', '') . ' ₽'; ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <div class="status-actions">
                                        <?php foreach ($statusOptions as $value => $label): ?>
                                            <?php if ($value !== 'все'): ?>
                                                <button class="status-button <?php echo ($order['status'] === $value) ? 'active' : ''; ?>"
                                                        onclick="changeStatus(<?php echo $order['id']; ?>, '<?php echo $value; ?>')">
                                                    <?php echo $label; ?>
                                                </button>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <button class="delete-button"
                                                onclick="confirmDelete(<?php echo $order['id']; ?>)">
                                            Удалить
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">Заказов не найдено.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript для обработки изменений статуса и удаления -->
    <script>
        function changeStatus(orderId, status) {
            if (confirm('Вы действительно хотите изменить статус заказа на "' + status + '"?')) {
                // AJAX запрос для изменения статуса
                fetch('orders.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=update_status&orderId=' + orderId + '&status=' + encodeURIComponent(status)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Статус заказа успешно изменён.');
                        location.reload(); // Обновляем страницу
                    } else {
                        alert('Ошибка при изменении статуса.');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        function confirmDelete(orderId) {
            if (confirm('Вы действительно хотите удалить этот заказ?')) {
                // AJAX запрос для удаления заказа
                fetch('orders.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=delete_order&orderId=' + orderId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Заказ успешно удалён.');
                        location.reload(); // Обновляем страницу
                    } else {
                        alert('Ошибка при удалении заказа.');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        // Функция для получения названий и цен товаров по их ID
        function getProductDetailsByIds($pdo, $productIds) {
            if (empty($productIds)) {
                return [];
            }

            // Создаем строку для IN-запроса
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $query = "SELECT id, name, price FROM products WHERE id IN ($placeholders)";
            $stmt = $pdo->prepare($query);
            $stmt->execute($productIds);

            // Сопоставляем ID с данными (названиями и ценами)
            $products = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $products[$row['id']] = [
                    'name' => $row['name'],
                    'price' => $row['price']
                ];
            }
            return $products;
    </script>

    <?php include '../templates/footer.php'; ?>
</body>
</html>