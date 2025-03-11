<?php
session_start();
include 'adatbazisra_csatlakozas.php';

$userId = isset($_SESSION['felhasznalo_id']) ? $_SESSION['felhasznalo_id'] : null;

// Állapotok leképezése
$status_map = [
    0 => "Függőben",
    1 => "Feldolgozás alatt",
    2 => "Kiszállítás alatt",
    3 => "Teljesítve",
    4 => "Lemondva"
];

if ($userId) {
    // Lekérdezzük a felhasználó rendeléseit és a hozzájuk tartozó tételeket
    $orders = adatokLekerdezese(
        "SELECT m.id, m.leadasdatuma, m.leadas_allapota, m.kezbesites, m.leadas_megjegyzes,
                rt.termek_id, rt.mennyiseg, e.nev AS termek_nev, e.egyseg_ar
         FROM megrendeles m
         LEFT JOIN rendeles_tetel rt ON m.id = rt.rendeles_id
         LEFT JOIN etel e ON rt.termek_id = e.id
         WHERE m.felhasznalo_id = ?
         ORDER BY m.leadasdatuma DESC",
        ["i", $userId]
    );

    // Csoportosítjuk a rendeléseket ID szerint
    $grouped_orders = [];
    foreach ($orders as $order) {
        $order_id = $order['id'];
        if (!isset($grouped_orders[$order_id])) {
            $grouped_orders[$order_id] = [
                'id' => $order_id,
                'leadasdatuma' => $order['leadasdatuma'],
                'leadas_allapota' => $status_map[$order['leadas_allapota']] ?? "Ismeretlen",
                'kezbesites' => $order['kezbesites'],
                'leadas_megjegyzes' => $order['leadas_megjegyzes'],
                'items' => []
            ];
        }
        if ($order['termek_id']) {
            $grouped_orders[$order_id]['items'][] = [
                'nev' => $order['termek_nev'],
                'mennyiseg' => $order['mennyiseg'],
                'ar' => $order['egyseg_ar']
            ];
        }
    }
} else {
    $grouped_orders = [];
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <title>Rendeléseim</title>
    <link rel="stylesheet" href="../css/rendeles_megtekint.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>
    <!-- Navigáció (változatlan) -->
    <nav>
        <a href="kezdolap.php" class="logo">
            <img src="../kepek/logo.png" alt="Flavorwave Logo">
            <h1>FlavorWave</h1>
        </a>
        <div class="navbar-center">
            <a href="kategoria.php">Menü</a>
            <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
                <a href="admin_felulet.php">Admin felület</a>
            <?php endif; ?>
            <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 2): ?>
                <a href="dolgozoi_felulet.php">Dolgozoi felulet</a>
            <?php endif; ?>
        </div>
        <div class="navbar-buttons">
            <?php if (isset($_SESSION["felhasznalo_nev"])): ?>
                <a href="kijelentkezes.php" class="login-btn">Kijelentkezés</a>
            <?php else: ?>
                <a href="bejelentkezes.php" class="login-btn">Bejelentkezés</a>
            <?php endif; ?>
            <a href="rendeles_megtekintes.php" class="order-btn">Rendeléseim</a>
            <a href="kosar.php" class="cart-btn">
                <i class='fas fa-shopping-cart cart-icon'></i>
            </a>
        </div>
        <div class="hamburger" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <div class="container">
        <h1 id="cim">Rendeléseim</h1>

        <?php if (!$userId): ?>
            <div class="not-logged-in">
                <p>Nem vagy bejelentkezve! Kérlek, jelentkezz be a rendeléseid megtekintéséhez.</p>
                <a href="bejelentkezes.php" class="btn btn-primary">Bejelentkezés</a>
            </div>
        <?php elseif (empty($grouped_orders)): ?>
            <p class="no-orders">Úgy tűnik, még nem adtál fel rendelést nálunk. Fedezd fel kínálatunkat, és rendeld meg kedvenceidet!</p>
        <?php else: ?>
            <div class="orders-grid">
                <?php foreach ($grouped_orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h3>Rendelési azonosító: <?php echo htmlspecialchars($order['id']); ?></h3>
                            <p class="order-date">Leadás dátuma: <?php echo htmlspecialchars($order['leadasdatuma']); ?></p>
                        </div>
                        <div class="order-details">
                            <p><strong>Állapot:</strong> <?php echo htmlspecialchars($order['leadas_allapota']); ?></p>
                            <p><strong>Kézbesítés módja:</strong> <?php echo htmlspecialchars($order['kezbesites']); ?></p>
                            <p><strong>Megjegyzés:</strong> <?php echo htmlspecialchars($order['leadas_megjegyzes']); ?></p>
                            <p><strong>Rendelt tételek:</strong></p>
                            <ul>
                                <?php if (empty($order['items'])): ?>
                                    <li>Még nem találhatóak tételek ehhez a rendeléshez.</li>
                                <?php else: ?>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <li><?php echo htmlspecialchars($item['nev']) . " (" . $item['mennyiseg'] . " db, " . number_format($item['ar'], 0, ',', ' ') . " Ft)"; ?></li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a href="kezdolap.php" class="btn btn-secondary back-button">Vissza a főoldalra</a>
    </div>

    <!-- Footer (változatlan) -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/navbar.js"></script>
</body>
</html>