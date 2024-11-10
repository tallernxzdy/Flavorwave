<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flavorwave</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <ul class="navbar_ul">
            <li><a class="navbar_link" href="kezdolap.php">Főoldal</a></li>
            <li><a class="navbar_link" href="menu.php">Menü</a></li>
            
            <!-- Jobb oldal: Bejelentkezés/Regisztráció vagy Kijelentkezés -->
            <div class="right_links">
                <?php if (isset($_SESSION["username"])): ?>
                    <li><a class="navbar_link logout" href="kijelentkezes.php">Kijelentkezés</a></li>
                <?php else: ?>
                    <li><a class="navbar_link login" href="bejelentkezes.php">Bejelentkezés</a></li>
                    <li><a class="navbar_link register" href="regisztracio.php">Regisztráció</a></li>
                <?php endif; ?>
            </div>
        </ul>
    </nav>

    <!-- Üdvözlő üzenet -->
    <h2>Üdvözöljük a Flavorwave oldalon</h2>
    <?php if (isset($_SESSION["username"])): ?>
        <p>Szia, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</p>
    <?php else: ?>
        <p>Jelentkezz be vagy regisztrálj a fiókod eléréséhez!</p>
    <?php endif; ?>
</body>
</html>
