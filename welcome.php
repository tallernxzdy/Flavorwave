<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: bejelentkezes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Üdvözöljük</title>
</head>
<body>
    <h2>Üdvözöljük, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
    <p><a href="kijelentkezes.php">Kijelentkezés</a></p>
</body>
</html>