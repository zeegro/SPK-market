// === Слайдер ===
const slider = document.querySelector('.slider');
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');

if (slider && slides.length > 0 && dots.length > 0) {
    let currentIndex = 0;

    function updateSlider() {
        slider.style.transform = `translateX(-${currentIndex * 100}%)`;

        dots.forEach(dot => dot.classList.remove('active'));
        if (dots[currentIndex]) {
            dots[currentIndex].classList.add('active');
        }
    }

    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            const index = parseInt(dot.getAttribute('data-index'));
            if (!isNaN(index)) {
                currentIndex = index;
                updateSlider();
            }
        });
    });

    setInterval(() => {
        currentIndex = (currentIndex + 1) % slides.length;
        updateSlider();
    }, 8000);
}

// === Навигация по точкам справа ===
document.addEventListener('DOMContentLoaded', () => {
    const categoryItems = document.querySelectorAll('.category-list li');
    const dotItems = document.querySelectorAll('.dot-container a');
    const sections = document.querySelectorAll('.category-block');

    if (sections.length > 0 && categoryItems.length > 0 && dotItems.length > 0) {
        let currentActiveIndex = null;

        function setActiveCategory(index) {
            if (index === currentActiveIndex) return;
            currentActiveIndex = index;

            categoryItems.forEach((item, i) => {
                item.classList.remove('active');
                if (i === index) item.classList.add('active');
            });

            dotItems.forEach((dotWrapper, i) => {
                dotWrapper.classList.remove('active');
                const dot = dotWrapper.querySelector('.category-dot');
                if (dot) dot.classList.remove('active');

                if (i === index) {
                    dotWrapper.classList.add('active');
                    if (dot) dot.classList.add('active');
                }
            });
        }

        function getActiveSectionIndex() {
            const scrollPosition = window.scrollY + 100;
            let closestIndex = 0;

            for (let i = 0; i < sections.length; i++) {
                const section = sections[i];
                const offsetTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;

                if (
                    scrollPosition >= offsetTop &&
                    scrollPosition < offsetTop + sectionHeight
                ) {
                    return i;
                }

                if (scrollPosition > offsetTop) {
                    closestIndex = i;
                }
            }

            if (window.scrollY + window.innerHeight >= document.body.scrollHeight - 50) {
                return sections.length - 1;
            }

            return closestIndex;
        }

        function onScroll() {
            const index = getActiveSectionIndex();
            setActiveCategory(index);
        }

        window.addEventListener('scroll', onScroll);

        // Клик по категории
        categoryItems.forEach((item, index) => {
            item.addEventListener('mouseenter', () => {
                item.classList.add('hovered');
                if (dotItems[index]) {
                    dotItems[index].classList.add('hovered');
                    const dot = dotItems[index].querySelector('.category-dot');
                    if (dot) dot.classList.add('hovered');
                }
            });

            item.addEventListener('mouseleave', () => {
                item.classList.remove('hovered');
                if (dotItems[index]) {
                    dotItems[index].classList.remove('hovered');
                    const dot = dotItems[index].querySelector('.category-dot');
                    if (dot) dot.classList.remove('hovered');
                }
            });

            item.addEventListener('click', e => {
                e.preventDefault();
                scrollToCategory(index);
            });
        });

        // Клик по точке
        dotItems.forEach((dotWrapper, index) => {
            dotWrapper.addEventListener('mouseenter', () => {
                if (categoryItems[index]) {
                    categoryItems[index].classList.add('hovered');
                }
                const dot = dotWrapper.querySelector('.category-dot');
                if (dot) dot.classList.add('hovered');
            });

            dotWrapper.addEventListener('mouseleave', () => {
                if (categoryItems[index]) {
                    categoryItems[index].classList.remove('hovered');
                }
                const dot = dotWrapper.querySelector('.category-dot');
                if (dot) dot.classList.remove('hovered');
            });

            dotWrapper.addEventListener('click', e => {
                e.preventDefault();
                scrollToCategory(index);
            });
        });

        function scrollToCategory(index) {
            if (!sections[index]) return;

            const section = sections[index];
            const headerOffset = 100;
            const elementPosition = section.offsetTop;
            const offsetPosition = elementPosition - headerOffset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }

        onScroll();
    }
});

