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
                echo "<p>Sikeres regisztráció! <a href='bejelentkezes.php'>Bejelentkezés</a></p>";
            } else {
                $errors[] = "Hiba történt a regisztráció során: " . $stmt->error;
            }

            $stmt->close();
        }
    }

    $conn->close();

    // Átadjuk a hibákat a JS-nek
    if (!empty($errors)) {
        echo "<script type='text/javascript'>
            window.onload = function() {
                showError(" . json_encode($errors) . ");
            }
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="../css/regisztracio.css">
    <link rel="stylesheet" href="../css/style.css">
    <script type="text/javascript">
        // JavaScript felugró ablak a hibákhoz
        function showError(errors) {
            if (errors.length > 0) {
                // Hibák megjelenítése egy felugró ablakban
                alert(errors.join("\n"));
            }
        }
    </script>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="kezdolap.php" class="logo">🌊 Flavorwave</a>
            <ul class="navbar_ul">
                <li><a class="navbar_link special_link kozepso" href="kezdolap.php">Főoldal</a></li>
                <li><a class="navbar_link special_link kozepso" href="menu.php">Menü</a></li>
            </ul>
            <div class="right_links">
                <?php if (isset($_SESSION["username"])): ?>
                    <a class="navbar_link logout" href="kijelentkezes.php">Kijelentkezés</a>
                <?php else: ?>
                    <a class="navbar_link login" href="bejelentkezes.php">Bejelentkezés</a>
                    <a class="navbar_link register" href="regisztracio.php">Regisztráció</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Regisztráció</h2>
        <form action="" method="POST">
            <label for="username">Felhasználónév:</label>
            <input type="text" name="username" id="username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Jelszó:</label>
            <input type="password" name="password" id="password" required>

            <label for="phone">Telefonszám:</label>
            <input type="tel" name="phone" id="phone" required>

            <button type="submit">Regisztrálás</button>
        </form>
        <p>Van már fiókja? <a href="bejelentkezes.php">Jelentkezzen be itt</a>.</p>
    </div>

    <footer class="footer">
        <div class="footer-container">
            <ul class="footer-links">
                <li><a href="#">Rólunk</a></li>
                <li><a href="#">Szolgáltatások</a></li>
                <li><a href="#">Kapcsolat</a></li>
                <li><a href="#">Blog</a></li>
            </ul>
            <div class="footer-socials">
                <a href="#" target="_blank"><i class="fab fa-facebook"></i></a>
                <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="#" target="_blank"><i class="fab fa-linkedin"></i></a>
                <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
            </div>
            <div class="footer-copy">
                <p>&copy; 2024 <a href="#">Flavorwave</a>. Minden jog fenntartva.</p>
            </div>
        </div>
    </footer>
</body>
</html>
