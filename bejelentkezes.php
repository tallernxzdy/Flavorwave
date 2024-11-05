<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Bejelentkezés</title>
</head>
<body>
    <h2>Bejelentkezés</h2>
    <form action="" method="POST">
        <label for="username">Felhasználónév:</label>
        <input type="text" name="username" id="username" required><br><br>

        <label for="password">Jelszó:</label>
        <input type="password" name="password" id="password" required><br><br>

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
                header("Location: welcome.php");
                exit();
            } else {
                echo "Hibás jelszó!";
            }
        } else {
            echo "Nem található felhasználó!";
        }
    
        $stmt->close();
    }
    
    $conn->close();
?>
</body>
</html>
