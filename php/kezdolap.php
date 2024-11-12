<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flavorwave</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="kezdolap.php" class="logo">🌊 Flavorwave</a>
            <ul class="navbar_ul">
                <li><a class="navbar_link special_link" href="kezdolap.php">Főoldal</a></li>
                <li><a class="navbar_link special_link" href="menu.php">Menü</a></li>
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

    <!-- Üdvözlő üzenet -->
    <h2>Üdvözöljük a Flavorwave oldalon</h2>
    <?php if (isset($_SESSION["username"])): ?>
        <p>Szia, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</p>
    <?php else: ?>
        <p>Jelentkezz be vagy regisztrálj a fiókod eléréséhez!</p>
    <?php endif; ?>
</body>
</html>
