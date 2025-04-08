<?php
    if (!isset($conn) || !($conn instanceof mysqli) || $conn->connect_errno != 0) {
        $conn = new mysqli('localhost', 'root', '', 'flavorwave');
        
        // Ellenőrizzük, hogy sikerült-e a kapcsolat
        if ($conn->connect_errno != 0) {
            die("Az adatbázis kapcsolódás nem sikerült: " . $conn->connect_error);
        }
        
        // Karakterkódolás beállítása
        $conn->set_charset("utf8mb4");
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
function adatokTorlese($id) {
    global $conn;
    if (!$conn) {
        return "Nincs aktív adatbázis kapcsolat.";
    }

    $sql = "DELETE FROM etel WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return "Hiba a lekérdezés előkészítésekor: " . $conn->error;
    }

    $stmt->bind_param("i", $id);
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

function handleImageUpload($categoryId, $uploadedFile, $kepNev) {
    $baseDir = '../kepek/';
    if (!file_exists($baseDir . 'osszeskep/')) {
        mkdir($baseDir . 'osszeskep/', 0755, true);
    }
    $categoryDir = $baseDir . $categoryId . '/';
    if (!file_exists($categoryDir)) {
        mkdir($categoryDir, 0755, true);
    }

    $extension = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
    $baseFileName = preg_replace('/[^a-z0-9\-_]/i', '', $kepNev);
    $fileName = $baseFileName . '.' . $extension;

    $existingFiles = glob($categoryDir . $baseFileName . '.*');
    if (!empty($existingFiles)) {
        return false; // Már létezik ilyen nevű fájl
    }

    $targetPath = $categoryDir . $fileName;
    if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
        copy($targetPath, $baseDir . 'osszeskep/' . $fileName);
        return "$categoryId/$fileName"; // Relatív útvonal az adatbázishoz
    }
    return false;
}

function renameImageFile($oldPath, $newName) {
    $baseDir = '../kepek/';
    $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
    $oldFullPath = $baseDir . $oldPath;
    $oldOsszeskepPath = $baseDir . 'osszeskep/' . basename($oldPath);

    $newFileName = preg_replace('/[^a-z0-9\-_]/i', '', $newName) . '.' . $extension;
    $newCategoryPath = dirname($oldFullPath) . '/' . $newFileName;
    $newOsszeskepPath = $baseDir . 'osszeskep/' . $newFileName;

    if (!file_exists($oldFullPath)) {
        return false; // Ha a régi fájl nem létezik
    }

    if (rename($oldFullPath, $newCategoryPath) && rename($oldOsszeskepPath, $newOsszeskepPath)) {
        return dirname($oldPath) . '/' . $newFileName;
    }
    return false;
}

function deleteImageFiles($imagePath) {
    $baseDir = '../kepek/';
    $mainPath = $baseDir . $imagePath;
    $osszeskepPath = $baseDir . 'osszeskep/' . basename($imagePath);

    if (file_exists($mainPath)) {
        unlink($mainPath);
    }
    if (file_exists($osszeskepPath)) {
        unlink($osszeskepPath);
    }
    return true;
}
?>