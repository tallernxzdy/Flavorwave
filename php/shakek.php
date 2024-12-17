<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../kepek/logo.png" type="image/png">
    <title>FlavorWave - Shakek</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/kategoriaelemek.css">
</head>
<body>
    <nav>
    <div class="logo">
        <a href="kezdolap.php" class="logo">
        <img src="../kepek/logo.png" alt="Flavorwave logó" class="logo-img">
        <h1>FlavorWave</h1>
        </a>
    </div>
    <ul>
        <li><a href="kategoria.php">Kategóriák</a></li>
        <li><a href="menu.php">Menü</a></li>
        <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
        <li><a href="admin_felulet.php">Admin felület</a></li>
        <?php endif; ?>

        <?php if (isset($_SESSION["username"])): ?>
        <li><a href="kijelentkezes.php">Kijelentkezés</a></li>
        <?php else: ?>
        <li><a href="bejelentkezes.php">Bejelentkezés</a></li>
        <li><a href="regisztracio.php">Regisztráció</a></li>
        <?php endif; ?>
    </ul>
    <div class="hamburger">
        <span class="line"></span>
        <span class="line"></span>
        <span class="line"></span>
    </div>
    </nav>

    <div class="menubar">
    <ul>
        <li><a href="menu.php">Menü</a></li>
        <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
        <li><a href="admin_felulet.php">Admin felület</a></li>
        <?php endif; ?>

        <?php if (isset($_SESSION["username"])): ?>
        <li><a href="kijelentkezes.php">Kijelentkezés</a></li>
        <?php else: ?>
        <li><a href="bejelentkezes.php">Bejelentkezés</a></li>
        <li><a href="regisztracio.php">Regisztráció</a></li>
        <?php endif; ?>
    </ul>
    </div>
    
    


    <div class="content-wrapper">
    <main>
        <!-- Tartalom -->
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
            <div class="footer-copy">
                &copy; 2024 FlavorWave - Minden jog fenntartva.
            </div>
        </div>
    </footer>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/navbar.js"></script>
</body>
</html>