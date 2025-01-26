<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="icon" href="../kepek/logo.png" type="image/png">
    <title>FlavorWave | Menü</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/menu.css">
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

    <?php if (isset($_SESSION["felhasznalo_nev"])): ?>
      <li><a href="kijelentkezes.php">Kijelentkezés</a></li>
    <?php else: ?>
      <li><a href="bejelentkezes.php">Bejelentkezés</a></li>
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

    <?php if (isset($_SESSION["felhasznalo_nev"])): ?>
      <li><a href="kijelentkezes.php">Kijelentkezés</a></li>
    <?php else: ?>
      <li><a href="bejelentkezes.php">Bejelentkezés</a></li>
    <?php endif; ?>
  </ul>
</div>







<div class="container my-5">
    <div class="row g-4">
      <!-- Kártya 1 -->
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="card hover-card">
          <img src="../kepek/pizza.jpg" class="card-img-top" alt="Pizza kép">
          <div class="card-body text-center">
            <h5 class="card-title">Pizza</h5>
            <p class="card-text">Rövid leírás az ételről.</p>
            <button class="modern-btn details-btn" data-bs-toggle="modal" data-bs-target="#modal-1">Részletek</button>
          </div>
        </div>
      </div>

      <!-- Kártya 2 -->
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="card hover-card">
          <img src="../kepek/pizza.jpg" class="card-img-top" alt="Saláta kép">
          <div class="card-body text-center">
            <h5 class="card-title">Saláta</h5>
            <p class="card-text">Friss saláták keveréke.</p>
            <button class="modern-btn details-btn" data-bs-toggle="modal" data-bs-target="#modal-2">Részletek</button>
          </div>
        </div>
      </div>

      <!-- Kártya 3 -->
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="card hover-card">
          <img src="../kepek/pizza.jpg" class="card-img-top" alt="Szendvics kép">
          <div class="card-body text-center">
            <h5 class="card-title">Szendvics</h5>
            <p class="card-text">Ropogós kenyérrel.</p>
            <button class="modern-btn details-btn" data-bs-toggle="modal" data-bs-target="#modal-3">Részletek</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODÁLIS 1 -->
  <div class="modal fade custom-fade" id="modal-1" tabindex="-1" aria-labelledby="modalLabel-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel-1">Pizza - Részletek</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <img src="../kepek/pizza.jpg" class="img-fluid mb-3" alt="Pizza kép">
          <p><strong>Kalória:</strong> 450 kcal</p>
          <p><strong>Összetevők:</strong> Paradicsom, Mozzarella, Bazsalikom</p>
          <p><strong>Ár:</strong> 2500 Ft</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="modern-btn add-to-cart" data-item="Pizza">Kosárba rakás</button>
          <button type="button" class="modern-btn close-btn" data-bs-dismiss="modal">Bezárás</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODÁLIS 2 -->
  <div class="modal fade custom-fade" id="modal-2" tabindex="-1" aria-labelledby="modalLabel-2" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel-2">Saláta - Részletek</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <img src="../kepek/salata.jpg" class="img-fluid mb-3" alt="Saláta kép">
          <p><strong>Kalória:</strong> 150 kcal</p>
          <p><strong>Összetevők:</strong> Salátakeverék, Olívaolaj</p>
          <p><strong>Ár:</strong> 1500 Ft</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="modern-btn add-to-cart" data-item="Saláta">Kosárba rakás</button>
          <button type="button" class="modern-btn close-btn" data-bs-dismiss="modal">Bezárás</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODÁLIS 3 -->
  <div class="modal fade custom-fade" id="modal-3" tabindex="-1" aria-labelledby="modalLabel-3" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel-3">Szendvics - Részletek</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <img src="../kepek/szendvics.jpg" class="img-fluid mb-3" alt="Szendvics kép">
          <p><strong>Kalória:</strong> 300 kcal</p>
          <p><strong>Összetevők:</strong> Kenyér, Sonka, Sajt</p>
          <p><strong>Ár:</strong> 2000 Ft</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="modern-btn add-to-cart" data-item="Szendvics">Kosárba rakás</button>
          <button type="button" class="modern-btn close-btn" data-bs-dismiss="modal">Bezárás</button>
        </div>
      </div>
    </div>
  </div>

  <!-- KOSÁRBA RAKÁS ÜZENET -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toast-added" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          Hozzáadva a kosárhoz!
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Bezárás"></button>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Kosárba rakás logika
      document.querySelectorAll(".add-to-cart").forEach(function (button) {
        button.addEventListener("click", function () {
          const toastEl = document.getElementById("toast-added");
          const toast = new bootstrap.Toast(toastEl);
          toast.show();
        });
      });
    });
  </script>





    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <!-- Footer linkek -->
            <ul class="footer-links">
                <li><a href="../html/rolunk.html">Rólunk</a></li>
                <li><a href="../html/kapcsolat.html">Kapcsolat</a></li>
                <li><a href="../html/adatvedelem.html">Adatvédelem</a></li>
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
                &copy; 2024 FlavorWave - Minden jog fenntartva.
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/navbar.js"></script>
</body>
</html>
