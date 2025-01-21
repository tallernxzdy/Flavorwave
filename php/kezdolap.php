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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <title>FlavorWave</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/carousel.css">
    <link rel="stylesheet" href="../css/parallax.css">
    <link rel="stylesheet" href="../css/kezdolap.css">


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
    </div>

    <!-- Jobb oldalon a gombok -->
    <div class="navbar-buttons">
        <?php if (isset($_SESSION["username"])): ?>
            <a href="kijelentkezes.php" class="login-btn">Kijelentkezés</a>
        <?php else: ?>
            <a href="bejelentkezes.php" class="login-btn">Bejelentkezés</a>
        <?php endif; ?>
        <a href="rendeles.php" class="order-btn">Rendelés</a>
        <a href="kosar.php" class="cart-btn">
            <img src="../kepek/kosar.png" alt="Kosár" class="cart-icon">
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
        <li><a href="kezdolap.php">FlavorWave</a></li>
        <li><a href="kategoria.php">Menü</a></li>
        <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
            <li><a href="admin_felulet.php">Admin felület</a></li>
        <?php endif; ?>
        <li><a href="kosar.php">Kosár</a></li>
        <?php if (isset($_SESSION["username"])): ?>
            <li><a href="kijelentkezes.php">Kijelentkezés</a></li>
        <?php else: ?>
            <li><a href="bejelentkezes.php">Bejelentkezés</a></li>
        <?php endif; ?>
    </ul>
</div>

<br><br><br><br>


















<section id="weekly-deals">
    <h2>Heti Ajánlat Vége:</h2>
    <div class="countdown" id="countdown">
        <span class="days">00</span> nap
        <span class="hours">00</span> óra
        <span class="minutes">00</span> perc
        <span class="seconds">00</span> másodperc
    </div>
    <div class="sliders-container">
        <div class="image-slider" id="image-slider-1">
            <img src="../kepek/pizza4.jpg" alt="Ajánlat 1">
            <img src="../kepek/pizza2.jpg" alt="Ajánlat 2">
            <img src="../kepek/pizza3.jpg" alt="Ajánlat 3">
        </div>
        <div class="image-slider" id="image-slider-2">
            <img src="../kepek/pizza4.jpg" alt="Ajánlat 4">
            <img src="../kepek/pizza2.jpg" alt="Ajánlat 5">
            <img src="../kepek/pizza3.jpg" alt="Ajánlat 6">
        </div>
    </div>
    <p>Ne maradj le! Az ajánlat visszaszámlálás alatt érhető el.</p>
    <a href="menu.php" class="cta-button">Fedezd fel az ajánlatokat!</a>
</section>

<style>
  .sliders-container {
    display: flex;
    justify-content: flex-start; /* A slider-ek egymás mellé helyezése */
    gap: 20px; /* Nincs távolság a slider-ek között */
    margin: 0 auto; /* Középre igazítás */
    width: 60%; /* A konténer szélessége 100% */
}

/* Slider stílus */
.image-slider {
    position: relative;
    width: 50%; /* A slider szélességét 50%-ra állítjuk */
    max-width: none; /* Ne legyen max szélesség */
    margin: 20px 0; /* Ne legyen margó */
    overflow: hidden;
    border-radius: 15px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}
/* Kép stílus */
.image-slider img {
    width: 100%;
    height: auto;
    display: none;
    animation: fadeIn 1s ease-in-out;
}

