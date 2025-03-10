<?php
// Az adatbázis kapcsolat létrehozása
$conn = new mysqli('localhost', 'root', '', 'flavorwave');

// Ellenőrizzük, hogy sikerült-e a kapcsolat
if ($conn->connect_errno != 0) {
    die("Az adatbázis kapcsolódás nem sikerült: " . $conn->connect_error);
}

// SQL function lekérdezésekhez:
function adatokLekerdezese($muvelet, $parameterek = []) {
    global $conn;  // Globálisan elérhetővé tesszük a $conn változót
    if (!$conn) {
        return "Nincs aktív adatbázis kapcsolat.";
    }

    $stmt = $conn->prepare($muvelet);
    if (!$stmt) {
        return "Hiba a lekérdezés előkészítésekor: " . $conn->error;
    }

    if (!empty($parameterek)) {
        $stmt->bind_param(...$parameterek);
    }

    if (!$stmt->execute()) {
        return "Hiba a lekérdezés végrehajtásakor: " . $stmt->error;
    }

    $eredmeny = $stmt->get_result();
    if ($eredmeny->num_rows > 0) {
        return $eredmeny->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

// SQL function módosításhoz:
function adatokValtoztatasa($muvelet, $parameterek) {
    global $conn;
    if (!$conn) {
        return "Nincs aktív adatbázis kapcsolat.";
    }

    $stmt = $conn->prepare($muvelet);
    if (!$stmt) {
        return "Hiba a lekérdezés előkészítésekor: " . $conn->error;
    }

    // A típusdefiníciós string és a paraméterek szétválasztása
    $tipusok = array_shift($parameterek); // Az első elem a típusdefiníciós string
    $stmt->bind_param($tipusok, ...$parameterek); // A többi elem a paraméterek

    if (!$stmt->execute()) {
        return "Hiba a lekérdezés végrehajtásakor: " . $stmt->error;
    }

    if ($stmt->affected_rows > 0) {
        return 'Sikeres művelet!';
    } else {
        return 'Nem történt változtatás.';
    }
}

// SQL function törléshez:
function adatokTorlese($muvelet, $parameterek = []) {
    global $conn;
    if (!$conn) {
        return "Nincs aktív adatbázis kapcsolat.";
    }

    $stmt = $conn->prepare($muvelet);
    if (!$stmt) {
        return "Hiba a lekérdezés előkészítésekor: " . $conn->error;
    }

    if (!empty($parameterek)) {
        $stmt->bind_param(...$parameterek);
    }

    if (!$stmt->execute()) {
        return "Hiba a lekérdezés végrehajtásakor: " . $stmt->error;
    }

    if ($stmt->affected_rows > 0) {
        return 'Sikeres törlés!';
    } else {
        return 'Nem történt törlés.';
    }
}

// Rendelések lekérdezése állapot szerint
function rendeleseLekerdezese($allapot) {
    global $conn;
    if (!$conn) {
        return "Nincs aktív adatbázis kapcsolat.";
    }

    $stmt = $conn->prepare("
        SELECT megrendeles.id, felhasznalo.felhasznalo_nev 
        FROM megrendeles
        JOIN felhasznalo ON megrendeles.felhasznalo_id = felhasznalo.id
        WHERE megrendeles.leadas_allapota = ?
    ");
    
    if (!$stmt) {
        return "Hiba a lekérdezés előkészítésekor: " . $conn->error;
    }
    
    $stmt->bind_param("i", $allapot);
    
    if (!$stmt->execute()) {
        return "Hiba a lekérdezés végrehajtásakor: " . $stmt->error;
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}
?>