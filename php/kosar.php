<?php
session_start();

// A bejelentkezett felhasználó ID-ja
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Adatbázis kapcsolat importálása
include './adatbazisra_csatlakozas.php'; 

// Kosár adatainak lekérdezése a tetelek és etel táblák összekapcsolásával
$query = "SELECT etel.nev, etel.kep_url, etel.egyseg_ar, tetelek.darab
          FROM tetelek 
          JOIN etel ON tetelek.etel_id = etel.id 
          WHERE tetelek.felhasznalo_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlavorWave - Kosár</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/kosar.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>
    <nav>
        <!-- Bal oldalon a logó -->
        <a href="kezdolap.php" class="logo">
            <img src="../kepek/logo.png" alt="Flavorwave Logo">
            <h1>FlavorWave</h1>
        </a>

        <!-- Középen a kategóriák (és Admin felület, ha jogosult) -->
        <div class="navbar-center">
            <a href="kategoria.php">Menü</a>
            <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
                <a href="admin_felulet.php">Admin felület</a>
            <?php endif; ?>
        </div>

        <!-- Jobb oldalon a gombok -->
        <div class="navbar-buttons">
            <?php if (isset($_SESSION["username"])): ?>
                <a href="kijelentkezes.php" class="login-btn">Kijelentkezés</a>
            <?php else: ?>
                <a href="bejelentkezes.php" class="login-btn">Bejelentkezés</a>
            <?php endif; ?>
            <a href="rendeles.php" class="order-btn">Rendelés</a>
            <a href="kosar.php" class="cart-btn">
                <img src="../kepek/kosar.png" alt="Kosár" class="cart-icon">
            </a>
        </div>
    </nav>

    <div class="cart-container">
        <div class="cart-header">
            <h1>Kosár</h1>
            <button class="btn btn-danger btn-sm">Kosár ürítése</button>
        </div>

        <!-- Cart Items -->
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $itemTotal = $row['egyseg_ar'] * $row['darab'];
                $total += $itemTotal;
                echo '<div class="cart-item">';
                echo '    <div class="item-details">';
                echo '        <img src="' . htmlspecialchars($row['kep_url']) . '" alt="' . htmlspecialchars($row['nev']) . '">';
                echo '        <div class="item-info">';
                echo '            <span class="item-name">' . htmlspecialchars($row['nev']) . '</span>';
                echo '            <span class="item-price">Ár: ' . htmlspecialchars($row['egyseg_ar']) . ' Ft</span>';
                echo '        </div>';
                echo '    </div>';
                echo '    <div class="quantity-controls">';
                echo '        <button>-</button>';
                echo '        <span>' . htmlspecialchars($row['darab']) . '</span>';
                echo '        <button>+</button>';
                echo '    </div>';
                echo '    <span>' . htmlspecialchars($itemTotal) . ' Ft</span>';
                echo '</div>';
            }
        } else {
            echo '<p>A kosár üres.</p>';
        }

        $stmt->close();
        $conn->close();
        ?>

        <!-- Total Section -->
        <div class="total-section">
            <span class="total-label">Végösszeg:</span>
            <span class="total-amount"><?php echo $total; ?> Ft</span>
        </div>

        <!-- Rendelés gomb vagy bejelentkezési felhívás -->
        <div class="checkout-section">
            <?php if ($userId): ?>
                <!-- Ha be van jelentkezve, akkor rendelés gombot jelenítünk meg -->
                <form action="rendeles_veglegesitese.php" method="post">
                    <button class="checkout-btn" type="submit">Rendelés</button>
                </form>
            <?php else: ?>
                <!-- Ha nincs bejelentkezve, akkor a bejelentkezési üzenetet és gombot -->
                <p class="login-prompt">Rendeléshez jelentkezzen be!</p>
                <a href="bejelentkezes.php" class="login-btn">Bejelentkezés</a>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>