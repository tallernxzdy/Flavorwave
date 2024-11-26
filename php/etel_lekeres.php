<?php
header('Content-Type: application/json');
$host = 'localhost';
$dbname = 'flavorwave';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM etel WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $etel = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($etel);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}