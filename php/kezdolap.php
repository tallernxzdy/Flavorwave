<?php
session_start();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Flavorwave</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
    <div class="navbar-container">
        <!-- Bal oldali elemek: Flavorwave és Menü -->
        <div class="navbar_left">
            
            <ul class="navbar_ul">
                <li><a href="kezdolap.php" class="logo">🌊 Flavorwave</a></li>
                <li><a class="navbar_link" href="menu.php">Menü</a></li>
                
                <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
                    <li><a class="navbar_link" href="admin_felulet.php">Admin felület</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Jobb oldali gombok: Bejelentkezés, Regisztráció, Kijelentkezés -->
        <div class="navbar_right">
            <?php if (isset($_SESSION["username"])): ?>
                <a class="navbar_link logout" href="kijelentkezes.php">Kijelentkezés</a>
            <?php else: ?>
                <a class="navbar_link login" href="bejelentkezes.php">Bejelentkezés</a>
                <a class="navbar_link register" href="regisztracio.php">Regisztráció</a>
            <?php endif; ?>
        </div>
    </div>
            
</nav>


<div id="carouselExampleCaptions" class="carousel slide">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="../kepek/pizza2.jpg" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h5>First slide label</h5>
        <p>Some representative placeholder content for the first slide.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="../kepek/pizza2.jpg" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h5>Second slide label</h5>
        <p>Some representative placeholder content for the second slide.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="../kepek/pizza2.jpg" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h5>Third slide label</h5>
        <p>Some representative placeholder content for the third slide.</p>
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>











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
