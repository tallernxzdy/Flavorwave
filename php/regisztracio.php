<?php
session_start();

// PHPMailer betöltése
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Adatbázis kapcsolat
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flavorwave";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

// Hibák és sikerüzenetek
$errors = [];
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Adatok tisztítása
    $felhasznalonev = trim($_POST['username']);
    $email = trim($_POST['email']);
    $jelszo = trim($_POST['password']);
    $tel_szam = trim($_POST['phone']);

    // Validációk
    if (empty($felhasznalonev)) {
        $errors[] = "A felhasználónév nem lehet üres!";
    } elseif (strlen($felhasznalonev) < 4) {
        $errors[] = "A felhasználónévnek legalább 4 karakter hosszúnak kell lennie!";
    }

    if (empty($email)) {
        $errors[] = "Az email cím nem lehet üres!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Érvénytelen email cím formátum!";
    }

    if (empty($jelszo)) {
        $errors[] = "A jelszó nem lehet üres!";
    } elseif (strlen($jelszo) < 8) {
        $errors[] = "A jelszónak legalább 8 karakter hosszúnak kell lennie!";
    } elseif (!preg_match("/[0-9]/", $jelszo)) {
        $errors[] = "A jelszónak tartalmaznia kell legalább egy számot!";
    } elseif (!preg_match("/[a-zA-Z]/", $jelszo)) {
        $errors[] = "A jelszónak tartalmaznia kell legalább egy betűt!";
    }

    if (empty($tel_szam)) {
        $errors[] = "A telefonszám nem lehet üres!";
    } elseif (!preg_match("/^(06|\+36)[0-9]{8,9}$/", $tel_szam)) {
        $errors[] = "Érvénytelen telefonszám! Használj 06 vagy +36 kezdést és 8-9 számjegyet!";
    }

    // Ha nincsenek hibák, folytatjuk
    if (empty($errors)) {
        // Ellenőrizzük, hogy a felhasználónév/email/tel.szám már létezik-e
        $sql = "SELECT felhasznalo_nev, email_cim, tel_szam FROM felhasznalo WHERE felhasznalo_nev = ? OR email_cim = ? OR tel_szam = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $felhasznalonev, $email, $tel_szam);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($row['felhasznalo_nev'] === $felhasznalonev) {
                    $errors[] = "Ez a felhasználónév már foglalt!";
                    break;
                }
                if ($row['email_cim'] === $email) {
                    $errors[] = "Ez az email cím már regisztrálva van!";
                    break;
                }
                if ($row['tel_szam'] === $tel_szam) {
                    $errors[] = "Ez a telefonszám már regisztrálva van!";
                    break;
                }
            }
        }

        // Ha még mindig nincsenek hibák, regisztráljuk a felhasználót
        if (empty($errors)) {
            $hashedPassword = password_hash($jelszo, PASSWORD_DEFAULT);

            $sql = "INSERT INTO felhasznalo (felhasznalo_nev, email_cim, jelszo, tel_szam) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $felhasznalonev, $email, $hashedPassword, $tel_szam);

            if ($stmt->execute()) {
                $success_message = "Sikeres regisztráció! <a href='bejelentkezes.php'>Bejelentkezés</a>";

                // Email küldése
                try {
                    $mail = new PHPMailer(true);

                    // SMTP beállítások
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'flavorwavereal@gmail.com';
                    $mail->Password = 'awch ocfs ldcr hded';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';

                    // Email tartalom
                    $mail->setFrom('flavorwavereal@gmail.com', 'FlavorWave');
                    $mail->addAddress($email, $felhasznalonev);

                    $mail->isHTML(true);
                    $mail->Subject = 'Sikeres regisztráció - FlavorWave';

                    $mail->Body = "
                        <h2>Üdvözlünk a FlavorWave közösségében, $felhasznalonev!</h2>
                        <p>Nagy örömünkre szolgál, hogy csatlakoztál hozzánk! Sikeresen regisztráltál a FlavorWave platformon, és már alig várjuk, hogy felfedezd a kínálatunkat.</p>
                        <p>Bejelentkezéshez használd az alábbi adatokat:</p>
                        <p><strong>Felhasználónév:</strong> $felhasznalonev</p>
                        <p><strong>Email cím:</strong> $email</p>
                        <p>Ha bármilyen kérdésed van, ne habozz felvenni velünk a kapcsolatot! Kezdj el böngészni, és találd meg a kedvenc ízeidet nálunk.</p>
                        <p>Sok örömet kívánunk,<br>A FlavorWave Csapat</p>
                    ";

                    $mail->AltBody = "Köszönjük regisztrációdat!";

                    $mail->send();
                    $success_message .= "<br>Elküldtünk egy megerősítő emailt a megadott címre!";
                } catch (Exception $e) {
                    echo $e;
                }
            } else {
                $errors[] = "Hiba történt a regisztráció során. Kérjük, próbáld újra később.";
            }
        }

        $stmt->close();
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
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>

<body>
    <?php
    include './navbar.php';
    ?>

    <div class="container">
        <h2>Regisztráció</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($errors[0]) ?>
            </div>
        <?php elseif (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?= $success_message ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Felhasználónév:</label>
                <input type="text" id="username" name="username" value="<?= (!empty($errors) && isset($_POST['username'])) ? htmlspecialchars($_POST['username']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="email">Email cím:</label>
                <input type="email" id="email" name="email" value="<?= (!empty($errors) && isset($_POST['email'])) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>

            <div class="form-group">
                <div class="input-container">
                    <label for="password">Jelszó (min. 8 karakter, szám és betű):</label>
                    <div class="input-container">
                        <input type="password" id="password" name="password">
                        <span class="input_img" data-role="toggle" id="togglePassword">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                                <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z" />
                                <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829" />
                                <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="phone">Telefonszám (06 vagy +36 kezdettel):</label>
                <input type="tel" id="phone" name="phone" value="<?= (!empty($errors) && isset($_POST['phone'])) ? htmlspecialchars($_POST['phone']) : '' ?>">
            </div>

            <button type="submit">Regisztráció</button>
        </form>

        <p class="form-footer">Már van fiókod? <a href="bejelentkezes.php">Bejelentkezés</a></p>
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
                © 2025 FlavorWave - Minden jog fenntartva.
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/navbar.js"></script>
</body>

</html>