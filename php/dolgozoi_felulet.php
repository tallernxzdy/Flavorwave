<?php
session_start(); // Munkamenet indítása

include 'adatbazisra_csatlakozas.php';

if (!isset($_SESSION['felhasznalo_id']) || $_SESSION['jog_szint'] == 0) {
    header('Location: bejelentkezes.php');
    exit();
}

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

// Rendelés elküldése
if (isset($_POST['finished_order'])) {
    $rendeles_id = $_POST['order_id'];

    if (!empty($rendeles_id)) {
        $stmt = $conn->prepare("UPDATE megrendeles SET leadas_allapota = 3 WHERE id = ?");
        $stmt->bind_param("i", $rendeles_id);

        if ($stmt->execute()) {
            $message = "A rendelés sikeresen elküldve!";
            $message_type = "success";
            // Töröljük a kiválasztott rendelést, hogy a táblázat eltűnjön
            unset($_SESSION['selected_order_id']);
        } else {
            $message = "Hiba történt a rendelés elküldésekor!";
            $message_type = "error";
        }
    } else {
        $message = "Nincs kiválasztva rendelés!";
        $message_type = "error";
    }
}

// Kiválasztás törlése, ha üresen küldik a formot
if (isset($_POST['order_id']) && empty($_POST['order_id']) && !isset($_POST['update_status']) && !isset($_POST['finished_order'])) {
    unset($_SESSION['selected_order_id']);
}

// Rendelés részleteinek lekérdezése
function getOrderDetails($order_id) {
    global $conn;
    $query = "SELECT 
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
              WHERE megrendeles.id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Kiválasztott rendelés ID kezelése
$selected_order_id = null;
if (isset($_POST['order_id']) && !empty($_POST['order_id'])) {
    $selected_order_id = $_POST['order_id'];
    $_SESSION['selected_order_id'] = $selected_order_id; // Mindig frissítjük a SESSION-t, ha van új kiválasztás
} elseif (isset($_SESSION['selected_order_id']) && !isset($_POST['finished_order'])) {
    $selected_order_id = $_SESSION['selected_order_id'];
} else {
    unset($_SESSION['selected_order_id']); // Biztosítjuk, hogy üres állapotban ne maradjon régi érték
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
    <link rel="stylesheet" href="../css/fooldal/ujfooldal.css">
    <link rel="stylesheet" href="../css/dolgozoi_felulet.css">
    <link rel="icon" href="../kepek/logo.png" type="image/png">
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
    $(document).ready(function () {
        // Három legördülő menü azonosítója
        const dropdowns = ['#pending_orders', '#in_progress_orders', '#completed_orders'];

        // Ha egy legördülő menüben választanak, a többit alaphelyzetbe állítjuk
        dropdowns.forEach(function (dropdown) {
            $(dropdown).on('change', function () {
                if ($(this).val()) {
                    // A többi legördülő menü alaphelyzetbe állítása
                    dropdowns.forEach(function (otherDropdown) {
                        if (otherDropdown !== dropdown) {
                            $(otherDropdown).val("");
                        }
                    });

                    // Űrlap elküldése a kiválasztott rendelés ID-vel
                    let orderId = $(this).val();
                    $.post("dolgozoi_felulet.php", { order_id: orderId }, function () {
                        location.reload(); // Frissítjük az oldalt a táblázat frissítéséhez
                    });
                } else {
                    // Ha üresre állítják, töröljük a kiválasztást
                    $.post("dolgozoi_felulet.php", { order_id: "" }, function () {
                        location.reload();
                    });
                }
            });
        });

        // Üzenetek kezelése
        function showMessage(message, type) {
            $("#message-text").text(message);
            $("#message").removeClass("alert-success alert-danger").addClass(type === "success" ? "alert-success" : "alert-danger").fadeIn();
            setTimeout(function () {
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
            openConfirmationModal(function () {
                $.post("dolgozoi_felulet.php", { update_status: true, order_id: orderId, new_status: 1 }, function (response) {
                    showMessage("A rendelés állapota sikeresen frissítve!", "success");
                    location.reload();
                }).fail(function () {
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
            openConfirmationModal(function () {
                $.post("dolgozoi_felulet.php", { update_status: true, order_id: orderId, new_status: 2 }, function (response) {
                    showMessage("A rendelés állapota sikeresen frissítve!", "success");
                    location.reload();
                }).fail(function () {
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
            openConfirmationModal(function () {
                $.post("dolgozoi_felulet.php", { finished_order: true, order_id: orderId }, function (response) {
                    showMessage("A rendelés sikeresen elküldve!", "success");
                    location.reload();
                }).fail(function () {
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

        $("#confirmAction").on("click", function () {
            if (currentAction) {
                currentAction();
                $("#confirmationModal").modal("hide");
            }
        });

        // Üzenet megjelenítése, ha van
        <?php if (!empty($message)): ?>
            showMessage("<?= htmlspecialchars($message) ?>", "<?= $message_type ?>");
        <?php endif; ?>
    });
    </script>
</body>

</html>