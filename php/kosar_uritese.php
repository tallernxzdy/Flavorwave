<?php
session_start();
include 'adatbazisra_csatlakozas.php';

if (isset($_SESSION['felhasznalo_id'])) {
    // Logged in user - clear database cart
    $userId = $_SESSION['felhasznalo_id'];
    $query = "DELETE FROM tetelek WHERE felhasznalo_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
} else {
    // Guest user - clear session and delete cookie
    if (isset($_SESSION['kosar'])) {
        unset($_SESSION['kosar']);
        setcookie('guest_cart', '', time() - 3600, '/'); // Delete cookie
    }
}

echo json_encode(['success' => true]);
?>