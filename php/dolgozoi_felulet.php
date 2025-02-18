<?php
    include 'adatbazisra_csatlakozas.php';
    // Rendelés állapot frissítése
    if (isset($_POST['update_status'])) {
        $uj_allapot = $_POST['new_status'];
        $rendeles_id = $_POST['order_id'];
        $stmt = $conn->prepare("UPDATE megrendeles SET leadas_allapota = ? WHERE id = ?");
        $stmt->bind_param("ii", $uj_allapot, $rendeles_id);
        $stmt->execute();
    }

    // Rendelés törlése
    if (isset($_POST['delete_order'])) {
        $rendeles_id = $_POST['order_id'];
        $stmt = $conn->prepare("DELETE FROM megrendeles WHERE id = ?");
        $stmt->bind_param("i", $rendeles_id);
        $stmt->execute();
    }


?>


<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolgozói Felület</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/admin_felulet.css">
    <link rel="stylesheet" href="../css/dolgozoi_felulet.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
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
                <li ><a href="admin_felulet.php">Admin felület</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 2): ?>
                <li ><a href="dolgozoi_felulet.php">dolgozói felület</a></li>
            <?php endif; ?>
            <li><a href="kosar.php">Kosár</a></li>
            <?php if (isset($_SESSION["felhasznalo_nev"])): ?>
                <li><a href="kijelentkezes.php">Kijelentkezés</a></li>
            <?php else: ?>
                <li><a href="bejelentkezes.php">Bejelentkezés</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="container">
        <h1 class="text-center my-4">Dolgozói felület</h1>
        
        <div class="card">
            <div class="card-header">
                <h3>Átvételre váró rendelések</h3>
            </div>
            <div class="card-body">
                <select id="pending_orders" class="form-select mb-3">
                    <option>Kérlek válassz egy menüt</option>
                    <?php foreach (rendeleseLekerdezese(0) as $order) { ?>
                        <option value="<?= $order['id'] ?>">Rendelés #<?= $order['id'] ?> - <?= htmlspecialchars($order['felhasznalo_nev']) ?></option>
                    <?php } ?>
                </select>
                <button class="btn btn-custom btn-info" onclick="updateStatus(1)">Készítésre áthelyezés</button>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Folyamatban lévő rendelések</h3>
            </div>
            <div class="card-body">
                <select id="in_progress_orders" class="form-select mb-3">
                    <option>Kérlek válassz egy menüt</option>
                    <?php foreach (rendeleseLekerdezese(1) as $order) { ?>
                        <option value="<?= $order['id'] ?>">Rendelés #<?= $order['id'] ?> - <?= htmlspecialchars($order['felhasznalo_nev']) ?></option>
                    <?php } ?>
                </select>
                <button class="btn btn-custom btn-success" onclick="updateStatus(2)">Kész rendelés</button>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Kész rendelések</h3>
            </div>
            <div class="card-body">
                <select id="completed_orders" class="form-select mb-3">
                    <option>Kérlek válassz egy menüt</option>
                    <?php foreach (rendeleseLekerdezese(2) as $order) { ?>
                        <option value="<?= $order['id'] ?>">Rendelés #<?= $order['id'] ?> - <?= htmlspecialchars($order['felhasznalo_nev']) ?></option>
                    <?php } ?>
                </select>
                <button class="btn btn-danger-custom btn-danger" onclick="deleteOrder()">Rendelés törlése</button>
            </div>
        </div>
    </div>

    <script>
        function updateStatus(newStatus) {
            let orderId = $("#pending_orders").val() || $("#in_progress_orders").val();
            if (!orderId) return;
            $.post("dolgozoi_felulet.php", { update_status: true, order_id: orderId, new_status: newStatus }, function() {
                location.reload();
            });
        }
        
        function deleteOrder() {
            let orderId = $("#completed_orders").val();
            if (!orderId) return;
            $.post("dolgozoi_felulet.php", { delete_order: true, order_id: orderId }, function() {
                location.reload();
            });
        }
    </script>
</body>
</html>



</body>
</html>