<?php
session_start();
include './adatbazisra_csatlakozas.php';

if (!isset($_SESSION['felhasznalo_id'])) {
    header("Location: bejelentkezes.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['felhasznalo_id'];
    $teljes_nev = $_POST['teljes_nev'] ?? '';
    $felhasznalo_nev = $_POST['felhasznalo_nev'] ?? '';
    $email_cim = $_POST['email_cim'] ?? '';
    $tel_szam = $_POST['tel_szam'] ?? '';
    $lakcim = $_POST['lakcim'] ?? '';

    // SQL lekérdezés az adatok frissítésére
    $stmt = $conn->prepare("UPDATE felhasznalo SET Teljes_nev = ?, felhasznalo_nev = ?, email_cim = ?, tel_szam = ?, lakcim = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $teljes_nev, $felhasznalo_nev, $email_cim, $tel_szam, $lakcim, $user_id);

    if ($stmt->execute()) {
        header("Location: profil_megtekintes.php");
    } else {
        header("Location: profil_megtekintes.php");
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>