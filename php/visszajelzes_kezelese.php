<?php

    include "adatbazisra_csatlakozas.php";
    

    // próba

    // Ha nincs felhasználó ID (pl. nincs bejelentkezve a felhasználó), akkor figyelmeztetés
    if (!$felhasznalo_id) {
        echo json_encode([
            "success" => false,
            "message" => "Kérlek, jelentkezz be a vélemény írásához!"
        ]);
        exit;
    }

    // Az adatbázis művelet
    $muvelet = "INSERT INTO velemenyek (felhasznalo_id, velemeny_szoveg, ertekeles) VALUES (?, ?, ?)";
    $parameterek = [
        "ssi",  // 's' = string (felhasznalo_id és velemeny_szoveg), 'i' = integer (ertekeles)
        $felhasznalo_id,  // A bejelentkezett felhasználó ID-ja
        $velemeny_szoveg,  // A vélemény szövege
        $ertekeles  // A pontszám
    ];

    $eredmeny = adatokValtoztatasa($muvelet, $parameterek);

    // Válasz a JavaScript-nek
    if ($eredmeny === "Sikeres művelet!") {
        echo json_encode([
            "success" => true,
            "message" => "Köszönjük a visszajelzésedet!"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Hiba történt: " . $eredmeny
        ]);
    }


    // próba vége


    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $input = json_decode(file_get_contents("php://input"), true);

        // Biztosítsuk, hogy a bemenet megfelelő
        if (!$input) {
            echo json_encode(["success" => false, "message" => "Érvénytelen JSON bemenet!"]);
            exit;
        }

        $ertekeles = $input["rating"] ?? null;
        $velemeny_szoveg = $input["opinion"] ?? null;
        $felhasznalo_id = $input["email"] ?? null;

        // Ellenőrzés, hogy minden kötelező adat megvan-e
        if (!$ertekeles || !$velemeny_szoveg) {
            echo json_encode([
                "success" => false,
                "message" => "Értékelés és vélemény megadása kötelező!"
            ]);
            exit;
        }

        // Ha nincs email, beállítjuk a felhasznalo_id-t null-ra
        if (empty($felhasznalo_id)) {
            $felhasznalo_id = null;
        }

        // Adatok beszúrása az adatbázisba
        $muvelet = "INSERT INTO velemenyek (felhasznalo_id, velemeny_szoveg, ertekeles) VALUES (?, ?, ?)";
        $parameterek = [
            "ssi",  // 's' = string (felhasznalo_id és velemeny_szoveg), 'i' = integer (ertekeles)
            $felhasznalo_id,
            $velemeny_szoveg,
            $ertekeles
        ];

        $eredmeny = adatokValtoztatasa($muvelet, $parameterek);

        // Sikeres beszúrás ellenőrzése
        if ($eredmeny === "Sikeres művelet!") {
            echo json_encode([
                "success" => true,
                "message" => "Köszönjük a visszajelzésedet!"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Hiba történt: " . $eredmeny
            ]);
        }
    } else {
        echo json_encode([ 
            "success" => false, 
            "message" => "Érvénytelen kérés." 
        ]);
    }

?>