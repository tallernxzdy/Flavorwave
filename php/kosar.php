<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlavorWave - Kosár</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/kosar.css">
</head>
<body>
    <div class="cart-container">
        <div class="cart-header">
            <h1>Kosár</h1>
            <button class="btn btn-danger btn-sm">Kosár ürítése</button>
        </div>

        <!-- Cart Items -->
        <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: bejelentkezes.php"); // Ha nincs bejelentkezve, átirányítás a bejelentkezési oldalra
        exit;
    }
    $userId = $_SESSION['user_id']; // Bejelentkezett felhasználó azonosítója

    include './adatbazisra_csatlakozas.php';

    // Kosár tartalmának lekérdezése az adatbázisból
    $query = "SELECT etelek.nev, etelek.kep_url, etelek.egyseg_ar, kosar.mennyiseg 
              FROM kosar 
              JOIN etelek ON kosar.etel_id = etelek.id 
              WHERE kosar.felhasznalo_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $total = 0;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $itemTotal = $row['egyseg_ar'] * $row['mennyiseg'];
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
            echo '        <span>' . htmlspecialchars($row['mennyiseg']) . '</span>';
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

        <button class="checkout-btn">Tovább a fizetéshez</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>