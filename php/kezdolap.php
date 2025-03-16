<?php
session_start();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../kepek/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/fooldal/ujfooldal.css">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <title>FlavorWave</title>

</head>
<body>
    <nav>
        <a href="kezdolap.php" class="logo">
            <img src="../kepek/logo.png" alt="Flavorwave Logo">
            <h1>FlavorWave</h1>
        </a>
        <div class="navbar-center">
            <a href="kategoria.php">Menü</a>
            <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
                <a href="admin_felulet.php">Admin felület</a>
            <?php endif; ?>
            <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 2): ?>
                <a href="dolgozoi_felulet.php">Dolgozói felület</a>
            <?php endif; ?>
        </div>
        <div class="navbar-buttons">
            <?php if (isset($_SESSION["felhasznalo_nev"])): ?>
                <a href="kijelentkezes.php" class="login-btn">Kijelentkezés</a>
            <?php else: ?>
                <a href="bejelentkezes.php" class="login-btn">Bejelentkezés</a>
            <?php endif; ?>
            <a href="rendeles_megtekintes.php" class="order-btn">Rendeléseim</a>
            <a href="kosar.php" class="cart-btn">
                <i class='fas fa-shopping-cart cart-icon'></i>
                Kosár
            </a>
        </div>
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <div class="menubar" id="menubar">
        <ul>
            <li><a href="kategoria.php">Menü</a></li>
            <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
                <li><a href="admin_felulet.php">Admin felület</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 2): ?>
                <li><a href="dolgozoi_felulet.php">Dolgozói felület</a></li>
            <?php endif; ?>
            <li><a href="kosar.php">Kosár</a></li>
            <?php if (isset($_SESSION["felhasznalo_nev"])): ?>
                <li><a href="kijelentkezes.php">Kijelentkezés</a></li>
            <?php else: ?>
                <li><a href="bejelentkezes.php">Bejelentkezés</a></li>
            <?php endif; ?>
            <li><a href="rendeles_megtekintes.php">Rendeléseim</a></li>
        </ul>
    </div>

    <div class="hero">
    <video autoplay loop muted playsinline class="hero-video">
        <source src="../kepek/hambi2.mp4" type="video/mp4">
        A böngésződ nem támogatja a videólejátszást.
    </video>
    <div class="hero-content">
        <h1>Friss, Forró, Finom</h1>
        <p>Rendelj kedvenc ételeid közül gyorsan és egyszerűen!</p>
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
            <img src="../kepek/pizzak/pizza2.jpg" alt="Ajánlat 1">
            <img src="../kepek/pizzak/pizza2.jpg" alt="Ajánlat 2">
            <img src="../kepek/pizzak/pizza2.jpg" alt="Ajánlat 3">
        </div>
        <div class="image-slider" id="image-slider-2">
            <img src="../kepek/pizzak/pizza2.jpg" alt="Ajánlat 4">
            <img src="../kepek/pizzak/pizza2.jpg" alt="Ajánlat 5">
            <img src="../kepek/pizzak/pizza2.jpg" alt="Ajánlat 6">
        </div>
    </div>
    <p>Ne maradj le! Az ajánlat visszaszámlálás alatt érhető el.</p>
    <a href="menu.php" class="cta-button primary-bttn">Fedezd fel az ajánlatokat!</a>
</section>


