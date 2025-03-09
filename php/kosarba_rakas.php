<?php
session_start();
include 'adatbazisra_csatlakozas.php';

$data = json_decode(file_get_contents('php://input'), true);
$itemId = $data['itemId'];

if (isset($_SESSION['felhasznalo_id'])) {
    // Logged in user - update database
    $userId = $_SESSION['felhasznalo_id'];

    // Check if the item already exists in the cart
    $query = "SELECT darab FROM tetelek WHERE felhasznalo_id = ? AND etel_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $userId, $itemId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If the item exists, increment the quantity
        $row = $result->fetch_assoc();
        $newQuantity = $row['darab'] + 1;

        $updateQuery = "UPDATE tetelek SET darab = ? WHERE felhasznalo_id = ? AND etel_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("iii", $newQuantity, $userId, $itemId);
        $updateStmt->execute();
    } else {
        // If the item doesn't exist, insert it with a quantity of 1
        $insertQuery = "INSERT INTO tetelek (felhasznalo_id, etel_id, darab) VALUES (?, ?, 1)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ii", $userId, $itemId);
        $insertStmt->execute();
    }
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