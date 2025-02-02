<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$userId = isset($_SESSION['felhasznalo_id']) ? $_SESSION['felhasznalo_id'] : null;
include './adatbazisra_csatlakozas.php';

$cartItems = [];
$total = 0;

// Check for guest cart in cookie and merge with session if needed
if (!$userId) {
    if (empty($_SESSION['kosar']) && isset($_COOKIE['guest_cart'])) {
        $_SESSION['kosar'] = json_decode($_COOKIE['guest_cart'], true);
    }
}

if ($userId) {
    // Logged in user - get cart from database
    $query = "SELECT etel.id, etel.nev, etel.kep_url, etel.egyseg_ar, tetelek.darab 
              FROM tetelek 
              JOIN etel ON tetelek.etel_id = etel.id 
              WHERE tetelek.felhasznalo_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
        $total += $row['egyseg_ar'] * $row['darab'];
    }
} else {
    // Guest user - get cart from session
    if (isset($_SESSION['kosar'])) {
        foreach ($_SESSION['kosar'] as $itemId => $quantity) {
            $query = "SELECT id, nev, egyseg_ar, kep_url FROM etel WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $itemId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $row['darab'] = $quantity;
                $cartItems[] = $row;
                $total += $row['egyseg_ar'] * $quantity;
            }
        }
    }
}
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
    <link rel="stylesheet" href="../css/footer.css">
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
            <?php if (isset($_SESSION["felhasznalo_nev"])): ?>
                <a href="kijelentkezes.php" class="login-btn">Kijelentkezés</a>
            <?php else: ?>
                <a href="bejelentkezes.php" class="login-btn">Bejelentkezés</a>
            <?php endif; ?>
            <a href="rendeles.php" class="order-btn">Rendelés</a>
            <a href="kosar.php" class="cart-btn">
                <img src="../kepek/kosar.png" alt="Kosár" class="cart-icon">
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
            <li><a href="kezdolap.php">FlavorWave</a></li>
            <li><a href="kategoria.php">Menü</a></li>
            <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
                <li><a href="admin_felulet.php">Admin felület</a></li>
            <?php endif; ?>
            <li><a href="kosar.php">Kosár</a></li>
            <?php if (isset($_SESSION["felhasznalo_nev"])): ?>
                <li><a href="kijelentkezes.php">Kijelentkezés</a></li>
            <?php else: ?>
                <li><a href="bejelentkezes.php">Bejelentkezés</a></li>
            <?php endif; ?>
        </ul>
    </div>


    <div class="cart-container">
        <div class="cart-header">
            <h1>Kosár</h1>
            <button class="btn btn-danger btn-sm" onclick="clearCart()">Kosár ürítése</button>
        </div>

        <?php foreach ($cartItems as $item): ?>
            <div class="cart-item" data-item-id="<?= $item['id'] ?>">
                <div class="item-details">
                    <img src="../kepek/<?= $item['kep_url'] ?>" alt="<?= $item['nev'] ?>">
                    <div class="item-info">
                        <span class="item-name"><?= $item['nev'] ?></span>
                        <span class="item-price" data-price="<?= $item['egyseg_ar'] ?>">Ár: <?= $item['egyseg_ar'] ?>
                            Ft</span>
                    </div>
                </div>
                <div class="quantity-controls">
                    <button onclick="updateQuantity(<?= $item['id'] ?>, 'decrease')">-</button>
                    <span class="quantity"><?= $item['darab'] ?></span>
                    <button onclick="updateQuantity(<?= $item['id'] ?>, 'increase')">+</button>
                </div>
                <span class="item-total"><?= $item['egyseg_ar'] * $item['darab'] ?> Ft</span>
                <button class="remove-from-cart" onclick="removeItem(<?= $item['id'] ?>)">Törlés</button>
            </div>
        <?php endforeach; ?>

        <div class="total-section">
            <span class="total-label">Végösszeg:</span>
            <span class="total-amount"><?= $total ?> Ft</span>
        </div>

        <div class="checkout-section">
            <?php if ($userId): ?>
                <?php if (!empty($cartItems)): ?>
                    <form action="rendeles.php" method="post">
                        <button class="checkout-btn" type="submit">Rendelés</button>
                    </form>
                <?php else: ?>
                    <p class="error">A kosár üres, rendeléshez adjon hozzá termékeket!</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="login-prompt">Rendeléshez jelentkezzen be!</p>
                <a href="bejelentkezes.php" class="login-btn">Bejelentkezés</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function updateTotal() {
            let total = 0;
            document.querySelectorAll('.cart-item').forEach(item => {
                const quantity = parseInt(item.querySelector('.quantity').textContent);
                const price = parseFloat(item.querySelector('.item-price').dataset.price);
                total += quantity * price;
            });
            document.querySelector('.total-amount').textContent = total + ' Ft';
        }

        function updateQuantity(itemId, action) {
            const cartItem = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);
            const quantitySpan = cartItem.querySelector('.quantity');
            const price = parseFloat(cartItem.querySelector('.item-price').dataset.price);
            const totalSpan = cartItem.querySelector('.item-total');

            fetch('kosar_mod.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    itemId: itemId,
                    action: action
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        quantitySpan.textContent = data.newQuantity;
                        totalSpan.textContent = (data.newQuantity * price) + ' Ft';

                        if (data.newQuantity <= 0) {
                            cartItem.remove();
                        }

                        updateTotal();
                    }
                });
        }

        function removeItem(itemId) {
            const cartItem = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);

            fetch('kosarbol_torles.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ itemId: itemId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cartItem.remove();
                        updateTotal();
                    }
                });
        }

        function clearCart() {
            fetch('kosar_uritese.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('.cart-item').forEach(item => item.remove());
                        document.querySelector('.total-amount').textContent = '0 Ft';
                    }
                });
        }
    </script>

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
                &copy; 2024 FlavorWave - Minden jog fenntartva.
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/navbar.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>