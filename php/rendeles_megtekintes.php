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

// Fizetési módok leképezése
$payment_map = [
    0 => "Készpénz",
    1 => "Bankkártya",
];

if ($userId) {
    // Lekérdezzük a felhasználó rendeléseit és a hozzájuk tartozó tételeket
    $orders = adatokLekerdezese(
        "SELECT m.id, m.leadasdatuma, m.leadas_allapota, m.kezbesites, m.leadas_megjegyzes, m.Fizetesi_mod,
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
                'fizetesi_mod' => $payment_map[$order['Fizetesi_mod']] ?? "Ismeretlen",
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
    <link rel="icon" href="../kepek/logo.png" type="image/png">
    <link rel="stylesheet" href="../css/fooldal/ujfooldal.css">
    <link rel="stylesheet" href="../css/rendeles_megtekint.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/navbar.css">
    
</head>
<body>
    <?php
        include './navbar.php';
    ?>

    <div class="container">
        <br><br><br><br>
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
                            <p><strong>Fizetési mód:</strong> <?php echo htmlspecialchars($order['fizetesi_mod']); ?></p>
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

    <div class="footer">
            <div class="footer-container">
                <ul class="footer-links">
                    <li><a href="../html/rolunk.html">Rólunk</a></li>
                    <li><a href="../html/kapcsolatok.html">Kapcsolat</a></li>
                    <li><a href="../html/adatvedelem.html">Adatvédelem</a></li>
                </ul>
                <div class="footer-socials">
                    <a href="https://www.facebook.com/" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.instagram.com/" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://x.com/" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.youtube.com/" target="_blank"><i class="fab fa-youtube"></i></a>
                </div>
                <div class="footer-copy">
                    © 2024 FlavorWave - Minden jog fenntartva.
                </div>
            </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/navbar.js"></script>
</body>
</html>