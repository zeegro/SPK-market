<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>СПК-Маркет</title>
        <!-- Base -->
        <link rel="stylesheet" href="../assets/css/base.css">
        <!-- Slider -->
        <link rel="stylesheet" href="../assets/css/slider.css">
</head>
<body>

<?php include 'templates/header.php'; ?>

<!-- Slider -->
<div class="slider-main">
    <div class="slider-container">
        <div class="slider" id="slider">
            <!-- Слайд 1 -->
            <div class="slide active" data-section="">
                <div class="slide-bg" style="background-image: url('assets/images/slider/slide1.jpg');" onerror="this.style.backgroundImage='url(https://via.placeholder.com/1200x800?text=Slide+1 )';"></div>
                <div class="slide-content">
                    <h2 class="main-title">Интернет-магазин СПК</h2>
                    <p class="subtitle">«Любимые продукты приедут к вам домой!»</p>
                </div>
            </div>
            <!-- Слайд 2 -->
            <div class="slide" data-section="sausages">
                <div class="slide-bg" style="background-image: url('assets/images/slider/slide2.jpg');" onerror="this.style.backgroundImage='url(https://via.placeholder.com/1200x800?text=Slide+2 )';"></div>
                <div class="slide-content">
                    <h2>Колбасы</h2>
                </div>
                <a href="catalog.php?category=sausages" class="btn slide-btn">Перейти</a>
            </div>
            <!-- Слайд 3 -->
            <div class="slide" data-section="meat">
                <div class="slide-bg" style="background-image: url('assets/images/slider/slide3.jpg');" onerror="this.style.backgroundImage='url(https://via.placeholder.com/1200x800?text=Slide+3 )';"></div>
                <div class="slide-content">
                    <h2>Мясо и мясные полуфабрикаты</h2>
                </div>
                <a href="catalog.php?category=meat" class="btn slide-btn">Перейти</a>
            </div>
            <!-- Слайд 4 -->
            <div class="slide" data-section="cheese">
                <div class="slide-bg" style="background-image: url('assets/images/slider/slide4.jpg');" onerror="this.style.backgroundImage='url(https://via.placeholder.com/1200x800?text=Slide+4 )';"></div>
                <div class="slide-content">
                    <h2>Сыры</h2>
                </div>
                <a href="catalog.php?category=cheese" class="btn slide-btn">Перейти</a>
            </div>
        </div>

    <!-- Pagination dots -->
        <div class="slider-pagination" id="pagination">
            <span class="dot active" data-index="0"></span>
            <span class="dot" data-index="1"></span>
            <span class="dot" data-index="2"></span>
            <span class="dot" data-index="3"></span>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>

</body>
</html>