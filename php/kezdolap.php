<?php
session_start();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flavorwave</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="kezdolap.php" class="logo">🌊 Flavorwave</a>
            <ul class="navbar_ul">
                <li><a class="navbar_link special_link kozepso" href="kezdolap.php">Főoldal</a></li>
                <li><a class="navbar_link special_link kozepso" href="menu.php">Menü</a></li>

                <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
                    <li><a class="navbar_link special_link kozepso" href="admin_felulet.php">Admin felület</a></li>
                <?php endif; ?>
            </ul>

        <div class="right_links">
            <?php if (isset($_SESSION["username"])): ?>
                <a class="navbar_link logout" href="kijelentkezes.php">Kijelentkezés</a>
            <?php else: ?>
                <a class="navbar_link login" href="bejelentkezes.php">Bejelentkezés</a>
                <a class="navbar_link register" href="regisztracio.php">Regisztráció</a>
            <?php endif; ?>
            
            <!-- Hamburger menü -->
            <div class="hamburger-menu" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <!-- Legördülő menü -->
            <ul class="dropdown-menu">
                <li><a href="profil.php">Profil</a></li>
                <li><a href="beallitasok.php">Beállítások</a></li>
                <li><a href="#" onclick="toggleDarkMode()">Sötét mód</a></li>
                <li><a href="megrendelesek.php">Megrednelések</a></li>
                <?php if (isset($_SESSION["username"])): ?>
                    <li><a href="kijelentkezes.php">Kijelentkezés</a></li>
                <?php endif; ?>
            </ul>
        </div>

        </div>
    </nav>

    <!-- Debug (opcionális) -->


    <h2>Üdvözöljük a Flavorwave oldalon</h2>
    <?php if (isset($_SESSION["username"])): ?>
        <p>Szia, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</p>
    <?php else: ?>
        <p>Jelentkezz be vagy regisztrálj a fiókod eléréséhez!</p>
    <?php endif; ?>


    <!--Footer-->
        <footer class="footer">
            <div class="footer-container">
                <ul class="footer-links">
                    <li><a href="#">Rólunk</a></li>
                    <li><a href="#">Szolgáltatások</a></li>
                    <li><a href="#">Kapcsolat</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
                <div class="footer-socials">
                    <a href="#" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-linkedin"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                </div>
                <div class="footer-copy">
                    <p>&copy; 2024 <a href="#">Flavorwave</a>. Minden jog fenntartva.</p>
                </div>
            </div>
        </footer>

        <script>
    function toggleMenu() {
        const menu = document.querySelector('.dropdown-menu');
        menu.classList.toggle('active'); // A "dropdown-menu" aktív állapotának váltása
    }

    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode'); // Sötét mód bekapcsolása
    }
</script>
</body>
</html>
