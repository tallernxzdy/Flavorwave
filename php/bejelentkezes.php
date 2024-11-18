<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="../css/bejelentkezes.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="navbar-container">
            <a href="kezdolap.php" class="logo">FlavorWave</a>
            <ul class="navbar_ul">
                <li><a href="kezdolap.php" class="navbar_link">Kezdőlap</a></li>
                <li><a href="regisztracio.php" class="navbar_link">Regisztráció</a></li>
                <li><a href="bejelentkezes.php" class="navbar_link login">Bejelentkezés</a></li>
            </ul>
        </div>
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
            // reCAPTCHA ellenőrzés
            $recaptchaSecret = '6LeLQ3wqAAAAAPwhFgGLO3yRPiqjDTRlm-dR3L5F'; 
            $recaptchaResponse = $_POST['g-recaptcha-response'];

            $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptchaSecret . "&response=" . $recaptchaResponse);
            $responseKeys = json_decode($response, true);

            if (intval($responseKeys["success"]) !== 1) {
                echo "<p class='error'>reCAPTCHA ellenőrzés sikertelen, próbáld újra!</p>";
            } else {
                // Felhasználói hitelesítés
                $felhasznalonev = $_POST['username'];
                $jelszo = $_POST['password'];
            
                $sql = "SELECT felhasznalo_nev, jelszo FROM felhasznalo WHERE felhasznalo_nev = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $felhasznalonev);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($dbFelhasznalonev, $hashedPassword);
            
                if ($stmt->num_rows > 0) {
                    $stmt->fetch();
            
                    if (password_verify($jelszo, $hashedPassword)) {
                        $_SESSION["username"] = $dbFelhasznalonev;
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
            <ul class="footer-links">
                <li><a href="kezdolap.php">Kezdőlap</a></li>
                <li><a href="regisztracio.php">Regisztráció</a></li>
                <li><a href="bejelentkezes.php">Bejelentkezés</a></li>
            </ul>
            <div class="footer-copy">
                &copy; 2024 FlavorWave - Minden jog fenntartva.
            </div>
        </div>
    </div>
</body>
</html>
