<?php include 'templates/header.php'; ?>
<link rel="stylesheet" href="assets/css/order.css">

<main class="order-main">
    <div class="order-container">
        <h1>Оформление заказа</h1>

        <div class="order-summary">
            <h2>Вы выбрали:</h2>
            <ul id="order-items"></ul>
            <p><strong>Итого:</strong> <span id="total-price">0.00</span> ₽</p>
        </div>

        <form id="orderForm" action="../includes/submit-order.php" method="post">
            <input type="hidden" name="cart_data" id="cartDataInput">

            <!-- Остальные поля -->
            <div class="form-group">
                <label for="customer_name">Имя *</label>
                <input type="text" id="customer_name" name="customer_name" required>
            </div>

            <div class="form-group">
                <label for="phone">Номер телефона *</label>
                <input type="tel" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="email">Электронная почта</label>
                <input type="email" id="email" name="email">
            </div>

            <div class="form-group">
                <label for="address">Адрес доставки *</label>
                <textarea id="address" name="address" required></textarea>
            </div>

            <button type="submit">Оформить заказ</button>
        </form>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const cartDataInput = document.getElementById('cartDataInput');
        if (cart.length === 0) {
            document.querySelector('.order-summary').innerHTML = '<p>Корзина пуста.</p>';
            return;
        }

        // Заполняем скрытое поле
        if (cartDataInput) {
            cartDataInput.value = JSON.stringify(cart);
        }

        // Показываем товары
        const orderItems = document.getElementById('order-items');
        const totalPriceEl = document.getElementById('total-price');
        let totalPrice = 0;

        cart.forEach(item => {
            const li = document.createElement('li');
            li.textContent = `${item.name} — ${item.price} ₽`;
            orderItems.appendChild(li);
            totalPrice += parseFloat(item.price);
        });

        totalPriceEl.textContent = totalPrice.toFixed(2);
    });
</script>

<?php include 'templates/footer.php'; ?>
<script src="https://unpkg.com/imask "></script>
<script src="assets/js/form-validation.js"></script>