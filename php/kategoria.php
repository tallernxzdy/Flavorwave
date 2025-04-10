<?php
// Munkamenet indítása a fájl elején
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <title>FlavorWave - Kategóriák</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/kategoriak.css">
    <link rel="stylesheet" href="../css/fooldal/ujfooldal.css">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
</head>

<body>
    <?php
        include './navbar.php';
    ?>

    <div class="content-wrapper">
        <main>
            <div class="title-container">
                <h1 class="page-title" id="cim">Kategóriák</h1>
            </div>
            <div class="flex-grid">
                <div class="flex-col" data-aos="fade-up" data-aos-delay="100">
                    <a href="hamburger.php" class="menu__option">
                        <div class="image-wrapper">
                            <img src="../kepek/3/standardburger.jpg" alt="Hamburgerek">
                        </div>
                        <div class="popular-label">Népszerű</div>
                        <h2>Hamburgerek</h2>
                        <div class="title-underline"></div>
                        <span class="order-btn">Rendelj most</span>
                    </a>
                </div>
                <div class="flex-col" data-aos="fade-up" data-aos-delay="200">
                    <a href="pizza.php" class="menu__option">
                        
                        <div class="image-wrapper">
                            <img src="../kepek/4/pizza.jpg" alt="Pizzák">
                        </div>
                        <div class="popular-label">Népszerű</div>
                        <h2>Pizzák</h2>
                        <div class="title-underline"></div>
                        <span class="order-btn">Rendelj most</span>
                    </a>
                </div>
                <div class="flex-col" data-aos="fade-up" data-aos-delay="300">
                    <a href="hotdog.php" class="menu__option">
                        <div class="image-wrapper">
                            <img src="../kepek/5/simahotdog.jpg" alt="Hot-dogok">
                        </div>
                        <h2>Hot-dogok</h2>
                        <div class="title-underline"></div>
                        <span class="order-btn">Rendelj most</span>
                    </a>
                </div>
                <div class="flex-col" data-aos="fade-up" data-aos-delay="400">
                    <a href="koretek.php" class="menu__option">
                        <div class="image-wrapper">
                            <img src="../kepek/6/hasabburgonya.jpg" alt="Köretek">
                        </div>
                        <h2>Köretek</h2>
                        <div class="title-underline"></div>
                        <span class="order-btn">Rendelj most</span>
                    </a>
                </div>
                <div class="flex-col" data-aos="fade-up" data-aos-delay="500">
                    <a href="desszertek.php" class="menu__option">
                        <div class="image-wrapper">
                            <img src="../kepek/7/almaspite.jpeg" alt="Desszertek">
                        </div>
                        <h2>Desszertek</h2>
                        <div class="title-underline"></div>
                        <span class="order-btn">Rendelj most</span>
                    </a>
                </div>
                <div class="flex-col" data-aos="fade-up" data-aos-delay="600">
                    <a href="shakek.php" class="menu__option">
                        <div class="image-wrapper">
                            <img src="../kepek/8/vaniliasshake.jpg" alt="Shakek">
                        </div>
                        <h2>Shakek</h2>
                        <div class="title-underline"></div>
                        <span class="order-btn">Rendelj most</span>
                    </a>
                </div>
                <div class="flex-col" data-aos="fade-up" data-aos-delay="700">
                    <a href="italok.php" class="menu__option">
                        <div class="image-wrapper">
                            <img src="../kepek/9/fanta.jpg" alt="Italok">
                        </div>
                        <h2>Italok</h2>
                        <div class="title-underline"></div>
                        <span class="order-btn">Rendelj most</span>
                    </a>
                </div>
            </div>
        </main>

        <div class="footer">
            <div class="footer-container">
                <ul class="footer-links">
                    <li><a href="../html/rolunk.html">Rólunk</a></li>
                    <li><a href="../html/kapcsolatok.html">Kapcsolat</a></li>
                    <li><a href="../html/adatvedelem.html">Adatvédelem</a></li>
                </ul>
                <div class="footer-socials">
                    <a href="https://www.facebook.com/" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.instagram.com/" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://x.com/" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.youtube.com/" target="_blank"><i class="fab fa-youtube"></i></a>
                </div>
                <div class="footer-copy">
                    © 2024 FlavorWave - Minden jog fenntartva.
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/navbar.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>