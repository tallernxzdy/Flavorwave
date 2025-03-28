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
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/fooldal/hero.css">
    <link rel="stylesheet" href="../css/fooldal/kupon.css">
    <link rel="stylesheet" href="../css/fooldal/hetiajanlat.css">
    <link rel="stylesheet" href="../css/fooldal/rendeles_lepesek.css">
    <link rel="stylesheet" href="../css/fooldal/etelajanlo.css">
    <link rel="stylesheet" href="../css/fooldal/shakek.css">
    <link rel="stylesheet" href="../css/fooldal/whyus.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/fooldal/nepszeruetelek.css">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <title>FlavorWave</title>
</head>

<body>

    <?php
    include 'adatbazisra_csatlakozas.php';
    include './navbar.php';
    ?>


    <div class="hero">
        <video autoplay loop muted playsinline class="hero-video">
            <source src="../kepek/hambi2.mp4" type="video/mp4">
        </video>
        <div class="hero-content">
            <h1>Friss, Forró, Finom</h1>
            <p>Rendelj kedvenc ételeid közül gyorsan és egyszerűen!</p>
            <div class="cta-buttons">
                <a href="rendeles_megtekintes.php" class="order-now">Rendelj Most</a>
                <a href="kategoria.php" class="view-menu">Tekintsd meg a Menüt</a>
            </div>
        </div>
    </div>

    <div class="coupon-slider">
        <div class="slides">
            <div class="slide">
                <div class="text">
                    <h3>25% minden Pizzából!</h3>
                    <p>Csak Ma! Használd a kuponkódunkat: <strong>Pizza25</strong></p>
                    <a href="../php/pizza.php" class="btn">Rendelj most</a>
                </div>
                <div class="image">
                    <img src="../kepek/pizzak/pizza3.jpg" alt="Burger Deal">
                </div>
            </div>
            <div class="slide">
                <div class="text">
                    <h3>Ingyenes Desszert!</h3>
                    <p>Minden 10.000Ft-s rendelés felett egy általad választható desszertet adunk, teljesen ingyen!</p>
                    <a href="../php/desszertek.php" class="btn">Kérem a desszertet!</a>
                </div>
                <div class="image">
                    <img src="../kepek/desszertek/almaspite.jpeg" alt="Dessert Offer">
                </div>
            </div>
            <div class="slide">
                <div class="text">
                    <h3>Kettőt fizet, hármat kap!</h3>
                    <p>Minden 2 darab megrendelt pizza mellé szabadon választhatsz egy harmadikat. Használd a kuponunkat a beváltáshoz: <strong>PIZZADEAL</strong></p>
                    <a href="../php/pizza.php" class="btn">Ragadd meg az alkalmat</a>
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
    <br>
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
                <img src="../kepek/pizzak/gombaspizza.jpg" alt="Ajánlat 1">
                <img src="../kepek/pizzak/hawaiipizza.jpg" alt="Ajánlat 2">
                <img src="../kepek/pizzak/pizza2.jpg" alt="Ajánlat 3">
            </div>
            <div class="image-slider" id="image-slider-2">
                <img src="../kepek/pizzak/margherita.jpg" alt="Ajánlat 4">
                <img src="../kepek/pizzak/pizza4.jpg" alt="Ajánlat 5">
                <img src="../kepek/pizzak/sonkaspizza.jpg" alt="Ajánlat 6">
            </div>
        </div>
        <p>Ne maradj le! Az ajánlat visszaszámlálás alatt érhető el.</p>
        <a href="menu.php" class="cta-button primary-bttn">Fedezd fel az ajánlatokat!</a>
    </section>

    <br>
    <br>

    <div class="steps-container">
        <div class="step" data-aos="fade-up"><i class="fas fa-utensils"></i>
            <p>Válassz ételt 🍕</p>
        </div>
        <div class="step" data-aos="fade-up"><i class="fas fa-map-marker-alt"></i>
            <p>Add meg a címed 📍</p>
        </div>
        <div class="step" data-aos="fade-up"><i class="fas fa-credit-card"></i>
            <p>Fizess bankkártyával vagy készpénzben 💳</p>
        </div>
        <div class="step" data-aos="fade-up"><i class="fas fa-smile"></i>
            <p>Élvezd az ételt 😋</p>
        </div>
    </div>

    <br>
    <br>

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
                    <button data-spice="mild">Enyhén</button>
                    <button data-spice="medium">Közepesen</button>
                    <button data-spice="hot">Nagyon</button>
                </div>
            </div>
            <div class="quiz-question">
                <i class="fas fa-cheese"></i>
                <h2>Mennyi sajtot szeretnél?</h2>
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
                    <img id="food-image" src="../kepek/pizzak/pizza2.jpg" alt="Ajánlott étel">
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
                        <h3>Epres shake</h3>
                        <p>A gyümölcsök kedvelőinek</p>
                        <a href="#" class="shaker-btn">Vásárlás</a>
                    </div>
                    <div class="shaker-image">
                        <img src="../kepek/shakek/epresshake.jpg" alt="Epres shake">
                    </div>
                </div>
                <div class="shaker-slide">
                    <div class="shaker-text">
                        <h3>Csokis shake</h3>
                        <p>A Csokoládé igazi fanatikusainak, három féle csokival.</p>
                        <a href="#" class="shaker-btn">Vásárlás</a>
                    </div>
                    <div class="shaker-image">
                        <img src="../kepek/shakek/csokisshake.jpg" alt="Csokis Shake">
                    </div>
                </div>
                <div class="shaker-slide">
                    <div class="shaker-text">
                        <h3>Karamellás shake</h3>
                        <p>Az igazán édes szájúaknak</p>
                        <a href="#" class="shaker-btn">Vásárlás</a>
                    </div>
                    <div class="shaker-image">
                        <img src="../kepek/shakek/karamellasshake.jpg" alt="Karamellás shake">
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

    <!-- Új szekció: Népszerű Ételeink Galériája -->
    <section class="popular-foods-gallery">
        <div class="container">
            <h2 class="gallery-title">
                Népszerű Ételeink
            </h2>
            <div class="food-gallery-grid">
                <div class="food-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="food-image">
                        <img src="../kepek/pizzak/margherita.jpg" alt="Margherita Pizza">
                    </div>
                    <div class="food-content">
                        <h3>Margherita Pizza</h3>
                        <p>Klasszikus olasz pizza friss paradicsommal és mozzarellával.</p>
                        <div class="food-rating">
                            <span class="star">★</span>
                            <span class="star">★</span>
                            <span class="star">★</span>
                            <span class="star">★</span>
                            <span class="star">★</span>
                        </div>
                        <a href="kategoria.php" class="order-btn">Rendelés</a>
                    </div>
                </div>
                <div class="food-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="food-image">
                        <img src="../kepek/pizzak/sonkaspizza.jpg" alt="Sonkás Pizza">
                    </div>
                    <div class="food-content">
                        <h3>Sonkás Pizza</h3>
                        <p>Ízletes sonka, mozzarella és paradicsomszósz tökéletes harmóniája.</p>
                        <div class="food-rating">
                            <span class="star">★</span>
                            <span class="star">★</span>
                            <span class="star">★</span>
                            <span class="star">★</span>
                            <span class="star">☆</span>
                        </div>
                        <a href="kategoria.php" class="order-btn">Rendelés</a>
                    </div>
                </div>
                <div class="food-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="food-image">
                        <img src="../kepek/desszertek/almaspite.jpeg" alt="Almás Pite">
                    </div>
                    <div class="food-content">
                        <h3>Almás Pite</h3>
                        <p>Frissen sült almás pite, fahéjjal és vaníliafagylalttal.</p>
                        <div class="food-rating">
                            <span class="star">★</span>
                            <span class="star">★</span>
                            <span class="star">★</span>
                            <span class="star">★</span>
                            <span class="star">★</span>
                        </div>
                        <a href="kategoria.php" class="order-btn">Rendelés</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="feedback-section">
        <?php if (isset($_SESSION['felhasznalo_id'])): ?>
            <a href="visszajelzesek.php" class="primary-bttn">Küldd el a véleményed!</a>
        <?php else: ?>
            <p>Kérjük, <a href="bejelentkezes.php">jelentkezz be</a>, hogy véleményt írhass!</p>
        <?php endif; ?>
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

    <script src="../js/fooldal/ujfooldal.js"></script>
    <script src="../js/navbar.js"></script>
</body>

</html>