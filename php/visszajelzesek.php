<?php
session_start();
include 'adatbazisra_csatlakozas.php';

if (!isset($_SESSION['felhasznalo_id'])) {
    die("Csak bejelentkezett felhasználók írhatnak véleményt.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ellenőrizzük, hogy a reCAPTCHA mező ki van-e töltve
    if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
        $_SESSION['uzenet'] = "Kérjük, erősítse meg, hogy nem robot!";
        header("Location: visszajelzesek.php");
        exit();
    }

    // reCAPTCHA érvényesítés a Google API-val
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $secret_key = "6Lf0bsoqAAAAADWSHQoOWiAnwyLyZL60Cfoi33K3"; // Titkos kulcs
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
    $result_json = json_decode($result, true);

    if (!$result_json['success']) {
        $_SESSION['uzenet'] = "reCAPTCHA ellenőrzés sikertelen. Próbálja újra!";
        header("Location: visszajelzesek.php");
        exit();
    }

    // Ha a reCAPTCHA érvényes, akkor mehet a vélemény rögzítése
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

            // Ellenőrizzük, hogy a felhasználó már küldött-e visszajelzést
            $sql_check = "SELECT id FROM velemenyek WHERE felhasznalo_id = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("i", $felhasznalo_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                // Ha már küldött visszajelzést, akkor frissítjük a meglévőt
                $sql_update = "UPDATE velemenyek SET velemeny_szoveg = ?, ertekeles = ?, email_cim = ? WHERE felhasznalo_id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("sisi", $velemeny_szoveg, $ertekeles, $email_cim, $felhasznalo_id);
                $stmt_update->execute();

                $_SESSION['uzenet'] = "Visszajelzés frissítve!";
            } else {
                // Ha még nem küldött visszajelzést, akkor beszúrjuk az újat
                $sql_insert = "INSERT INTO velemenyek (felhasznalo_id, velemeny_szoveg, ertekeles, email_cim) 
                               VALUES (?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("isis", $felhasznalo_id, $velemeny_szoveg, $ertekeles, $email_cim);
                $stmt_insert->execute();

                $_SESSION['uzenet'] = "Köszönjük a visszajelzést!";
            }
        } else {
            $_SESSION['uzenet'] = "Hiba: A felhasználó nem található.";
        }
    } else {
        $_SESSION['uzenet'] = "Kérjük, minden mezőt töltsön ki!";
    }
    header("Location: visszajelzesek.php");
    exit();
}

// Vélemények lekérdezése
$sql = "SELECT felhasznalo_nev, velemeny_szoveg, ertekeles FROM velemenyek 
        INNER JOIN felhasznalo ON velemenyek.felhasznalo_id = felhasznalo.id 
        ORDER BY velemenyek.id DESC";
$result = $conn->query($sql);
$velemenyek = ($result && $result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
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
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/visszajelzesek.css">
    <!-- reCAPTCHA script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script></script>
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
            <i class='fas fa-shopping-cart cart-icon'></i>
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
            <li><a href="rendeles.php">Rendelés</a></li>
    </ul>
</div>
    <div class="container">
        <h1>Visszajelzés</h1>
        <?php if (isset($_SESSION['uzenet'])) { echo "<p class='alert alert-info'>" . $_SESSION['uzenet'] . "</p>"; unset($_SESSION['uzenet']); } ?>
        <form method="POST" action="visszajelzesek.php">
            <label for="megelegedettseg">Mennyire elégedett? (1-10)</label>
            <select id="megelegedettseg" name="megelegedettseg" required>
                <?php for ($i = 1; $i <= 10; $i++) echo "<option value='$i'>$i</option>"; ?>
            </select>

            <label for="visszajelzes">Visszajelzés szövege</label>
            <textarea id="visszajelzes" name="visszajelzes" rows="5" required></textarea>
            <br>
            <div class="g-recaptcha" data-sitekey="6Lf0bsoqAAAAADgj9B0eBgXozNmq1q2vYqEMXzvb"></div>
            <br>
            <button type="submit">Küldés</button>
        </form>
    </div>

    <hr></hr>

    <div class="container">
        <h2>Mit mondanak rólunk?</h2>
        <?php if (count($velemenyek) >= 3): ?>
            <div class="row">
                <?php foreach ($velemenyek as $row): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-text">"<?php echo htmlspecialchars($row['velemeny_szoveg']); ?>"</p>
                                <p class="card-subtitle">- <?php echo htmlspecialchars($row['felhasznalo_nev']); ?></p>
                                <p class="card-text">Értékelés: <?php echo str_repeat("⭐", $row['ertekeles']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">Légy az elsők között, aki visszajelzést küld!</p>
        <?php endif; ?>
    </div>
</body>
</html>