.image-slider img.active {
    display: block;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Gomb stílus */
.cta-button {
    display: inline-block;
    margin-top: 20px;
    padding: 15px 30px;
    font-size: 18px;
    font-weight: bold;
    color: #fff;
    background: linear-gradient(45deg, #ff7e5f, #feb47b);
    border: none;
    border-radius: 50px;
    text-decoration: none;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s, box-shadow 0.3s;
}

.cta-button:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 25px rgba(0, 0, 0, 0.3);
    background: linear-gradient(45deg, #feb47b, #ff7e5f);
}

.cta-button:active {
    transform: translateY(0);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

</style>

<script>
// Visszaszámláló logika
const countdown = document.getElementById('countdown');
const endDate = new Date();
endDate.setDate(endDate.getDate() + 7); // Heti ajánlat 7 nap múlva véget ér

function updateCountdown() {
    const now = new Date();
    const timeLeft = endDate - now;

    if (timeLeft <= 0) {
        countdown.innerHTML = '<strong>Az ajánlat véget ért!</strong>';
        return;
    }

    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

    countdown.querySelector('.days').textContent = String(days).padStart(2, '0');
    countdown.querySelector('.hours').textContent = String(hours).padStart(2, '0');
    countdown.querySelector('.minutes').textContent = String(minutes).padStart(2, '0');
    countdown.querySelector('.seconds').textContent = String(seconds).padStart(2, '0');
}

setInterval(updateCountdown, 1000);

// Váltakozó képek logika
function changeImage(sliderId) {
    const images = document.querySelectorAll(`#${sliderId} img`);
    let currentIndex = 0;

    function switchImage() {
        images[currentIndex].classList.remove('active');
        currentIndex = (currentIndex + 1) % images.length;
        images[currentIndex].classList.add('active');
    }

    images[currentIndex].classList.add('active'); // Induló kép
    setInterval(switchImage, 3000); // 3 másodpercenként vált
}

// Kezdés a két sliderrel
changeImage('image-slider-1');
changeImage('image-slider-2');

</script>





























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


<!-- próba innentől -->

<div class="container mt-5 text-center">
    <button id="feedbackBtn" class="btn btn-primary">Vélemény írása</button>
  </div>

  <!-- Vélemény kártya -->
  <div id="feedbackCard" class="card shadow p-4 position-fixed top-50 start-50 translate-middle d-none" style="width: 400px;">
    <h5 class="card-title">Értékeld az alkalmazást</h5>
    <div class="card-body">
      <!-- Pontszám választás -->
      <div class="mb-3">
        <label class="form-label">Pontszám (1-10):</label>
        <div id="ratingButtons" class="d-flex justify-content-between">
          <!-- Gombokat JavaScript tölti be -->
        </div>
      </div>

      <!-- Vélemény szöveg -->
      <div class="mb-3">
        <label for="opinionText" class="form-label">Vélemény:</label>
        <textarea id="opinionText" class="form-control" rows="3" placeholder="Írd ide a véleményedet..."></textarea>
      </div>

      <!-- Email cím -->
      <div class="mb-3">
        <label for="emailInput" class="form-label">Email cím (nem kötelező):</label>
        <input type="email" id="emailInput" class="form-control" placeholder="email@example.com">
      </div>

      <!-- Gombok -->
      <div class="d-flex justify-content-between">
        <button id="submitFeedback" class="btn btn-success">Küldés</button>
        <button id="closeCard" class="btn btn-danger">Bezárás</button>
      </div>
    </div>
  </div>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const feedbackBtn = document.getElementById("feedbackBtn");
    const feedbackCard = document.getElementById("feedbackCard");
    const closeCard = document.getElementById("closeCard");
    const ratingButtons = document.getElementById("ratingButtons");
    const submitFeedback = document.getElementById("submitFeedback");

    // Pontszám gombok létrehozása
    for (let i = 1; i <= 10; i++) {
      const btn = document.createElement("button");
      btn.className = "btn btn-outline-primary btn-sm";
      btn.textContent = i;
      btn.value = i;
      btn.addEventListener("click", () => {
        document.querySelectorAll("#ratingButtons button").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
      });
      ratingButtons.appendChild(btn);
    }

    // Kártya megnyitása
    feedbackBtn.addEventListener("click", () => {
      feedbackCard.classList.remove("d-none");
    });

    // Kártya bezárása
    closeCard.addEventListener("click", () => {
      feedbackCard.classList.add("d-none");
    });

    // Vélemény küldése
    submitFeedback.addEventListener("click", () => {
      const rating = document.querySelector("#ratingButtons button.active")?.value || null;
      const opinion = document.getElementById("opinionText").value.trim();
      const email = document.getElementById("emailInput").value.trim();

      if (!rating) {
        alert("Kérlek, válassz pontszámot!");
        return;
      }

      if (!opinion) {
        alert("Kérlek, írj véleményt!");
        return;
      }

      // Adatok küldése PHP-hez
      fetch("visszajelzes_kezelese.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ rating, opinion, email })
      })
      .then(response => {
        console.log(response)
          if (!response.ok) {
              throw new Error(`HTTP hiba! Állapot: ${response.status}`);
          }
          return response.text(); // Ideiglenesen text-ként olvasd be a választ
      })
      .then(data => {
          console.log("Válasz szövegként:", data); // Nézd meg, mit küld vissza a PHP
          return JSON.parse(data); // Ezután próbáld meg JSON-ként értelmezni
      })
      .then(parsedData => {
          alert(parsedData.message);
          if (parsedData.success) {
              feedbackCard.classList.add("d-none");
          }
      })
      .catch(err => console.error("Hiba:", err)); 

    });
  });
</script>



<


<!-- idáig -->






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
