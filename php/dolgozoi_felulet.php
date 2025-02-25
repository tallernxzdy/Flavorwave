<?php
    session_start(); // Munkamenet indítása

    include 'adatbazisra_csatlakozas.php';

    // Üzenetek inicializálása
    $message = "";
    $message_type = "";

    // Rendelés állapot frissítése
    if (isset($_POST['update_status'])) {
        $rendeles_id = $_POST['order_id'];
        $uj_allapot = $_POST['new_status'];

        if (!empty($rendeles_id)) {
            $stmt = $conn->prepare("UPDATE megrendeles SET leadas_allapota = ? WHERE id = ?");
            $stmt->bind_param("ii", $uj_allapot, $rendeles_id);

            if ($stmt->execute()) {
                $message = "A rendelés állapota sikeresen frissítve!";
                $message_type = "success";
            } else {
                $message = "Hiba történt a rendelés állapotának frissítésekor!";
                $message_type = "error";
            }
        } else {
            $message = "Nincs kiválasztva rendelés!";
            $message_type = "error";
        }
    }

    // Rendelés törlése
    if (isset($_POST['delete_order'])) {
        $rendeles_id = $_POST['order_id'];
        $stmt = $conn->prepare("DELETE FROM megrendeles WHERE id = ?");
        $stmt->bind_param("i", $rendeles_id);

        if ($stmt->execute()) {
            $message = "A rendelés sikeresen törölve!";
            $message_type = "success";
        } else {
            $message = "Hiba történt a rendelés törlésekor!";
            $message_type = "error";
        }
    }
?>

    



<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolgozói Felület</title>
    <!-- CSS fájok -->
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/admin_felulet.css">
    <link rel="stylesheet" href="../css/dolgozoi_felulet.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
            <i class='fas fa-shopping-cart cart-icon'></i>
        </a>
    </div>

    <!-- Hamburger menü ikon -->
    <div class="hamburger" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>
</nav>


    <!-- Üzenetek megjelenítése -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

<!-- Hamburger menü tartalma -->
<div class="menubar" id="menubar">
    <ul>
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
            <li><a href="rendeles.php">Rendelés</a></li>
    </ul>
</div>