<br>
<br>
<div class="shaker-master-container">
<div class="coupon-slider">
    <div class="slides">
        <div class="slide">
            <div class="text">
                <h3>50% OFF on Burgers!</h3>
                <p>Only today! Use coupon code: <strong>BURGER50</strong></p>
                <a href="#" class="btn">Order Now</a>
            </div>
            <div class="image">
                <img src="../kepek/pizzak/pizza2.jpg" alt="Burger Deal">
            </div>
        </div>
        <div class="slide">
            <div class="text">
                <h3>Free Dessert!</h3>
                <p>With orders above $20. Valid until 2025-01-31.</p>
                <a href="#" class="btn">Claim Now</a>
            </div>
            <div class="image">
                <img src="../kepek/pizzak/pizza2.jpg" alt="Dessert Offer">
            </div>
        </div>
        <div class="slide">
            <div class="text">
                <h3>Buy 1 Get 1 Free</h3>
                <p>On all pizzas. Use code: <strong>PIZZADEAL</strong></p>
                <a href="#" class="btn">Grab the Deal</a>
            </div>
            <div class="image">
                <img src="../kepek/pizzak/pizza2.jpg" alt="Pizza Deal">
            </div>
        </div>
    </div>
    <button class="prev"><i class="fas fa-chevron-left"></i></button>
    <button class="next"><i class="fas fa-chevron-right"></i></button>
</div>

<div class="dots">
    <div class="dot active"></div>
    <div class="dot"></div>
    <div class="dot"></div>
</div>
</div>

<div class="steps-container">
    <div class="step" data-aos="fade-up"><i class="fas fa-utensils"></i> <p>Válassz ételt 🍕</p></div>
    <div class="step" data-aos="fade-up"><i class="fas fa-map-marker-alt"></i> <p>Add meg a címed 📍</p></div>
    <div class="step" data-aos="fade-up"><i class="fas fa-credit-card"></i> <p>Fizess online vagy készpénzben 💳</p></div>
    <div class="step" data-aos="fade-up"><i class="fas fa-smile"></i> <p>Élvezd az ételt 😋</p></div>
</div>

<div class="quiz-wrapper">
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
</div>
    

    <div class="shaker-master-container">
    <div class="shaker-master">
        <div class="shaker-slides">
            <div class="shaker-slide">
                <div class="shaker-text">
                    <h3>Shaker 1: Classic</h3>
                    <p>Próbáld ki a klasszikus shakerünket!</p>
                    <a href="#" class="shaker-btn">Vásárlás</a>
                </div>
                <div class="shaker-image">
                    <img src="../kepek/shakek/karamellasshake.jpg" alt="Classic Shaker">
                </div>
            </div>
            <div class="shaker-slide">
                <div class="shaker-text">
                    <h3>Shaker 2: Neon</h3>
                    <p>Világítsd fel a bulit!</p>
                    <a href="#" class="shaker-btn">Vásárlás</a>
                </div>
                <div class="shaker-image">
                    <img src="../kepek/shakek/karamellasshake.jpg" alt="Neon Shaker">
                </div>
            </div>
            <div class="shaker-slide">
                <div class="shaker-text">
                    <h3>Shaker 3: Premium</h3>
                    <p>A profik választása!</p>
                    <a href="#" class="shaker-btn">Vásárlás</a>
                </div>
                <div class="shaker-image">
                    <img src="../kepek/shakek/karamellasshake.jpg" alt="Premium Shaker">
                </div>
            </div>
        </div>
        <button class="shaker-prev"><i class="fas fa-chevron-left"></i></button>
        <button class="shaker-next"><i class="fas fa-chevron-right"></i></button>
    </div>
    <div class="shaker-dots">
        <div class="shaker-dot active"></div>
        <div class="shaker-dot"></div>
        <div class="shaker-dot"></div>
    </div>
</div>
    <section class="why-us">
    <h3>Miért minket válassz?</h3>
    <div class="features">
        <div class="feature">
            <div class="icon">⚡</div>
            <p>Villámgyors kiszállítás</p>
        </div>
        <div class="feature">
            <div class="icon">🌿</div>
            <p>Friss alapanyagok</p>
        </div>
        <div class="feature">
            <div class="icon">⭐</div>
            <p>Egyedi ízek</p>
        </div>
    </div>
</section>

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
                © 2025 FlavorWave - Minden jog fenntartva.
            </div>
        </div>
    </div>

    <script src="../js/fooldal/ujfooldal.js">

    </script>
</body>
</html>