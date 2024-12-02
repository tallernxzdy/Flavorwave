<?php

// SQL function lekérdezésekhez:
function adatokLekerdezese($muvelet) {
    $db = new mysqli ('localhost', 'root', '', 'flavorwave');
    if ($db->connect_errno == 0 ) {
        $eredmeny = $db->query($muvelet);
        if ($db->errno == 0) {
            if ($eredmeny->num_rows != 0) {
                return $adatok = $eredmeny->fetch_all(MYSQLI_ASSOC);
            } else {
                return 'Nincs találat!';
            }
        }
        return $db->error;
    } else {
        return $db->connect_error;
    }
}

// SQL function módosításhoz:
function adatokValtoztatasa($muvelet, $parameterek) {
    $db = new mysqli('localhost', 'root', '', 'flavorwave');
    if ($db->connect_errno == 0) {
        $stmt = $db->prepare($muvelet);
        if ($stmt) {
            $stmt->bind_param(...$parameterek); // Paramétereket köt hozzá
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                return 'Sikeres művelet!';
            } else if ($stmt->affected_rows == 0) {
                return 'Sikertelen művelet!';
            } else {
                return $stmt->error;
            }
        } else {
            return $db->error;
        }
    } else {
        return $db->connect_error;
    }
}


// SQL function törléshez:
function adatokTorlese($feltetel) {
    $db = new mysqli('localhost', 'root', '', 'flavorwave');
    if ($db->connect_errno == 0) {
        $muvelet = "DELETE FROM `etel` WHERE $feltetel";
        $db->query($muvelet);
        if ($db->errno == 0) {
            if ($db->affected_rows > 0) {
                return 'Sikeres törlés!';
            } else {
                return 'Nem történt törlés!';
            }
        }
        return $db->error;
    } else {
        return $db->connect_error;
    }
}


// SQL function feltöltéshez:
function adatokFeltoltese() {
    $db = new mysqli ('localhost', 'root', '', 'flavorwave');
    if ($db->connect_errno == 0 ) {
        $muvelet = "INSERT INTO `etel` (id, nev, egyseg_ar, leiras, kategoria_id)	VALUES (?, ?, ?, ?, ?)";
        $db->query($muvelet);
        if ($db->errno == 0) {
            return 'Sikeres feltöltés!';
        }
        return $db->error;
    } else {
        return $db->connect_error;
    }
}

?>
