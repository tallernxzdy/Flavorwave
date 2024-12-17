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
    <title>FlavorWave</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/carousel.css">
    <link rel="stylesheet" href="../css/parallax.css">

</head>
<body>

    <!-- NAVBAR -->
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
</div>



<!-- gördülő kép -->
<div class="parallax" id="parallax"></div>
<div class="main-content">
  <h2>Üdvözöljük a Flavorwave oldalon</h2>
  <p>Itt találhatod a legjobb ételeket és ajánlatokat!</p>
</div>


<script>
   document.addEventListener("scroll", function () {
    const parallax = document.querySelector(".parallax");
    const scrollOffset = window.pageYOffset; // Az aktuális görgetési pozíció
    parallax.style.backgroundPositionY = `${scrollOffset * -0.25}px`; // Lassított mozgás
    });

</script>






<div class="main-content">
  <h2>Üdvözöljük a Flavorwave oldalon</h2>
  <?php if (isset($_SESSION["username"])): ?>
    <p>Szia, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</p>
  <?php else: ?>
    <p>Jelentkezz be vagy regisztrálj a fiókod eléréséhez!</p>
  <?php endif; ?>
</div>


<div class="slider">
  <input type="radio" name="toggle" id="btn-1" checked>
  <input type="radio" name="toggle" id="btn-2">
  <input type="radio" name="toggle" id="btn-3">

  <div class="slider-controls">
    <label for="btn-1"></label>
    <label for="btn-2"></label>
    <label for="btn-3"></label>
  </div>

  <ul class="slides">
    <li class="slide">
      <div class="slide-content">
        <h2 class="slide-title">Slide #1</h2>
        <p class="slide-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quaerat dignissimos commodi eos totam perferendis possimus dolorem, deleniti vitae harum? Enim.</p>
        <a href="#" class="slide-link">Learn more</a>
      </div>
      <p class="slide-image">
        <img src="../kepek/pizza2.jpg" alt="stuff" width="320" height="240">
      </p>
    </li>
    <li class="slide">
      <div class="slide-content">
        <h2 class="slide-title">Slide #2</h2>
        <p class="slide-text">Nisi ratione magni ea quis animi incidunt velit voluptate dolorem enim possimus, nam provident excepturi ipsam nihil molestiae minus delectus!</p>
        <a href="#" class="slide-link">Amazing deal</a>
      </div>
      <p class="slide-image">
        <img src="../kepek/pizza2.jpg" alt="stuff" width="320" height="240">
      </p>
    </li>
    <li class="slide">
      <div class="slide-content">
        <h2 class="slide-title">Slide #3</h2>
        <p class="slide-text">Quisquam quod ut quasi, vero obcaecati laudantium asperiores corporis ad atque. Expedita fugit dicta maxime vel doloribus sequi, facilis dignissimos.</p>
        <a href="#" class="slide-link">Get started</a>
      </div>
      <p class="slide-image">
        <img src="../kepek/pizza2.jpg" alt="stuff" width="320" height="240">
      </p>
    </li>
  </ul>
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
            &copy; 2024 FlavorWave - Minden jog fenntartva.
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/navbar.js"></script>


</body>
</html>
