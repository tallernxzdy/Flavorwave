<?php
session_start(); // Munkamenet indítása

include 'adatbazisra_csatlakozas.php';

if (!isset($_SESSION['felhasznalo_id']) || $_SESSION['jog_szint'] == 0) {
    header('Location: bejelentkezes.php');
    exit();
}

// Oldal újratöltésekor töröljük a kiválasztott rendelést
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_POST['ajax'])) {
    unset($_SESSION['selected_order_id']);
    $selected_order_id = null;
}

// Üzenetek inicializálása
$message = "";
$message_type = "";

// AJAX kérés kezelése a táblázat frissítéséhez
if (isset($_POST['ajax']) && $_POST['ajax'] === 'get_order_details') {
    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    if ($order_id > 0) {
        $_SESSION['selected_order_id'] = $order_id;
        $order_details = getOrderDetails($order_id);
        ob_start();
        if (!empty($order_details)) {
?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Rendelés ID</th>
                            <th>Felhasználó</th>
                            <th>Étel</th>
                            <th>Mennyiség</th>
                            <th>Egységár</th>
                            <th>Összesen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_details as $item) { ?>
                            <tr>
                                <td><?= $item['rendeles_id'] ?></td>
                                <td><?= htmlspecialchars($item['felhasznalo_nev']) ?></td>
                                <td><?= htmlspecialchars($item['etel_nev']) ?></td>
                                <td><?= $item['mennyiseg'] ?></td>
                                <td><?= $item['egyseg_ar'] ?> Ft</td>
                                <td><?= $item['osszesen'] ?> Ft</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
<?php
        } else {
            echo '<p class="text-center">Nincs tétel ehhez a rendeléshez.</p>';
        }
        $html = ob_get_clean();
        echo json_encode(['html' => $html]);
        exit;
    } else {
        unset($_SESSION['selected_order_id']);
        echo json_encode(['html' => '<p class="text-center">Kérlek, válassz ki egy rendelést a részletek megtekintéséhez.</p>']);
        exit;
    }
}

// AJAX kérés a legördülő menük frissítéséhez
if (isset($_POST['ajax']) && $_POST['ajax'] === 'refresh_dropdowns') {
    $dropdowns = [
        'pending' => rendeleseLekerdezese(0),
        'in_progress' => rendeleseLekerdezese(1),
        'completed' => rendeleseLekerdezese(2)
    ];
    echo json_encode($dropdowns);
    exit;
}

// Rendelés állapot frissítése
if (isset($_POST['update_status'])) {
    $rendeles_id = $_POST['order_id'];
    $uj_allapot = $_POST['new_status'];

    if (!empty($rendeles_id)) {
        $result = adatokValtoztatasa(
            "UPDATE megrendeles SET leadas_allapota = ? WHERE id = ?",
            ["ii", $uj_allapot, $rendeles_id]
        );

        if ($result === 'Sikeres művelet!') {
            $message = "A rendelés állapota sikeresen frissítve!";
            $message_type = "success";
            $_SESSION['selected_order_id'] = $rendeles_id; // Kiválasztás megtartása
        } else {
            $message = "Hiba történt a rendelés állapotának frissítésekor: $result";
            $message_type = "error";
        }
    } else {
        $message = "Nincs kiválasztva rendelés!";
        $message_type = "error";
    }
}

// Rendelés elküldése
if (isset($_POST['finished_order'])) {
    $rendeles_id = $_POST['order_id'];

    if (!empty($rendeles_id)) {
        $result = adatokValtoztatasa(
            "UPDATE megrendeles SET leadas_allapota = 3 WHERE id = ?",
            ["i", $rendeles_id]
        );

        if ($result === 'Sikeres művelet!') {
            $message = "A rendelés sikeresen elküldve!";
            $message_type = "success";
            unset($_SESSION['selected_order_id']);
            $selected_order_id = null;
        } else {
            $message = "Hiba történt a rendelés elküldésekor: $result";
            $message_type = "error";
        }
    } else {
        $message = "Nincs kiválasztva rendelés!";
        $message_type = "error";
    }
}

