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
    <title>Flavorwave</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav>
  <div class="logo">
    <a href="kezdolap.php" class="logo">🌊 Flavorwave</a>
  </div>
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






<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="../kepek/pizza.jpg" class="d-block w-100" alt="First slide">
      <div class="carousel-caption d-flex align-items-center justify-content-center h-100">
        <main>
          <h2>Üdvözöljük a Flavorwave oldalon</h2>
          <?php if (isset($_SESSION["username"])): ?>
              <p>Szia, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</p>
          <?php else: ?>
              <p>Jelentkezz be vagy regisztrálj a fiókod eléréséhez!</p>
          <?php endif; ?>
        </main>
      </div>
    </div>
    <div class="carousel-item">
      <img src="../kepek/pizza2.jpg" class="d-block w-100" alt="Second slide">
      <div class="carousel-caption d-flex align-items-center justify-content-center h-100">
        <main>
          <h2>Üdvözöljük a Flavorwave oldalon</h2>
          <?php if (isset($_SESSION["username"])): ?>
              <p>Szia, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</p>
          <?php else: ?>
              <p>Jelentkezz be vagy regisztrálj a fiókod eléréséhez!</p>
          <?php endif; ?>
        </main>
      </div>
    </div>
    <div class="carousel-item">
      <img src="../kepek/pizza3.jpg" class="d-block w-100" alt="Third slide">
      <div class="carousel-caption d-flex align-items-center justify-content-center h-100">
        <main>
          <h2>Üdvözöljük a Flavorwave oldalon</h2>
          <?php if (isset($_SESSION["username"])): ?>
              <p>Szia, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</p>
          <?php else: ?>
              <p>Jelentkezz be vagy regisztrálj a fiókod eléréséhez!</p>
          <?php endif; ?>
        </main>
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


<h1>Hello itt lesz valami!
</h1>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/navbar.js"></script>
</body>
</html>
