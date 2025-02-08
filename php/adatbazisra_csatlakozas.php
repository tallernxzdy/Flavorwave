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
    if ($conn) {
        $stmt = $conn->prepare($muvelet);
        if ($stmt) {
            if (!empty($parameterek)) {
                $stmt->bind_param(...$parameterek);
            }
            $stmt->execute();
            $eredmeny = $stmt->get_result();
            if ($eredmeny->num_rows > 0) {
                return $eredmeny->fetch_all(MYSQLI_ASSOC);
            } else {
                return [];
            }
        }
        return $conn->error;
    } else {
        return $conn->connect_error;
    }
}

// SQL function módosításhoz:
function adatokValtoztatasa($muvelet, $parameterek) {
    global $conn;
    if ($conn) {
        $stmt = $conn->prepare($muvelet);
        if ($stmt) {
            // A típusdefiníciós string és a paraméterek szétválasztása
            $tipusok = array_shift($parameterek); // Az első elem a típusdefiníciós string
            $stmt->bind_param($tipusok, ...$parameterek); // A többi elem a paraméterek
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                return 'Sikeres művelet!';
            } else if ($stmt->affected_rows == 0) {
                return 'Sikertelen művelet!';
            } else {
                return $stmt->error;
            }
        } else {
            return $conn->error;
        }
    } else {
        return $conn->connect_error;
    }
}

// SQL function törléshez:
function adatokTorlese($feltetel) {
    global $conn;
    if ($conn) {
        $muvelet = "DELETE FROM `etel` WHERE $feltetel";
        $conn->query($muvelet);
        if ($conn->errno == 0) {
            if ($conn->affected_rows > 0) {
                return 'Sikeres törlés!';
            } else {
                return 'Nem történt törlés!';
            }
        }
        return $conn->error;
    } else {
        return $conn->connect_error;
    }
}

// SQL function feltöltéshez:
function adatokFeltoltese() {
    global $conn;
    if ($conn) {
        $muvelet = "INSERT INTO `etel` (id, nev, egyseg_ar, leiras, kategoria_id) VALUES (?, ?, ?, ?, ?)";
        $conn->query($muvelet);
        if ($conn->errno == 0) {
            return 'Sikeres feltöltés!';
        }
        return $conn->error;
    } else {
        return $conn->connect_error;
    }
}
?>
