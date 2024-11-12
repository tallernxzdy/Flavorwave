<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Regisztráció</title>
</head>
<body>
    <h2>Regisztráció</h2>
    <form action="" method="POST">
        <label for="username">Felhasználónév:</label>
        <input type="text" name="username" id="username" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br><br>

        <label for="password">Jelszó:</label>
        <input type="password" name="password" id="password" required><br><br>

        <label for="phone">Telefonszám:</label>
        <input type="text" name="phone" id="phone" required><br><br>

        <button type="submit">Regisztrálás</button>
    </form>

    <p>Ha már van fiókja, <a href="./php/bejelentkezes.php">jelentkezzen be itt</a>.</p>

<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "flavorwave";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Kapcsolódási hiba: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $felhasznalonev = $_POST['username'];
        $email = $_POST['email'];
        $jelszo = $_POST['password'];
        $tel_szam = $_POST['phone'];
        $hashedPassword = password_hash($jelszo, PASSWORD_DEFAULT);

        $sql = "INSERT INTO felhasznalo (felhasznalo_nev, email_cim, jelszo, tel_szam) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            die("Hiba az előkészítés során: " . $conn->error);
        }
        
        $stmt->bind_param("ssss", $felhasznalonev, $email, $hashedPassword, $tel_szam);

        if ($stmt->execute()) {
            echo "Sikeres regisztráció! <a href='bejelentkezes.php'>Bejelentkezés</a>";
        } else {
            if ($stmt->errno == 1062) {
                echo "Ez a felhasználónév már létezik!";
            } else {
                echo "Hiba történt: " . $stmt->error;
            }
        }

        $stmt->close();
    }

    $conn->close();
?>
</body>
</html>