// Kiválasztás törlése, ha üresen küldik a formot
if (isset($_POST['order_id']) && empty($_POST['order_id']) && !isset($_POST['update_status']) && !isset($_POST['finished_order']) && !isset($_POST['ajax'])) {
    unset($_SESSION['selected_order_id']);
    $selected_order_id = null;
}

// Rendelés részleteinek lekérdezése
function getOrderDetails($order_id) {
    return adatokLekerdezese(
        "SELECT 
            megrendeles.id AS rendeles_id,
            felhasznalo.felhasznalo_nev,
            etel.nev AS etel_nev,
            rendeles_tetel.mennyiseg,
            etel.egyseg_ar,
            (rendeles_tetel.mennyiseg * etel.egyseg_ar) AS osszesen
         FROM megrendeles
         INNER JOIN rendeles_tetel ON rendeles_tetel.rendeles_id = megrendeles.id
         INNER JOIN etel ON rendeles_tetel.termek_id = etel.id
         INNER JOIN felhasznalo ON felhasznalo.id = megrendeles.felhasznalo_id
         WHERE megrendeles.id = ?",
        ["i", $order_id]
    );
}

// Kiválasztott rendelés ID kezelése
$selected_order_id = null;
if (isset($_SESSION['selected_order_id'])) {
    $selected_order_id = $_SESSION['selected_order_id'];
}
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolgozói Felület</title>
    <!-- CSS fájlok -->
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/dolgozoi_felulet.css">
    <link rel="icon" href="../kepek/logo.png" type="image/png">
    <!-- Font Awesome a profil ikonhoz -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</head>

