<?php
session_start();
include 'adatbazisra_csatlakozas.php';

$userId = isset($_SESSION['felhasznalo_id']) ? $_SESSION['felhasznalo_id'] : null;
$cartItems = [];
$total = 0;

// Kosárba rakás logikája
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etel_id'])) {
    $etelId = $_POST['etel_id'];

    if ($userId) {
        // Bejelentkezett felhasználó - adatbázisból ellenőrizzük
        $query = "SELECT darab FROM tetelek WHERE felhasznalo_id = ? AND etel_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $userId, $etelId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Ha már benne van, növeljük a mennyiséget
            $row = $result->fetch_assoc();
            $newQuantity = $row['darab'] + 1;
            $updateQuery = "UPDATE tetelek SET darab = ? WHERE felhasznalo_id = ? AND etel_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("iii", $newQuantity, $userId, $etelId);
            $updateStmt->execute();
        } else {
            // Ha nincs benne, beszúrjuk új elemként
            $insertQuery = "INSERT INTO tetelek (felhasznalo_id, etel_id, darab) VALUES (?, ?, 1)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ii", $userId, $etelId);
            $insertStmt->execute();
        }
    } else {
        // Vendég felhasználó - session-ből ellenőrizzük
        if (isset($_SESSION['kosar'][$etelId])) {
            // Ha már benne van, növeljük a mennyiséget
            $_SESSION['kosar'][$etelId]++;
        } else {
            // Ha nincs benne, beszúrjuk új elemként
            $_SESSION['kosar'][$etelId] = 1;
        }
        // Frissítjük a cookie-t is
        setcookie('guest_cart', json_encode($_SESSION['kosar']), time() + 604800, '/');
    }

    // Visszairányítjuk a felhasználót a kosár oldalra
    header("Location: kosar.php");
    exit();
}

// Kosár adatainak lekérése
if ($userId) {
    // Bejelentkezett felhasznalo - csak adatbázisból
    $query = "SELECT etel.id, etel.nev, etel.kep_url, etel.egyseg_ar, tetelek.darab, kategoria.kategoria_nev 
              FROM tetelek 
              JOIN etel ON tetelek.etel_id = etel.id 
              JOIN kategoria ON etel.kategoria_id = kategoria.id 
              WHERE tetelek.felhasznalo_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
        $total += $row['egyseg_ar'] * $row['darab'];
    }
    
    // Ha be van jelentkezve, ne nézzük a session kosarat
} else {
    // Vendég felhasznalo - csak session-ből
    if (isset($_SESSION['kosar'])) {
        foreach ($_SESSION['kosar'] as $itemId => $quantity) {
            $query = "SELECT etel.id, etel.nev, etel.egyseg_ar, etel.kep_url, kategoria.kategoria_nev 
                      FROM etel 
                      JOIN kategoria ON etel.kategoria_id = kategoria.id 
                      WHERE etel.id = ?";
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
    <?php
    include './navbar.php';
    ?>

    <div class="cart-container">
        <div class="cart-header">
            <h1>Kosár</h1>
            <?php if (!empty($cartItems)): ?>
                <button class="btn btn-danger btn-sm" id="clearCartBtn" onclick="clearCart()">Kosár ürítése</button>
            <?php endif; ?>
        </div>

        <?php foreach ($cartItems as $item): ?>
            <div class="cart-item" data-item-id="<?= $item['id'] ?>">
                <div class="item-details">
                    <img src="../kepek/<?= htmlspecialchars($item['kategoria_nev']) ?>/<?= htmlspecialchars($item['kep_url']) ?>" alt="<?= htmlspecialchars($item['nev']) ?>">
                    <div class="item-info">
                        <span class="item-name"><?= htmlspecialchars($item['nev']) ?></span>
                        <span class="item-price" data-price="<?= $item['egyseg_ar'] ?>">Ár: <?= $item['egyseg_ar'] ?> Ft</span>
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
                        checkIfCartEmpty();
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
                    body: JSON.stringify({
                        itemId: itemId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cartItem.remove();
                        updateTotal();
                        checkIfCartEmpty();
                    }
                });
        }

        function checkIfCartEmpty() {
            const cartItems = document.querySelectorAll('.cart-item');
            const checkoutSection = document.querySelector('.checkout-section');
            const clearCartBtn = document.getElementById('clearCartBtn');

            if (cartItems.length === 0) {
                // Ha üres a kosár
                if (clearCartBtn) {
                    clearCartBtn.style.display = 'none'; // Elrejtjük a gombot
                }
                checkoutSection.innerHTML = `
            <p class="error">A kosár üres, rendeléshez adjon hozzá termékeket!</p>
        `;
            } else {
                // Ha nem üres a kosár
                if (clearCartBtn) {
                    clearCartBtn.style.display = 'inline-block'; // Megjelenítjük a gombot
                }
                if (checkoutSection.querySelector('.error')) {
                    const userId = <?php echo json_encode($userId); ?>;
                    if (userId) {
                        checkoutSection.innerHTML = `
                    <form action="rendeles.php" method="post">
                        <button class="checkout-btn" type="submit">Rendelés</button>
                    </form>
                `;
                    }
                }
            }
        }

        // Kosár ürítésekor is ellenőrizzük az állapotot
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
                        document.querySelectorAll('.cart-item').forEach(item => {
                            item.style.opacity = '0';
                            setTimeout(() => item.remove(), 500);
                        });

                        setTimeout(() => {
                            document.querySelector('.total-amount').textContent = '0 Ft';
                            checkIfCartEmpty(); // Frissítjük a gomb és a checkout szekció állapotát
                        }, 500);
                    } else {
                        alert('Hiba történt a kosár törlése közben. Próbálja újra!');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Oldal betöltésekor is ellenőrizzük a kosár állapotát
        document.addEventListener('DOMContentLoaded', checkIfCartEmpty);
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
                © 2024 FlavorWave - Minden jog fenntartva.
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/navbar.js"></script>
</body>

</html>