<?php
session_start();
include 'adatbazisra_csatlakozas.php';

$response = ['success' => false];

if (isset($_SESSION['felhasznalo_id'])) {
    // Bejelentkezett felhasználó - töröljük az adatbázisból
    $userId = $_SESSION['felhasznalo_id'];
    $deleteQuery = "DELETE FROM tetelek WHERE felhasznalo_id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $userId);

    if ($deleteStmt->execute()) {
        $response['success'] = true;
    }
} else {
    // Vendég felhasználó - töröljük a session-ből és a cookie-ból
    unset($_SESSION['kosar']);
    setcookie('guest_cart', '', time() - 3600, '/');
    $response['success'] = true;
}

// JSON válasz küldése
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
