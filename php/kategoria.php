<?php
// Munkamenet indítása a fájl elején
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../kepek/logo.png" type="image/png">
    <title>FlavorWave - Menü</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/kategoriak.css">
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
                <a href="rendeles_megtekintes.php" class="order-btn">Rendeléseim</a>
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
                <li><a href="rendelesek_megtekintes.php">Rendeléseim</a></li>
            </ul>
        </div>
    <div class="content-wrapper">

        <h1>Kategoriák</h1>
        <div class="flex-grid">
            <div class="flex-col">
                <a href="hamburger.php" class="menu__option">
                    <img src="../kepek/hamburgerek/standardburger.jpg" alt="Hamburgerek">
                    <h2>Hamburgerek</h2>
                </a>
            </div>
            <div class="flex-col">
                <a href="pizza.php" class="menu__option">
                    <img src="../kepek/pizzak/pizza.jpg" alt="Pizzák">
                    <h2>Pizzák</h2>
                </a>
            </div>
            <div class="flex-col">
                <a href="hotdog.php" class="menu__option">
                    <img src="../kepek/hot-dogok/simahotdog.jpg" alt="Hot-dogok">
                    <h2>Hot-dogok</h2>
                </a>
            </div>
            <div class="flex-col">
                <a href="koretek.php" class="menu__option">
                    <img src="../kepek/koretek/hasabburgonya.jpg" alt="Köretek">
                    <h2>Köretek</h2>
                </a>
            </div>
            <div class="flex-col">
                <a href="desszertek.php" class="menu__option">
                    <img src="../kepek/desszertek/almaspite.jpeg" alt="Desszertek">
                    <h2>Desszertek</h2>
                </a>
            </div>
            <div class="flex-col">
                <a href="shakek.php" class="menu__option">
                    <img src="../kepek/shakek/vaniliasshake.jpg" alt="Shakek">
                    <h2>Shakek</h2>
                </a>
            </div>
            <div class="flex-col">
                <a href="italok.php" class="menu__option">
                    <img src="../kepek/italok/fanta.jpg" alt="Italok">
                    <h2>Italok</h2>
                </a>
            </div>
        </div>

        </main>

        <footer class="footer">
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
                <div class="footer-copy">&copy; 2024 FlavorWave - Minden jog fenntartva.</div>
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/navbar.js"></script>
</body>

</html>