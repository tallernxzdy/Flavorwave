<?php
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);
    $rating = $input["rating"] ?? null;
    $opinion = $input["opinion"] ?? null;
    $email = $input["email"] ?? null;

    if (!$rating || !$opinion) {
        echo json_encode(["success" => false, "message" => "Minden kötelező mezőt tölts ki!"]);
        exit;
    }

    // Adatok mentése (pl. adatbázisba)
    // Példa: Adatbázis kapcsolat
    // $conn = new mysqli("localhost", "username", "password", "database");
    // $stmt = $conn->prepare("INSERT INTO feedback (rating, opinion, email) VALUES (?, ?, ?)");
    // $stmt->bind_param("iss", $rating, $opinion, $email);
    // $stmt->execute();

    echo json_encode(["success" => true, "message" => "Köszönjük a visszajelzésedet!"]);
} else {
    echo json_encode(["success" => false, "message" => "Érvénytelen kérés."]);
}