// === Выравнивание карточек ===
document.addEventListener("DOMContentLoaded", function () {
    function equalizeCardHeights() {
        const grids = document.querySelectorAll(".products-grid");

        grids.forEach(grid => {
            const cards = Array.from(grid.querySelectorAll(".product-card"));

            if (cards.length === 0) return;

            const rows = [];
            cards.forEach(card => {
                const top = card.offsetTop;
                let row = rows.find(r => Math.abs(r.offsetTop - top) < 10);
                if (!row) {
                    row = { offsetTop: top, cards: [] };
                    rows.push(row);
                }
                row.cards.push(card);
            });

            rows.forEach(row => {
                if (row.cards.length <= 1) return;

                const maxHeight = Math.max(...row.cards.map(card => card.offsetHeight));

                row.cards.forEach(card => {
                    card.style.height = `${maxHeight}px`;
                });
            });
        });
    }

    window.addEventListener("load", equalizeCardHeights);
    window.addEventListener("resize", equalizeCardHeights);
});

// === Модальное окно "Подробнее" ===
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById('productModal');
    const modalClose = document.getElementById('modalClose');
    const modalImage = document.getElementById('modalImage');
    const modalName = document.getElementById('modalName');
    const modalDescription = document.getElementById('modalDescription');
    const modalInfo1 = document.getElementById('modalInfo1');
    const modalInfo2 = document.getElementById('modalInfo2');
    const modalPrice = document.getElementById('modalPrice');

    if (modal && modalImage && modalName && modalDescription && modalInfo1 && modalInfo2 && modalPrice && modalClose) {
        document.querySelectorAll('.product-details').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                const card = this.closest('.product-card');

                const name = card.querySelector('h4')?.textContent || '';
                const description = card.getAttribute('data-description') || 'Нет описания';
                const info1 = card.getAttribute('data-additional-info') || '';
                const info2 = card.getAttribute('data-additional-info2') || '';
                const price = card.getAttribute('data-price') || '0.00 ₽';
                const image = card.getAttribute('data-image');

                modalImage.src = image;
                modalName.textContent = name;
                modalDescription.textContent = description;
                modalInfo1.textContent = info1;
                modalInfo2.textContent = info2;
                modalPrice.textContent = price;

                modal.style.display = 'flex';
            });
        });

        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });

        modalClose.addEventListener('click', function () {
            modal.style.display = 'none';
        });
    }
});

// === Кнопка "Наверх" ===
document.addEventListener("DOMContentLoaded", function () {
    const backToTopBtn = document.getElementById('backToTop');

    if (backToTopBtn) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });

        backToTopBtn.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});

// === Работа с корзиной ===
document.addEventListener('DOMContentLoaded', () => {
    const cartCount = document.querySelector('.cart-count');
    const addToCartButtons = document.querySelectorAll('.add-to-cart');

    // Загружаем из localStorage
    function loadCartFromLocalStorage() {
        return JSON.parse(localStorage.getItem('cart') || '[]');
    }

    // Сохраняем в localStorage
    function saveCartToLocalStorage(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCounter();
    }

    // Обновляем количество товаров в корзине
    function updateCartCounter() {
        const cart = loadCartFromLocalStorage();
        if (cartCount) {
            cartCount.textContent = cart.length;
        }
    }

    // Клик по кнопке "В корзину"
    addToCartButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const card = e.target.closest('.product-card');
            const productId = card.dataset.productId; // Предположим, что ты добавил это поле
            const productName = card.querySelector('h4').textContent;
            const productPrice = parseFloat(card.querySelector('.price').textContent);
            const productImage = card.querySelector('img').src;

            const cart = loadCartFromLocalStorage();

            const product = {
                id: productId,
                name: productName,
                price: productPrice,
                image: productImage,
                addedAt: new Date().toISOString()
            };

            cart.push(product);
            saveCartToLocalStorage(cart);

            alert(`Товар "${productName}" добавлен в корзину`);
        });
    });

    // Обновляем счётчик при загрузке страницы
    updateCartCounter();
});