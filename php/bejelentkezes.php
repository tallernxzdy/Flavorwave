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

    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$recaptchaSecret."&response=".$recaptchaResponse);
    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"]) !== 1) {
        $uzenet = "<p class='error'>reCAPTCHA ellenőrzés sikertelen, próbáld újra!</p>";
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
                // Session variables
                $_SESSION["felhasznalo_id"] = $felhasznaloId;
                $_SESSION["felhasznalo_nev"] = $dbFelhasznalonev;
                $_SESSION["jog_szint"] = $dbAdminJogosultsagEllenorzes;

                // Start transaction for cart merging
                $conn->begin_transaction();

                try {
                    // 1. Get guest cart from session and cookie
                    $guestCart = [];
                    
                    // From session
                    if (isset($_SESSION['kosar']) && is_array($_SESSION['kosar'])) {
                        $guestCart = $_SESSION['kosar'];
                    }
                    else if(isset($_COOKIE['guest_cart'])) {
                        $cookieCart = json_decode($_COOKIE['guest_cart'], true);
                        if (is_array($cookieCart)) {
                            foreach ($cookieCart as $itemId => $qty) {
                                if (isset($guestCart[$itemId])) {
                                    $guestCart[$itemId] += $qty;
                                } else {
                                    $guestCart[$itemId] = $qty;
                                }
                            }
                        }
                    }
                    

                    // 2. Merge guest cart with user's database cart
                    if (!empty($guestCart)) {
                        foreach ($guestCart as $itemId => $quantity) {
                            // Verify item exists in database
                            $checkFood = $conn->prepare("SELECT id FROM etel WHERE id = ?");
                            $checkFood->bind_param("i", $itemId);
                            $checkFood->execute();
                            if ($checkFood->get_result()->num_rows === 0) continue;

                            // Check if item already in user's cart
                            $checkQuery = $conn->prepare("SELECT darab FROM tetelek WHERE felhasznalo_id = ? AND etel_id = ?");
                            $checkQuery->bind_param("ii", $felhasznaloId, $itemId);
                            $checkQuery->execute();
                            
                            if ($checkQuery->get_result()->num_rows > 0) {
                                // Update existing quantity
                                $update = $conn->prepare("UPDATE tetelek SET darab = darab + ? WHERE felhasznalo_id = ? AND etel_id = ?");
                                $update->bind_param("iii", $quantity, $felhasznaloId, $itemId);
                                $update->execute();
                            } else {
                                // Insert new item
                                $insert = $conn->prepare("INSERT INTO tetelek (felhasznalo_id, etel_id, darab) VALUES (?, ?, ?)");
                                $insert->bind_param("iii", $felhasznaloId, $itemId, $quantity);
                                $insert->execute();
                            }
                        }
                    }

                    // 3. Clear guest cart data
                    unset($_SESSION['kosar']);
                    setcookie('guest_cart', '', time() - 3600, '/');

                    $conn->commit();
                    
                    // Redirect to cart page to show merged cart
                    header('Location: kezdolap.php');
                    exit();

                } catch (Exception $e) {
                    $conn->rollback();
                    $uzenet = "<p class='error'>Hiba történt a kosár egyesítése közben. Próbáld újra!</p>";
                }
            } else {
                $uzenet = "<p class='error'>Hibás jelszó vagy felhasználónév!</p>";
            }
        } else {
            $uzenet = "<p class='error'>Nincs ilyen névvel rendelkező felhasználó!</p>";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="icon" href="../kepek/logo.png" type="image/png">
    <title>FlavorWave | Bejelentkezés</title>
    <script src="https://www.google.com/recaptcha/api.js?hl=hu" async defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../css/bejelentkezes.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>
    <?php include './navbar.php'; ?>
    
    <div class="container">
        <h2>Bejelentkezés</h2>
        <form id="loginForm" method="POST">
            <label for="username">Felhasználónév:</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Jelszó:</label>
            <div class="input-container">
                <input type="password" name="password" id="password" required>
                <span class="input_img" data-role="toggle" id="togglePassword">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                        <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
                        <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
                        <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
                    </svg>
                </span>
            </div>

            <div class="g-recaptcha" data-sitekey="6LeLQ3wqAAAAAHrRYE5lONuFxNYZOUmtENqlcgSf"></div><br>

            <div id="errorContainer" style="color: red; margin-top: 10px;">
                <?php if (isset($uzenet) && !empty($uzenet)) echo $uzenet; ?>
            </div>

            <button type="submit">Bejelentkezés</button>
        </form>
        <p>Nincs fiókod? <a href="regisztracio.php">Regisztrálj most!</a></p>
    </div>

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
                    © 2024 FlavorWave - Minden jog fenntartva.
                </div>
            </div>
        </div>

    <script>
        document.getElementById("togglePassword").addEventListener("click", function(event) {
            event.preventDefault(); // Megakadályozza az alapértelmezett viselkedést
            const jelszoInput = document.getElementById("password");
            const ikon = this.querySelector("svg");

            if (jelszoInput.type === "password") {
                jelszoInput.type = "text";
                ikon.innerHTML = `
            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
        `;
            } else {
                jelszoInput.type = "password";
                ikon.innerHTML = `
            <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
            <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
            <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
        `;
            }
        });
    </script>
</body>
</html>