<div class="container">
    <h1 class="text-center my-4">Dolgozói felület</h1>
    
    <!-- Átvételre váró rendelések -->
    <div class="card">
        <div class="card-header">
            <h3>Átvételre váró rendelések</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="dolgozoi_felulet.php">
                <select name="order_id" class="form-select mb-3">
                    <option>Kérlek válassz egy menüt</option>
                    <?php foreach (rendeleseLekerdezese(0) as $order) { ?>
                        <option value="<?= $order['id'] ?>">Rendelés #<?= $order['id'] ?> - <?= htmlspecialchars($order['felhasznalo_nev']) ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="new_status" value="1">
                <button type="submit" name="update_status" class="btn btn-custom btn-info">Készítésre áthelyezés</button>
            </form>
        </div>
    </div>

    <!-- Folyamatban lévő rendelések -->
    <div class="card">
        <div class="card-header">
            <h3>Folyamatban lévő rendelések</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="dolgozoi_felulet.php">
                <select name="order_id" class="form-select mb-3">
                    <option>Kérlek válassz egy menüt</option>
                    <?php foreach (rendeleseLekerdezese(1) as $order) { ?>
                        <option value="<?= $order['id'] ?>">Rendelés #<?= $order['id'] ?> - <?= htmlspecialchars($order['felhasznalo_nev']) ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="new_status" value="2">
                <button type="submit" name="update_status" class="btn btn-custom btn-success">Kész rendelés</button>
            </form>
        </div>
    </div>

    <!-- Kész rendelések -->
    <div class="card">
        <div class="card-header">
            <h3>Kész rendelések</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="dolgozoi_felulet.php">
                <select name="order_id" class="form-select mb-3">
                    <option>Kérlek válassz egy menüt</option>
                    <?php foreach (rendeleseLekerdezese(2) as $order) { ?>
                        <option value="<?= $order['id'] ?>">Rendelés #<?= $order['id'] ?> - <?= htmlspecialchars($order['felhasznalo_nev']) ?></option>
                    <?php } ?>
                </select>
                <button type="submit" name="delete_order" class="btn btn-danger-custom btn-danger">Rendelés törlése</button>
            </form>
        </div>
    </div>
</div>


    <!-- megerősítő modal üzenetek-->
    <div class="container">
    <!-- Üzenetek megjelenítése -->
    <div id="message" class="alert alert-dismissible fade show" role="alert" style="display: none;">
        <span id="message-text"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Megerősítő modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Megerősítés</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Biztosan létre szeretnéd hozni a változtatásokat?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégse</button>
                    <button type="button" class="btn btn-primary" id="confirmAction">Igen</button>
                </div>
            </div>
        </div>
    </div>



    <script>
    let currentAction = null;

    function openConfirmationModal(action) {
        currentAction = action;
        $("#confirmationModal").modal("show");
    }

    $("#confirmAction").on("click", function() {
        if (currentAction) {
            currentAction();
            $("#confirmationModal").modal("hide");
        }
    });

    function moveToPreparation() {
        let orderId = $("#pending_orders").val();

        if (!orderId || orderId === "Kérlek válassz egy menüt") {
            showMessage("Válassz ki egy rendelést a készítésre áthelyezéshez!", "error");
            return;
        }

        openConfirmationModal(function() {
            $.post("dolgozoi_felulet.php", 
                { update_status: true, order_id: orderId, new_status: 1 }, 
                function(response) {
                    console.log("Szerver válasz:", response);
                    if (response.status === "success") {
                        showMessage(response.message, "success");
                        location.reload();
                    } else {
                        showMessage("Hiba: " + response.message, "error");
                    }
                }, 
                "json"
            ).fail(function() {
                showMessage("Szerverhiba történt!", "error");
            });
        });
    }

    function completeOrder() {
        let orderId = $("#in_progress_orders").val();

        if (!orderId || orderId === "Kérlek válassz egy menüt") {
            showMessage("Válassz ki egy rendelést a készre jelöléshez!", "error");
            return;
        }

        openConfirmationModal(function() {
            $.post("dolgozoi_felulet.php", 
                { update_status: true, order_id: orderId, new_status: 2 }, 
                function(response) {
                    console.log("Szerver válasz:", response);
                    if (response.status === "success") {
                        showMessage(response.message, "success");
                        location.reload();
                    } else {
                        showMessage("Hiba: " + response.message, "error");
                    }
                }, 
                "json"
            ).fail(function() {
                showMessage("Szerverhiba történt!", "error");
            });
        });
    }

    function deleteOrder() {
        let orderId = $("#completed_orders").val();
        if (!orderId) {
            showMessage("Válassz ki egy rendelést a törléshez!", "error");
            return;
        }

        openConfirmationModal(function() {
            $.post("dolgozoi_felulet.php", 
                { delete_order: true, order_id: orderId }, 
                function(response) {
                    if (response.status === "success") {
                        showMessage(response.message, "success");
                        location.reload();
                    } else {
                        showMessage("Hiba: " + response.message, "error");
                    }
                }, 
                "json"
            ).fail(function() {
                showMessage("Szerverhiba történt!", "error");
            });
        });
    }
    
    function showMessage(message, type = "success") {
        const messageElement = $("#message");
        const messageText = $("#message-text");

        // Üzenet stílusának beállítása (siker vagy hiba)
        messageElement.removeClass("alert-success alert-danger").addClass(`alert-${type === "success" ? "success" : "danger"}`);
        messageText.text(message); // Üzenet szövegének beállítása
        messageElement.fadeIn(); // Üzenet megjelenítése

        // Üzenet eltűntetése 5 másodperc után
        setTimeout(() => {
            messageElement.fadeOut();
        }, 5000);
    }


    </script>



</body>
</html>