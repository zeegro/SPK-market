<?php include '../templates/header.php'; ?>
<link rel="stylesheet" href="../assets/css/order.css">
<main class="order-main">
    <div class="order-container">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <?php
            // Подключение к БД
            include '../includes/db.php';

            // Проверяем, есть ли данные
            $cartData = json_decode($_POST['cart_data'] ?? '[]', true);

            // Если нет данных или не массив
            if (!is_array($cartData) || empty($cartData)) {
                echo "<p style='color: red;'>Ошибка: в заказе нет товаров</p>";
                exit();
            }

            // Получаем данные пользователя
            $name = $_POST['customer_name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $email = $_POST['email'] ?? '';
            $address = $_POST['address'] ?? '';
            $productIds = [];
            $totalPrice = 0;

            // Обрабатываем товары
            foreach ($cartData as $item) {
                $productId = $item['id'];
                $productName = $item['name'];
                $productPrice = floatval($item['price']);

                $productIds[] = $productId;
                $totalPrice += $productPrice;
            }

            try {
                // Сохраняем заказ
                $stmt = $pdo->prepare("INSERT INTO orders (
                    customer_name, phone, email, address, product_ids, total_price
                ) VALUES (?, ?, ?, ?, ?, ?)");

                $stmt->execute([$name, $phone, $email, $address, json_encode($productIds), $totalPrice]);

                // Успех
                $orderId = $pdo->lastInsertId();

            } catch (PDOException $e) {
                echo "<p style='color: red;'>Ошибка при оформлении заказа</p>";
                exit();
            }
            ?>

            <h1>Заказ оформлен!</h1>
            <p style="font-size: 18px; color: green;">✅ Ваш заказ №<?= $orderId ?> успешно отправлен.</p>

            <div class="order-summary">
                <h2>Что вы заказали:</h2>
                <ul id="order-items">
                    <?php foreach ($cartData as $item): ?>
                        <li><?= htmlspecialchars($item['name']) ?> — <?= number_format($item['price'], 2) ?> ₽</li>
                    <?php endforeach; ?>
                </ul>
                <p><strong>Итого:</strong> <?= number_format($totalPrice, 2) ?> ₽</p>
            </div>

            <p>Вы можете отменить заказ в течение <span id="countdown">20</span> секунд.</p>

            <div class="order-actions">
                <button id="cancelOrder" class="btn cancel-btn">Отменить заказ</button>
                <button id="dontCancel" class="btn secondary-btn">Не отменять</button>
            </div>

        <?php else: ?>
            <p>Нет данных о заказе.</p>
        <?php endif; ?>
    </div>
</main>

<!-- Скрипт для таймера и отмены -->
<script>
    const countdownEl = document.getElementById('countdown');
    const cancelBtn = document.getElementById('cancelOrder');
    const dontCancelBtn = document.getElementById('dontCancel');

    let timeLeft = 20;

    function updateCountdown() {
        countdownEl.textContent = timeLeft;
        if (timeLeft <= 0) {
            clearInterval(timer);
            window.location.href = '../index.php';
        }
        timeLeft--;
    }

    const timer = setInterval(updateCountdown, 1000);

    // Отменить заказ
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            fetch('../cancel-order.php?order_id=<?= $orderId ?>')
                .then(() => {
                    alert("Заказ отменён.");
                    localStorage.removeItem('cart');
                    window.location.href = '../index.php';
                })
                .catch(() => {
                    alert("Не удалось отменить заказ.");
                });
        });
    }

    // Не отменять
    if (dontCancelBtn) {
        dontCancelBtn.addEventListener('click', () => {
            clearInterval(timer);
            window.location.href = '../index.php';
        });
    }
</script>

<?php include '../templates/footer.php'; ?>