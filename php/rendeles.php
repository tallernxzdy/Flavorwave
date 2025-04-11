<?php
session_start();
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['felhasznalo_id'])) {
    header("Location: bejelentkezes.php");
    exit();
}

include './adatbazisra_csatlakozas.php';

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
$success = $_SESSION['order_success'] ?? false;
unset($_SESSION['order_success']);

$userId = $_SESSION['felhasznalo_id'];

// Felhasználó adatainak betöltése
$userData = [];
$query = "SELECT tel_szam, lakcim, Teljes_nev FROM felhasznalo WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();
}

// Kosár tartalmának betöltése
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

// Rendelés feldolgozása
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if (empty($cartItems)) {
        $errors[] = "A kosár üres, nem lehet leadni a rendelést.";
    } else {
        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $paymentMethod = isset($_POST['payment_method']) ? (int)$_POST['payment_method'] : null;

        // Validáció
        $errors = [];
        if (empty($name))
            $errors[] = "Név megadása kötelező";
        elseif (empty($address))
            $errors[] = "Cím megadása kötelező";
        elseif (empty($phone))
            $errors[] = "Telefonszám megadása kötelező";
        elseif (!preg_match('/^\+?[0-9]{9,12}$/', $phone))
            $errors[] = "Érvénytelen telefonszám formátum";
        elseif (!isset($paymentMethod) || ($paymentMethod !== 0 && $paymentMethod !== 1))
            $errors[] = "Fizetési mód kiválasztása kötelező";

        if (empty($errors)) {
            $conn->begin_transaction();
            try {
                // Felhasználó adatok frissítése
                $stmt = $conn->prepare("UPDATE felhasznalo SET tel_szam=?, lakcim=?, Teljes_nev=? WHERE id=?");
                $stmt->bind_param("sssi", $phone, $address, $name, $userId);
                $stmt->execute();

                // Rendelés létrehozása
                $stmt = $conn->prepare("INSERT INTO megrendeles (felhasznalo_id, leadas_megjegyzes, kezbesites, leadas_allapota, leadasdatuma, Fizetesi_mod) 
                                       VALUES (?, ?, 'házhozszállítás', 0, CURDATE(), ?)");
                $stmt->bind_param("isi", $userId, $notes, $paymentMethod);
                $stmt->execute();
                $orderId = $conn->insert_id;

                // Tételek hozzáadása
                $stmt = $conn->prepare("INSERT INTO rendeles_tetel (rendeles_id, termek_id, mennyiseg, Fizetesi_mod) VALUES (?, ?, ?, ?)");
                foreach ($cartItems as $item) {
                    $stmt->bind_param("iiii", $orderId, $item['id'], $item['darab'], $paymentMethod);
                    $stmt->execute();
                }

                // Kosár ürítése
                $conn->query("DELETE FROM tetelek WHERE felhasznalo_id=$userId");

                // Felhasználó e-mail címének lekérdezése
                $stmt = $conn->prepare("SELECT email_cim FROM felhasznalo WHERE id = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $userEmail = $result->fetch_assoc()['email_cim'];

                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'flavorwavereal@gmail.com';
                    $mail->Password = 'awch ocfs ldcr hded';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->CharSet = "UTF-8";
                    $mail->Port = 587;

                    $mail->setFrom('flavorwavereal@gmail.com', 'FlavorWave');
                    $mail->addAddress($userEmail, $name);

                    $paymentText = $paymentMethod == 0 ? "Készpénz" : "Bankkártya";
                    $mail->isHTML(true);
                    $mail->Subject = 'Rendelés visszaigazolás - FlavorWave';
                    $mail->Body = "
                        <h2>Kedves $name!</h2>
                        <p>Köszönjük rendelésedet! Az alábbiakban összefoglaljuk a rendelésed részleteit:</p>
                        <h3>Rendelési adatok:</h3>
                        <ul>
                            <li><strong>Rendelés azonosító:</strong> #$orderId</li>
                            <li><strong>Szállítási cím:</strong> $address</li>
                            <li><strong>Telefonszám:</strong> $phone</li>
                            <li><strong>Fizetési mód:</strong> $paymentText</li>
                            <li><strong>Megjegyzés:</strong> " . ($notes ?: 'Nincs') . "</li>
                        </ul>
                        <h3>Rendelt tételek:</h3>
                        <ul>";
                    foreach ($cartItems as $item) {
                        $mail->Body .= "<li>{$item['nev']} - {$item['darab']} db - " . ($item['egyseg_ar'] * $item['darab']) . " Ft</li>";
                    }
                    $mail->Body .= "
                        </ul>
                        <p><strong>Összesen:</strong> $total Ft</p>
                        <p>Hamarosan felvesszük veled a kapcsolatot a kiszállítás részleteivel kapcsolatban.</p>
                        <p>Üdvözlettel,<br>FlavorWave Csapat</p>";

                    $mail->send();
                } catch (Exception $e) {
                    error_log("E-mail küldési hiba: {$mail->ErrorInfo}");
                }

                $conn->commit();

                $_SESSION['order_success'] = true;
                header("Location: rendeles.php");
                exit();
            } catch (Exception $e) {
                $conn->rollback();
                $errors[] = "Hiba történt a rendelés feldolgozása során";
                $_SESSION['errors'] = $errors;
                header("Location: rendeles.php");
                exit();
            }
        } else {
            $_SESSION['errors'] = $errors;
            header("Location: rendeles.php");
            exit();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../kepek/logo.png" type="image/png">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/rendeles.css">
</head>

<body>
    <?php
        include './navbar.php';
    ?>

    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Sikeres rendelés</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Köszönjük rendelését! A rendelés részleteit elküldtük emailben.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="order-container">
        <h1 class="mb-4">Rendelés véglegesítése</h1>

        <div class="row">
            <div class="col-md-6">
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Teljes név:</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($userData['Teljes_nev'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Szállítási cím (Irányítószám, Utca, Házszám):</label>
                        <input type="text" class="form-control" id="address" name="address"
                            value="<?= htmlspecialchars($userData['lakcim'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefonszám:</label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                            value="<?= htmlspecialchars($userData['tel_szam'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fizetési mód</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cash" value="0" required>
                            <label class="form-check-label" for="cash">Készpénz</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="card" value="1">
                            <label class="form-check-label" for="card">Bankkártya</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Megjegyzés (opcionális)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Rendelés véglegesítése</button>
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

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errors[0]) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                    myModal.show();

                    document.getElementById('successModal').addEventListener('hidden.bs.modal', function () {
                        window.location.href = 'kezdolap.php';
                    });
                });
            </script>
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