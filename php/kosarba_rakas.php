<?php
session_start();
include 'adatbazisra_csatlakozas.php';

$data = json_decode(file_get_contents('php://input'), true);
$itemId = $data['itemId'];

// After successful login authentication
if (isset($_SESSION['kosar']) && !empty($_SESSION['kosar'])) {
    $userId = $_SESSION['felhasznalo_id']; // The newly logged-in user's ID
    
    foreach ($_SESSION['kosar'] as $etelId => $quantity) {
        // Check if item already exists in user's cart
        $checkQuery = "SELECT darab FROM tetelek WHERE felhasznalo_id = ? AND etel_id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ii", $userId, $etelId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing quantity
            $row = $result->fetch_assoc();
            $newQuantity = $row['darab'] + $quantity;
            
            $updateQuery = "UPDATE tetelek SET darab = ? WHERE felhasznalo_id = ? AND etel_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("iii", $newQuantity, $userId, $etelId);
            $updateStmt->execute();
        } else {
            // Insert new item
            $insertQuery = "INSERT INTO tetelek (felhasznalo_id, etel_id, darab) VALUES (?, ?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("iii", $userId, $etelId, $quantity);
            $insertStmt->execute();
        }
    }
    
    // Clear the guest cart
    unset($_SESSION['kosar']);
    setcookie('guest_cart', '', time() - 3600, '/'); 
}

echo json_encode(['success' => true]);
?>