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
    <link rel="stylesheet" href="../css/fooldal/etelajanlo.css">

    <link rel="stylesheet" href="../css/fooldal/hetiajanlat.css">
    <link rel="stylesheet" href="../css/fooldal/kupon.css">
    <link rel="stylesheet" href="../css/fooldal/rendeles_lepesek.css">
    <link rel="stylesheet" href="../css/fooldal/shakek.css">
    <link rel="stylesheet" href="../css/fooldal/hero.css">



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
        <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 2): ?>
            <a href="dolgozoi_felulet.php">Dolgozoi felulet</a>
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


<br>


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
    <button class="prev">❮</button>
    <button class="next">❯</button>
</div>

<div class="dots">
    <div class="dot active"></div>
    <div class="dot"></div>
    <div class="dot"></div>
</div>
<br>



    <div class="steps-container">
        <div class="step"><i class="fas fa-utensils"></i> <p>Válassz ételt 🍕</p></div>
        <div class="step"><i class="fas fa-map-marker-alt"></i> <p>Add meg a címed 📍</p></div>
        <div class="step"><i class="fas fa-credit-card"></i> <p>Fizess online vagy készpénzben 💳</p></div>
        <div class="step"><i class="fas fa-smile"></i> <p>Élvezd az ételt 😋</p></div>
    </div>


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
        <button class="shaker-prev">❮</button>
        <button class="shaker-next">❯</button>
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
            © 2025 FlavorWave - Minden jog fenntartva.
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/navbar.js"></script>
    <script src="../js/fooldal/hetiajanlat.js"></script>
    <script src="../js/fooldal/kupon.js"></script>
    <script src="../js/fooldal/rendeles_lepesek.js"></script>
    <script src="../js/fooldal/etelajanlo.js"></script>
    <script src="../js/fooldal/shakek.js"></script>


</body>
</html>