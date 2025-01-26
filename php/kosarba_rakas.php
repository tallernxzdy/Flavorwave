<?php
session_start();
include 'adatbazisra_csatlakozas.php';

$data = json_decode(file_get_contents('php://input'), true);
$itemId = $data['itemId'];

if (isset($_SESSION['felhasznalo_id'])) {
    // Logged in user - update database
    $userId = $_SESSION['felhasznalo_id'];
    $query = "INSERT INTO tetelek (felhasznalo_id, etel_id, darab) VALUES (?, ?, 1) 
              ON DUPLICATE KEY UPDATE darab = darab + 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $userId, $itemId);
    $stmt->execute();
} else {
    // Guest user - update session and cookie
    if (!isset($_SESSION['kosar'])) {
        $_SESSION['kosar'] = [];
    }
    $_SESSION['kosar'][$itemId] = isset($_SESSION['kosar'][$itemId]) ? $_SESSION['kosar'][$itemId] + 1 : 1;

    // Update cookie with 7-day expiration
    setcookie('guest_cart', json_encode($_SESSION['kosar']), time() + 604800, '/');
}

echo json_encode(['success' => true]);
?>