<?php
session_start();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Flavorwave</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-container">
            <ul class="navbar_ul">
                <li><a href="kezdolap.php" class="logo">🌊 Flavorwave</a></li>
                <li><a class="navbar_link" href="kezdolap.php">Főoldal</a></li>
                <li><a class="navbar_link" href="menu.php">Menü</a></li>

                <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
                    <li><a class="navbar_link" href="admin_felulet.php">Admin felület</a></li>
                <?php endif; ?>

                <?php if (isset($_SESSION["username"])): ?>
                    <li class="navbar_link logout"><a href="kijelentkezes.php">Kijelentkezés</a></li>
                <?php else: ?>
                    <li class="navbar_link login"><a href="bejelentkezes.php">Bejelentkezés</a></li>
                    <li class="navbar_link register"><a href="regisztracio.php">Regisztráció</a></li>
                <?php endif; ?>
                
                <!-- Hamburger menü -->
                <li><div class="hamburger-menu" onclick="toggleMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </div></li>
            </ul>

            
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <main>
        <h2>Üdvözöljük a Flavorwave oldalon</h2>
        <?php if (isset($_SESSION["username"])): ?>
            <p>Szia, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</p>
        <?php else: ?>
            <p>Jelentkezz be vagy regisztrálj a fiókod eléréséhez!</p>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <!-- Footer linkek -->
            <ul class="footer-links">
                <li><a href="kezdolap.php">Kezdőlap</a></li>
                <li><a href="menu.php">Menü</a></li>
                <li><a href="rolunk.php">Rólunk</a></li>
                <li><a href="kapcsolat.php">Kapcsolat</a></li>
                <li><a href="adatvedelem.php">Adatvédelem</a></li>
            </ul>

            <!-- Social media ikonok -->
            <div class="footer-socials">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>

            <!-- Copyright rész -->
            <div class="footer-copy">
                &copy; 2024 FlavorWave - Minden jog fenntartva. | <a href="aszf.php">ÁSZF</a>
            </div>
        </div>
    </footer>

</body>
</html>
