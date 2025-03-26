<?php
session_start();
include 'adatbazisra_csatlakozas.php';

function getCartItemCount($conn) {
    $itemCount = 0;

    if (isset($_SESSION['felhasznalo_id'])) {
        // Bejelentkezett felhasználó - lekérdezés az adatbázisból
        $userId = $_SESSION['felhasznalo_id'];
        $query = "SELECT SUM(darab) as total FROM tetelek WHERE felhasznalo_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $itemCount = $row['total'] ? (int)$row['total'] : 0;
    } else {
        // Vendég felhasználó - lekérdezés a session-ből
        if (isset($_SESSION['kosar']) && !empty($_SESSION['kosar'])) {
            foreach ($_SESSION['kosar'] as $itemId => $quantity) {
                $itemCount += (int)$quantity;
            }
        }
    }

    return $itemCount;
}

header('Content-Type: application/json');
echo json_encode(['count' => getCartItemCount($conn)]);
?>