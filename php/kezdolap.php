<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">
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
            </ul>
            <div class="right_links">
                <?php if (isset($_SESSION["username"])): ?>
                    <a class="navbar_link logout" href="kijelentkezes.php">Kijelentkezés</a>
                <?php else: ?>
                    <a class="navbar_link login" href="bejelentkezes.php">Bejelentkezés</a>
                    <a class="navbar_link register" href="regisztracio.php">Regisztráció</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

<!-- Üdvözlő üzenet -->
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

    
</body>
</html>
