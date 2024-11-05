<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
<ul class="navbar_ul">
    <li>
        <a class="navbar" href="kezdolap.php">Főoldal</a>
    </li>
    <li>
        <a class="navbar" href="menu.php">Menü</a>
    </li>
    <li>
        <a class="navbar bejelentkezes float right" href="bejelentkezes.php">bejelentkezés</a>
    </li>
    <li>
         <a class="navbar regisztracio float right" href="regisztracio.php">regisztráció</a>
    </li>
    <li>
        <a class="navbar kijelentkezés jobbra_csusztatas" href="kijelentkezes.php">kijelentkezés</a>
    </li>
</ul>

<h2>Üdvözöljük a Flavorwave oldalon</h2>
<p><a href="regisztracio.php">Regisztráció</a></p>
<p><a href="bejelentkezes.php">Bejelentkezes</a></p>


<?php
session_start();
?>

    <?php if (isset($_SESSION["username"])): ?>
        <p>Szia, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</p>
        <p><a href="kijelentkezes.php">Kijelentkezés</a></p>
    <?php else: ?>
        <p>Jelentkezz be vagy regisztrálj!</p>
        <p><a href="bejelentkezes.php">Bejelentkezés</a> | <a href="regisztracio.php">Regisztráció</a></p>
    <?php endif; ?>

</body>
</html>