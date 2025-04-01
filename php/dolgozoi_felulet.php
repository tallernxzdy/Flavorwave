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
        // Frissítsd a rendelés állapotát 3-ra (elküldött állapot)
        $stmt = $conn->prepare("UPDATE megrendeles SET leadas_allapota = 3 WHERE id = ?");
        $stmt->bind_param("i", $rendeles_id);

        if ($stmt->execute()) {
            $message = "A rendelés sikeresen elküldve!";
            $message_type = "success";
            
            // Store the order ID in session to display its details
            $_SESSION['last_completed_order'] = $rendeles_id;
        } else {
            $message = "Hiba történt a rendelés elküldésekor!";
            $message_type = "error";
        }
    } else {
        $message = "Nincs kiválasztva rendelés!";
        $message_type = "error";
    }
}

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

?>





<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolgozói Felület</title>
    <!-- CSS fájok -->
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="../css/fooldal/ujfooldal.css">
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</head>

<body>
    <?php
        include './navbar.php';
    ?>

    <div class="container mt-5">
        <h1 class="text-center my-4">Dolgozói felület</h1>

        <!-- Átvételre váró rendelések -->
        <div class="card mt-5 mb-5">
            <div class="card-header">
                <h3>Átvételre váró rendelések</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="dolgozoi_felulet.php">
                    <select name="order_id" class="form-select mb-3">
                        <option>Kérlek válassz egy menüt</option>
                        <?php foreach (rendeleseLekerdezese(0) as $order) { ?>
                            <option value="<?= $order['id'] ?>">Rendelés #<?= $order['id'] ?> -
                                <?= htmlspecialchars($order['felhasznalo_nev']) ?></option>
                        <?php } ?>
                    </select>
                    <input type="hidden" name="new_status" value="1">
                    <button type="submit" name="update_status" class="btn btn-custom btn-info">Készítésre
                        áthelyezés</button>
                </form>
            </div>
        </div>

        <!-- Folyamatban lévő rendelések -->
        <div class="card mt-5 mb-5">
            <div class="card-header">
                <h3>Folyamatban lévő rendelések</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="dolgozoi_felulet.php">
                    <select name="order_id" class="form-select mb-3">
                        <option>Kérlek válassz egy menüt</option>
                        <?php foreach (rendeleseLekerdezese(1) as $order) { ?>
                            <option value="<?= $order['id'] ?>">Rendelés #<?= $order['id'] ?> -
                                <?= htmlspecialchars($order['felhasznalo_nev']) ?></option>
                        <?php } ?>
                    </select>
                    <input type="hidden" name="new_status" value="2">
                    <button type="submit" name="update_status" class="btn btn-custom btn-success">Kész rendelés</button>
                </form>
            </div>
        </div>

        <!-- Kész rendelések -->
        <div class="card mt-5 mb-5">
            <div class="card-header">
                <h3>Kész rendelések</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="dolgozoi_felulet.php">
                    <select name="order_id" class="form-select mb-3">
                        <option>Kérlek válassz egy menüt</option>
                        <?php foreach (rendeleseLekerdezese(2) as $order) { ?>
                            <option value="<?= $order['id'] ?>">Rendelés #<?= $order['id'] ?> -
                                <?= htmlspecialchars($order['felhasznalo_nev']) ?></option>
                        <?php } ?>
                    </select>
                    <button type="submit" name="finished_order" class="btn btn-danger-custom btn-danger">Rendelés
                        Elküldése</button>
                </form>
            </div>
        </div>
    </div>



    <!-- admin felületre ugrás -->
    <?php
    if (isset($_SESSION['jog_szint']) && $_SESSION['jog_szint'] == 1) {
    echo '<div class="d-grid gap-2 col-6 mx-auto"> 
            <a class="btn btn-secondary" href="admin_felulet.php">Admin felület</a> 
        </div>';
    }
    ?>


<?php
    // Megrendelések lekérdezése
    $query = "SELECT megrendeles.id, rendeles_tetel.mennyiseg, etel.nev 
    AS etel_nev, etel.egyseg_ar, felhasznalo.felhasznalo_nev FROM megrendeles 
    INNER JOIN rendeles_tetel ON rendeles_tetel.rendeles_id = megrendeles.id 
    INNER JOIN etel ON rendeles_tetel.termek_id = etel.id 
    INNER JOIN felhasznalo ON felhasznalo.id = megrendeles.felhasznalo_id 
    WHERE leadas_allapota = 3;";

    $result = $conn->query($query);
?>

    <div class="container mt-5 mb-5">
        <h3 class="text-center">Kész megrendelések listája</h3>
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
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $osszesen = $row["mennyiseg"] * $row["egyseg_ar"];
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['felhasznalo_nev']}</td>
                                    <td>{$row['etel_nev']}</td>
                                    <td>{$row['mennyiseg']}</td>
                                    <td>{$row['egyseg_ar']} Ft</td>
                                    <td>{$osszesen} Ft</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>Nincs elérhető adat.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
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
        <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
            aria-hidden="true">
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

            $("#confirmAction").on("click", function () {
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

                openConfirmationModal(function () {
                    $.post("dolgozoi_felulet.php",
                        { update_status: true, order_id: orderId, new_status: 1 },
                        function (response) {
                            console.log("Szerver válasz:", response);
                            if (response.status === "success") {
                                showMessage(response.message, "success");
                                location.reload();
                            } else {
                                showMessage("Hiba: " + response.message, "error");
                            }
                        },
                        "json"
                    ).fail(function () {
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

                openConfirmationModal(function () {
                    $.post("dolgozoi_felulet.php",
                        { update_status: true, order_id: orderId, new_status: 2 },
                        function (response) {
                            console.log("Szerver válasz:", response);
                            if (response.status === "success") {
                                showMessage(response.message, "success");
                                location.reload();
                            } else {
                                showMessage("Hiba: " + response.message, "error");
                            }
                        },
                        "json"
                    ).fail(function () {
                        showMessage("Szerverhiba történt!", "error");
                    });
                });
            }

            function finishOrder() {
                let orderId = $("#completed_orders").val();
                if (!orderId) {
                    showMessage("Válassz ki egy rendelést a törléshez!", "error");
                    return;
                }

                openConfirmationModal(function () {
                    $.post("dolgozoi_felulet.php",
                        { finished_order: true, order_id: orderId },
                        function (response) {
                            if (response.status === "success") {
                                showMessage(response.message, "success");
                                location.reload();
                            } else {
                                showMessage("Hiba: " + response.message, "error");
                            }
                        },
                        "json"
                    ).fail(function () {
                        showMessage("Szerverhiba történt!", "error");
                    });
                });
            }

            function showMessage(message, type) {
                $("#message-text").text(message);
                $("#message").removeClass("alert-success alert-danger").addClass(type === "success" ? "alert-success" : "alert-danger").fadeIn();

                setTimeout(function () {
                    $("#message").fadeOut();
                }, 3000);
            }

        </script>

</body>

</html>