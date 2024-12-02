<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Bejelentkezés</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../css/bejelentkezes.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>
    <!-- Navbar -->
    
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
    
    

    <!-- Bejelentkezés form -->
    <div class="container">
        <h2>Bejelentkezés</h2>
        <form id="loginForm" method="POST">
            <label for="username">Felhasználónév:</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Jelszó:</label>
            <input type="password" name="password" id="password" required>

            <div class="g-recaptcha" data-sitekey="6LeLQ3wqAAAAAHrRYE5lONuFxNYZOUmtENqlcgSf"></div><br>

            <button type="submit">Bejelentkezés</button>
        </form>
        <p>Nincs fiókod? <a href="regisztracio.php">Regisztrálj most!</a></p>
    </div>

    <!-- JavaScript for error handling -->
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function (event) {
            event.preventDefault(); // Ne frissítse az oldalt

            const formData = new FormData(this);
            const response = await fetch('', {
                method: 'POST',
                body: formData
            });

            const text = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            const errorElement = doc.querySelector('.error');

            if (errorElement) {
                alert(errorElement.textContent.trim());
            } else {
                // Sikeres bejelentkezés esetén az oldal átirányítása
                window.location.href = 'kezdolap.php';
            }
        });
    </script>

    <!-- PHP Bejelentkezési logika -->
    <?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flavorwave";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recaptchaSecret = '6LeLQ3wqAAAAAPwhFgGLO3yRPiqjDTRlm-dR3L5F';
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptchaSecret . "&response=" . $recaptchaResponse);
    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"]) !== 1) {
        echo "<p class='error'>reCAPTCHA ellenőrzés sikertelen, próbáld újra!</p>";
    } else {
        $felhasznalonev = $_POST['username'];
        $jelszo = $_POST['password'];

        $sql = "SELECT id, felhasznalo_nev, jelszo, jog_szint FROM felhasznalo WHERE felhasznalo_nev = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $felhasznalonev);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($felhasznaloId, $dbFelhasznalonev, $hashedPassword, $dbAdminJogosultsagEllenorzes);

        if ($stmt->num_rows > 0) {
            $stmt->fetch();

            if (password_verify($jelszo, $hashedPassword)) {
                // Session változók beállítása
                $_SESSION["felhasznalo_id"] = $felhasznaloId; // ID a sessionbe
                $_SESSION["username"] = $dbFelhasznalonev;
                $_SESSION["jog_szint"] = $dbAdminJogosultsagEllenorzes;

                echo "<script>window.location.href = 'kezdolap.php';</script>";
                exit();
            } else {
                echo "<p class='error'>Hibás jelszó!</p>";
            }
        } else {
            echo "<p class='error'>Nem található felhasználó!</p>";
        }

        $stmt->close();
    }
}

$conn->close();
?>

    <!-- Footer -->
    <div class="footer">
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
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/navbar.js"></script>
</body>
</html>
