<?php
session_start();

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../kepek/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <title>FlavorWave</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- <link rel="stylesheet" href="../css/navbar.css"> -->
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/carousel.css">
    <link rel="stylesheet" href="../css/parallax.css">
    <link rel="stylesheet" href="../css/kezdolap.css">
    <link rel="stylesheet" href="../css/navbar.css">


</head>
<body>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlavorWave - Modern Navbar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>

<body>
<nav>
    <!-- Bal oldalon a logó -->
    <a href="kezdolap.php" class="logo">
        <img src="../kepek/logo.png" alt="Flavorwave Logo">
        <h1>FlavorWave</h1>
    </a>

    <!-- Középen a kategóriák (és Admin felület, ha jogosult) -->
    <div class="navbar-center">
        <a href="kategoria.php">Menü</a>
        <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
            <a href="admin_felulet.php">Admin felület</a>
        <?php endif; ?>
    </div>

    <!-- Jobb oldalon a gombok -->
    <div class="navbar-buttons">
        <?php if (isset($_SESSION["felhasznalo_nev"])): ?>
            <a href="kijelentkezes.php" class="login-btn">Kijelentkezés</a>
        <?php else: ?>
            <a href="bejelentkezes.php" class="login-btn">Bejelentkezés</a>
        <?php endif; ?>
        <a href="rendeles.php" class="order-btn">Rendelés</a>
        <a href="kosar.php" class="cart-btn">
            <i class='fas fa-shopping-cart cart-icon'></i>
        </a>
    </div>

    <!-- Hamburger menü ikon -->
    <div class="hamburger" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>
</nav>

<!-- Hamburger menü tartalma -->
<div class="menubar" id="menubar">
    <ul>
        <li><a href="kategoria.php">Menü</a></li>
        <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
            <li><a href="admin_felulet.php">Admin felület</a></li>
        <?php endif; ?>
        <li><a href="kosar.php">Kosár</a></li>
        <?php if (isset($_SESSION["felhasznalo_nev"])): ?>
            <li><a href="kijelentkezes.php">Kijelentkezés</a></li>
        <?php else: ?>
            <li><a href="bejelentkezes.php">Bejelentkezés</a></li>
        <?php endif; ?>
            <li><a href="rendeles.php">Rendelés</a></li>
    </ul>
</div>
</body>

</html>












