<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Bejelentkezés</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="../css/bejelentkezes.css">
</head>
<body>
    <h2>Bejelentkezés</h2>
    <form action="" method="POST">
        <label for="username">Felhasználónév:</label>
        <input type="text" name="username" id="username" required><br><br>

        <label for="password">Jelszó:</label>
        <input type="password" name="password" id="password" required><br><br>
        
        <div class="g-recaptcha" data-sitekey="6LeLQ3wqAAAAAHrRYE5lONuFxNYZOUmtENqlcgSf"></div><br>

        <button type="submit">Bejelentkezés</button>
    </form>

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
            echo "reCAPTCHA ellenőrzés sikertelen, próbáld újra!";
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
                    header("Location: kezdolap.php");
                    exit();
                } else {
                    echo "Hibás jelszó!";
                }
            } else {
                echo "Nem található felhasználó!";
            }
        
            $stmt->close();
        }
    }

    $conn->close();
?>
</body>
</html>
