<?php
session_start();
include 'adatbazisra_csatlakozas.php';

$data = json_decode(file_get_contents('php://input'), true);
$itemId = $data['itemId'];
$action = $data['action'];
$response = ['success' => false, 'newQuantity' => 0];

try {
    if (isset($_SESSION['felhasznalo_id'])) {
        // Logged in user - update database
        $userId = $_SESSION['felhasznalo_id'];

        // Check if the item exists in the cart
        $stmt = $conn->prepare("SELECT darab FROM tetelek WHERE felhasznalo_id = ? AND etel_id = ?");
        $stmt->bind_param("ii", $userId, $itemId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If the item exists, update the quantity
            $row = $result->fetch_assoc();
            $newQuantity = $row['darab'];

            if ($action === "increase") {
                $newQuantity++;
            } else {
                $newQuantity--;
            }

            // If the quantity is 0 or less, delete the item
            if ($newQuantity <= 0) {
                $deleteQuery = "DELETE FROM tetelek WHERE felhasznalo_id = ? AND etel_id = ?";
                $deleteStmt = $conn->prepare($deleteQuery);
                $deleteStmt->bind_param("ii", $userId, $itemId);
                $deleteStmt->execute();
                $newQuantity = 0;
            } else {
                // Otherwise, update the quantity
                $updateQuery = "UPDATE tetelek SET darab = ? WHERE felhasznalo_id = ? AND etel_id = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("iii", $newQuantity, $userId, $itemId);
                $updateStmt->execute();
            }
        }
    } else {
        // Guest user - update session
        if (!isset($_SESSION['kosar'])) {
            $_SESSION['kosar'] = [];
        }

        if (isset($_SESSION['kosar'][$itemId])) {
            if ($action === "increase") {
                $_SESSION['kosar'][$itemId]++;
            } else {
                $_SESSION['kosar'][$itemId]--;
            }

            $newQuantity = $_SESSION['kosar'][$itemId];

            // If the quantity is 0 or less, remove the item
            if ($_SESSION['kosar'][$itemId] <= 0) {
                unset($_SESSION['kosar'][$itemId]);
                $newQuantity = 0;
            }
        }
    }

    $response = ['success' => true, 'newQuantity' => $newQuantity];
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>