<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friss, Forró, Finom - Ételrendelés</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Poppins:wght@300;400;600&display=swap');

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            background: #1a1a1a;
            color: #fff;
        }

        .hero {
            position: relative;
            height: 100vh;
            background: url('../kepek/pizza2.jpg') center/cover no-repeat fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
            animation: changeBackground 15s infinite;
        }

        @keyframes changeBackground {
            0% {
                background-image: url('../kepek/pizza2.jpg');
            }

            33% {
                background-image: url('../kepek/pizza2.jpg');
            }

            66% {
                background-image: url('../kepek/pizza2.jpg');
            }

            100% {
                background-image: url('../kepek/pizza2.jpg');
            }
        }

        .hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 0 20px;
        }

        .hero h1 {
            font-size: 3.5rem; /* Csökkentett betűméret */
            margin-bottom: 20px;
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: 2px; /* Csökkentett betűközt */
            animation: fadeInDown 1s ease-out;
            text-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
            font-family: 'Montserrat', sans-serif;
        }

        .hero p {
            font-size: 1.4rem; /* Csökkentett betűméret */
            margin-bottom: 30px; /* Csökkentett margó */
            line-height: 1.6;
            animation: fadeInUp 1.5s ease-out;
            font-weight: 300;
        }

        .hero .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 10px; /* Csökkentett távolság */
            flex-wrap: wrap; /* Gombok egymás alá kerülése */
            animation: fadeInUp 2s ease-out;
        }

        .cta-buttons a {
            text-decoration: none;
            padding: 12px 30px; /* Csökkentett padding */
            font-size: 1.1rem; /* Csökkentett betűméret */
            border-radius: 30px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            white-space: nowrap; /* Szöveg egy sorban tartása */
        }

        .cta-buttons .order-now {
            background: linear-gradient(135deg, #ff7e5f, #feb47b);
            color: #fff;
            box-shadow: 0 4px 15px rgba(255, 126, 95, 0.5);
        }

        .cta-buttons .order-now:hover {
            background: linear-gradient(135deg, #feb47b, #ff7e5f);
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(255, 126, 95, 0.7);
        }

        .cta-buttons .view-menu {
            background: transparent;
            color: #fff;
            border: 2px solid #fff;
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
        }

        .cta-buttons .view-menu:hover {
            background: rgba(255, 255, 255, 0.9);
            color: #000;
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.5);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .feedback-section {
            margin-top: 30px; /* Csökkentett margó */
            text-align: center;
            animation: fadeInUp 2.5s ease-out;
        }

        .feedback-section a,
        .feedback-section p {
            font-size: 1rem; /* Csökkentett betűméret */
            color: #fff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .feedback-section a:hover {
            color: #feb47b;
        }

        .feedback-section .primary-bttn {
            background: linear-gradient(135deg, #ff7e5f, #feb47b);
            padding: 8px 20px; /* Csökkentett padding */
            border-radius: 30px;
            color: #fff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(255, 126, 95, 0.5);
        }

        .feedback-section .primary-bttn:hover {
            background: linear-gradient(135deg, #feb47b, #ff7e5f);
            transform: scale(1.05);
        }

        /* Média lekérdezések */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 3rem;
                letter-spacing: 1px;
            }

            .hero p {
                font-size: 1.3rem;
            }

            .cta-buttons a {
                font-size: 1rem;
                padding: 10px 25px;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.2rem;
            }

            .cta-buttons a {
                font-size: 0.9rem;
                padding: 8px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="hero">
        <div class="hero-content">
            <h1>Friss, Forró, Finom</h1>
            <p>Rendelj kedvenc ételeid közül gyorsan és egyszerűen! Fedezd fel az ízek világát.</p>
            <div class="cta-buttons">
                <a href="menu.php" class="order-now">Rendelj Most</a>
                <a href="kategoria.php" class="view-menu">Tekintsd meg a Menüt</a>
            </div>
        </div>
    </div>

    <div class="feedback-section">
        <?php if (isset($_SESSION['felhasznalo_id'])): ?>
            <a href="visszajelzesek.php" class="primary-bttn">Küldd el a véleményed!</a>
        <?php else: ?>
            <p>Kérjük, <a href="bejelentkezes.php">jelentkezz be</a>, hogy véleményt írhass!</p>
        <?php endif; ?>
    </div>
</body>

</html>


<br>






<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Heti Ajánlat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff7e5f;
            --secondary-color: #feb47b;
            --text-color: #333;
            --button-hover: #ff4a6e;
            --shadow-color: rgba(0, 0, 0, 0.2);
            --gradient-start: #ff7e5f;
            --gradient-end: #feb47b;
        }

        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a1a, #2c3e50);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px; Padding a kisebb képernyőkre */
        }

        section#weekly-deals {
            text-align: center;
            padding: 30px 20px; /* Csökkentett padding */
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            box-shadow: 0 10px 30px var(--shadow-color);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 95%; /* Szélesebb a kisebb képernyőkre */
            max-width: 1200px;
            margin: 0 auto;
        }

        section#weekly-deals h2 {
            font-size: 2rem; /* Csökkentett betűméret */
            margin-bottom: 15px; /* Csökkentett margó */
            color: var(--primary-color);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .countdown {
            display: flex;
            justify-content: center;
            gap: 10px; /* Csökkentett távolság */
            margin-bottom: 30px; /* Csökkentett margó */
            font-size: 1.2rem; /* Csökkentett betűméret */
            font-weight: bold;
            flex-wrap: wrap; /* Hogy elférjen kisebb képernyőn */
        }

        .countdown span {
            padding: 8px 15px; /* Csökkentett padding */
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .sliders-container {
            display: flex;
            justify-content: center;
            gap: 15px; /* Csökkentett távolság */
            margin: 0 auto;
            width: 100%;
            flex-wrap: wrap; /* Hogy a sliderek egymás alá kerüljenek */
        }

        .image-slider {
            position: relative;
            width: 100%; /* Teljes szélesség a kisebb képernyőkre */
            max-width: 500px; /* Maximális szélesség */
            margin: 15px 0; /* Csökkentett margó */
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .image-slider img {
            width: 100%;
            height: auto;
            display: none;
            animation: fadeIn 1s ease-in-out;
        }

        .image-slider img.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .cta-button {
            display: inline-block;
            margin-top: 15px; /* Csökkentett margó */
            padding: 12px 25px; /* Csökkentett padding */
            font-size: 1rem; /* Csökkentett betűméret */
            font-weight: bold;
            color: #fff;
            background: linear-gradient(45deg, var(--gradient-start), var(--gradient-end));
            border: none;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .cta-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.3);
            background: linear-gradient(45deg, var(--gradient-end), var(--gradient-start));
        }

        .cta-button:active {
            transform: translateY(0);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        /* Média lekérdezések */
        @media (min-width: 768px) {
            .sliders-container {
                flex-wrap: nowrap; /* Nagyobb képernyőn maradjanak egymás mellett */
            }

            .image-slider {
                width: 45%; /* Két slider egymás mellett */
            }
        }

        @media (max-width: 480px) {
            section#weekly-deals h2 {
                font-size: 1.8rem; /* Még kisebb betűméret */
            }

            .countdown {
                font-size: 1rem; /* Még kisebb betűméret */
            }

            .cta-button {
                font-size: 0.9rem; /* Még kisebb betűméret */
            }
        }
    </style>
</head>
<body>

<section id="weekly-deals">
    <h2>Heti Ajánlat Vége:</h2>
    <div class="countdown" id="countdown">
        <span class="days">00</span> nap
        <span class="hours">00</span> óra
        <span class="minutes">00</span> perc
        <span class="seconds">00</span> másodperc
    </div>
    <div class="sliders-container">
        <div class="image-slider" id="image-slider-1">
            <img src="../kepek/pizza4.jpg" alt="Ajánlat 1">
            <img src="../kepek/pizza2.jpg" alt="Ajánlat 2">
            <img src="../kepek/pizza3.jpg" alt="Ajánlat 3">
        </div>
        <div class="image-slider" id="image-slider-2">
            <img src="../kepek/pizza4.jpg" alt="Ajánlat 4">
            <img src="../kepek/pizza2.jpg" alt="Ajánlat 5">
            <img src="../kepek/pizza3.jpg" alt="Ajánlat 6">
        </div>
    </div>
    <p>Ne maradj le! Az ajánlat visszaszámlálás alatt érhető el.</p>
    <a href="menu.php" class="cta-button">Fedezd fel az ajánlatokat!</a>
</section>

<script>
    // Visszaszámláló logika
    const countdown = document.getElementById('countdown');
    const endDate = new Date();
    endDate.setDate(endDate.getDate() + 7); // Heti ajánlat 7 nap múlva véget ér

    function updateCountdown() {
        const now = new Date();
        const timeLeft = endDate - now;

        if (timeLeft <= 0) {
            countdown.innerHTML = '<strong>Az ajánlat véget ért!</strong>';
            return;
        }

        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

        countdown.querySelector('.days').textContent = String(days).padStart(2, '0');
        countdown.querySelector('.hours').textContent = String(hours).padStart(2, '0');
        countdown.querySelector('.minutes').textContent = String(minutes).padStart(2, '0');
        countdown.querySelector('.seconds').textContent = String(seconds).padStart(2, '0');
    }

    setInterval(updateCountdown, 1000);

    // Váltakozó képek logika
    function changeImage(sliderId) {
        const images = document.querySelectorAll(`#${sliderId} img`);
        let currentIndex = 0;

        function switchImage() {
            images[currentIndex].classList.remove('active');
            currentIndex = (currentIndex + 1) % images.length;
            images[currentIndex].classList.add('active');
        }

        images[currentIndex].classList.add('active'); // Induló kép
        setInterval(switchImage, 3000); // 3 másodpercenként vált
    }

    // Kezdés a két sliderrel
    changeImage('image-slider-1');
    changeImage('image-slider-2');
</script>

</body>
</html>
































<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupon Slider</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff758c;
            --secondary-color: #ffe6e9;
            --text-color: #333;
            --button-hover: #ff4a6e;
            --shadow-color: rgba(0, 0, 0, 0.2);
            --gradient-start: #ff7e5f;
            --gradient-end: #feb47b;
        }

        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a1a, #2c3e50);
            color: #fff;
            display: flex;
            justify-content: center;

            min-height: 100vh;
        }

        /* Slider Container */
        .coupon-slider {
            position: relative;
            width: 90%;
            max-width: 1200px;
            margin: 50px auto;
            overflow: hidden;
            border-radius: 25px;
            box-shadow: 0 10px 30px var(--shadow-color);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Slides Wrapper */
        .slides {
            display: flex;
            transition: transform 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }

        /* Individual Slide */
        .slide {
            flex: 0 0 100%;
            display: flex;
            align-items: center;
            color: var(--text-color);
            padding: 40px;
            position: relative;
        }

        .slide .text {
            flex: 1;
            padding: 20px;
            text-align: left;
            animation: fadeIn 1s ease;
        }

        .slide h3 {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .slide p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: #fff;
        }

        .slide .btn {
            display: inline-block;
            padding: 15px 30px;
            background: var(--primary-color);
            color: #fff;
            border-radius: 50px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 117, 140, 0.4);
        }

        .slide .btn:hover {
            background: var(--button-hover);
            box-shadow: 0 8px 20px rgba(255, 74, 110, 0.6);
            transform: scale(1.1);
        }

        .slide .image {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .slide .image img {
            max-width: 90%;
            border-radius: 20px;
            box-shadow: 0 8px 20px var(--shadow-color);
            transform: scale(1);
            transition: transform 0.5s ease;
        }

        .slide .image img:hover {
            transform: scale(1.1);
        }

        /* Navigation Buttons */
        .coupon-slider button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.8);
            border: none;
            border-radius: 50%;
            padding: 15px;
            cursor: pointer;
            box-shadow: 0 4px 10px var(--shadow-color);
            transition: all 0.3s ease;
        }

        .coupon-slider button:hover {
            background: #fff;
            transform: translateY(-50%) scale(1.2);
        }

        .coupon-slider .prev {
            left: 10px;
        }

        .coupon-slider .next {
            right: 10px;
        }

        /* Decorative Dots */
        .dots {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .dot {
            width: 15px;
            height: 15px;
            background: #fff;
            border: 2px solid var(--primary-color);
            border-radius: 50%;
            margin: 0 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dot.active {
            background: var(--primary-color);
            transform: scale(1.3);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Glow Effect */
        .slide::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            border-radius: 30px;
            background: linear-gradient(135deg, rgba(255, 126, 95, 0.3), rgba(254, 180, 123, 0.3));
            z-index: -1;
            filter: blur(20px);
            opacity: 0;
            transition: opacity 0.8s ease;
        }

        .slide.show::before {
            opacity: 1;
        }

        /* Floating Animation */
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .slide .image img {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body>

<div class="coupon-slider">
    <div class="slides">
        <div class="slide">
            <div class="text">
                <h3>50% OFF on Burgers!</h3>
                <p>Only today! Use coupon code: <strong>BURGER50</strong></p>
                <a href="#" class="btn">Order Now</a>
            </div>
            <div class="image">
                <img src="../kepek/pizza2.jpg" alt="Burger Deal">
            </div>
        </div>
        <div class="slide">
            <div class="text">
                <h3>Free Dessert!</h3>
                <p>With orders above $20. Valid until 2025-01-31.</p>
                <a href="#" class="btn">Claim Now</a>
            </div>
            <div class="image">
                <img src="../kepek/pizza2.jpg" alt="Dessert Offer">
            </div>
        </div>
        <div class="slide">
            <div class="text">
                <h3>Buy 1 Get 1 Free</h3>
                <p>On all pizzas. Use code: <strong>PIZZADEAL</strong></p>
                <a href="#" class="btn">Grab the Deal</a>
            </div>
            <div class="image">
                <img src="../kepek/pizza2.jpg" alt="Pizza Deal">
            </div>
        </div>
    </div>
    <button class="prev">&#10094;</button>
    <button class="next">&#10095;</button>
</div>

<div class="dots">
    <div class="dot active"></div>
    <div class="dot"></div>
    <div class="dot"></div>
</div>

<script>
    const slides = document.querySelector('.slides');
    const slide = document.querySelectorAll('.slide');
    const prevButton = document.querySelector('.prev');
    const nextButton = document.querySelector('.next');
    const dots = document.querySelectorAll('.dot');
    let currentIndex = 0;
    let autoSlideInterval;

    const updateSlider = () => {
        slides.style.transform = `translateX(-${currentIndex * 100}%)`;
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentIndex);
        });
    };

    const nextSlide = () => {
        currentIndex = (currentIndex < slide.length - 1) ? currentIndex + 1 : 0;
        updateSlider();
    };

    const prevSlide = () => {
        currentIndex = (currentIndex > 0) ? currentIndex - 1 : slide.length - 1;
        updateSlider();
    };

    const startAutoSlide = () => {
        autoSlideInterval = setInterval(nextSlide, 7000);
    };

    const stopAutoSlide = () => {
        clearInterval(autoSlideInterval);
    };

    prevButton.addEventListener('click', () => {
        prevSlide();
        stopAutoSlide();
        startAutoSlide();
    });

    nextButton.addEventListener('click', () => {
        nextSlide();
        stopAutoSlide();
        startAutoSlide();
    });

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentIndex = index;
            updateSlider();
            stopAutoSlide();
            startAutoSlide();
        });
    });

    // Start automatic sliding on page load
    startAutoSlide();

    // Optional: Pause auto-slide on hover
    const slider = document.querySelector('.coupon-slider');
    slider.addEventListener('mouseenter', stopAutoSlide);
    slider.addEventListener('mouseleave', startAutoSlide);
