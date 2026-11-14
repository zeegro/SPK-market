// === Навигация через меню слева ===
document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('#documenter_nav a');

    // Добавляем обработчики кликов для плавной прокрутки
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href').substring(1); // Убираем символ #
            const targetSection = document.getElementById(targetId);

            if (targetSection) {
                window.scrollTo({
                    top: targetSection.offsetTop - 20, // Отступ от верха
                    behavior: 'smooth'
                });
            }
        });
    });

    // Подсветка текущего раздела при скролле
    window.addEventListener('scroll', () => {
        const sections = document.querySelectorAll('section');
        let currentSectionId = '';

        sections.forEach(section => {
            const sectionTop = section.offsetTop - 50; // Учитываем отступ
            if (window.scrollY >= sectionTop) {
                currentSectionId = section.getAttribute('id');
            }
        });

        // Обновляем активный пункт меню
        navLinks.forEach(link => {
            link.classList.remove('current');
            if (link.getAttribute('href').includes(currentSectionId)) {
                link.classList.add('current');
            }
        });
    });
});

// === Кнопка "Наверх" ===
document.addEventListener('DOMContentLoaded', () => {
    const backToTopBtn = document.getElementById('backToTop');

    if (backToTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});