<body>
    <?php include './navbar.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center my-4">Dolgozói felület</h1>

        <!-- Átvételre váró rendelések -->
        <div class="card bg-warning mt-5 mb-5">
            <div class="card-header bg-dark text-white">
                <h3>Friss rendelések</h3>
            </div>
            <div class="card-body bg-light">
                <form method="POST" action="dolgozoi_felulet.php">
                    <select name="order_id" id="pending_orders" class="form-select mb-3">
                        <option value="">Kérlek válassz egy megrendelést</option>
                        <?php foreach (rendeleseLekerdezese(0) as $order) { ?>
                            <option value="<?= $order['id'] ?>" <?= isset($selected_order_id) && $selected_order_id == $order['id'] ? 'selected' : '' ?>>
                                Rendelés #<?= $order['id'] ?> - <?= htmlspecialchars($order['felhasznalo_nev']) ?>
                            </option>
                        <?php } ?>
                    </select>
                    <input type="hidden" name="new_status" value="1">
                    <button type="submit" name="update_status" class="btn btn-custom btn-info">
                        Készítésre áthelyezés
                    </button>
                </form>
            </div>
        </div>

        <!-- Folyamatban lévő rendelések -->
        <div class="card bg-warning mt-5 mb-5">
            <div class="card-header bg-dark text-white">
                <h3>Folyamatban lévő rendelések</h3>
            </div>
            <div class="card-body bg-light">
                <form method="POST" action="dolgozoi_felulet.php">
                    <select name="order_id" id="in_progress_orders" class="form-select mb-3">
                        <option value="">Kérlek válassz egy megrendelést</option>
                        <?php foreach (rendeleseLekerdezese(1) as $order) { ?>
                            <option value="<?= $order['id'] ?>" <?= isset($selected_order_id) && $selected_order_id == $order['id'] ? 'selected' : '' ?>>
                                Rendelés #<?= $order['id'] ?> - <?= htmlspecialchars($order['felhasznalo_nev']) ?>
                            </option>
                        <?php } ?>
                    </select>
                    <input type="hidden" name="new_status" value="2">
                    <button type="submit" name="update_status" class="btn btn-custom btn-success">Kész rendelés</button>
                </form>
            </div>
        </div>

        <!-- Kész rendelések -->
        <div class="card bg-warning mt-5 mb-5">
            <div class="card-header bg-dark text-white">
                <h3>Kész rendelések</h3>
            </div>
            <div class="card-body bg-light">
                <form method="POST" action="dolgozoi_felulet.php">
                    <select name="order_id" id="completed_orders" class="form-select mb-3">
                        <option value="">Kérlek válassz egy megrendelést</option>
                        <?php foreach (rendeleseLekerdezese(2) as $order) { ?>
                            <option value="<?= $order['id'] ?>" <?= isset($selected_order_id) && $selected_order_id == $order['id'] ? 'selected' : '' ?>>
                                Rendelés #<?= $order['id'] ?> - <?= htmlspecialchars($order['felhasznalo_nev']) ?>
                            </option>
                        <?php } ?>
                    </select>
                    <button type="submit" name="finished_order" class="btn btn-danger-custom btn-danger">Rendelés Elküldése</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Admin felületre ugrás -->
    <?php
    if (isset($_SESSION['jog_szint']) && $_SESSION['jog_szint'] == 1) {
        echo '<div class="d-grid gap-2 col-6 mx-auto">
                <a class="btn btn-secondary" href="admin_felulet.php">Admin felület</a>
              </div>';
    }
    ?>

    <!-- Kiválasztott rendelés részletei -->
    <div class="container mt-5 mb-5">
        <h3 class="text-center">Kiválasztott rendelés részletei</h3>
        <div id="order-details">
            <?php if (!empty($selected_order_id)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Rendelés ID</th>
                                <th>Felhasználó</th>
                                <th>Étel</th>
                                <th>Mennyiség</th>
                                <th>Egységár</th>
                                <th>Összesen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $order_details = getOrderDetails($selected_order_id);
                            if (!empty($order_details)) {
                                foreach ($order_details as $item) {
                                    echo "<tr>
                                            <td>{$item['rendeles_id']}</td>
                                            <td>" . htmlspecialchars($item['felhasznalo_nev']) . "</td>
                                            <td>" . htmlspecialchars($item['etel_nev']) . "</td>
                                            <td>{$item['mennyiseg']}</td>
                                            <td>{$item['egyseg_ar']} Ft</td>
                                            <td>{$item['osszesen']} Ft</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>Nincs tétel ehhez a rendeléshez.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center">Kérlek, válassz ki egy rendelést a részletek megtekintéséhez.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Megerősítő modal üzenetek -->
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
    </div>

    <script>
    $(document).ready(function() {
        // Három legördülő menü azonosítója
        const dropdowns = ['#pending_orders', '#in_progress_orders', '#completed_orders'];

        // Táblázat frissítése AJAX-szal
        function updateOrderDetails(orderId) {
            $.post("dolgozoi_felulet.php", {
                ajax: 'get_order_details',
                order_id: orderId
            }, function(response) {
                try {
                    const data = JSON.parse(response);
                    $("#order-details").html(data.html);
                } catch (e) {
                    showMessage("Hiba történt a táblázat frissítésekor!", "error");
                }
            }).fail(function() {
                showMessage("Szerverhiba történt!", "error");
            });
        }

        // Legördülő menük frissítése AJAX-szal
        function refreshDropdowns(selectedOrderId = null) {
            $.post("dolgozoi_felulet.php", {
                ajax: 'refresh_dropdowns'
            }, function(response) {
                try {
                    const data = JSON.parse(response);
                    // Frissítjük a legördülő menüket
                    $("#pending_orders").html('<option value="">Kérlek válassz egy megrendelést</option>');
                    data.pending.forEach(order => {
                        $("#pending_orders").append(
                            `<option value="${order.id}" ${selectedOrderId == order.id ? 'selected' : ''}>Rendelés #${order.id} - ${order.felhasznalo_nev}</option>`
                        );
                    });

                    $("#in_progress_orders").html('<option value="">Kérlek válassz egy megrendelést</option>');
                    data.in_progress.forEach(order => {
                        $("#in_progress_orders").append(
                            `<option value="${order.id}" ${selectedOrderId == order.id ? 'selected' : ''}>Rendelés #${order.id} - ${order.felhasznalo_nev}</option>`
                        );
                    });

                    $("#completed_orders").html('<option value="">Kérlek válassz egy megrendelést</option>');
                    data.completed.forEach(order => {
                        $("#completed_orders").append(
                            `<option value="${order.id}" ${selectedOrderId == order.id ? 'selected' : ''}>Rendelés #${order.id} - ${order.felhasznalo_nev}</option>`
                        );
                    });
                } catch (e) {
                    showMessage("Hiba történt a legördülő menük frissítésekor!", "error");
                }
            }).fail(function() {
                showMessage("Szerverhiba történt!", "error");
            });
        }

        // Ha egy legördülő menüben választanak, a többit alaphelyzetbe állítjuk
        dropdowns.forEach(function(dropdown) {
            $(dropdown).on('change', function() {
                const orderId = $(this).val();
                // A többi legördülő menü alaphelyzetbe állítása
                dropdowns.forEach(function(otherDropdown) {
                    if (otherDropdown !== dropdown) {
                        $(otherDropdown).val("");
                    }
                });

                // Táblázat frissítése
                updateOrderDetails(orderId);
            });
        });

        // Üzenetek kezelése
        function showMessage(message, type) {
            $("#message-text").text(message);
            $("#message").removeClass("alert-success alert-danger").addClass(type === "success" ? "alert-success" : "alert-danger").fadeIn();
            setTimeout(function() {
                $("#message").fadeOut();
            }, 3000);
        }

        // Készítésre áthelyezés
        function moveToPreparation() {
            let orderId = $("#pending_orders").val();
            if (!orderId) {
                showMessage("Válassz ki egy rendelést a készítésre áthelyezéshez!", "error");
                return;
            }
            openConfirmationModal(function() {
                $.post("dolgozoi_felulet.php", {
                    update_status: true,
                    order_id: orderId,
                    new_status: 1
                }, function(response) {
                    showMessage("A rendelés állapota sikeresen frissítve!", "success");
                    refreshDropdowns(orderId); // Legördülő menük frissítése, kiválasztás megtartása
                    updateOrderDetails(orderId); // Táblázat frissítése
                }).fail(function() {
                    showMessage("Szerverhiba történt!", "error");
                });
            });
        }

        // Kész rendelés
        function completeOrder() {
            let orderId = $("#in_progress_orders").val();
            if (!orderId) {
                showMessage("Válassz ki egy rendelést a készre jelöléshez!", "error");
                return;
            }
            openConfirmationModal(function() {
                $.post("dolgozoi_felulet.php", {
                    update_status: true,
                    order_id: orderId,
                    new_status: 2
                }, function(response) {
                    showMessage("A rendelés állapota sikeresen frissítve!", "success");
                    refreshDropdowns(orderId); // Legördülő menük frissítése, kiválasztás megtartása
                    updateOrderDetails(orderId); // Táblázat frissítése
                }).fail(function() {
                    showMessage("Szerverhiba történt!", "error");
                });
            });
        }

        // Rendelés elküldése
        function finishOrder() {
            let orderId = $("#completed_orders").val();
            if (!orderId) {
                showMessage("Válassz ki egy rendelést az elküldéshez!", "error");
                return;
            }
            openConfirmationModal(function() {
                $.post("dolgozoi_felulet.php", {
                    finished_order: true,
                    order_id: orderId
                }, function(response) {
                    showMessage("A rendelés sikeresen elküldva!", "success");
                    $("#completed_orders").val("");
                    refreshDropdowns(); // Legördülő menük frissítése, kiválasztás nélkül
                    updateOrderDetails(""); // Táblázat visszaállítása
                }).fail(function() {
                    showMessage("Szerverhiba történt!", "error");
                });
            });
        }

        // Megerősítő modal
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

        // Gombok eseménykezelői
        $("button[name='update_status']").on("click", function(e) {
            e.preventDefault();
            if ($(this).text().includes("Készítésre áthelyezés")) {
                moveToPreparation();
            } else if ($(this).text().includes("Kész rendelés")) {
                completeOrder();
            }
        });

        $("button[name='finished_order']").on("click", function(e) {
            e.preventDefault();
            finishOrder();
        });

        // Hamburger menü toggle
        function toggleMenu() {
            $('.hamburger').toggleClass('active');
            $('#menubar').toggleClass('active');
        }

        $('.hamburger').on('click', function() {
            toggleMenu();
        });

        // Menü linkek kattintásakor bezárjuk a menüt
        $('#menubar a').on('click', function() {
            toggleMenu();
        });

        // Üzenet megjelenítése, ha van
        <?php if (!empty($message)): ?>
            showMessage("<?= htmlspecialchars($message) ?>", "<?= $message_type ?>");
        <?php endif; ?>
    });
    </script>
</body>
</html>