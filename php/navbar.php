<?php

use function PHPSTORM_META\elementType;

include_once 'adatbazisra_csatlakozas.php';

// A getCartItemCount függvényt ide is bemásoljuk, hogy a navbarban használhassuk
function getCartItemCount($conn) {
    $itemCount = 0;

    if (isset($_SESSION['felhasznalo_id'])) {
        $userId = $_SESSION['felhasznalo_id'];
        $query = "SELECT SUM(darab) as total FROM tetelek WHERE felhasznalo_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $itemCount = $row['total'] ? (int)$row['total'] : 0;
    } else {
        if (isset($_SESSION['kosar']) && !empty($_SESSION['kosar'])) {
            foreach ($_SESSION['kosar'] as $itemId => $quantity) {
                $itemCount += (int)$quantity;
            }
        }
    }

    return $itemCount;
}
?>

<nav>
    <!-- Bal oldalon a logó -->
    <a href="kezdolap.php" class="logo">
        <img src="../kepek/logo.png" alt="Flavorwave Logo">
        <h1>FlavorWave</h1>
    </a>

    <!-- Középen a kategóriák (és Admin felület, ha jogosult) -->
    <div class="navbar-center">
        <a href="kategoria.php">Menü</a>
        <a href="rendeles_megtekintes.php" class="order-button">Rendeléseim</a>
        <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
            <a href="admin_felulet.php">Admin felület</a>
        <?php endif; ?>
        <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 2): ?>
            <a href="dolgozoi_felulet.php">Dolgozoi felület</a>
        <?php endif; ?>
    </div>

    <!-- Jobb oldalon a gombok -->
    <div class="navbar-buttons">
        <?php if (isset($_SESSION["felhasznalo_nev"])): ?>
            <a href="kijelentkezes.php" class="login-btn">Kijelentkezés</a>
        <?php else: ?>
            <a href="bejelentkezes.php" class="login-btn">Bejelentkezés</a>
        <?php endif; ?>
        
        <a href="kosar.php" class="cart-btn">
            <i class='fas fa-shopping-cart cart-icon'></i> Kosár
            <span class="cart-count"><?php echo getCartItemCount($conn); ?></span>
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
        <li><a href="rendelesek_megtekintes.php">Rendeléseim</a></li>
        <?php if (isset($_SESSION["felhasznalo_nev"])): ?>
            <li><a href="kijelentkezes.php">Kijelentkezés</a></li>
        <?php else: ?>
            <li><a href="bejelentkezes.php" class="login-btn">Bejelentkezés</a></li>
        <?php endif; ?>
        <li>
            <a href="kosar.php">
                Kosár
                <span class="cart-count"><?php echo getCartItemCount($conn); ?></span>
            </a>
        </li>
        
    </ul>
</div>