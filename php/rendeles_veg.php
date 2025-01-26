<?php
session_start();
include 'adatbazisra_csatlakozas.php';

if (!isset($_SESSION['felhasznalo_id'])) {
    header("Location: bejelentkezes.php");
    exit();
}

$userId = $_SESSION['felhasznalo_id'];

// Kosár tartalmának lekérése
$query = "SELECT etel.id, etel.nev, etel.egyseg_ar, tetelek.darab 
          FROM tetelek 
          JOIN etel ON tetelek.etel_id = etel.id 
          WHERE tetelek.felhasznalo_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Rendelés létrehozása
$query = "INSERT INTO megrendeles (felhasznalo_id, leadas_megjegyzes, kezbesites, leadas_allapota, leadasdatuma) 
          VALUES (?, '', 'szállítás', 0, NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$orderId = $stmt->insert_id;

// Rendelés tételek mentése
while ($row = $result->fetch_assoc()) {
    $query = "INSERT INTO rendeles_tetel (rendeles_id, termek_id, mennyiseg) 
              VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $orderId, $row['id'], $row['darab']);
    $stmt->execute();
}

// Kosár ürítése
$query = "DELETE FROM tetelek WHERE felhasznalo_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();

echo "Rendelés sikeresen leadva!";
?>