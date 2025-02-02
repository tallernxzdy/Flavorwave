<?php
// rendeles.php
session_start();
if (!isset($_SESSION['felhasznalo_id'])) {
    header("Location: bejelentkezes.php");
    exit();
}

include './adatbazisra_csatlakozas.php';

$userId = $_SESSION['felhasznalo_id'];
$errors = [];
$success = false;

// Get cart items
$cartItems = [];
$total = 0;

$query = "SELECT etel.id, etel.nev, etel.egyseg_ar, tetelek.darab 
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

// Process order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form inputs
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (empty($name))
        $errors[] = "Név megadása kötelező";
    if (empty($address))
        $errors[] = "Cím megadása kötelező";
    if (empty($phone))
        $errors[] = "Telefonszám megadása kötelező";
    if (!preg_match('/^\+?[0-9]{9,12}$/', $phone))
        $errors[] = "Érvénytelen telefonszám formátum";

    if (empty($errors)) {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Frissítsd a felhasználói adatokat
            $updateUserQuery = "UPDATE felhasznalo SET tel_szam = ?, lakcim = ? WHERE id = ?";
            $stmt = $conn->prepare($updateUserQuery);
            $stmt->bind_param("ssi", $phone, $address, $userId);
            $stmt->execute();
            $orderId = $conn->insert_id;

            // Insert order items
            $itemQuery = "INSERT INTO rendeles_tetel (rendeles_id, termek_id, mennyiseg) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($itemQuery);

            foreach ($cartItems as $item) {
                $stmt->bind_param("iii", $orderId, $item['id'], $item['darab']);
                $stmt->execute();
            }

            // Clear cart
            $deleteQuery = "DELETE FROM tetelek WHERE felhasznalo_id = ?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            // Commit transaction
            $conn->commit();

            // Send email
            $to = $_SESSION['email_cim'];
            $subject = "FlavorWave - Rendelés visszaigazolás (#$orderId)";

            $emailBody = "Köszönjük rendelését!\n\n";
            $emailBody .= "Rendelés részletei:\n";
            foreach ($cartItems as $item) {
                $emailBody .= "- {$item['nev']} ({$item['darab']} db) - " . ($item['darab'] * $item['egyseg_ar']) . " Ft\n";
            }
            $emailBody .= "\nÖsszesen: $total Ft\n";
            $emailBody .= "\nSzállítási adatok:\n";
            $emailBody .= "Név: $name\n";
            $emailBody .= "Cím: $address\n";
            $emailBody .= "Telefonszám: $phone\n";
            $emailBody .= "Megjegyzés: " . ($notes ?: "nincs") . "\n";

            $headers = "From: info@flavorwave.com";
            mail($to, $subject, $emailBody, $headers);

            $success = true;
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Hiba történt a rendelés feldolgozása során";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlavorWave - Rendelés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/rendeles.css">

</head>

<body>
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
        </div>

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

        <div class="hamburger" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

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

    <div class="order-container">
        <h1 class="mb-4">Rendelés véglegesítése</h1>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <script>
                alert('Köszönjük rendelését! A rendelés részleteit elküldtük emailben.');
                window.location.href = 'kezdolap.php';
            </script>
        <?php else: ?>
            <div class="row">
                <div class="col-md-6">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Teljes név</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Szállítási cím</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefonszám</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Megjegyzés</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Rendelés véglegesítése</button>
                    </form>
                </div>

                <div class="col-md-6">
                    <div class="cart-summary">
                        <h4 class="mb-3">Rendelés összegzése</h4>
                        <?php foreach ($cartItems as $item): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span><?= $item['nev'] ?> (<?= $item['darab'] ?> db)</span>
                                <span><?= $item['darab'] * $item['egyseg_ar'] ?> Ft</span>
                            </div>
                        <?php endforeach; ?>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Összesen:</span>
                            <span><?= $total ?> Ft</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

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
</body>

</html>