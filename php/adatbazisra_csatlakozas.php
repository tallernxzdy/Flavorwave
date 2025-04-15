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


    // láthatóvá tett fájlelnevezés
    function safeFileName($string) {
        $string = str_replace(' ', '-', $string);
        $string = preg_replace('/[^a-zA-Z0-9\-\p{L}]/u', '', $string);
        $string = preg_replace('/-+/', '-', $string);
        $string = mb_strtolower($string, 'UTF-8');
        return trim($string, '-');
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
        $baseFileName = safeFileName($kepNev); // safeFileName használata
        $fileName = $baseFileName . '.' . $extension;
        
        $targetPath = $categoryDir . $fileName;
        if (file_exists($targetPath)) {
            return false; // Már létezik ilyen nevű fájl
        }
        
        if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
            copy($targetPath, $baseDir . 'osszeskep/' . $fileName);
            return $fileName; // Csak a fájlnevet adjuk vissza
        }
        
        return false;
    }

    function renameImageFile($oldPath, $newName) {
        $baseDir = '../kepek/';
        $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
        $oldFileName = basename($oldPath);
        $categoryId = strtok($oldPath, '/'); // Kategória ID kinyerése
        
        $oldFullPath = $baseDir . $oldPath;
        $oldOsszeskepPath = $baseDir . 'osszeskep/' . $oldFileName;
    
        $newFileName = safeFileName($newName) . '.' . $extension; // safeFileName használata
        $newCategoryPath = $baseDir . $categoryId . '/' . $newFileName;
        $newOsszeskepPath = $baseDir . 'osszeskep/' . $newFileName;
    
        if (file_exists($oldFullPath) && !file_exists($newCategoryPath)) {
            if (rename($oldFullPath, $newCategoryPath)) {
                if (file_exists($oldOsszeskepPath)) {
                    rename($oldOsszeskepPath, $newOsszeskepPath);
                }
                return $newFileName;
            }
        }
        return false;
    }


    function moveImageToCategory($oldCategoryId, $newCategoryId, $fileName) {
        $baseDir = '../kepek/';
        $oldPath = $baseDir . $oldCategoryId . '/' . $fileName;
        $newCategoryDir = $baseDir . $newCategoryId . '/';
        $newPath = $newCategoryDir . $fileName;
        $osszeskepPath = $baseDir . 'osszeskep/' . $fileName;

        // Ellenőrizzük, hogy létezik-e a régi fájl
        if (!file_exists($oldPath)) {
            return false; // A fájl nem létezik, nem tudjuk áthelyezni
        }

        // Hozzuk létre az új kategória mappát, ha nem létezik
        if (!file_exists($newCategoryDir)) {
            mkdir($newCategoryDir, 0755, true);
        }

        // Ellenőrizzük, hogy az új helyen nem létezik-e már a fájl
        if (file_exists($newPath)) {
            return false; // Már létezik ilyen nevű fájl az új kategóriában
        }

        // Próbáljuk meg áthelyezni a fájlt
        if (rename($oldPath, $newPath)) {
            // Frissítsük az osszeskep mappát
            if (file_exists($osszeskepPath)) {
                unlink($osszeskepPath); // Töröljük a régi másolatot
            }
            copy($newPath, $osszeskepPath); // Másoljuk az új helyre
            return $fileName; // Visszaadjuk a fájlnevet
        }

        return false; // Sikertelen áthelyezés
    }


    function deleteImageFiles($imagePath, $categoryId = null) {
        $baseDir = '../kepek/';
        $mainPath = $categoryId ? $baseDir . $categoryId . '/' . $imagePath : $baseDir . $imagePath;
        $osszeskepPath = $baseDir . 'osszeskep/' . basename($imagePath);
        
        if (file_exists($mainPath)) unlink($mainPath);
        if (file_exists($osszeskepPath)) unlink($osszeskepPath);
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

?>