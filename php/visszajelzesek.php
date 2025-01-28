<?php
session_start();
include 'adatbazisra_csatlakozas.php';

if (!isset($_SESSION['felhasznalo_id'])) {
    die("Csak bejelentkezett felhasználók írhatnak véleményt.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // reCAPTCHA ellenőrzése
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $secret_key = "SECRET_KULCSOD"; // Ide írd be a Secret Key-et
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $secret_key,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result);

    if (!$response->success) {
        $_SESSION['uzenet'] = "Kérjük, erősítse meg, hogy nem robot!";
        header("Location: visszajelzesek.php");
        exit();
    }

    // Tovább a vélemény mentése...
    $ertekeles = intval($_POST['megelegedettseg']);
    $velemeny_szoveg = trim($_POST['visszajelzes']);
    $felhasznalo_id = $_SESSION['felhasznalo_id'];

    if ($ertekeles && $velemeny_szoveg) {
        $sql_email = "SELECT email_cim FROM felhasznalo WHERE id = ?";
        $stmt_email = $conn->prepare($sql_email);
        $stmt_email->bind_param("i", $felhasznalo_id);
        $stmt_email->execute();
        $result = $stmt_email->get_result();
        $felhasznalo_adat = $result->fetch_assoc();

        if ($felhasznalo_adat) {
            $email_cim = $felhasznalo_adat['email_cim'];

            $sql = "INSERT INTO velemenyek (felhasznalo_id, velemeny_szoveg, ertekeles, email_cim) 
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    velemeny_szoveg = VALUES(velemeny_szoveg),
                    ertekeles = VALUES(ertekeles)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isis", $felhasznalo_id, $velemeny_szoveg, $ertekeles, $email_cim);

            if ($stmt->execute()) {
                $_SESSION['uzenet'] = "Köszönjük a visszajelzést!";
                header("Location: visszajelzesek.php");
                exit();
            } else {
                $_SESSION['uzenet'] = "Hiba történt a vélemény mentésekor: " . $stmt->error;
                header("Location: visszajelzesek.php");
                exit();
            }
        } else {
            $_SESSION['uzenet'] = "Hiba: A felhasználó nem található.";
            header("Location: visszajelzesek.php");
            exit();
        }
    } else {
        $_SESSION['uzenet'] = "Kérjük, minden mezőt töltsön ki!";
        header("Location: visszajelzesek.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../kepek/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <title>Visszajelzés</title>
    <link rel="stylesheet" href="../css/visszajelzesek.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <!-- reCAPTCHA script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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


    <div class="container">
        <h1>Visszajelzés</h1>
        <?php if (isset($_SESSION['uzenet'])) { echo "<p class='uzenet'>" . $_SESSION['uzenet'] . "</p>"; unset($_SESSION['uzenet']); } ?>
        <form method="POST" action="">
            <label for="megelegedettseg">Mennyire elégedett? (1-10)</label>
            <select id="megelegedettseg" name="megelegedettseg" required>
                <option value="" disabled selected>Válasszon...</option>
                <?php for ($i = 1; $i <= 10; $i++) {
                    echo "<option value='$i'>$i</option>";
                } ?>
            </select>

            <label for="visszajelzes">Visszajelzés szövege</label>
            <textarea id="visszajelzes" name="visszajelzes" rows="5" required></textarea>

            <!-- reCAPTCHA -->
            <div class="g-recaptcha" data-sitekey="SITE_KULCSOD"></div>
            <br>

            <button type="submit">Küldés</button>
        </form>
    </div>
</body>
</html>