</script>

</body>
</html>
<br>






















<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendelési Lépések</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800&display=swap');
        
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a1a, #2c3e50);
            color: #fff;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            justify-content: center;
        }

        .steps-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 50px 20px; /* Csökkentett padding */
            width: 100%;
        }

        .step {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            position: relative;
            width: 90%; /* Rugalmas szélesség */
            max-width: 800px;
            padding: 20px 30px; /* Csökkentett padding */
            margin: 30px 0; /* Csökkentett margó */
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            opacity: 0;
            transform: translateY(50px) scale(0.9);
            transition: all 0.8s ease;
        }

        .step.show {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .step i {
            font-size: 2.5rem; /* Csökkentett betűméret */
            margin-right: 20px; /* Csökkentett margó */
            color: #ff7e5f;
            text-shadow: 0 0 10px rgba(255, 126, 95, 0.7);
        }

        .step p {
            font-size: 1.4rem; /* Csökkentett betűméret */
            font-weight: 600;
            margin: 0;
            color: #fff;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .step::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            border-radius: 30px;
            background: linear-gradient(135deg, rgba(255, 126, 95, 0.3), rgba(254, 180, 123, 0.3));
            z-index: -1;
            filter: blur(20px);
            opacity: 0;
            transition: opacity 0.8s ease;
        }

        .step.show::before {
            opacity: 1;
        }

        .step:nth-child(2) i {
            color: #6a89cc;
        }

        .step:nth-child(3) i {
            color: #82ccdd;
        }

        .step:nth-child(4) i {
            color: #b8e994;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .step i {
            animation: float 3s ease-in-out infinite;
        }

        /* Média lekérdezések */
        @media (max-width: 768px) {
            .step {
                width: 95%; /* Még nagyobb szélesség kisebb képernyőkre */
                padding: 15px 20px; /* Még kisebb padding */
                margin: 20px 0; /* Még kisebb margó */
            }

            .step i {
                font-size: 2rem; /* Még kisebb betűméret */
                margin-right: 15px; /* Még kisebb margó */
            }

            .step p {
                font-size: 1.2rem; /* Még kisebb betűméret */
            }
        }

        @media (max-width: 480px) {
            .step {
                flex-direction: column; /* Álló elrendezés */
                align-items: center;
                text-align: center;
            }

            .step i {
                margin-right: 0; /* Nincs margó az ikonnak */
                margin-bottom: 10px; /* Alsó margó az ikonnak */
            }
        }
    </style>
</head>
<body>
    <div class="steps-container">
        <div class="step"><i class="fas fa-utensils"></i> <p>Válassz ételt 🍕</p></div>
        <div class="step"><i class="fas fa-map-marker-alt"></i> <p>Add meg a címed 📍</p></div>
        <div class="step"><i class="fas fa-credit-card"></i> <p>Fizess online vagy készpénzben 💳</p></div>
        <div class="step"><i class="fas fa-smile"></i> <p>Élvezd az ételt 😋</p></div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const steps = document.querySelectorAll(".step");
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("show");
                    }
                });
            }, { threshold: 0.5 });
            
            steps.forEach(step => observer.observe(step));
        });
    </script>
