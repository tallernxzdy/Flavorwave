<?php
session_start();
include 'adatbazisra_csatlakozas.php';

$data = json_decode(file_get_contents('php://input'), true);
$itemId = $data['itemId'];

if (isset($_SESSION['felhasznalo_id'])) {
    // Logged in user - delete from database
    $userId = $_SESSION['felhasznalo_id'];
    $query = "DELETE FROM tetelek WHERE felhasznalo_id = ? AND etel_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $userId, $itemId);
    $stmt->execute();
} else {
    // Guest user - delete from session
    if (isset($_SESSION['kosar'][$itemId])) {
        unset($_SESSION['kosar'][$itemId]);
        // Update cookie
        setcookie('guest_cart', json_encode($_SESSION['kosar']), time() + 604800, '/');
    }
}

echo json_encode(['success' => true]);
?>