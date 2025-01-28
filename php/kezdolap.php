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
        <?php if (isset($_SESSION["felhasznalo_nev"])): ?>
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
        <?php if (isset($_SESSION["felhasznalo_nev"])): ?>
            <li><a href="kijelentkezes.php">Kijelentkezés</a></li>
        <?php else: ?>
            <li><a href="bejelentkezes.php">Bejelentkezés</a></li>
        <?php endif; ?>
    </ul>
</div>

<br><br><br>












<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Section - Food Delivery</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            overflow-x: hidden;
        }

        .hero {
            position: relative;
            height: 100vh;
            background: url('../kepek/pizza2.jpg') center/cover no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            text-align: center;
            overflow: hidden;
        }

        .hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6); /* Overlay for better text visibility */
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
        }

        .hero h1 {
            font-size: 4.5rem;
            margin-bottom: 20px;
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: 3px;
            animation: fadeInDown 1s ease-out;
            text-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
        }

        .hero p {
            font-size: 1.6rem;
            margin-bottom: 40px;
            line-height: 1.5;
            animation: fadeInUp 1.5s ease-out;
        }

        .hero .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            animation: fadeInUp 2s ease-out;
        }

        .cta-buttons a {
            text-decoration: none;
            padding: 15px 40px;
            font-size: 1.4rem;
            border-radius: 30px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-weight: 700;
        }

        .cta-buttons .order-now {
            background: linear-gradient(135deg, #ff7e5f, #feb47b);
            color: #fff;
            box-shadow: 0 4px 15px rgba(255, 126, 95, 0.5);
        }

        .cta-buttons .order-now:hover {
            background: linear-gradient(135deg, #feb47b, #ff7e5f);
            transform: scale(1.05);
        }

        .cta-buttons .view-menu {
            background: transparent;
            color: #fff;
            border: 2px solid #fff;
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
        }

        .cta-buttons .view-menu:hover {
            background: rgba(255, 255, 255, 0.9);
            color: #000;
            transform: scale(1.05);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="hero">
        <div class="hero-content">
            <h1>Friss, Forró, Finom</h1>
            <p>Rendelj kedvenc ételeid közül gyorsan és egyszerűen! Fedezd fel az ízek világát.</p>
            <div class="cta-buttons">
                <a href="menu.php" class="order-now">Rendelj Most</a>
                <a href="kategoria.php" class="view-menu">Tekintsd meg a Menüt</a>
            </div>
        </div>
    </div>


<div>

    <?php if (isset($_SESSION['felhasznalo_id'])): ?>
        <!-- Ha be van jelentkezve, akkor jelenjen meg a véleményező gomb -->
        <a href="visszajelzesek.php" class="primary-bttn">Küldd el a véleményed!</a>
        <?php else: ?>
            <!-- Ha nincs bejelentkezve, jelenjen meg egy másik üzenet -->
            <p>Kérjük, <a href="bejelentkezes.php">jelentkezz be</a>, hogy véleményt írhass!</p>
            <?php endif; ?>
</div>
            

</body>
</html>


<br>







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


































<style>
    :root {
      --primary-color: #ff758c;
      --secondary-color: #ffe6e9;
      --text-color: #333;
      --button-hover: #ff4a6e;
      --shadow-color: rgba(0, 0, 0, 0.2);
    }

    /* General Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    /* Slider Container */
    .coupon-slider {
      position: relative;
      width: 90%;
      max-width: 1200px;
      margin: 50px auto;
      overflow: hidden;
      border-radius: 25px;
      box-shadow: 0 10px 30px var(--shadow-color);
      background: #fff;
    }

    /* Slides Wrapper */
    .slides {
      display: flex;
      transition: transform 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    }

    /* Individual Slide */
    .slide {
      flex: 0 0 100%;
      display: flex;
      align-items: center;
      color: var(--text-color);
      padding: 20px;
    }

    .slide .text {
      flex: 1;
      padding: 20px;
      text-align: left;
      animation: fadeIn 1s ease;
    }

    .slide h3 {
      font-size: 3rem;
      color: var(--primary-color);
      margin-bottom: 20px;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }

    .slide p {
      font-size: 1.2rem;
      margin-bottom: 30px;
    }

    .slide .btn {
      display: inline-block;
      padding: 15px 30px;
      background: var(--primary-color);
      color: #fff;
      border-radius: 50px;
      font-weight: bold;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 5px 15px rgba(255, 117, 140, 0.4);
    }

    .slide .btn:hover {
      background: var(--button-hover);
      box-shadow: 0 8px 20px rgba(255, 74, 110, 0.6);
      transform: scale(1.1);
    }

    .slide .image {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
    }

    .slide .image img {
      max-width: 90%;
      border-radius: 20px;
      box-shadow: 0 8px 20px var(--shadow-color);
      transform: scale(1);
      transition: transform 0.5s ease;
    }

    .slide .image img:hover {
      transform: scale(1.1);
    }

    /* Navigation Buttons */
    .coupon-slider button {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(255, 255, 255, 0.8);
      border: none;
      border-radius: 50%;
      padding: 15px;
      cursor: pointer;
      box-shadow: 0 4px 10px var(--shadow-color);
      transition: all 0.3s ease;
    }

    .coupon-slider button:hover {
      background: #fff;
      transform: translateY(-50%) scale(1.2);
    }

    .coupon-slider .prev {
      left: 10px;
    }

    .coupon-slider .next {
      right: 10px;
    }

    /* Decorative Dots */
    .dots {
      display: flex;
      justify-content: center;
      margin-top: 20px;
    }

    .dot {
      width: 15px;
      height: 15px;
      background: #fff;
      border: 2px solid var(--primary-color);
      border-radius: 50%;
      margin: 0 5px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .dot.active {
      background: var(--primary-color);
      transform: scale(1.3);
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>

<div class="coupon-slider">
  <div class="slides">
    <div class="slide">
      <div class="text">
        <h3>50% OFF on Burgers!</h3>
        <p>Only today! Use coupon code: <strong>BURGER50</strong></p>
        <a href="#" class="btn">Order Now</a>
      </div>
      <div class="image">
        <img src="../kepek/pizza2.jpg" alt="Burger Deal">
      </div>
    </div>
    <div class="slide">
      <div class="text">
        <h3>Free Dessert!</h3>
        <p>With orders above $20. Valid until 2025-01-31.</p>
        <a href="#" class="btn">Claim Now</a>
      </div>
      <div class="image">
        <img src="../kepek/pizza2.jpg" alt="Dessert Offer">
      </div>
    </div>
    <div class="slide">
      <div class="text">
        <h3>Buy 1 Get 1 Free</h3>
        <p>On all pizzas. Use code: <strong>PIZZADEAL</strong></p>
        <a href="#" class="btn">Grab the Deal</a>
      </div>
      <div class="image">
        <img src="../kepek/pizza2.jpg" alt="Pizza Deal">
      </div>
    </div>
  </div>
  <button class="prev">&#10094;</button>
  <button class="next">&#10095;</button>
</div>

<div class="dots">
  <div class="dot active"></div>
  <div class="dot"></div>
  <div class="dot"></div>
</div>

<script>
  const slides = document.querySelector('.slides');
  const slide = document.querySelectorAll('.slide');
  const prevButton = document.querySelector('.prev');
  const nextButton = document.querySelector('.next');
  const dots = document.querySelectorAll('.dot');
  let currentIndex = 0;
  let autoSlideInterval;

  const updateSlider = () => {
    slides.style.transform = `translateX(-${currentIndex * 100}%)`;
    dots.forEach((dot, index) => {
      dot.classList.toggle('active', index === currentIndex);
    });
  };

  const nextSlide = () => {
    currentIndex = (currentIndex < slide.length - 1) ? currentIndex + 1 : 0;
    updateSlider();
  };

  const prevSlide = () => {
    currentIndex = (currentIndex > 0) ? currentIndex - 1 : slide.length - 1;
    updateSlider();
  };

  const startAutoSlide = () => {
    autoSlideInterval = setInterval(nextSlide, 7000);
  };

  const stopAutoSlide = () => {
    clearInterval(autoSlideInterval);
  };

  prevButton.addEventListener('click', () => {
    prevSlide();
    stopAutoSlide();
    startAutoSlide();
  });

  nextButton.addEventListener('click', () => {
    nextSlide();
    stopAutoSlide();
    startAutoSlide();
  });

  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
      currentIndex = index;
      updateSlider();
      stopAutoSlide();
      startAutoSlide();

});
  });

  // Start automatic sliding on page load
  startAutoSlide();

  // Optional: Pause auto-slide on hover
  const slider = document.querySelector('.coupon-slider');
  slider.addEventListener('mouseenter', stopAutoSlide);
  slider.addEventListener('mouseleave', startAutoSlide);
</script>



<br>






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