</body>
</html>









<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlavorWave - Ételajánló Quiz</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800&display=swap');

        /* Alap stílusok */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* Fő konténer */
        .quiz-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e1e2f, #2a2a40);
            padding: 20px; /* Csökkentett padding */
        }

        .quiz-container {
            width: 95%; /* Növelt szélesség */
            max-width: 1000px;
            padding: 30px; /* Csökkentett padding */
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            text-align: center;
            animation: slideInFromRight 1s ease-out;
        }

        @keyframes slideInFromRight {
            0% {
                transform: translateX(100%);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .quiz-container h1 {
            font-size: 2.5rem; /* Csökkentett betűméret */
            margin-bottom: 10px;
            color: #ff7e5f;
            text-shadow: 0 0 10px rgba(255, 126, 95, 0.7);
            animation: popIn 1s ease-out;
        }

        .quiz-container p {
            font-size: 1.1rem; /* Csökkentett betűméret */
            margin-bottom: 20px; /* Csökkentett margó */
            color: #fff;
            animation: fadeIn 1.5s ease-out;
        }

        @keyframes popIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            60% {
                transform: scale(1.1);
                opacity: 1;
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        .quiz-question {
            display: none;
            width: 100%;
            padding: 15px; /* Csökkentett padding */
            margin: 15px 0; /* Csökkentett margó */
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.8s ease;
            text-align: center;
        }

        .quiz-question.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .quiz-question h2 {
            font-size: 1.8rem; /* Csökkentett betűméret */
            margin-bottom: 15px; /* Csökkentett margó */
            color: #fff;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .quiz-options {
            display: flex;
            justify-content: center;
            gap: 10px; /* Csökkentett távolság */
            flex-wrap: wrap;
        }

        .quiz-options button {
            padding: 12px 25px; /* Csökkentett padding */
            font-size: 0.9rem; /* Csökkentett betűméret */
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, #ff7e5f, #feb47b);
            border: none;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 126, 95, 0.3);
            animation: float 3s ease-in-out infinite;
        }

        .quiz-options button:hover {
            background: linear-gradient(135deg, #feb47b, #ff7e5f);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 126, 95, 0.5);
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .quiz-result-card {
            display: none;
            width: 100%;
            padding: 30px; /* Csökkentett padding */
            margin: 15px 0; /* Csökkentett margó */
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            opacity: 0;
            transform: translateY(-50px);
            transition: all 0.8s ease;
            text-align: center;
            animation: slideIn 0.8s ease-out forwards;
        }

        .quiz-result-card.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .quiz-result-card h2 {
            font-size: 2rem; /* Csökkentett betűméret */
            margin-bottom: 15px; /* Csökkentett margó */
            color: #ff7e5f;
            text-shadow: 0 0 10px rgba(255, 126, 95, 0.7);
        }

        .quiz-result-card p {
            font-size: 1.2rem; /* Csökkentett betűméret */
            margin-bottom: 15px; /* Csökkentett margó */
            color: #fff;
        }

        .quiz-result-card .image-container {
            margin: 15px 0; /* Csökkentett margó */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .quiz-result-card img {
            width: 100%;
            max-width: 300px;
            height: auto; /* Automatikus magasság */
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: popIn 1s ease-out;
        }

        .quiz-result-card img:hover {
            transform: scale(1.1);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
        }

        .quiz-result-card a {
            display: inline-block;
            margin-top: 15px; /* Csökkentett margó */
            padding: 12px 25px; /* Csökkentett padding */
            font-size: 1rem; /* Csökkentett betűméret */
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, #ff7e5f, #feb47b);
            border-radius: 15px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 126, 95, 0.3);
            position: relative;
            overflow: hidden;
            animation: float 3s ease-in-out infinite;
        }

        .quiz-result-card a::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300%;
            height: 300%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3), transparent);
            transform: translate(-50%, -50%) scale(0);
            transition: transform 0.5s ease;
        }

        .quiz-result-card a:hover::before {
            transform: translate(-50%, -50%) scale(1);
        }

        .quiz-result-card a:hover {
            background: linear-gradient(135deg, #feb47b, #ff7e5f);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 126, 95, 0.5);
        }

        .quiz-question i {
            font-size: 3rem; /* Csökkentett betűméret */
            margin-bottom: 15px; /* Csökkentett margó */
            color: #ff7e5f;
            text-shadow: 0 0 10px rgba(255, 126, 95, 0.7);
            animation: float 3s ease-in-out infinite;
        }

        /* Média lekérdezések */
        @media (max-width: 768px) {
            .quiz-container h1 {
                font-size: 2rem;
            }
            .quiz-container p {
                font-size: 1rem;
            }
            .quiz-options {
                flex-direction: column;
                align-items: center;
            }
            .quiz-options button {
                width: 80%;
                margin-bottom: 10px;
            }
            .quiz-result-card h2 {
                font-size: 1.8rem;
            }
            .quiz-result-card p {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            .quiz-container h1 {
                font-size: 1.7rem;
            }
            .quiz-options button {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="quiz-wrapper">
        <div class="quiz-container">
            <h1>Nem tudod milyen pizzát válassz?</h1>
            <p>Mi segítünk! Csak válassz tetszésnek megfelelő alapanyagot!</p>
            <div class="quiz-question active">
                <i class="fas fa-pizza-slice"></i>
                <h2>Milyen pizzát szeretnél?</h2>
                <div class="quiz-options">
                    <button data-type="meat">Húsos</button>
                    <button data-type="veggie">Zöldséges</button>
                    <button data-type="cheese">Sajtos</button>
                </div>
            </div>
            <div class="quiz-question">
                <i class="fas fa-pepper-hot"></i>
                <h2>Mennyire szereted a csípőset?</h2>
                <div class="quiz-options">
                    <button data-spice="mild">Enyhe</button>
                    <button data-spice="medium">Közepes</button>
                    <button data-spice="hot">Csípős</button>
                </div>
            </div>
            <div class="quiz-question">
                <i class="fas fa-cheese"></i>
                <h2>Mennyire szereted a sajtot?</h2>
                <div class="quiz-options">
                    <button data-cheese="light">Kevés</button>
                    <button data-cheese="normal">Normál</button>
                    <button data-cheese="extra">Extra</button>
                </div>
            </div>
            <div class="quiz-result-card">
                <h2>Az ajánlott ételed:</h2>
                <p id="recommended-food"></p>
                <div class="image-container">
                    <img id="food-image" src="" alt="Ajánlott étel">
                </div>
                <a id="order-link" href="#">Megrendelem!</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const questions = document.querySelectorAll(".quiz-question");
            const resultCard = document.querySelector(".quiz-result-card");
            const recommendedFood = document.getElementById("recommended-food");
            const foodImage = document.getElementById("food-image");
            const orderLink = document.getElementById("order-link");

            let answers = {
                type: null,
                spice: null,
                cheese: null
            };

            let currentQuestionIndex = 0;

            function showNextQuestion() {
                questions[currentQuestionIndex].classList.remove("active");
                currentQuestionIndex++;
                if (currentQuestionIndex < questions.length) {
                    questions[currentQuestionIndex].classList.add("active");
                } else {
                    recommendFood();
                    resultCard.classList.add("show");
                }
            }

            questions.forEach((question, index) => {
                const options = question.querySelectorAll(".quiz-options button");
                options.forEach(option => {
                    option.addEventListener("click", () => {
                        const key = Object.keys(answers)[index];
                        answers[key] = option.dataset[key];
                        showNextQuestion();
                    });
                });
            });

            function recommendFood() {
                const { type, spice, cheese } = answers;
                let food = "";
                let image = "";
                let link = "pizza.php?type=";

                if (type === "meat") {
                    food = "Húsos Pizza";
                    image = "../kepek/pizza2.jpg";
                    link += "meat";
                } else if (type === "veggie") {
                    food = "Zöldséges Pizza";
                    image = "../kepek/pizza2.jpg";
                    link += "veggie";
                } else if (type === "cheese") {
                    food = "Sajtos Pizza";
                    image = "../kepek/pizza2.jpg";
                    link += "cheese";
                }

                if (spice === "medium") {
                    food += " közepesen csípős";
                    link += "&spice=medium";
                } else if (spice === "hot") {
                    food += " extra csípős";
                    link += "&spice=hot";
                }

                if (cheese === "extra") {
                    food += " extra sajttal";
                    link += "&cheese=extra";
                }

                recommendedFood.textContent = food;
                foodImage.src = image;
                orderLink.href = link;
            }
        });
    </script>
</body>
</html>


    <!-- Footer -->
    <div class="footer">
    <div class="footer-container">
        <ul class="footer-links">
            <li><a href="../html/rolunk.html">Rólunk</a></li>
            <li><a href="../html/kapcsolatok.html">Kapcsolat</a></li>
            <li><a href="../html/adatvedelem.html">Adatvédelem</a></li>
        </ul>
        <div class="footer-socials">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
        </div>
        <div class="footer-copy">
            &copy; 2025 FlavorWave - Minden jog fenntartva.
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/navbar.js"></script>


</body>
</html>
