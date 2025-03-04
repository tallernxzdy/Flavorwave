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
    <!-- <link rel="stylesheet" href="../css/fooldal/hero.css"> -->

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
    background: url('../kepek/pizzak/pizza2.jpg') center/cover no-repeat fixed;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    overflow: hidden;
    animation: changeBackground 15s infinite;
}

@keyframes changeBackground {
    0% {
        background-image: url('../kepek/pizzak/pizza2.jpg');
    }

    33% {
        background-image: url('../kepek/pizzak/pizza2.jpg');
    }

    66% {
        background-image: url('../kepek/pizzak/pizza2.jpg');
    }

    100% {
        background-image: url('../kepek/pizzak/pizza2.jpg');
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
    <link rel="stylesheet" href="../css/fooldal/hetiajanlat.css">

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

<script src="../js/fooldal/hetiajanlat.js">

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
    <link rel="stylesheet" href="../css/fooldal/kupon.css">
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

<script src="../js/fooldal/kupon.js">

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
    <link rel="stylesheet" href="../css/fooldal/rendeles_lepesek.css">

</head>
<body>
    <div class="steps-container">
        <div class="step"><i class="fas fa-utensils"></i> <p>Válassz ételt 🍕</p></div>
        <div class="step"><i class="fas fa-map-marker-alt"></i> <p>Add meg a címed 📍</p></div>
        <div class="step"><i class="fas fa-credit-card"></i> <p>Fizess online vagy készpénzben 💳</p></div>
        <div class="step"><i class="fas fa-smile"></i> <p>Élvezd az ételt 😋</p></div>
    </div>

    <script src="../js/fooldal/rendeles_lepesek.js">

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
    <link rel="stylesheet" href="../css/fooldal/etelajanlo.css">

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

    <script src="../js/fooldal/etelajanlo.js">
       
    </script>
</body>
</html>













<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shaker Master</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../css/fooldal/shakek.css">
</head>
<body>

<div class="shaker-master-container">
    <div class="shaker-master">
        <div class="shaker-slides">
            <div class="shaker-slide">
                <div class="shaker-text">
                    <h3>Shaker 1: Classic</h3>
                    <p>Próbáld ki a klasszikus shakerünket, ami tökéletes minden italhoz!</p>
                    <a href="#" class="shaker-btn">Vásárlás</a>
                </div>
                <div class="shaker-image">
                    <img src="https://via.placeholder.com/400" alt="Classic Shaker">
                </div>
            </div>
            <div class="shaker-slide">
                <div class="shaker-text">
                    <h3>Shaker 2: Neon</h3>
                    <p>Világítsd fel a bulit a neon shakerünkkel!</p>
                    <a href="#" class="shaker-btn">Vásárlás</a>
                </div>
                <div class="shaker-image">
                    <img src="https://via.placeholder.com/400" alt="Neon Shaker">
                </div>
            </div>
            <div class="shaker-slide">
                <div class="shaker-text">
                    <h3>Shaker 3: Premium</h3>
                    <p>A prémium shakerünk a legjobb választás a profiknak!</p>
                    <a href="#" class="shaker-btn">Vásárlás</a>
                </div>
                <div class="shaker-image">
                    <img src="https://via.placeholder.com/400" alt="Premium Shaker">
                </div>
            </div>
        </div>
        <button class="shaker-prev">&#10094;</button>
        <button class="shaker-next">&#10095;</button>
    </div>

    <div class="shaker-dots">
        <div class="shaker-dot active"></div>
        <div class="shaker-dot"></div>
        <div class="shaker-dot"></div>
    </div>

    <!-- Particles for Background -->
    <div class="shaker-particles">
        <span style="top: 10%; left: 20%; animation-delay: 0s;"></span>
        <span style="top: 20%; left: 50%; animation-delay: 2s;"></span>
        <span style="top: 30%; left: 70%; animation-delay: 4s;"></span>
        <span style="top: 40%; left: 10%; animation-delay: 6s;"></span>
        <span style="top: 50%; left: 90%; animation-delay: 8s;"></span>
        <span style="top: 60%; left: 30%; animation-delay: 10s;"></span>
        <span style="top: 70%; left: 60%; animation-delay: 12s;"></span>
        <span style="top: 80%; left: 40%; animation-delay: 14s;"></span>
    </div>
</div>

<script src="../js/fooldal/shakek.js">

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