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
        $stmt = $conn->prepare("UPDATE tetelek SET darab = darab " . ($action === "increase" ? "+ 1" : "- 1") . " WHERE felhasznalo_id = ? AND etel_id = ?");
        $stmt->bind_param("ii", $userId, $itemId);
        $stmt->execute();

        // Get new quantity
        $stmt = $conn->prepare("SELECT darab FROM tetelek WHERE felhasznalo_id = ? AND etel_id = ?");
        $stmt->bind_param("ii", $userId, $itemId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $newQuantity = $row['darab'];

            // Remove item if quantity reaches 0
            if ($newQuantity <= 0) {
                $conn->query("DELETE FROM tetelek WHERE felhasznalo_id = $userId AND etel_id = $itemId");
                $newQuantity = 0;
            }

            $response = ['success' => true, 'newQuantity' => $newQuantity];
        }
    } else {
        // Guest user - update session and cookie
        if (isset($_SESSION['kosar'][$itemId])) {
            if ($action === "increase") {
                $_SESSION['kosar'][$itemId]++;
            } else {
                $_SESSION['kosar'][$itemId]--;
            }

            $newQuantity = $_SESSION['kosar'][$itemId];

            // Remove from session if quantity <= 0
            if ($_SESSION['kosar'][$itemId] <= 0) {
                unset($_SESSION['kosar'][$itemId]);
                $newQuantity = 0;
            }

            // Update cookie
            setcookie('guest_cart', json_encode($_SESSION['kosar']), time() + 604800, '/');
            $response = ['success' => true, 'newQuantity' => $newQuantity];
        }
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>