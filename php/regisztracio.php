<?php
session_start(); // Session kezelés

// Adatbázis kapcsolat
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flavorwave";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

// Hibák tárolására szolgáló változó
$errors = [];
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $felhasznalonev = $_POST['username'];
    $email = $_POST['email'];
    $jelszo = $_POST['password'];
    $tel_szam = $_POST['phone'];

    // Ellenőrizzük, hogy minden mező ki van-e töltve
    if (empty($felhasznalonev)) {
        $errors[] = "A felhasználónév nem lehet üres!";
    }
    if (empty($email)) {
        $errors[] = "Az email cím nem lehet üres!";
    }
    if (empty($jelszo)) {
        $errors[] = "A jelszó nem lehet üres!";
    }
    if (empty($tel_szam)) {
        $errors[] = "A telefonszám nem lehet üres!";
    }

    // Email formátum ellenőrzése
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Érvénytelen email cím!";
    }

    // Telefonszám formátum ellenőrzése (06 vagy +36 kezdetű)
    if (!preg_match("/^(06|\+36)[0-9]{9}$/", $tel_szam)) {
        $errors[] = "Érvénytelen telefonszám! A telefonszámnak 06 vagy +36 előtaggal kell kezdődnie, és 9 számjegyből kell állnia.";
    }

    // Ha nincsenek hibák, akkor próbáljuk meg ellenőrizni, hogy már létezik-e ilyen felhasználó
    if (count($errors) == 0) {
        // Ellenőrizzük, hogy a felhasználónév, email cím, és telefonszám már létezik-e
        $sql = "SELECT * FROM felhasznalo WHERE felhasznalo_nev = ? OR email_cim = ? OR tel_szam = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $felhasznalonev, $email, $tel_szam);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Ha van találat, akkor megjelenítjük, hogy miért nem regisztrálhat
            while ($row = $result->fetch_assoc()) {
                if ($row['felhasznalo_nev'] == $felhasznalonev) {
                    $errors[] = "Ez a felhasználónév már létezik!";
                }
                if ($row['email_cim'] == $email) {
                    $errors[] = "Ez az email cím már regisztrálva van!";
                }
                if ($row['tel_szam'] == $tel_szam) {
                    $errors[] = "Ez a telefonszám már regisztrálva van!";
                }
            }
        }

        // Ha nincs hiba, akkor beszúrjuk az új felhasználót
        if (count($errors) == 0) {
            // Jelszó hashelése
            $hashedPassword = password_hash($jelszo, PASSWORD_DEFAULT);

            $sql = "INSERT INTO felhasznalo (felhasznalo_nev, email_cim, jelszo, tel_szam) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $felhasznalonev, $email, $hashedPassword, $tel_szam);

            if ($stmt->execute()) {
                $success_message = "Sikeres regisztráció! <a href='bejelentkezes.php'>Bejelentkezés</a>";
            } else {
                $errors[] = "Hiba történt a regisztráció során: " . $stmt->error;
            }

            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="icon" href="../kepek/logo.png" type="image/png">
    <title>FlavorWave | Regisztráció</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/regisztracio.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
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
    <h2>Regisztráció</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php echo $errors[0]; ?>
        </div>
    <?php elseif (!empty($success_message)): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="username">Felhasználónév:</label>
        <input type="text" name="username" id="username">

        <label for="email">Email:</label>
        <input type="email" name="email" id="email">

        <label for="password">Jelszó:</label>
        <input type="password" name="password" id="password">

        <label for="phone">Telefonszám:</label>
        <input type="tel" name="phone" id="phone">

        <button type="submit" class="btn btn-primary">Regisztrálás</button>
    </form>
    <p>Van már fiókja? <a href="bejelentkezes.php">Jelentkezzen be itt</a>.</p>
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
            &copy; 2024 FlavorWave - Minden jog fenntartva.
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/navbar.js"></script>
</body>
</html>