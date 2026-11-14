// Маска для телефона
const phoneInput = document.getElementById('phone');
if (phoneInput) {
    const phoneMask = new IMask(phoneInput, {
        mask: '+{7}(000)000-00-00'
    });
}

// Валидация email при отправке
const form = document.getElementById('contactForm');
const emailInput = document.getElementById('email');

if (form && emailInput) {
    form.addEventListener('submit', function(e) {
        const email = emailInput.value;
        if (email.trim() !== '') {
            const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,6}$/i;
            if (!email.match(emailPattern)) {
                e.preventDefault();
                alert('Пожалуйста, введите корректный email.');
            }
        }
    });
}